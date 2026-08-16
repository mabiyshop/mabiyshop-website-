<?php

/**
 * Temporary local-only, read-only audit for AddressLocationResolver.
 *
 * Usage:
 *   php resolver_audit_tmp.php --sanity
 *   php resolver_audit_tmp.php
 *
 * The script only issues SELECT queries. Laravel's persistent cache is replaced
 * with the in-memory array driver, and a query listener aborts on write SQL.
 */

use App\Services\AddressLocationResolver;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

$startedAt = microtime(true);
$sanity = in_array('--sanity', $argv, true);
$diagnostic = in_array('--diagnostic-failures', $argv, true);
$projectRoot = __DIR__ . DIRECTORY_SEPARATOR . 'system_operator';

require $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$app = require $projectRoot . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';
$app->make(Kernel::class)->bootstrap();

config(['cache.default' => 'array']);
Cache::setDefaultDriver('array');

DB::listen(function ($query) {
    $sql = ltrim((string) $query->sql);
    if (!preg_match('/^(select|show|describe|desc|explain|pragma|with)\b/i', $sql)) {
        throw new RuntimeException('Read-only audit blocked SQL: ' . $sql);
    }
});

$resolver = $app->make(AddressLocationResolver::class);
$structuredMethod = new ReflectionMethod(AddressLocationResolver::class, 'structuredVariants');
$structuredMethod->setAccessible(true);

$districts = DB::table('districts')
    ->select('id', 'title', 'city_id')
    ->orderBy('id')
    ->get();
$upazilas = DB::table('upazilas')
    ->select('id', 'district_id', 'title', 'zone_id')
    ->orderBy('id')
    ->get();
$areas = DB::table('unions')
    ->select('id', 'upazila_id', 'title', 'area_id')
    ->orderBy('id')
    ->get();

$districtsById = $districts->keyBy('id');
$upazilasById = $upazilas->keyBy('id');

$stats = [
    'total' => 0,
    'passed' => 0,
    'failed' => 0,
    'safe_rejected' => 0,
];
$failures = [];
$failureSamples = [];

$record = function ($category, $outcome, $message = '') use (&$stats, &$failures, &$failureSamples) {
    $stats['total']++;
    $stats[$outcome]++;
    if ($outcome !== 'failed') {
        return;
    }

    $failures[$category] = ($failures[$category] ?? 0) + 1;
    if (count($failureSamples) < 20) {
        $failureSamples[] = '[' . $category . '] ' . $message;
    }
};

$candidateForUpazila = function (array $result, $upazilaId) {
    foreach ($result['candidates'] ?? [] as $candidate) {
        if ((int) ($candidate['upazila_id'] ?? 0) === (int) $upazilaId) {
            return $candidate;
        }
    }
    return null;
};

$topCandidate = function (array $result) {
    return $result['candidates'][0] ?? null;
};

$assertCandidateMapping = function (
    $category,
    array $result,
    $district,
    $upazila,
    $area = null,
    $expectedEvidence = null
) use ($record, $candidateForUpazila) {
    $candidate = $candidateForUpazila($result, $upazila->id);
    if (!$candidate) {
        $record($category, 'failed', 'Missing upazila candidate #' . $upazila->id . ' for result ' . ($result['match_type'] ?? 'unknown'));
        return;
    }

    $valid = (int) ($candidate['district_id'] ?? 0) === (int) $district->id
        && (string) ($candidate['city_id'] ?? '') === (string) $district->city_id
        && (string) ($candidate['zone_id'] ?? '') === (string) $upazila->zone_id;

    if ($area) {
        $valid = $valid
            && (int) ($candidate['matched_area_id'] ?? 0) === (int) $area->id
            && (string) ($candidate['area_id'] ?? '') === (string) $area->area_id;
    }

    if ($expectedEvidence !== null) {
        $evidence = $candidate['location_evidence'] ?? [];
        $valid = $valid && is_array($evidence) && in_array($expectedEvidence, $evidence, true);
    }

    $record(
        $category,
        $valid ? 'passed' : 'failed',
        'Mapping/evidence mismatch for upazila #' . $upazila->id . ($area ? ', area #' . $area->id : '')
    );
};

$toBengaliDigits = function ($value) {
    return strtr((string) $value, ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯']);
};

if ($diagnostic) {
    $normalizeMethod = new ReflectionMethod(AddressLocationResolver::class, 'normalize');
    $normalizeMethod->setAccessible(true);
    $diagnosticFailures = [];
    $diagnosticCounts = [
        'tests' => 0,
        'area_mapping' => 0,
        'structured_variant' => 0,
        'ambiguity_safety' => 0,
    ];

    $mappingFailure = function ($category, $record, $district, $upazila, $address, array $result, $area = null, $variant = null) use (
        &$diagnosticFailures,
        &$diagnosticCounts,
        $candidateForUpazila,
        $normalizeMethod,
        $resolver,
        $upazilasById
    ) {
        $candidate = $candidateForUpazila($result, $upazila->id);
        $valid = $candidate
            && (int) ($candidate['district_id'] ?? 0) === (int) $district->id
            && (string) ($candidate['city_id'] ?? '') === (string) $district->city_id
            && (string) ($candidate['zone_id'] ?? '') === (string) $upazila->zone_id;
        if ($area) {
            $valid = $valid
                && (int) ($candidate['matched_area_id'] ?? 0) === (int) $area->id
                && (string) ($candidate['area_id'] ?? '') === (string) $area->area_id;
        }
        if ($variant !== null) {
            $valid = $valid
                && is_array($candidate['location_evidence'] ?? null)
                && in_array($variant, $candidate['location_evidence'], true);
        }
        if ($valid) {
            return;
        }

        $diagnosticCounts[$category]++;
        $group = 'missing_or_wrong_parent_candidate';
        if ($candidate && $area && isset($candidate['matched_area_id'])) {
            $actualArea = DB::table('unions')
                ->select('id', 'upazila_id', 'title', 'area_id')
                ->where('id', $candidate['matched_area_id'])
                ->first();
            if ($actualArea) {
                $expectedTitle = $normalizeMethod->invoke($resolver, $area->title);
                $actualTitle = $normalizeMethod->invoke($resolver, $actualArea->title);
                $parentTitle = $normalizeMethod->invoke($resolver, $upazila->title);
                if ($actualTitle === $expectedTitle) {
                    $group = 'duplicate_authoritative_title_same_parent';
                } elseif ($actualTitle === $parentTitle) {
                    $group = 'parent_context_title_competition';
                } elseif (strpos($actualTitle, $expectedTitle) !== false || strpos($expectedTitle, $actualTitle) !== false) {
                    $group = 'substring_locality_collision';
                } else {
                    $group = 'same_upazila_competing_localities';
                }
            }
        } elseif ($candidate && $variant !== null) {
            $group = 'structured_evidence_or_mapping_mismatch';
        }

        $diagnosticFailures[] = [
            'category' => $category,
            'root_cause_group' => $group,
            'authoritative_record' => [
                'kind' => $area ? 'area' : 'upazila',
                'id' => (int) $record->id,
                'title' => $record->title,
            ],
            'parent' => [
                'district_id' => (int) $district->id,
                'district_title' => $district->title,
                'upazila_id' => (int) $upazila->id,
                'upazila_title' => $upazila->title,
            ],
            'input' => $address,
            'variant' => $variant,
            'expected' => [
                'match_candidate_upazila_id' => (int) $upazila->id,
                'district_id' => (int) $district->id,
                'city_id' => $district->city_id,
                'zone_id' => $upazila->zone_id,
                'matched_area_id' => $area ? (int) $area->id : null,
                'area_id' => $area ? $area->area_id : null,
                'location_evidence_contains' => $variant,
            ],
            'actual' => [
                'match_type' => $result['match_type'] ?? null,
                'candidate_for_expected_upazila' => $candidate,
                'candidates' => $result['candidates'] ?? [],
            ],
        ];
    };

    foreach ($areas as $area) {
        $diagnosticCounts['tests']++;
        $upazila = $upazilasById->get($area->upazila_id);
        $district = $upazila ? $districtsById->get($upazila->district_id) : null;
        if (!$upazila || !$district) {
            $diagnosticCounts['area_mapping']++;
            $diagnosticFailures[] = [
                'category' => 'area_mapping',
                'root_cause_group' => 'missing_authoritative_parent',
                'authoritative_record' => ['kind' => 'area', 'id' => (int) $area->id, 'title' => $area->title],
            ];
            continue;
        }
        $address = $area->title . ' ' . $upazila->title . ' ' . $district->title;
        $mappingFailure('area_mapping', $area, $district, $upazila, $address, $resolver->resolve($address), $area);
    }

    $structuredRows = [];
    foreach ($upazilas as $upazila) {
        $variants = $structuredMethod->invoke($resolver, (string) $upazila->title);
        if (count($variants) > 1) {
            $structuredRows[] = ['kind' => 'upazila', 'row' => $upazila, 'variants' => $variants];
        }
    }
    foreach ($areas as $area) {
        $variants = $structuredMethod->invoke($resolver, (string) $area->title);
        if (count($variants) > 1) {
            $structuredRows[] = ['kind' => 'area', 'row' => $area, 'variants' => $variants];
        }
    }
    foreach ($structuredRows as $structured) {
        $row = $structured['row'];
        $upazila = $structured['kind'] === 'upazila' ? $row : $upazilasById->get($row->upazila_id);
        $district = $upazila ? $districtsById->get($upazila->district_id) : null;
        if (!$upazila || !$district) {
            continue;
        }
        foreach ($structured['variants'] as $variant) {
            $diagnosticCounts['tests']++;
            $address = $variant . ' ' . $district->title;
            $mappingFailure(
                'structured_variant',
                $row,
                $district,
                $upazila,
                $address,
                $resolver->resolve($address),
                $structured['kind'] === 'area' ? $row : null,
                $variant
            );
        }
    }

    $duplicateTitles = $upazilas
        ->groupBy(function ($upazila) { return mb_strtolower(trim((string) $upazila->title), 'UTF-8'); })
        ->filter(function ($group) { return $group->count() > 1; });
    foreach ($duplicateTitles as $title => $group) {
        $diagnosticCounts['tests']++;
        $result = $resolver->resolve((string) $group->first()->title);
        if (($result['match_type'] ?? '') !== 'strong') {
            continue;
        }
        $diagnosticCounts['ambiguity_safety']++;
        $diagnosticFailures[] = [
            'category' => 'ambiguity_safety',
            'root_cause_group' => 'duplicate_upazila_title_resolved_strong',
            'authoritative_records' => $group->map(function ($row) use ($districtsById) {
                $district = $districtsById->get($row->district_id);
                return [
                    'id' => (int) $row->id,
                    'title' => $row->title,
                    'district_id' => (int) $row->district_id,
                    'district_title' => $district ? $district->title : null,
                    'zone_id' => $row->zone_id,
                ];
            })->values()->all(),
            'input' => (string) $group->first()->title,
            'expected' => ['match_type' => 'ambiguous'],
            'actual' => $result,
        ];
    }

    $payload = [
        'mode' => 'targeted_failure_diagnostic',
        'authoritative_rows_loaded' => [
            'districts' => $districts->count(),
            'upazilas' => $upazilas->count(),
            'areas' => $areas->count(),
        ],
        'counts' => $diagnosticCounts,
        'runtime_seconds' => round(microtime(true) - $startedAt, 2),
        'failures' => $diagnosticFailures,
    ];
    $outputPath = __DIR__ . DIRECTORY_SEPARATOR . 'resolver_audit_failures_tmp.json';
    file_put_contents($outputPath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo json_encode([
        'output' => basename($outputPath),
        'counts' => $diagnosticCounts,
        'runtime_seconds' => $payload['runtime_seconds'],
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), PHP_EOL;
    exit(0);
}

$districtRows = $sanity ? $districts->take(3) : $districts;
$upazilaRows = $sanity ? $upazilas->take(3) : $upazilas;
$areaRows = $sanity ? $areas->take(3) : $areas;

foreach ($districtRows as $district) {
    $result = $resolver->resolve((string) $district->title);
    $recognized = (int) ($result['district']['district_id'] ?? 0) === (int) $district->id;
    if (!$recognized) {
        foreach ($result['candidates'] ?? [] as $candidate) {
            if ((int) ($candidate['district_id'] ?? 0) === (int) $district->id) {
                $recognized = true;
                break;
            }
        }
    }
    $cityMatches = !$recognized
        ? false
        : !isset($result['district']['district_id'])
            || (string) ($result['district']['city_id'] ?? '') === (string) $district->city_id;
    $record('district_recognition', $recognized && $cityMatches ? 'passed' : 'failed', 'District #' . $district->id . ' (' . $district->title . ')');
}

foreach ($upazilaRows as $upazila) {
    $district = $districtsById->get($upazila->district_id);
    if (!$district) {
        $record('upazila_parent', 'failed', 'Missing district #' . $upazila->district_id . ' for upazila #' . $upazila->id);
        continue;
    }
    $result = $resolver->resolve($upazila->title . ' ' . $district->title);
    $assertCandidateMapping('upazila_zone_mapping', $result, $district, $upazila);
}

foreach ($areaRows as $area) {
    $upazila = $upazilasById->get($area->upazila_id);
    $district = $upazila ? $districtsById->get($upazila->district_id) : null;
    if (!$upazila || !$district) {
        $record('area_parent', 'failed', 'Missing parent for area #' . $area->id);
        continue;
    }
    $result = $resolver->resolve($area->title . ' ' . $upazila->title . ' ' . $district->title);
    $assertCandidateMapping('area_mapping', $result, $district, $upazila, $area);
}

$structuredRows = [];
foreach ($upazilas as $upazila) {
    $variants = $structuredMethod->invoke($resolver, (string) $upazila->title);
    if (count($variants) > 1) {
        $structuredRows[] = ['kind' => 'upazila', 'row' => $upazila, 'variants' => $variants];
    }
}
foreach ($areas as $area) {
    $variants = $structuredMethod->invoke($resolver, (string) $area->title);
    if (count($variants) > 1) {
        $structuredRows[] = ['kind' => 'area', 'row' => $area, 'variants' => $variants];
    }
}
if ($sanity) {
    $structuredRows = array_slice($structuredRows, 0, 5);
}

foreach ($structuredRows as $structured) {
    $row = $structured['row'];
    $upazila = $structured['kind'] === 'upazila' ? $row : $upazilasById->get($row->upazila_id);
    $district = $upazila ? $districtsById->get($upazila->district_id) : null;
    if (!$upazila || !$district) {
        $record('structured_parent', 'failed', 'Missing parent for structured ' . $structured['kind'] . ' #' . $row->id);
        continue;
    }

    foreach ($structured['variants'] as $variant) {
        if (preg_match('/^\d+$/u', $variant)) {
            $record('bare_number_generation', 'failed', 'Generated bare number "' . $variant . '" from "' . $row->title . '"');
            continue;
        }
        $record('bare_number_generation', 'passed');

        $address = $variant . ' ' . $district->title;
        $result = $resolver->resolve($address);
        $assertCandidateMapping(
            'structured_variant',
            $result,
            $district,
            $upazila,
            $structured['kind'] === 'area' ? $row : null,
            $variant
        );

        if (preg_match('/\d/u', $variant)) {
            $bengaliVariant = $toBengaliDigits($variant);
            $bengaliResult = $resolver->resolve($bengaliVariant . ' ' . $district->title);
            $assertCandidateMapping(
                'bengali_digit_normalization',
                $bengaliResult,
                $district,
                $upazila,
                $structured['kind'] === 'area' ? $row : null
            );
        }
    }
}

$contradictionRows = $sanity ? $upazilas->take(2) : $upazilas;
foreach ($contradictionRows as $upazila) {
    $ownDistrict = $districtsById->get($upazila->district_id);
    $wrongDistrict = $districts->first(function ($district) use ($upazila) {
        return (int) $district->id !== (int) $upazila->district_id;
    });
    if (!$ownDistrict || !$wrongDistrict) {
        continue;
    }
    $result = $resolver->resolve($upazila->title . ' ' . $wrongDistrict->title);
    $top = $topCandidate($result);
    $unsafe = ($result['match_type'] ?? '') === 'strong'
        && (int) ($top['upazila_id'] ?? 0) === (int) $upazila->id;
    $record('contradictory_district', $unsafe ? 'failed' : 'safe_rejected', 'Upazila #' . $upazila->id . ' accepted under district #' . $wrongDistrict->id);
}

$duplicateTitles = $upazilas
    ->groupBy(function ($upazila) { return mb_strtolower(trim((string) $upazila->title), 'UTF-8'); })
    ->filter(function ($group) { return $group->count() > 1; });
if ($sanity) {
    $duplicateTitles = $duplicateTitles->take(2);
}
foreach ($duplicateTitles as $title => $group) {
    $result = $resolver->resolve((string) $group->first()->title);
    $record(
        'ambiguity_safety',
        ($result['match_type'] ?? '') === 'strong' ? 'failed' : 'safe_rejected',
        'Duplicate authoritative upazila title resolved strong: ' . $title
    );
}

ksort($failures);
$runtime = microtime(true) - $startedAt;

echo PHP_EOL;
echo 'Address resolver audit: ' . ($sanity ? 'SANITY' : 'FULL') . PHP_EOL;
echo 'Authoritative rows loaded: districts=' . $districts->count()
    . ', upazilas=' . $upazilas->count()
    . ', areas=' . $areas->count() . PHP_EOL;
echo 'Structured authoritative titles tested: ' . count($structuredRows) . PHP_EOL;
echo 'Total tests: ' . $stats['total'] . PHP_EOL;
echo 'Passed: ' . $stats['passed'] . PHP_EOL;
echo 'Failed: ' . $stats['failed'] . PHP_EOL;
echo 'Ambiguous/safely rejected: ' . $stats['safe_rejected'] . PHP_EOL;
echo 'Runtime: ' . number_format($runtime, 2) . ' seconds' . PHP_EOL;

echo 'Failures by category:' . PHP_EOL;
if (!$failures) {
    echo '  none' . PHP_EOL;
} else {
    foreach ($failures as $category => $count) {
        echo '  ' . $category . ': ' . $count . PHP_EOL;
    }
}

if ($failureSamples) {
    echo 'Representative failures:' . PHP_EOL;
    foreach ($failureSamples as $sample) {
        echo '  - ' . $sample . PHP_EOL;
    }
}

exit($stats['failed'] > 0 ? 1 : 0);
