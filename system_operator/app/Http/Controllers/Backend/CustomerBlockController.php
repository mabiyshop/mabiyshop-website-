<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminCustomerBlockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerBlockController extends Controller
{
    private $adminCustomerBlockService;

    public function __construct(AdminCustomerBlockService $adminCustomerBlockService)
    {
        $this->adminCustomerBlockService = $adminCustomerBlockService;
    }

    public function updateBlockStatus(Request $request, $customerId)
    {
        $request->validate([
            'status' => 'required|string|in:NORMAL,WATCH,BLOCKED',
            'reason' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
        ]);

        $customer = User::findOrFail($customerId);

        if (is_null(Auth::user()) || !Auth::user()->can('customer.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $this->adminCustomerBlockService->setBlockStatus(
            $customer,
            $request->input('status'),
            $request->input('reason'),
            $request->input('expires_at'),
            Auth::id()
        );

        return redirect()->route('admin.user.edit', $customerId)->with('success', 'Customer block status updated successfully.');
    }

    public function getBlockHistory($customerId)
    {
        $customer = User::findOrFail($customerId);

        if (is_null(Auth::user()) || !Auth::user()->can('customer.edit')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $history = $this->adminCustomerBlockService->getHistory($customer);

        return response()->json($history);
    }
}
