<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\CustomerBlock;
use App\Models\Addresses;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Database\QueryException;

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
                    'last_shipping_address' => $this->lastUsableShippingAddress($user),
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

        $otpStartedAt = microtime(true);
        $phoneHash = md5($normalizedPhone);
        \Log::info('Checkout OTP send timing', [
            'stage' => 'before_generation',
            'phone_hash' => $phoneHash,
            'otp_total_ms' => 0,
        ]);

        $otp = random_int(1000, 9999);
        $msg = 'আপনার OTP CODE:' . $otp . ' কোডটি ৫ মিনিট পর অকার্যকর হয়ে যাবে';

        $smsStartedAt = microtime(true);
        \Log::info('Checkout OTP send timing', [
            'stage' => 'before_sms_provider',
            'phone_hash' => $phoneHash,
            'otp_pre_sms_ms' => round(($smsStartedAt - $otpStartedAt) * 1000, 2),
        ]);

        try {
            \Helper::sendSmsNonMusking($normalizedPhone, $msg);
        } catch (Throwable $e) {
            $smsFinishedAt = microtime(true);
            \Log::info('Checkout OTP send timing', [
                'stage' => 'after_sms_provider',
                'phone_hash' => $phoneHash,
                'sms_provider_ms' => round(($smsFinishedAt - $smsStartedAt) * 1000, 2),
                'result' => 'failed',
            ]);
            \Log::info('Checkout OTP send timing', [
                'stage' => 'before_response',
                'phone_hash' => $phoneHash,
                'otp_total_ms' => round(($smsFinishedAt - $otpStartedAt) * 1000, 2),
                'otp_post_sms_ms' => 0,
                'result' => 'failed',
            ]);
            return ['status' => 0, 'message' => 'Failed to send OTP.'];
        }

        $smsFinishedAt = microtime(true);
        \Log::info('Checkout OTP send timing', [
            'stage' => 'after_sms_provider',
            'phone_hash' => $phoneHash,
            'sms_provider_ms' => round(($smsFinishedAt - $smsStartedAt) * 1000, 2),
            'result' => 'completed',
        ]);

        $cacheKey = 'checkout_otp:' . md5($normalizedPhone);
        Cache::put($cacheKey, [
            'otp' => $otp,
            'phone' => $normalizedPhone,
            'verified' => false,
        ], now()->addMinutes(5));

        Cache::put('checkout_otp_attempts:' . md5($normalizedPhone), 0, now()->addMinutes(5));

        Cache::put($dailyLimitKey, $dailyCount + 1, now()->endOfDay());

        $responseStartedAt = microtime(true);
        \Log::info('Checkout OTP send timing', [
            'stage' => 'before_response',
            'phone_hash' => $phoneHash,
            'otp_total_ms' => round(($responseStartedAt - $otpStartedAt) * 1000, 2),
            'otp_post_sms_ms' => round(($responseStartedAt - $smsFinishedAt) * 1000, 2),
            'result' => 'success',
        ]);

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

        $user = User::where('phone', $normalizedPhone)->first();

        return [
            'status' => 1,
            'message' => 'OTP verified successfully.',
            'last_shipping_address' => $user ? $this->lastUsableShippingAddress($user) : null,
        ];
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

    public function resolveVerifiedCustomer(string $phone, string $name, string $streetAddress): User
    {
        $normalizedPhone = $this->normalizePhone($phone);
        $phoneState = $normalizedPhone ? $this->phoneCheck($normalizedPhone) : ['status' => 0];
        $trusted = ($phoneState['status'] ?? 0) == 1 && empty($phoneState['otp_required']);

        if (!$normalizedPhone || (!$trusted && !$this->isCheckoutOtpVerified($normalizedPhone))) {
            throw new \DomainException('Checkout phone verification is required.');
        }

        try {
            return DB::transaction(function () use ($normalizedPhone, $name) {
            $customer = User::where('phone', $normalizedPhone)->lockForUpdate()->first();
            if ($customer) {
                return $customer;
            }

            $customer = new User();
            $customer->name = trim($name);
            $customer->phone = $normalizedPhone;
            $customer->password = Hash::make(Str::random(40));
            $customer->status = 1;
            $customer->save();
            return $customer;
            });
        } catch (QueryException $exception) {
            $customer = User::where('phone', $normalizedPhone)->first();
            if ($customer) {
                return $customer;
            }
            throw $exception;
        }
    }

    public function normalizePhone(string $phone): ?string
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

    private function lastUsableShippingAddress(User $user): ?array
    {
        $orders = Order::where('user_id', $user->id)
            ->where('status', 6)
            ->orderByDesc('id')
            ->get();

        foreach ($orders as $order) {
            $snapshot = $order->shipping_address_snapshot;
            if (is_string($snapshot)) {
                $snapshot = json_decode($snapshot, true);
            }
            if (!is_array($snapshot)) {
                $legacyAddress = $order->historical_shipping_address;
                $snapshot = is_object($legacyAddress) ? (array) $legacyAddress : $legacyAddress;
            }
            if (!is_array($snapshot)) {
                continue;
            }

            $shippingFirstName = trim((string) ($snapshot['shipping_first_name'] ?? ''));
            if ($shippingFirstName === '') {
                $shippingFirstName = trim((string) ($snapshot['shipping_last_name'] ?? ''));
            }

            if (
                $shippingFirstName === ''
                || empty($snapshot['shipping_address'])
                || empty($snapshot['shipping_district'])
                || empty($snapshot['shipping_thana'])
            ) {
                continue;
            }

            return [
                'shipping_first_name' => $shippingFirstName,
                'shipping_phone' => $snapshot['shipping_phone'] ?? null,
                'shipping_address' => $snapshot['shipping_address'],
                'shipping_district' => $snapshot['shipping_district'],
                'shipping_thana' => $snapshot['shipping_thana'],
                'shipping_union' => $snapshot['shipping_union'] ?? null,
                'district_title' => $snapshot['district_title'] ?? null,
                'upazila_title' => $snapshot['upazila_title'] ?? null,
                'union_title' => $snapshot['union_title'] ?? null,
            ];
        }

        $address = Addresses::where('id', $user->default_address_id)
            ->where('user_id', $user->id)
            ->where('is_deleted', 0)
            ->with('district', 'upazila', 'union')
            ->first();
        if ($address && $address->shipping_first_name && $address->shipping_address && $address->shipping_district && $address->shipping_thana) {
            return [
                'shipping_first_name' => $address->shipping_first_name,
                'shipping_phone' => $address->shipping_phone,
                'shipping_address' => $address->shipping_address,
                'shipping_district' => $address->shipping_district,
                'shipping_thana' => $address->shipping_thana,
                'shipping_union' => $address->shipping_union ?: null,
                'district_title' => $address->district->title ?? null,
                'upazila_title' => $address->upazila->title ?? null,
                'union_title' => $address->union->title ?? null,
            ];
        }

        return null;
    }
}
