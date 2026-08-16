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

class LoanController extends Controller
{
    public function __construct(){
        $this->middleware(function($request,$next){
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index(){
        if(is_null($this->user) || !$this->user->can('accounts.manage.loan')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        if (Auth::user()->getRoleNames() == '["seller"]') {
            $accounts = CurrentAsset::where('branch_id', Auth::user()->id)->all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->where('branch_id', Auth::user()->id)->get();
        }else{
            $accounts = CurrentAsset::all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->get();
        }
        
        return view('backend.pages.accounts.manage-loans', compact('accounts', 'branches'));  
    }


    public function getLoanData(Request $request){
        if(isset($request->branch_id) && $request->branch_id > 0){
            $data = Loan::where('branch_id', $request->branch_id)->get();
        }else{
            $data = Loan::get();
        }
        return DataTables::of($data)->addIndexColumn()
        ->editColumn('date', function ($row) {
            return date('d F, Y', strtotime($row->date));
        })

        ->editColumn('payment_last_date', function ($row) {
            return date('d F, Y', strtotime($row->payment_last_date));
        })

        ->editColumn('amount', function($row){
            return Helper::getDefaultCurrency()->currency_symbol . ' ' . $row->amount;
        })

        ->editColumn('interest', function($row){
            return $row->interest.' %';
        })

        ->editColumn('branch_id', function($row){
            return $row->branch->shopinfo->name ?? '';
        })
       
        ->editColumn('payment_method', function ($row) {
            return $row->asset->name ?? '';
        })

        ->editColumn('payable', function($row){
            return Helper::getDefaultCurrency()->currency_symbol . ' ' . $row->payable;
        })

        ->editColumn('paid', function($row){
            return Helper::getDefaultCurrency()->currency_symbol . ' ' . $row->paid;
        })

        ->addColumn('pending', function($row){
            return Helper::getDefaultCurrency()->currency_symbol . ' ' . ($row->payable - $row->paid);
        })

        ->addColumn('action', function($row){
            $btn = '';

            $btn = '<a class="btn-sm btn btn-success payments text-light" data-id="'.$row->id.'"  ><i class="mdi mdi-eye"></i> Payments</a>';
            $btn = $btn.'<a class="btn-sm btn btn-info ml-1 text-light pay_btn" data-id="'.$row->id.'"><i class="fa fa-money"></i> Pay</a>';
            
            return $btn;
        })

        ->rawColumns(['date', 'amount','branch_id','interest','payable','paid','pending','payment_last_date','payment_method','action'])->make(true);
    }

    public function store(Request $request){
        $loan = new Loan();
        $loan->branch_id = $request->branch_id;
        $loan->date = $request->date;
        $loan->loan_from = $request->loan_from;
        $loan->amount = $request->amount;
        $loan->interest = $request->interest;
        $loan->payment_last_date = $request->payment_last_date;
        $loan->payment_method = $request->payment_method;
        $loan->payable = $request->amount + (($request->amount * $loan->interest) / 100);
        $loan->save();

        Helper::creditAssetBalance($loan->payment_method, $loan->amount);
        Helper::accountHistory('','Loan' , 0, $request->amount, $request->amount, $request->payment_method, $request->date, $loan->id, Helper::getBusinessBalance());
        
        return redirect()->back()->with('success', 'Loan created successfully!');
    }


    public function loanAndInvestorPayment(Request $request){

        if($request->loan){
            $loan = Loan::find($request->loan);
            $loan->paid = ($loan->paid + $request->amount);
            $loan->save();

            $perpose = $request->loan_invest.' Installments';

            $branch = $loan->branch_id;
        }

        if($request->investor){
            $investor = Investor::find($request->investor);
            $investor->paid = ($investor->paid + $request->amount);
            $investor->save();

            $perpose = 'Pay Investor';
            $branch = Helper::getsettings('default_branch_id');
        }

        $payment = new LoanInvestorPayment();
        $payment->date = $request->date;
        $payment->loan_invest = $request->loan_invest;
        $payment->amount = $request->amount;
        $payment->payment_method = $request->payment_method;
        $payment->loan = $request->loan ?? null;
        $payment->investor = $request->investor ?? null;
        $payment->save();

        Helper::debitAssetBalance($payment->payment_method, $payment->amount);

        Helper::accountHistory($branch,'', $perpose, $request->amount, 0, $request->amount, $request->payment_method, $request->date, $payment->id, Helper::getBusinessBalance());
        
        return redirect()->back()->with('success', 'Payment Successfully!');
    }

    public function getLoanPaymentData($loanid){
        $payments = LoanInvestorPayment::where('loan', $loanid)->get();

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
                <td>'.$row->loans->loan_from.', 
                ৳ '.$row->loans->amount .',
                    '.$row->loans->interest .' %
                </td>
            </tr>';
            $count++;
        }

        return  $html;
    }

}
