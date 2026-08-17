<?php

namespace App\Http\Middleware;

use App\Services\ManualBlockService;
use Closure;
use Throwable;

class RejectBlockedCustomer
{
    private $manualBlockService;

    public function __construct(ManualBlockService $manualBlockService)
    {
        $this->manualBlockService = $manualBlockService;
    }

    public function handle($request, Closure $next)
    {
        $user = auth('customer-api')->user();

        if ($user === null) {
            return response()->json([
                'status' => 0,
                'error' => 'unauthenticated',
                'message' => 'Authentication required.',
            ], 401);
        }

        try {
            $blockState = $this->manualBlockService->resolve(
                (int) $user->id,
                $user->phone,
                $request->ip()
            );

            $request->attributes->set('manual_fraud_state', $blockState);

            if (
                ($blockState['state'] ?? null) === 'BLOCKED'
                && ($blockState['direct_block'] ?? false) === true
            ) {
                return response()->json([
                    'status' => 0,
                    'error' => 'customer_blocked',
                    'message' => 'This account cannot place an order at this time.',
                ], 403);
            }
        } catch (Throwable $exception) {
            return $next($request);
        }

        return $next($request);
    }
}
