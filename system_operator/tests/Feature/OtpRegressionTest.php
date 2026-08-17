<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OtpRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');

        DB::table('users')->insert([
            'name' => 'Test User',
            'phone' => '01700000001',
            'email' => 'test1@example.com',
            'password' => bcrypt('oldpassword'),
            'status' => 1,
        ]);

        DB::table('users')->insert([
            'name' => 'Test User 2',
            'phone' => '01700000002',
            'email' => 'test2@example.com',
            'password' => bcrypt('oldpassword'),
            'status' => 1,
        ]);
    }

    public function test_otp_is_exactly_4_digits()
    {
        $response = $this->postJson('/api/v1/generate-otp', [
            'mobile_number' => '01700000001',
            'otp_for_login' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $otp = DB::table('otp')->where('mobile_number', '01700000001')->first();
        $this->assertNotNull($otp);
        $this->assertSame(4, strlen((string) $otp->otp_code));
        $this->assertGreaterThanOrEqual(1000, (int) $otp->otp_code);
        $this->assertLessThanOrEqual(9999, (int) $otp->otp_code);
    }

    public function test_valid_otp_passes()
    {
        $otp = '1234';

        DB::table('otp')->insert([
            'mobile_number' => '01700000001',
            'otp_code' => $otp,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/otp-login', [
            'mobile_number' => '01700000001',
            'otp' => $otp,
            'session_key' => 'test_session',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_wrong_otp_fails()
    {
        DB::table('otp')->insert([
            'mobile_number' => '01700000001',
            'otp_code' => '1234',
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/otp-login', [
            'mobile_number' => '01700000001',
            'otp' => '9999',
            'session_key' => 'test_session',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 0]);
    }

    public function test_expired_otp_fails()
    {
        DB::table('otp')->insert([
            'mobile_number' => '01700000001',
            'otp_code' => '1234',
            'status' => 0,
            'created_at' => now()->subMinutes(6),
            'updated_at' => now()->subMinutes(6),
        ]);

        $response = $this->postJson('/api/v1/otp-login', [
            'mobile_number' => '01700000001',
            'otp' => '1234',
            'session_key' => 'test_session',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 0]);
    }

    public function test_used_otp_fails()
    {
        DB::table('otp')->insert([
            'mobile_number' => '01700000001',
            'otp_code' => '1234',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/otp-login', [
            'mobile_number' => '01700000001',
            'otp' => '1234',
            'session_key' => 'test_session',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 0]);
    }

    public function test_newest_otp_invalidates_previous_unused_otp()
    {
        DB::table('otp')->insert([
            'mobile_number' => '01700000001',
            'otp_code' => '1111',
            'status' => 0,
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $response = $this->postJson('/api/v1/generate-otp', [
            'mobile_number' => '01700000001',
            'otp_for_login' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $oldOtp = DB::table('otp')->where('mobile_number', '01700000001')
            ->where('otp_code', '1111')
            ->first();

        $this->assertNotNull($oldOtp);
        $this->assertNotSame(0, $oldOtp->status);

        $newOtp = DB::table('otp')->where('mobile_number', '01700000001')
            ->where('status', 0)
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($newOtp);
        $this->assertNotEquals('1111', $newOtp->otp_code);
    }

    public function test_otp_login_cannot_bypass_otp_verification()
    {
        $response = $this->postJson('/api/v1/login', [
            'phone' => '01700000001',
            'password' => 'any_value',
            'otp_login' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 2]);
        $this->assertEmpty($response->json('token'));
    }

    public function test_new_user_password_is_not_derived_from_otp()
    {
        $otp = '1234';

        DB::table('otp')->insert([
            'mobile_number' => '01700000003',
            'otp_code' => $otp,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/user-register', [
            'mobile_number' => '01700000003',
            'otp' => $otp,
            'session_key' => 'test_session',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $user = User::where('phone', '01700000003')->first();
        $this->assertNotNull($user);
        $this->assertFalse(password_verify('1234', $user->password));
    }

    public function test_brute_force_limiter_blocks_after_five_failed_attempts()
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/v1/otp-login', [
                'mobile_number' => '01700000002',
                'otp' => '0000',
                'session_key' => 'test_session',
            ]);

            $response->assertStatus(200);
            $response->assertJson(['status' => 0]);
        }

        $response = $this->postJson('/api/v1/otp-login', [
            'mobile_number' => '01700000002',
            'otp' => '0000',
            'session_key' => 'test_session',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 0]);
    }
}
