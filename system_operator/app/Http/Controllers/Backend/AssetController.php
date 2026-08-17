<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admins;
use App\Models\Division;
use App\Models\District;
use App\Models\UsersMeta;
use App\Models\Addresses;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\CurrentAsset;
use App\Models\Account;
use App\Models\Investor;
use App\Models\Loan;
use App\Models\LoanInvestorPayment;

use Hash,Image,Auth,DB,Helper;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class AssetController extends Controller
{
    public function __construct(){
        $this->middleware(function($request,$next){
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index(){
        if(is_null($this->user) || !$this->user->can('accounts.view')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        if (Auth::user()->getRoleNames() == '["seller"]') {
            $accounts = CurrentAsset::where('branch_id', Auth::user()->id)->all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->where('branch_id', Auth::user()->id)->get();
        }else{
            $accounts = CurrentAsset::all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->get();
        }
        return view('backend.pages.accounts.manage-accounts', compact('accounts', 'branches'));  
    }

    public function getAssetData(){
        if (Auth::user()->getRoleNames() == '["seller"]') {
            $accounts = CurrentAsset::where('branch_id', Auth::user()->id)->all();
        }else{
            $data = CurrentAsset::all();
        }
        return DataTables::of($data)->addIndexColumn()
       
        ->editColumn('branch_id', function ($row) {
            return $row->branch->name ?? '';
        })

        ->addColumn('action', function($row){
            $btn = '';
            
            $btn = '<a class="btn-sm btn btn-success edit_btn" data-id="'.$row->id.'" data-name="'.$row->name.'" data-type="'.$row->type.'" data-branch="'.$row->branch_id.'"><i class="mdi mdi-playlist-edit"></i> Edit</a>';
        
            $btn = $btn.'<a class="btn-sm btn btn-info ml-1" href="'.route('admin.order.edit',$row->id).'"><i class="mdi mdi-eye"></i> Ledger</a>';
            
            return $btn;
        })

        ->rawColumns(['branch_id', 'action'])->make(true);
    }


    public function store(Request $request){
        $asset = new CurrentAsset();
        $asset->name = $request->name;
        if (Auth::user()->getRoleNames() == '["seller"]') {
            $asset->branch_id = Auth::user()->id;
        }else{
            $asset->branch_id = $request->branch_id;
        }
        $asset->type = $request->type;
        $asset->amount = $request->amount;
        $asset->save();

        Helper::accountHistory($request->branch_id,'','asset_balance' , 0, $request->amount, $request->amount, $asset->id, '', $asset->id, Helper::getBusinessBalance());

        return redirect()->back()->with('success', 'Asset created successfully!');
    }


    public function update(Request $request){
        $asset = CurrentAsset::find($request->account_id);
        $asset->name = $request->name;
        if (Auth::user()->getRoleNames() == '["seller"]') {
            $asset->branch_id = Auth::user()->id;
        }else{
            $asset->branch_id = $request->branch_id;
        }
        $asset->type = $request->type;
        $asset->save();

        // Helper::accountHistory($request->branch_id,'','asset_balance' , 0, $request->amount, $request->amount, $asset->id, '', $asset->id, Helper::getBusinessBalance());

        return redirect()->back()->with('success', 'Asset updated successfully!');
    }

}
