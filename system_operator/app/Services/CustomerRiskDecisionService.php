<?php

namespace App\Services;

class CustomerRiskDecisionService
{
    public function decide(
        array $mabiyHistory,
        array $courierHistory,
        array $options = []
    ): array {
        $minimumCourierOrders = $this->numberOption($options, 'minimum_courier_orders', 3);
        $goodCourierSuccessRate = $this->numberOption($options, 'good_courier_success_rate', 60);
        $trustedCourierSuccessRate = $this->numberOption($options, 'trusted_courier_success_rate', 80);
        $trustedMabiyCompletedOrders = $this->numberOption($options, 'trusted_mabiy_completed_orders', 2);
        $highRiskCourierSuccessBelow = $this->numberOption($options, 'high_risk_courier_success_below', 40);
        $highMabiyReturnRate = $this->numberOption($options, 'high_mabiy_return_rate', 50);
        $manualBlock = (bool) ($options['manual_block'] ?? false);

        $courierTotal = $this->nonNegativeInteger($courierHistory['combined']['total'] ?? 0);
        $courierSuccessRate = $this->nonNegativeFloat($courierHistory['combined']['success_rate'] ?? 0);
        $mabiyTotalOrders = $this->nonNegativeInteger($mabiyHistory['total_orders'] ?? 0);
        $mabiyCompletedOrders = $this->nonNegativeInteger($mabiyHistory['completed_orders'] ?? 0);
        $mabiyReturnAffectedRate = $this->nonNegativeFloat($mabiyHistory['return_affected_rate'] ?? 0);

        $pathao = $courierHistory['couriers']['pathao'] ?? [];
        $pathaoRating = $this->nullableString($pathao['rating'] ?? null);
        $pathaoRiskLevel = $this->nullableString($pathao['risk_level'] ?? null);

        $result = [
            'risk_level' => 'NEW',
            'order_allowed' => true,
            'otp_required' => true,
            'manual_review' => false,
            'courier_total' => $courierTotal,
            'courier_success_rate' => $courierSuccessRate,
            'mabiy_completed_orders' => $mabiyCompletedOrders,
            'mabiy_return_affected_rate' => $mabiyReturnAffectedRate,
            'pathao_rating' => $pathaoRating,
            'pathao_risk_level' => $pathaoRiskLevel,
            'reasons' => [],
        ];

        if ($manualBlock) {
            $result['risk_level'] = 'BLOCKED';
            $result['order_allowed'] = false;
            $result['manual_review'] = true;
            $result['reasons'][] = 'manual_block';

            return $result;
        }

        $mabiyGood = $mabiyCompletedOrders >= $trustedMabiyCompletedOrders;
        $mabiyHighReturn = $mabiyTotalOrders >= 3
            && $mabiyReturnAffectedRate >= $highMabiyReturnRate;

        if (($courierHistory['status'] ?? 'unavailable') !== 'ok') {
            $result['reasons'][] = 'provider_unavailable';

            if ($mabiyGood && $mabiyReturnAffectedRate < $highMabiyReturnRate) {
                $result['risk_level'] = 'GOOD';
                $result['reasons'][] = 'mabiy_good_history';
            }

            if ($mabiyHighReturn) {
                $result['reasons'][] = 'mabiy_high_return_history';
            }

            return $result;
        }

        if ($courierTotal === 0) {
            $result['reasons'][] = 'no_courier_history';

            if ($mabiyGood) {
                $result['reasons'][] = 'mabiy_good_history';
            }

            if ($mabiyHighReturn) {
                $result['reasons'][] = 'mabiy_high_return_history';
            }

            return $result;
        }

        if ($courierTotal < $minimumCourierOrders) {
            $result['risk_level'] = 'REVIEW';
            $result['reasons'][] = 'insufficient_courier_history';
        } elseif ($courierSuccessRate < $highRiskCourierSuccessBelow) {
            $result['risk_level'] = 'HIGH_RISK';
            $result['manual_review'] = true;
            $result['reasons'][] = 'very_low_courier_success';
        } elseif ($courierSuccessRate < $goodCourierSuccessRate) {
            $result['risk_level'] = 'REVIEW';
            $result['manual_review'] = true;
            $result['reasons'][] = 'courier_review_range';
        } else {
            $result['risk_level'] = 'GOOD';
            $result['reasons'][] = 'good_courier_history';
        }

        $pathaoHighRisk = strtolower((string) $pathaoRating) === 'risky_customer'
            || strtolower((string) $pathaoRiskLevel) === 'high';

        if ($pathaoHighRisk) {
            $result['reasons'][] = 'pathao_high_risk';

            if ($result['risk_level'] === 'GOOD') {
                $result['risk_level'] = 'REVIEW';
                $result['manual_review'] = true;
            }
        }

        if ($mabiyGood) {
            $result['reasons'][] = 'mabiy_good_history';
        }

        if ($mabiyHighReturn) {
            $result['reasons'][] = 'mabiy_high_return_history';

            if ($result['risk_level'] === 'REVIEW') {
                $result['risk_level'] = 'HIGH_RISK';
                $result['manual_review'] = true;
            } elseif ($result['risk_level'] === 'GOOD') {
                $result['risk_level'] = 'REVIEW';
                $result['manual_review'] = true;
            }
        }

        $trusted = $courierTotal >= $minimumCourierOrders
            && $courierSuccessRate >= $trustedCourierSuccessRate
            && $mabiyGood
            && $mabiyReturnAffectedRate < $highMabiyReturnRate
            && !$pathaoHighRisk;

        if ($trusted) {
            $result['risk_level'] = 'TRUSTED';
            $result['otp_required'] = false;
            $result['manual_review'] = false;
            $result['reasons'][] = 'trusted_customer';
        }

        return $result;
    }

    private function numberOption(array $options, string $key, $default): float
    {
        return isset($options[$key]) && is_numeric($options[$key])
            ? max(0, (float) $options[$key])
            : (float) $default;
    }

    private function nonNegativeInteger($value): int
    {
        return is_numeric($value) ? max(0, (int) $value) : 0;
    }

    private function nonNegativeFloat($value): float
    {
        return is_numeric($value) ? max(0, (float) $value) : 0.0;
    }

    private function nullableString($value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
