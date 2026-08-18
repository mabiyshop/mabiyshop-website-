<?php

namespace App\Services;

use App\Models\CustomerBlock;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminCustomerBlockService
{
    public function setBlockStatus(
        User $customer,
        string $status,
        ?string $reason = null,
        ?string $expiresAt = null,
        int $blockedBy = 0
    ): void {
        $data = [
            'user_id' => $customer->id,
            'phone' => $customer->phone,
            'status' => $status,
            'reason' => $reason,
            'blocked_by' => $blockedBy ?: Auth::id(),
        ];

        if ($expiresAt) {
            $data['expires_at'] = $expiresAt;
        }

        CustomerBlock::create($data);
    }

    public function getHistory(User $customer): array
    {
        return CustomerBlock::where('user_id', $customer->id)
            ->orderByDesc('id')
            ->get(['id', 'status', 'reason', 'blocked_by', 'expires_at', 'created_at'])
            ->toArray();
    }

    public function getCurrentStatus(User $customer): ?array
    {
        $latest = CustomerBlock::where('user_id', $customer->id)
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return [
                'status' => 'NORMAL',
                'reason' => null,
                'expires_at' => null,
                'created_at' => null,
            ];
        }

        return [
            'status' => $latest->status,
            'reason' => $latest->reason,
            'expires_at' => $latest->expires_at,
            'created_at' => $latest->created_at,
        ];
    }
}
