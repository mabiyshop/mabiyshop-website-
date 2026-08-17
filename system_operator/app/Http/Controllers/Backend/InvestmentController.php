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

class InvestmentController extends Controller
{
    public function __construct(){
        $this->middleware(function($request,$next){
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index(){
        if(is_null($this->user) || !$this->user->can('accounts.manage.investor')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $accounts = CurrentAsset::all();
        $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->get();
        
        return view('backend.pages.accounts.manage-investor', compact('accounts', 'branches'));  
    }

    public function getInvestorData(){
        
        $data = Investor::all();
        
        return DataTables::of($data)->addIndexColumn()
       
        ->editColumn('share', function($row){
            return $row->share.' %';
        })

        ->editColumn('amount', function($row){
            return Helper::getDefaultCurrency()->currency_symbol . ' ' . $row->amount;
        })

        ->editColumn('paid', function($row){
            return Helper::getDefaultCurrency()->currency_symbol . ' ' . $row->paid ?? 0;
        })

        ->addColumn('balance', function($row){
            return Helper::getDefaultCurrency()->currency_symbol . ' ' . ((\Helper::getBusinessBalance() * $row->share) / 100);
        })

        ->addColumn('action', function($row){
            $btn = '';
            
            $btn = '<a class="btn-sm btn btn-success edit_btn text-light" data-id="'.$row->id.'" data-name="'.$row->name.'" data-share="'.$row->share.'" ><i class="mdi mdi-playlist-edit"></i> Edit</a>';
            $btn = $btn.'<a class="btn-sm btn btn-warning ml-1 payments text-light" data-id="'.$row->id.'"  ><i class="mdi mdi-eye"></i> Payments</a>';
            $btn = $btn.'<a class="btn-sm btn btn-info ml-1 text-light pay_btn" data-id="'.$row->id.'"><i class="fa fa-money"></i> Pay</a>';

            return $btn;
        })

        ->rawColumns(['share', 'amount', 'paid', 'balance', 'action'])->make(true);
    }


    public function store(Request $request){
        $investor = new Investor();
        $investor->name = $request->name;
        $investor->amount = $request->amount;
        $investor->share = $request->share;
        $investor->payment_method = $request->payment_method;
        $investor->save();

        Helper::creditAssetBalance($investor->payment_method, $investor->amount);
        Helper::accountHistory('','owner Invest' , 0, $request->amount, $request->amount, $request->payment_method, '', $request->payment_method, Helper::getBusinessBalance());
        
        return redirect()->back()->with('success', 'Investor created successfully!');
    }

    public function update(Request $request){
        $investor = Investor::find($request->investor_id);
        $investor->name = $request->name;
        $investor->share = $request->share;
        $investor->save();

        return redirect()->back()->with('success', 'Investor updated successfully!');
    }

    public function getInvestorPaymentData($investor_id){
        $payments = LoanInvestorPayment::where('investor', $investor_id)->get();

        $html = '';
        $count = 1;
        foreach($payments as $row){
            $html .='
            <tr>
                <th scope="row">'.$count.'</th>
                <td>'.$row->date.'</td>
                <td>'.$row->loan_invest.'</td>
                <td>৳ '.$row->amount.'</td>
                <td>'.$row->asset->name.'</td>
                <td>'.$row->investors->name.',
                '.$row->investors->amount .' ,
                '.$row->investors->share .' %
                </td>
            </tr>';
            $count++;
        }

        return  $html;
    }
}
