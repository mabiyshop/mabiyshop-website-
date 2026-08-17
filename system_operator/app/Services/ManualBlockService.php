<?php

namespace App\Services;

use App\Models\CustomerBlock;
use Illuminate\Database\Eloquent\Builder;

class ManualBlockService
{
    public function resolve(
        ?int $userId,
        ?string $phone,
        ?string $ipAddress = null
    ): array {
        $normalizedPhone = $this->normalizePhone($phone);

        $userMatch = $userId !== null
            ? $this->latestActiveRecord('user_id', $userId)
            : null;
        $phoneMatch = $normalizedPhone !== null
            ? $this->latestActiveRecord('phone', $normalizedPhone)
            : null;
        $ipMatch = $this->validIpAddress($ipAddress)
            ? $this->latestActiveRecord('ip_address', $ipAddress)
            : null;
        $ipSignal = $ipMatch
            && in_array($ipMatch->status, ['WATCH', 'BLOCKED'], true);

        if ($userMatch && $userMatch->status === 'BLOCKED') {
            return $this->result('BLOCKED', true, $ipSignal, 'user_id', $userMatch);
        }

        if ($phoneMatch && $phoneMatch->status === 'BLOCKED') {
            return $this->result('BLOCKED', true, $ipSignal, 'phone', $phoneMatch);
        }

        if ($userMatch && $userMatch->status === 'WATCH') {
            return $this->result('WATCH', false, $ipSignal, 'user_id', $userMatch);
        }

        if ($phoneMatch && $phoneMatch->status === 'WATCH') {
            return $this->result('WATCH', false, $ipSignal, 'phone', $phoneMatch);
        }

        if ($ipSignal) {
            return $this->result('WATCH', false, true, 'ip', $ipMatch);
        }

        return $this->result('NORMAL', false, false, null);
    }

    private function latestActiveRecord(string $column, $value): ?CustomerBlock
    {
        return $this->activeRecords()
            ->where($column, $value)
            ->latest('id')
            ->first();
    }

    private function activeRecords(): Builder
    {
        return CustomerBlock::query()
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    private function normalizePhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (!is_string($digits)) {
            return null;
        }

        if (strpos($digits, '880') === 0) {
            $digits = '0' . substr($digits, 3);
        }

        return preg_match('/^01\d{9}$/', $digits) === 1 ? $digits : null;
    }

    private function validIpAddress(?string $ipAddress): bool
    {
        return is_string($ipAddress)
            && filter_var($ipAddress, FILTER_VALIDATE_IP) !== false;
    }

    private function result(
        string $state,
        bool $directBlock,
        bool $ipSignal,
        ?string $matchedBy,
        ?CustomerBlock $record = null
    ): array {
        return [
            'state' => $state,
            'direct_block' => $directBlock,
            'ip_signal' => $ipSignal,
            'matched_by' => $matchedBy,
            'reason' => $record ? $record->reason : null,
            'expires_at' => $record && $record->expires_at
                ? $record->expires_at->toDateTimeString()
                : null,
        ];
    }
}
