<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderRiskService
{
    public function getMabiyHistory(int $userId): array
    {
        $totalOrders = Order::where('user_id', $userId)->count();
        $completedOrders = Order::where('user_id', $userId)->where('status', 6)->count();
        $cancelledOrders = Order::where('user_id', $userId)->where('status', 5)->count();
        $pendingOrders = Order::where('user_id', $userId)->where('status', 1)->count();
        $processingOrders = Order::where('user_id', $userId)->where('status', 2)->count();

        $returnAffectedOrders = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.user_id', $userId)
            ->where('order_details.status', 12)
            ->distinct()
            ->count('orders.id');

        $completionRate = $totalOrders > 0
            ? round(($completedOrders / $totalOrders) * 100, 2)
            : 0.0;

        $returnAffectedRate = $totalOrders > 0
            ? round(($returnAffectedOrders / $totalOrders) * 100, 2)
            : 0.0;

        return [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'cancelled_orders' => $cancelledOrders,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders,
            'return_affected_orders' => $returnAffectedOrders,
            'completion_rate' => $completionRate,
            'return_affected_rate' => $returnAffectedRate,
        ];
    }
}
