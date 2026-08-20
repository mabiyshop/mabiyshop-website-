<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\CustomerBlock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class CheckoutOtpService
{
    private FraudSecuritySettingsService $settingsService;
    private ManualBlockService $manualBlockService;

    public function __construct(FraudSecuritySettingsService $settingsService, ManualBlockService $manualBlockService)
    {
        $this->settingsService = $settingsService;
        $this->manualBlockService = $manualBlockService;
    }

    public function phoneCheck(string $phone): array
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null) {
            return [
                'status' => 0,
                'otp_required' => true,
                'otp_bypass_reason' => 'invalid_phone',
                'customer_exists' => false,
            ];
        }

        $user = User::where('phone', $normalizedPhone)->first();
        $customerExists = (bool) $user;

        try {
            $blockState = $this->manualBlockService->resolve(
                $user ? (int) $user->id : null,
                $normalizedPhone,
                null
            );
        } catch (\Illuminate\Database\QueryException $e) {
            if ((string) ($e->getCode() ?? '') === '42S02') {
                $blockState = ['state' => null, 'direct_block' => false];
            } else {
                throw $e;
            }
        }

        if (($blockState['state'] ?? null) === 'BLOCKED' && ($blockState['direct_block'] ?? false) === true) {
            return [
                'status' => 0,
                'otp_required' => true,
                'otp_bypass_reason' => 'blocked_customer',
                'customer_exists' => $customerExists,
            ];
        }

        if ($user) {
            try {
                $history = $this->getMabiyDeliveryHistory($user->id);
            } catch (Throwable $e) {
                return [
                    'status' => 1,
                    'otp_required' => true,
                    'otp_bypass_reason' => 'history_lookup_failed',
                    'customer_exists' => $customerExists,
                ];
            }

            $minDelivered = (int) $this->settingsService->get('otp_bypass_min_delivered_orders', 1);
            $minSuccessRate = (float) $this->settingsService->get('otp_bypass_mabiy_success_rate', 50);

            $deliveredOrders = (int) ($history['completed_orders'] ?? 0);
            $returnAffectedOrders = (int) ($history['return_affected_orders'] ?? 0);
            $cancelledOrders = (int) ($history['cancelled_orders'] ?? 0);
            $relevantFinished = $deliveredOrders + $returnAffectedOrders + $cancelledOrders;

            if ($relevantFinished === 0) {
                return [
                    'status' => 1,
                    'otp_required' => true,
                    'otp_bypass_reason' => 'no_delivery_history',
                    'customer_exists' => $customerExists,
                ];
            }

            $successRate = $relevantFinished > 0
                ? ($deliveredOrders / $relevantFinished) * 100
                : 0.0;

            if ($deliveredOrders >= $minDelivered && $successRate >= $minSuccessRate) {
                return [
                    'status' => 1,
                    'otp_required' => false,
                    'otp_bypass_reason' => 'trusted_mabiy_history',
                    'customer_exists' => $customerExists,
                ];
            }

            return [
                'status' => 1,
                'otp_required' => true,
                'otp_bypass_reason' => 'insufficient_mabiy_history',
                'customer_exists' => $customerExists,
            ];
        }

        return [
            'status' => 1,
            'otp_required' => true,
            'otp_bypass_reason' => 'new_customer',
            'customer_exists' => false,
        ];
    }

    public function sendCheckoutOtp(string $phone): array
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null) {
            return ['status' => 0, 'message' => 'Invalid phone number.'];
        }

        $dailyLimitKey = 'checkout_otp_daily_limit:' . md5($normalizedPhone);
        $dailyCount = Cache::get($dailyLimitKey, 0);

        if ($dailyCount >= 4) {
            return ['status' => 0, 'message' => 'Maximum OTP generation limit exceeds for today.'];
        }

        $otp = random_int(1000, 9999);
        $msg = 'আপনার OTP CODE:' . $otp . ' কোডটি ৫ মিনিট পর অকার্যকর হয়ে যাবে';

        try {
            \Helper::sendSmsNonMusking($normalizedPhone, $msg);
        } catch (Throwable $e) {
            return ['status' => 0, 'message' => 'Failed to send OTP.'];
        }

        $cacheKey = 'checkout_otp:' . md5($normalizedPhone);
        Cache::put($cacheKey, [
            'otp' => $otp,
            'phone' => $normalizedPhone,
            'verified' => false,
        ], now()->addMinutes(5));

        Cache::put('checkout_otp_attempts:' . md5($normalizedPhone), 0, now()->addMinutes(5));

        Cache::put($dailyLimitKey, $dailyCount + 1, now()->endOfDay());

        return ['status' => 1, 'message' => 'OTP sent successfully.'];
    }

    public function verifyCheckoutOtp(string $phone, string $otpCode): array
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null) {
            return ['status' => 0, 'message' => 'Invalid phone number.'];
        }

        $cacheKey = 'checkout_otp:' . md5($normalizedPhone);
        $stored = Cache::get($cacheKey);

        if (!$stored || !is_array($stored)) {
            return ['status' => 0, 'message' => 'OTP expired or not found.'];
        }

        $attemptsKey = 'checkout_otp_attempts:' . md5($normalizedPhone);
        $attempts = (int) Cache::get($attemptsKey, 0);

        if ($attempts >= 5) {
            return ['status' => 0, 'message' => 'Too many wrong attempts. Please resend OTP.'];
        }

        if ((int) ($stored['otp'] ?? 0) !== (int) $otpCode) {
            Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(5));
            return ['status' => 0, 'message' => 'Invalid OTP.'];
        }

        Cache::forget($attemptsKey);

        $stored['verified'] = true;
        $stored['verified_at'] = now()->toDateTimeString();
        Cache::put($cacheKey, $stored, now()->addMinutes(10));

        return ['status' => 1, 'message' => 'OTP verified successfully.'];
    }

    public function isCheckoutOtpVerified(string $phone): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null) {
            return false;
        }

        $cacheKey = 'checkout_otp:' . md5($normalizedPhone);
        $stored = Cache::get($cacheKey);

        if (!$stored || !is_array($stored)) {
            return false;
        }

        if (empty($stored['verified'])) {
            return false;
        }

        if (isset($stored['verified_at']) && now()->parse($stored['verified_at'])->addMinutes(10)->isPast()) {
            Cache::forget($cacheKey);
            return false;
        }

        return true;
    }

    public function consumeCheckoutOtpVerification(string $phone): void
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null) {
            return;
        }

        Cache::forget('checkout_otp:' . md5($normalizedPhone));
        Cache::forget('checkout_otp_attempts:' . md5($normalizedPhone));
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (strpos($digits, '880') === 0) {
            $digits = '0' . substr($digits, 3);
        }

        return preg_match('/^01\d{9}$/', $digits) === 1 ? $digits : null;
    }

    private function getMabiyDeliveryHistory(int $userId): array
    {
        $completedOrders = Order::where('user_id', $userId)->where('status', 6)->count();
        $cancelledOrders = Order::where('user_id', $userId)->where('status', 5)->count();
        $returnAffectedOrders = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.user_id', $userId)
            ->where('order_details.status', 12)
            ->distinct()
            ->count('orders.id');

        return [
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'return_affected_orders' => $returnAffectedOrders,
        ];
    }
}
