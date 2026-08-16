<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AddressLocationResolver
{
    private const STRONG_SCORE = 85;
    private const UNIQUE_MARGIN = 20;
    private const MAX_CANDIDATES = 15;
    private $cachedLocationIndex;

    public function resolve(string $address): array
    {
        $normalizedAddress = $this->normalize($address);

        if ($normalizedAddress === '') {
            return $this->result('none', []);
        }

        $locations = $this->locationIndex();
        $districts = $locations['districts'];
        $upazilas = $locations['upazilas'];

        $districtEvidence = [];
        foreach ($districts as $district) {
            $normalizedTitle = $this->normalize($district->title);
            if ($this->containsPhrase($normalizedAddress, $normalizedTitle)) {
                $districtEvidence[$district->id] = 35;
            }
        }

        foreach ($this->aliases('districts') as $alias => $districtId) {
            if ($this->containsPhrase($normalizedAddress, $this->normalize($alias))) {
                $districtEvidence[(int) $districtId] = 35;
            }
        }

        $explicitDistrictIds = array_keys($districtEvidence);
        $restrictedDistrictId = count($explicitDistrictIds) === 1
            ? (int) $explicitDistrictIds[0]
            : null;
        $hasMultipleExplicitDistricts = count($explicitDistrictIds) > 1;
        $unqualifiedRestrictedUpazilaIds = $this->unqualifiedRestrictedLocalityUpazilaIds(
            $normalizedAddress,
            $restrictedDistrictId
        );

        $districtsById = $districts->keyBy('id');
        $upazilasById = $upazilas->keyBy('id');
        $addressWords = $this->meaningfulWords($normalizedAddress);
        foreach (array_keys($districtEvidence) as $districtId) {
            $district = $districtsById->get($districtId);
            if ($district) {
                $addressWords = array_values(array_diff(
                    $addressWords,
                    $this->meaningfulWords($this->normalize($district->title))
                ));
            }
        }
        $candidates = [];

        foreach ($upazilas as $upazila) {
            if (in_array((int) $upazila->id, $unqualifiedRestrictedUpazilaIds, true)) {
                continue;
            }
            if ($restrictedDistrictId && (int) $upazila->district_id !== $restrictedDistrictId) {
                continue;
            }

            $normalizedTitle = $this->normalize($upazila->title);
            $exact = $this->containsPhrase($normalizedAddress, $normalizedTitle);
            $partial = !$exact && $this->hasLeadingWordMatch($normalizedTitle, $addressWords);

            if (!$exact && !$partial) {
                continue;
            }

            $candidate = $this->makeCandidate(
                $districtsById->get($upazila->district_id),
                $upazila
            );

            if (!$candidate) {
                continue;
            }

            $specificTitleBonus = $exact && strpos($normalizedTitle, ' ') !== false ? 25 : 0;
            $candidate['score'] = ($exact ? self::STRONG_SCORE : 35)
                + $specificTitleBonus
                + ($districtEvidence[$upazila->district_id] ?? 0);
            $candidate['_exact_upazila'] = $exact;
            $candidate['_partial_upazila'] = $partial;
            $candidate['_normalized_upazila'] = $normalizedTitle;
            $candidate['_area_match'] = false;
            $candidates[$upazila->id] = $candidate;
        }

        foreach ($this->matchingUpazilaAliases($normalizedAddress) as $upazilaId) {
            $upazila = $upazilasById->get($upazilaId);
            if (!$upazila) {
                continue;
            }
            if ($restrictedDistrictId && (int) $upazila->district_id !== $restrictedDistrictId) {
                continue;
            }

            $candidate = $this->makeCandidate(
                $districtsById->get($upazila->district_id),
                $upazila
            );
            if (!$candidate) {
                continue;
            }

            $aliasScore = self::STRONG_SCORE
                + ($districtEvidence[$upazila->district_id] ?? 0);
            if (!isset($candidates[$upazila->id]) || $candidates[$upazila->id]['score'] < $aliasScore) {
                $candidate['score'] = $aliasScore;
                $candidate['_exact_upazila'] = true;
                $candidate['_partial_upazila'] = false;
                $candidate['_normalized_upazila'] = $this->normalize($upazila->title);
                $candidate['_area_match'] = false;
                $candidates[$upazila->id] = $candidate;
            }
        }

        foreach ($this->matchingStructuredUpazilas($normalizedAddress, $locations['structured_upazilas']) as $upazilaId) {
            $upazila = $upazilasById->get($upazilaId);
            if (!$upazila) {
                continue;
            }
            if ($restrictedDistrictId && (int) $upazila->district_id !== $restrictedDistrictId) {
                continue;
            }

            $candidate = $this->makeCandidate($districtsById->get($upazila->district_id), $upazila);
            if (!$candidate) {
                continue;
            }

            $structuredScore = self::STRONG_SCORE
                + 25
                + ($districtEvidence[$upazila->district_id] ?? 0);
            if (!isset($candidates[$upazila->id]) || $candidates[$upazila->id]['score'] < $structuredScore) {
                $candidate['score'] = $structuredScore;
                $candidate['_exact_upazila'] = true;
                $candidate['_partial_upazila'] = false;
                $candidate['_normalized_upazila'] = $this->normalize($upazila->title);
                $candidate['_area_match'] = false;
                $candidates[$upazila->id] = $candidate;
            }
        }

        foreach ($this->matchingLocalityAliases($normalizedAddress, $restrictedDistrictId) as $upazilaId) {
            $upazila = $upazilasById->get($upazilaId);
            if (!$upazila) {
                continue;
            }
            if ($restrictedDistrictId && (int) $upazila->district_id !== $restrictedDistrictId) {
                continue;
            }

            if (!isset($candidates[$upazila->id])) {
                $candidate = $this->makeCandidate(
                    $districtsById->get($upazila->district_id),
                    $upazila
                );
                if (!$candidate) {
                    continue;
                }
                $candidate['score'] = 0;
                $candidate['_exact_upazila'] = false;
                $candidate['_partial_upazila'] = false;
                $candidate['_normalized_upazila'] = $this->normalize($upazila->title);
                $candidate['_area_match'] = false;
                $candidates[$upazila->id] = $candidate;
            }

            $localityScore = self::STRONG_SCORE
                + ($districtEvidence[$upazila->district_id] ?? 0);
            $candidates[$upazila->id]['score'] = max(
                $candidates[$upazila->id]['score'],
                $localityScore
            );
            $candidates[$upazila->id]['_area_match'] = true;
        }

        foreach ($this->findMatchingAreas($normalizedAddress, $addressWords, $locations['areas_by_phrase']) as $area) {
            if ($unqualifiedRestrictedUpazilaIds) {
                continue;
            }
            $upazila = $upazilasById->get($area->upazila_id);
            if (!$upazila) {
                continue;
            }
            if ($restrictedDistrictId && (int) $upazila->district_id !== $restrictedDistrictId) {
                continue;
            }

            if (!isset($candidates[$upazila->id])) {
                $candidate = $this->makeCandidate(
                    $districtsById->get($upazila->district_id),
                    $upazila
                );

                if (!$candidate) {
                    continue;
                }

                $candidate['score'] = 0;
                $candidate['_exact_upazila'] = false;
                $candidate['_partial_upazila'] = false;
                $candidate['_normalized_upazila'] = $this->normalize($upazila->title);
                $candidate['_area_match'] = false;
                $candidates[$upazila->id] = $candidate;
            }

            $areaScore = self::STRONG_SCORE + ($districtEvidence[$upazila->district_id] ?? 0);
            if ($candidates[$upazila->id]['score'] < $areaScore) {
                $candidates[$upazila->id]['score'] = $areaScore;
            } elseif ($candidates[$upazila->id]['_exact_upazila']) {
                $candidates[$upazila->id]['score'] += 25;
            }

            $candidates[$upazila->id]['matched_area_id'] = $area->id;
            $candidates[$upazila->id]['matched_area_title'] = $area->title;
            $candidates[$upazila->id]['area_id'] = $area->area_id;
            $candidates[$upazila->id]['location_evidence'] = array_values(array_unique(array_merge(
                $candidates[$upazila->id]['location_evidence'],
                $this->structuredVariants($area->title)
            )));
            $candidates[$upazila->id]['_area_match'] = true;
        }

        $this->applyAmbiguityPenalties($candidates);

        $candidates = array_values($candidates);
        usort($candidates, function (array $left, array $right) {
            if ($left['score'] === $right['score']) {
                return strcmp(
                    $left['district_title'] . $left['upazila_title'],
                    $right['district_title'] . $right['upazila_title']
                );
            }

            return $right['score'] <=> $left['score'];
        });

        $candidates = array_slice($candidates, 0, self::MAX_CANDIDATES);
        foreach ($candidates as &$candidate) {
            unset(
                $candidate['_exact_upazila'],
                $candidate['_partial_upazila'],
                $candidate['_normalized_upazila'],
                $candidate['_area_match']
            );
        }
        unset($candidate);

        if (!$candidates) {
            if ($unqualifiedRestrictedUpazilaIds) {
                return $this->result('ambiguous', []);
            }

            if ($restrictedDistrictId) {
                $district = $districtsById->get($restrictedDistrictId);
                if ($district && $district->city_id) {
                    return $this->result('district_only', [], [
                        'district_id' => $district->id,
                        'district_title' => $district->title,
                        'city_id' => $district->city_id,
                    ]);
                }
            }

            return $this->result('none', []);
        }

        $topScore = $candidates[0]['score'];
        $secondScore = $candidates[1]['score'] ?? 0;
        $isStrong = $topScore >= self::STRONG_SCORE
            && ($topScore - $secondScore) >= self::UNIQUE_MARGIN;

        return $this->result(
            $isStrong && !$hasMultipleExplicitDistricts ? 'strong' : 'ambiguous',
            $candidates
        );
    }

    private function normalize(string $value): string
    {
        $value = strtr($value, ['০'=>'0','১'=>'1','২'=>'2','৩'=>'3','৪'=>'4','৫'=>'5','৬'=>'6','৭'=>'7','৮'=>'8','৯'=>'9']);
        $value = mb_strtolower(trim($value), 'UTF-8');
        $value = preg_replace('/[,&;|\/\\\\\-_]+/u', ' ', $value);
        $value = preg_replace('/[^\p{L}\p{M}\p{N}\s]+/u', ' ', $value);

        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    private function containsPhrase(string $address, string $phrase): bool
    {
        if (mb_strlen($phrase) < 3) {
            return false;
        }

        return mb_strpos(' ' . $address . ' ', ' ' . $phrase . ' ') !== false;
    }

    private function meaningfulWords(string $address): array
    {
        return array_values(array_unique(array_filter(
            preg_split('/\s+/u', $address),
            function (string $word) {
                return mb_strlen($word) >= 4;
            }
        )));
    }

    private function hasLeadingWordMatch(string $title, array $addressWords): bool
    {
        $titleWords = preg_split('/\s+/u', $title);
        $leadingWord = $titleWords[0] ?? '';

        return mb_strlen($leadingWord) >= 4
            && in_array($leadingWord, $addressWords, true);
    }

    private function findMatchingAreas(string $normalizedAddress, array $addressWords, array $areasByPhrase)
    {
        $searchWords = array_values(array_filter($addressWords, function (string $word) {
            return mb_strlen($word) >= 5;
        }));

        if (!$searchWords) {
            return collect();
        }

        $words = preg_split('/\s+/u', $normalizedAddress);
        $matches = [];
        $wordCount = count($words);
        for ($start = 0; $start < $wordCount; $start++) {
            for ($length = 1; $length <= min(12, $wordCount - $start); $length++) {
                $phrase = implode(' ', array_slice($words, $start, $length));
                foreach ([$phrase, str_replace(' ', '', $phrase)] as $key) {
                    foreach ($areasByPhrase[$key] ?? [] as $area) {
                        $matches[$area->id] = $area;
                    }
                }
            }
        }

        return collect(array_values($matches));
    }

    private function locationIndex(): array
    {
        if ($this->cachedLocationIndex !== null) {
            return $this->cachedLocationIndex;
        }

        return $this->cachedLocationIndex = Cache::remember('address-location-resolver-index-v32', 86400, function () {
            $areas = DB::table('unions')->select('id', 'upazila_id', 'title', 'area_id')->get();
            $areasByPhrase = [];
            foreach ($areas as $area) {
                foreach ($this->structuredVariants($area->title) as $variant) {
                    $title = $this->normalize($variant);
                    foreach (array_unique([$title, str_replace(' ', '', $title)]) as $key) {
                        if (mb_strlen($key) >= 4) {
                            $areasByPhrase[$key][] = $area;
                        }
                    }
                }
            }
            $upazilas = DB::table('upazilas')->select('id', 'district_id', 'title', 'zone_id')->get();
            $structuredUpazilas = [];
            foreach ($upazilas as $upazila) {
                foreach (array_slice($this->structuredVariants($upazila->title), 1) as $variant) {
                    $structuredUpazilas[$variant][] = (int) $upazila->id;
                }
            }
            return [
                'districts' => DB::table('districts')->select('id', 'division_id', 'title', 'city_id')->get(),
                'upazilas' => $upazilas,
                'areas_by_phrase' => $areasByPhrase,
                'structured_upazilas' => $structuredUpazilas,
            ];
        });
    }

    private function aliases(string $group): array
    {
        return (array) config('address_location_aliases.' . $group, []);
    }

    private function matchingUpazilaAliases(string $normalizedAddress): array
    {
        $matches = [];
        foreach ($this->aliases('upazilas') as $alias => $upazilaIds) {
            if (!$this->containsPhrase($normalizedAddress, $this->normalize($alias))) {
                continue;
            }

            foreach ((array) $upazilaIds as $upazilaId) {
                $matches[] = (int) $upazilaId;
            }
        }

        return array_values(array_unique($matches));
    }

    private function matchingStructuredUpazilas(string $normalizedAddress, array $structuredUpazilas): array
    {
        $matches = [];
        foreach ($structuredUpazilas as $form => $upazilaIds) {
            if ($this->containsPhrase($normalizedAddress, $form)) {
                foreach ($upazilaIds as $upazilaId) {
                    $matches[] = (int) $upazilaId;
                }
            }
        }

        return array_values(array_unique($matches));
    }

    private function structuredVariants(string $sourceTitle): array
    {
        $title = $this->normalize($sourceTitle);
        $terms = [
            'sector' => ['sector', 'সেক্টর'],
            'block' => ['block', 'ব্লক'],
            'ward' => ['ward', 'ওয়ার্ড'],
            'road' => ['road', 'রোড'],
            'area' => ['area', 'এরিয়া', 'এলাকা'],
        ];

        $base = $term = $identifier = null;
        if (preg_match('/^(.+?) (sector|block|ward|road|area) ([0-9]+|[a-z])$/u', $title, $parts)) {
            [, $base, $term, $identifier] = $parts;
        } elseif (preg_match('/^(sector|block|ward|road|area) ([0-9]+|[a-z]) (.+)$/u', $title, $parts)) {
            $term = $parts[1];
            $identifier = $parts[2];
            $base = $parts[3];
        } else {
            return [$title];
        }

        if (mb_strlen($base) < 3 || preg_match('/^(sector|block|ward|road|area) [a-z0-9]+$/u', $base)) {
            return [$title];
        }

        $baseVariants = array_merge([$base], (array) config('address_location_aliases.structured_bases.' . $base, []));
        $variants = [$title];
        foreach (array_unique($baseVariants) as $baseVariant) {
            foreach ($terms[$term] as $termVariant) {
                $variants[] = $baseVariant . ' ' . $termVariant . ' ' . $identifier;
                $variants[] = $termVariant . ' ' . $identifier . ' ' . $baseVariant;
            }
            $variants[] = $baseVariant . ' ' . $identifier;
        }

        return array_values(array_unique(array_map([$this, 'normalize'], $variants)));
    }

    private function matchingLocalityAliases(
        string $normalizedAddress,
        ?int $restrictedDistrictId
    ): array {
        $matches = [];
        foreach ($this->aliases('localities') as $alias => $mapping) {
            if (!$this->containsPhrase($normalizedAddress, $this->normalize($alias))) {
                continue;
            }

            $requiredDistrictId = isset($mapping['requires_district_id'])
                ? (int) $mapping['requires_district_id']
                : null;
            if ($requiredDistrictId && $restrictedDistrictId !== $requiredDistrictId) {
                continue;
            }

            $matches[] = (int) $mapping['upazila_id'];
        }

        return array_values(array_unique($matches));
    }

    private function unqualifiedRestrictedLocalityUpazilaIds(
        string $normalizedAddress,
        ?int $restrictedDistrictId
    ): array {
        $matches = [];
        foreach ($this->aliases('localities') as $alias => $mapping) {
            if (!isset($mapping['requires_district_id'])) {
                continue;
            }
            if (!$this->containsPhrase($normalizedAddress, $this->normalize($alias))) {
                continue;
            }

            $requiredDistrictId = (int) $mapping['requires_district_id'];
            if ($restrictedDistrictId !== $requiredDistrictId) {
                $matches[] = (int) $mapping['upazila_id'];
            }
        }

        return array_values(array_unique($matches));
    }

    private function makeCandidate($district, $upazila): ?array
    {
        if (!$district || !$district->city_id || !$upazila->zone_id) {
            return null;
        }

        return [
            'district_id' => $district->id,
            'district_title' => $district->title,
            'city_id' => $district->city_id,
            'upazila_id' => $upazila->id,
            'upazila_title' => trim($upazila->title),
            'zone_id' => $upazila->zone_id,
            'location_evidence' => $this->structuredVariants($upazila->title),
            'score' => 0,
        ];
    }

    private function applyAmbiguityPenalties(array &$candidates): void
    {
        $exactTitleCounts = [];
        $partialCount = 0;

        foreach ($candidates as $candidate) {
            if ($candidate['_exact_upazila']) {
                $title = $candidate['_normalized_upazila'];
                $exactTitleCounts[$title] = ($exactTitleCounts[$title] ?? 0) + 1;
            }
            if ($candidate['_partial_upazila']) {
                $partialCount++;
            }
        }

        foreach ($candidates as &$candidate) {
            if (
                $candidate['_exact_upazila']
                && ($exactTitleCounts[$candidate['_normalized_upazila']] ?? 0) > 1
            ) {
                $candidate['score'] -= 15;
            }

            if ($candidate['_partial_upazila'] && $partialCount > 1) {
                $candidate['score'] -= 10;
            }

            $candidate['score'] = max(0, $candidate['score']);
        }
        unset($candidate);
    }

    private function result(
        string $matchType,
        array $candidates,
        ?array $district = null
    ): array
    {
        return [
            'match_type' => $matchType,
            'candidates' => $candidates,
            'district' => $district,
        ];
    }
}
