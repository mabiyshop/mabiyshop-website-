<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\CourierHistoryProviderService;
use App\Services\AdminCustomerBlockService;
use App\Services\FraudSecuritySettingsService;
use Auth;
use DB;
use Helper;
use Yajra\DataTables\DataTables;

class FraudSecurityController extends Controller
{
    private $courierHistoryProviderService;
    private $adminCustomerBlockService;
    private $settingsService;

    public function __construct(
        CourierHistoryProviderService $courierHistoryProviderService,
        AdminCustomerBlockService $adminCustomerBlockService,
        FraudSecuritySettingsService $settingsService
    ) {
        $this->courierHistoryProviderService = $courierHistoryProviderService;
        $this->adminCustomerBlockService = $adminCustomerBlockService;
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        return view('backend.pages.fraud-security.list');
    }

    public function getFraudOrders()
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = Order::select('orders.*')
            ->where('orders.fraud_status', 'BLOCKED')
            ->where('orders.is_deleted', 0)
            ->with('statuses', 'user', 'address')
            ->orderBy('orders.id', 'desc');

        return DataTables::of($data)->addIndexColumn()

            ->editColumn('id', function ($row) {
                return 'MS' . date('y', strtotime($row->created_at)) . $row->id;
            })

            ->editColumn('created_at', function ($row) {
                return date('d M, Y h:ia', strtotime($row->created_at));
            })

            ->addColumn('user_name', function ($row) {
                return optional($row->user)->name;
            })

            ->addColumn('shipping_name', function ($row) {
                if ($row->is_pickpoint == 1) {
                    return optional($row->pickpoint_address)->title;
                }
                return optional($row->historical_shipping_address)->shipping_first_name . ' ' . optional($row->historical_shipping_address)->shipping_last_name;
            })

            ->addColumn('shipping_phone', function ($row) {
                if ($row->is_pickpoint == 1) {
                    return optional($row->pickpoint_address)->phone;
                }
                return optional($row->historical_shipping_address)->shipping_phone;
            })

            ->editColumn('total_amount', function ($row) {
                return Helper::getDefaultCurrency()->currency_symbol . ' ' . $row->total_amount;
            })

            ->addColumn('risk_level', function ($row) {
                return $row->risk_level ?? '-';
            })

            ->addColumn('fraud_status', function ($row) {
                $badge = '<span class="badge text-light" style="background-color: ';
                if ($row->fraud_status === 'BLOCKED') {
                    $badge .= '#e62e2d;">Blocked</span>';
                } elseif ($row->fraud_status === 'REVIEW') {
                    $badge .= '#ec8b23;">Review</span>';
                } else {
                    $badge .= '#3b8104;">Normal</span>';
                }
                return $badge;
            })

            ->addColumn('courier_success_rate', function ($row) {
                if ($row->courier_success_rate !== null) {
                    $rate = (float) $row->courier_success_rate;
                    $color = '#3b8104';
                    if ($rate < 40) {
                        $color = '#e62e2d';
                    } elseif ($rate < 60) {
                        $color = '#ec8b23';
                    }
                    return '<span class="badge text-light" style="background-color: ' . $color . ';">' . round($rate) . '%</span>';
                }
                return '<span class="badge text-light" style="background-color: #999;">Not checked</span>';
            })

            ->addColumn('otp_required', function ($row) {
                return $row->otp_required ? '<span class="badge text-light" style="background-color: #ec8b23;">Yes</span>' : '<span class="badge text-light" style="background-color: #3b8104;">No</span>';
            })

            ->addColumn('fraud_reason', function ($row) {
                $reasons = is_array($row->risk_reasons) ? $row->risk_reasons : (json_decode($row->risk_reasons, true) ?: []);
                if (!empty($reasons)) {
                    return '<span title="' . htmlspecialchars(implode(', ', $reasons)) . '">' . htmlspecialchars(implode(', ', array_slice($reasons, 0, 2))) . (count($reasons) > 2 ? '...' : '') . '</span>';
                }
                return $row->fraud_reason ?? '-';
            })

            ->addColumn('action', function ($row) {
                $btn = '<a class="icon_btn text-success" href="' . route('admin.fraud-security.show', $row->id) . '"><i class="mdi mdi-eye"></i></a> ';
                if ($row->fraud_status === 'BLOCKED' || $row->fraud_status === 'REVIEW') {
                    $btn .= '<a class="icon_btn text-primary release-btn" data-id="' . $row->id . '" href="#" title="Release to Main Orders"><i class="mdi mdi-check"></i></a>';
                }
                return $btn;
            })

            ->rawColumns(['fraud_status', 'courier_success_rate', 'otp_required', 'fraud_reason', 'action'])->make(true);
    }

    public function show($id)
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $order = Order::findOrFail($id);
        $order->refunded = optional($order->product_return_by_order_id)->sum('refund_amount');

        $customerBlock = null;
        if ($order->user_id) {
            $customerBlock = $this->adminCustomerBlockService->getCurrentStatus($order->user);
        }

        $orderLog = DB::table('order_log')->where('order_id', $id)->get();

        return view('backend.pages.fraud-security.show', compact('order', 'orderLog', 'customerBlock'));
    }

    public function release(Request $request, $id)
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.edit')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        $order->fraud_status = 'NORMAL';
        $order->manual_review = false;
        $order->fraud_action_by = Auth::id();
        $order->fraud_action_at = now();

        if ($request->filled('note')) {
            $order->fraud_reason = ($order->fraud_reason ? $order->fraud_reason . ' | ' : '') . 'Released: ' . $request->note;
        }

        $order->save();

        Helper::setOrderLog($order->id, null, 'Order released from Fraud & Security to normal workflow by ' . Auth::user()->name, Auth::id(), $request->note);

        return response()->json(['status' => 1, 'message' => 'Order released successfully.']);
    }

    public function keepBlocked(Request $request, $id)
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.edit')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        $order->fraud_status = 'BLOCKED';
        $order->manual_review = true;
        $order->fraud_reason = $request->reason;
        $order->fraud_action_by = Auth::id();
        $order->fraud_action_at = now();
        $order->save();

        Helper::setOrderLog($order->id, null, 'Order kept blocked in Fraud & Security by ' . Auth::user()->name . '. Reason: ' . $request->reason, Auth::id(), $request->reason);

        return response()->json(['status' => 1, 'message' => 'Order kept blocked.']);
    }

    public function markAsFraud(Request $request, $id)
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.edit')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        $order->fraud_status = 'BLOCKED';
        $order->manual_review = true;
        $order->fraud_reason = $request->reason;
        $order->fraud_action_by = Auth::id();
        $order->fraud_action_at = now();
        $order->save();

        Helper::setOrderLog($order->id, null, 'Order marked as fraud by ' . Auth::user()->name . '. Reason: ' . $request->reason, Auth::id(), $request->reason);

        return response()->json(['status' => 1, 'message' => 'Order marked as fraud.']);
    }

    public function settings()
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $settings = [
            'good_courier_success_rate' => $this->settingsService->get('good_courier_success_rate', 60),
            'high_risk_courier_success_below' => $this->settingsService->get('high_risk_courier_success_below', 40),
            'trusted_courier_success_rate' => $this->settingsService->get('trusted_courier_success_rate', 80),
            'trusted_mabiy_completed_orders' => $this->settingsService->get('trusted_mabiy_completed_orders', 2),
            'high_mabiy_return_rate' => $this->settingsService->get('high_mabiy_return_rate', 50),
            'minimum_courier_orders' => $this->settingsService->get('minimum_courier_orders', 3),
            'new_customer_otp_required' => $this->settingsService->get('new_customer_otp_required', true) ? 1 : 0,
            'new_customer_manual_review' => $this->settingsService->get('new_customer_manual_review', false) ? 1 : 0,
            'high_risk_action' => $this->settingsService->get('high_risk_action', 'review'),
            'provider_unavailable_action' => $this->settingsService->get('provider_unavailable_action', 'allow'),
            'otp_bypass_min_delivered_orders' => $this->settingsService->get('otp_bypass_min_delivered_orders', 1),
            'otp_bypass_mabiy_success_rate' => $this->settingsService->get('otp_bypass_mabiy_success_rate', 50),
        ];

        return view('backend.pages.fraud-security.settings', compact('settings'));
    }

    public function settingsSave(Request $request)
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.edit')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'good_courier_success_rate' => 'required|numeric|min:0|max:100',
            'high_risk_courier_success_below' => 'required|numeric|min:0|max:100',
            'trusted_courier_success_rate' => 'required|numeric|min:0|max:100',
            'trusted_mabiy_completed_orders' => 'required|integer|min:0',
            'high_mabiy_return_rate' => 'required|numeric|min:0|max:100',
            'minimum_courier_orders' => 'required|integer|min:0',
            'new_customer_otp_required' => 'required|in:0,1',
            'new_customer_manual_review' => 'required|in:0,1',
            'high_risk_action' => 'required|in:review,block',
            'provider_unavailable_action' => 'required|in:allow,review',
            'otp_bypass_min_delivered_orders' => 'required|integer|min:1',
            'otp_bypass_mabiy_success_rate' => 'required|numeric|min:0|max:100',
        ]);

        if ((float) $request->high_risk_courier_success_below >= (float) $request->good_courier_success_rate) {
            return response()->json(['error' => 'High-risk threshold must be below Normal threshold.'], 422);
        }

        $this->settingsService->set('good_courier_success_rate', (float) $request->good_courier_success_rate, 'number');
        $this->settingsService->set('high_risk_courier_success_below', (float) $request->high_risk_courier_success_below, 'number');
        $this->settingsService->set('trusted_courier_success_rate', (float) $request->trusted_courier_success_rate, 'number');
        $this->settingsService->set('trusted_mabiy_completed_orders', (int) $request->trusted_mabiy_completed_orders, 'integer');
        $this->settingsService->set('high_mabiy_return_rate', (float) $request->high_mabiy_return_rate, 'number');
        $this->settingsService->set('minimum_courier_orders', (int) $request->minimum_courier_orders, 'integer');
        $this->settingsService->set('new_customer_otp_required', (bool) $request->new_customer_otp_required, 'boolean');
        $this->settingsService->set('new_customer_manual_review', (bool) $request->new_customer_manual_review, 'boolean');
        $this->settingsService->set('high_risk_action', $request->high_risk_action, 'string');
        $this->settingsService->set('provider_unavailable_action', $request->provider_unavailable_action, 'string');
        $this->settingsService->set('otp_bypass_min_delivered_orders', (int) $request->otp_bypass_min_delivered_orders, 'integer');
        $this->settingsService->set('otp_bypass_mabiy_success_rate', (float) $request->otp_bypass_mabiy_success_rate, 'number');

        Helper::setOrderLog(0, null, 'Fraud & Security settings updated by ' . Auth::user()->name, Auth::id(), null);

        return response()->json(['status' => 1, 'message' => 'Settings saved successfully.']);
    }

    public function courierCheck($id)
    {
        if (is_null(Auth::user()) || !Auth::user()->can('order.view')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order = Order::findOrFail($id);
        $user = $order->user;

        if (!$user) {
            return response()->json(['error' => 'Customer not found for this order.'], 404);
        }

        try {
            $courierHistory = $this->courierHistoryProviderService->getHistory((string) $user->phone);
            $order->courier_history_snapshot = $courierHistory;
            $order->courier_success_rate = $courierHistory['combined']['success_rate'] ?? null;
            $order->save();

            return response()->json(['status' => 1, 'data' => $courierHistory]);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Courier check failed: ' . $e->getMessage()], 500);
        }
    }
}
