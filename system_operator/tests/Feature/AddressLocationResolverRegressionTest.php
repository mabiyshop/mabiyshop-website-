<?php

namespace Tests\Feature;

use App\Services\AddressLocationResolver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use Tests\TestCase;

class AddressLocationResolverRegressionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('districts', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('division_id');
            $table->string('title');
            $table->unsignedInteger('city_id')->nullable();
        });
        Schema::create('upazilas', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('district_id');
            $table->string('title');
            $table->unsignedInteger('zone_id')->nullable();
        });
        Schema::create('unions', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('upazila_id');
            $table->string('title');
            $table->unsignedInteger('area_id')->nullable();
        });

        DB::table('districts')->insert([
            ['id' => 5, 'division_id' => 1, 'title' => 'B. Baria', 'city_id' => 32],
            ['id' => 10, 'division_id' => 1, 'title' => 'Chittagong', 'city_id' => 2],
            ['id' => 14, 'division_id' => 1, 'title' => 'Dhaka', 'city_id' => 1],
            ['id' => 19, 'division_id' => 1, 'title' => 'Gazipur', 'city_id' => 22],
        ]);
        DB::table('upazilas')->insert([
            ['id' => 51, 'district_id' => 14, 'title' => 'Hazaribag', 'zone_id' => 56],
            ['id' => 32, 'district_id' => 14, 'title' => 'Dhamrai, Savar', 'zone_id' => 1016],
            ['id' => 80, 'district_id' => 14, 'title' => 'Mirpur 1', 'zone_id' => 171],
            ['id' => 81, 'district_id' => 14, 'title' => 'Mirpur 10', 'zone_id' => 19],
            ['id' => 82, 'district_id' => 14, 'title' => 'Mirpur 11', 'zone_id' => 20],
            ['id' => 88, 'district_id' => 14, 'title' => 'Mirpur 7', 'zone_id' => 475],
            ['id' => 199, 'district_id' => 5, 'title' => 'Akhaura', 'zone_id' => 546],
            ['id' => 261, 'district_id' => 10, 'title' => 'Halishahar', 'zone_id' => 75],
            ['id' => 900, 'district_id' => 14, 'title' => 'Uttara Section 10', 'zone_id' => 900],
            ['id' => 901, 'district_id' => 19, 'title' => 'Mirpur', 'zone_id' => 901],
        ]);
        DB::table('unions')->insert([
            ['id' => 17, 'upazila_id' => 82, 'title' => 'Road', 'area_id' => 397],
            ['id' => 34, 'upazila_id' => 80, 'title' => 'Zoo Road', 'area_id' => 17573],
            ['id' => 35, 'upazila_id' => 261, 'title' => 'Port Connecting Road', 'area_id' => 17574],
            ['id' => 1634, 'upazila_id' => 51, 'title' => 'Section', 'area_id' => 16368],
            ['id' => 3643, 'upazila_id' => 199, 'title' => 'Mirpur', 'area_id' => 7693],
        ]);

        Cache::flush();
    }

    public function test_generic_dhaka_mirpur_is_ambiguous_and_never_returns_dhamrai_savar(): void
    {
        foreach (['dhaka mirpur', 'mirpur dhaka', 'Dhaka, Mirpur'] as $address) {
            $result = $this->resolver()->resolve($address);

            $this->assertSame('ambiguous', $result['match_type'], $address);
            $this->assertNotEmpty($result['candidates'], $address);
            $this->assertSame([14], array_values(array_unique(array_column($result['candidates'], 'district_id'))), $address);
            $this->assertSame([], array_values(array_filter(
                $result['candidates'],
                static function (array $candidate): bool {
                    return stripos($candidate['upazila_title'], 'mirpur') === false;
                }
            )), $address);
        }
    }

    public function test_structured_mirpur_and_section_forms_remain_strong(): void
    {
        $cases = [
            'section 07 mirpur' => 88,
            'section 07 mirpur dhaka' => 88,
            'section 10 mirpur' => 81,
            'section 10 mirpur dhaka' => 81,
            'mirpur 7 dhaka' => 88,
            'mirpur 11 dhaka' => 82,
        ];

        foreach ($cases as $address => $upazilaId) {
            $result = $this->resolver()->resolve($address);
            $this->assertSame('strong', $result['match_type'], $address);
            $this->assertSame($upazilaId, $result['candidates'][0]['upazila_id'], $address);
        }
    }

    public function test_halishahar_controlled_spelling_variants_remain_strong(): void
    {
        foreach (['halishahar chattogram', 'halishohor chattogram', 'halishohar chittagong'] as $address) {
            $result = $this->resolver()->resolve($address);
            $this->assertSame('strong', $result['match_type'], $address);
            $this->assertSame(261, $result['candidates'][0]['upazila_id'], $address);
        }
    }

    public function test_generic_area_words_do_not_override_locations_but_specific_areas_still_match(): void
    {
        $genericRoad = $this->resolver()->resolve('road dhaka');
        $this->assertSame('district_only', $genericRoad['match_type']);
        $this->assertSame([], array_values(array_filter(
            $genericRoad['candidates'],
            static function (array $candidate): bool {
                return isset($candidate['matched_area_title'])
                    && strcasecmp(trim($candidate['matched_area_title']), 'road') === 0;
            }
        )));

        $specificRoad = $this->resolver()->resolve('zoo road mirpur 1 dhaka');
        $this->assertSame('strong', $specificRoad['match_type']);
        $this->assertSame(80, $specificRoad['candidates'][0]['upazila_id']);
        $this->assertSame('Zoo Road', $specificRoad['candidates'][0]['matched_area_title']);

        $nationwideCompoundArea = $this->resolver()->resolve('port connecting road chattogram');
        $this->assertSame('strong', $nationwideCompoundArea['match_type']);
        $this->assertSame(261, $nationwideCompoundArea['candidates'][0]['upazila_id']);
        $this->assertSame('Port Connecting Road', $nationwideCompoundArea['candidates'][0]['matched_area_title']);
    }

    public function test_stale_cache_cannot_promote_bare_structural_areas(): void
    {
        $this->resolver()->resolve('dhaka');
        $index = Cache::get('address-location-resolver-index-v37');
        $index['areas_by_phrase']['road'][] = (object) [
            'id' => 17,
            'upazila_id' => 82,
            'title' => 'Road',
            'area_id' => 397,
        ];
        $index['areas_by_phrase']['section'][] = (object) [
            'id' => 1634,
            'upazila_id' => 51,
            'title' => 'Section',
            'area_id' => 16368,
        ];
        Cache::put('address-location-resolver-index-v37', $index, 86400);

        foreach (['road dhaka' => 'Road', 'section dhaka' => 'Section'] as $address => $bareTitle) {
            $result = $this->resolver()->resolve($address);

            $this->assertSame('district_only', $result['match_type'], $address);
            $this->assertSame([], array_values(array_filter(
                $result['candidates'],
                static function (array $candidate) use ($bareTitle): bool {
                    return isset($candidate['matched_area_title'])
                        && strcasecmp(trim($candidate['matched_area_title']), $bareTitle) === 0;
                }
            )), $address);
        }
    }

    public function test_explicit_district_rejects_contradictory_mirpur_candidate(): void
    {
        $result = $this->resolver()->resolve('gazipur mirpur 10');

        $this->assertNotSame('strong', $result['match_type']);
        $this->assertSame([], array_values(array_filter(
            $result['candidates'],
            static function (array $candidate): bool {
                return (int) $candidate['district_id'] !== 19;
            }
        )));
    }

    public function test_frozen_thresholds_are_unchanged(): void
    {
        $reflection = new ReflectionClass(AddressLocationResolver::class);

        $this->assertSame(85, $reflection->getConstant('STRONG_SCORE'));
        $this->assertSame(20, $reflection->getConstant('UNIQUE_MARGIN'));
    }

    private function resolver(): AddressLocationResolver
    {
        return app(AddressLocationResolver::class);
    }
}
