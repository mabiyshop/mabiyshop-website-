<?php

namespace App\Http\Middleware;

use App\Services\OrderRiskService;
use App\Services\CourierHistoryProviderService;
use App\Services\CustomerRiskDecisionService;
use Closure;
use Throwable;

class ApplyOrderRiskDecision
{
    private $orderRiskService;
    private $courierHistoryProviderService;
    private $customerRiskDecisionService;

    public function __construct(
        OrderRiskService $orderRiskService,
        CourierHistoryProviderService $courierHistoryProviderService,
        CustomerRiskDecisionService $customerRiskDecisionService
    ) {
        $this->orderRiskService = $orderRiskService;
        $this->courierHistoryProviderService = $courierHistoryProviderService;
        $this->customerRiskDecisionService = $customerRiskDecisionService;
    }

    public function handle($request, Closure $next)
    {
        $user = auth('customer-api')->user();

        if ($user === null) {
            return $next($request);
        }

        try {
            $mabiyHistory = $this->orderRiskService->getMabiyHistory((int) $user->id);
            $courierHistory = $this->courierHistoryProviderService->getHistory((string) $user->phone);
            $decision = $this->customerRiskDecisionService->decide($mabiyHistory, $courierHistory, [
                'manual_block' => false,
            ]);

            if (($decision['order_allowed'] ?? true) === false) {
                return response()->json([
                    'status' => 0,
                    'error' => 'order_rejected_by_risk',
                    'message' => 'Order cannot be processed at this time.',
                    'risk_decision' => $decision,
                ], 403);
            }

            $request->attributes->set('risk_decision', $decision);
        } catch (Throwable $exception) {
            return response()->json([
                'status' => 0,
                'error' => 'risk_check_failed',
                'message' => 'Risk check failed. Please try again later.',
            ], 500);
        }

        return $next($request);
    }
}
