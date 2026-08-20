<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FraudSecuritySettingsService
{
    private array $defaults = [
        'good_courier_success_rate' => 60,
        'high_risk_courier_success_below' => 40,
        'trusted_courier_success_rate' => 80,
        'trusted_mabiy_completed_orders' => 2,
        'high_mabiy_return_rate' => 50,
        'minimum_courier_orders' => 3,
        'new_customer_otp_required' => true,
        'new_customer_manual_review' => false,
        'high_risk_action' => 'review',
        'provider_unavailable_action' => 'allow',
        'otp_bypass_min_delivered_orders' => 1,
        'otp_bypass_mabiy_success_rate' => 50,
    ];

    public function get(string $key, $default = null)
    {
        $value = $this->defaults[$key] ?? $default;

        $row = DB::table('fraud_security_settings')->where('key', $key)->first();
        if (!$row) {
            return $value;
        }

        $type = $row->type ?? 'string';

        if ($type === 'boolean') {
            return filter_var($row->value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($type === 'number') {
            return is_numeric($row->value) ? (float) $row->value : $value;
        }

        if ($type === 'integer') {
            return is_numeric($row->value) ? (int) $row->value : $value;
        }

        return $row->value ?? $value;
    }

    public function all(): array
    {
        $stored = DB::table('fraud_security_settings')->get()->pluck('value', 'key')->toArray();

        $result = [];
        foreach ($this->defaults as $key => $default) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    public function set(string $key, $value, string $type = 'string'): void
    {
        DB::table('fraud_security_settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
