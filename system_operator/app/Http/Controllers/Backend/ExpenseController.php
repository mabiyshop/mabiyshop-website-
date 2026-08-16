<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\CareerRequest;
use App\Models\Admins;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Helper;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function expenseList()
    {
        if(is_null($this->user) || !$this->user->can('expense.view')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        return view('backend.pages.expense.list');
    }

    public function getExpenseList()
    {
        if(is_null($this->user) || !$this->user->can('expense.view')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        if (Auth::user()->getRoleNames() == '["seller"]') {
            $data = Expense::where('branch_id', Auth::user()->id)->where('is_deleted', 0)->latest();
        }else{
            $data = Expense::where('is_deleted', 0)->latest();
        }
        
        return DataTables::of($data)->addIndexColumn()

            ->editColumn('branch_id', function ($row) {
                return $row->shop->name ?? '';
            })

            ->editColumn('status', function ($row) {
                $text = '';
                if ($row->status == 1) {
                    $text = '<span class="badge badge-warning">Pending</span>';
                } else if ($row->status == 6) {
                    $text = '<span class="badge badge-success">Completed</span>';
                }

                return $text;
            })
            ->editColumn('expense_category', function ($row) {
                return $row->expenses_category->title ?? '';
            })
            ->editColumn('payment_method', function ($row) {
                return $row->payment_methods->name ?? '';
            })
            ->addColumn('action', function ($row) {
                $btn = '';
               
                if (Auth::user()->can('expense.edit')) {
                    $btn = $btn . '<a class="icon_btn text-info" href="' . route('admin.expense.edit', $row->id) . '"><i class="mdi mdi-playlist-edit"></i></a>';
                }
                if (Auth::user()->can('expense.delete')) {
                    // $btn = $btn . '<a class="icon_btn text-danger delete_btn" href="' . route('admin.career.delete', $row->id) . '"><i class="mdi mdi-delete"></i></a>';

                    $btn = $btn . '<a class="icon_btn text-danger delete_btn" data-url="' . route('admin.expense.delete', $row->id) . '" data-toggle="modal" data-target="#deleteModal" href="#"><i class="mdi mdi-delete"></i></a>';
                }

                return $btn;
            })

            ->rawColumns(['description','branch_id','expense_category', 'payment_method', 'status', 'action'])->make(true);
    }

    public function expenseCreate()
    {
        if(is_null($this->user) || !$this->user->can('expense.create')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $shops = Admins::where('status',1)->where('is_branch', 1)->get();
        return View('backend.pages.expense.create', compact('shops'));
    }

    public function expenseStore(Request $request)
    {
        if(is_null($this->user) || !$this->user->can('expense.create')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $expense = new Expense();

        $expense->branch_id = $request->shop_name;
        $expense->payment_method = $request->payment_method;
        $expense->title = $request->title;
        $expense->expense_category = $request->expense_category;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
        $expense->status = $request->status;
        $expense->save();

        if ($request->status == 6) {
            Helper::debitAssetBalance($expense->payment_method, $expense->amount);
            Helper::accountHistory($request->shop_name, '', 'expenses', $request->amount, 0, $request->amount, $expense->id, '', $request->payment_method, Helper::getBusinessBalance());
        }
        
        return redirect()->back()->with('success', 'Expense successfully created!');
    }

    public function expenseEdit($id)
    {
        if(is_null($this->user) || !$this->user->can('expense.edit')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        $shops = Admins::where('status',1)->where('is_branch', 1)->get();
        $data = Expense::where('id', $id)->first();
        return View('backend.pages.expense.edit', get_defined_vars());
    }

    public function expenseUpdate(Request $request)
    {
        if(is_null($this->user) || !$this->user->can('expense.edit')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        // dd($request->id);
        $expense = Expense::find($request->id);
        $expense->branch_id = $request->shop_name;
        $expense->payment_method = $request->payment_method;
        $expense->title = $request->title;
        $expense->expense_category = $request->expense_category;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
        $expense->status = $request->status;
        $expense->save();

        if ($request->status == 6) {
            Helper::debitAssetBalance($expense->payment_method, $expense->amount);
            Helper::accountHistory($request->shop_name, '', 'expenses', $request->amount, 0, $request->amount, $expense->id, '', $request->payment_method, Helper::getBusinessBalance());
        }
        
        return redirect()->back()->with('success', 'Expense successfully updated!');
    }

    public function expenseDelete(Request $request, $id)
    {
        if(is_null($this->user) || !$this->user->can('expense.delete')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        $expense = Expense::find($id);

        //Insert Trash Data
        $type = 'expense';
        $type_id = $id;
        $reason = $request->reason ?? '';
        $data = $expense;
        \Helper::setTrashInfo($type, $type_id, $reason, $data);

        $expense->is_deleted = 1;
        $expense->save();

        return redirect()->back()->with('success', 'Expense successfully deleted!');
    }

    public function expenseReport(){
        if(is_null($this->user) || !$this->user->can('expense.report')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        return view('backend.pages.expense.report');
    }


    public function getExpenseReport(Request $request)
    {
        if(is_null($this->user) || !$this->user->can('expense.report')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        $data = Expense::where('is_deleted', 0);
        if($request->expense_category > 0){
            $data->where('expense_category',$request->expense_category);
            if($request->filter_by == 'today'){
                $today = date('Y-m-d');
                $data->whereDay('created_at', $today);
            }
            else if($request->filter_by == 'this month'){
                $month = date('Y-m-d');
                $data->whereMonth('created_at', $month);
            }
            else if($request->filter_by == 'this year'){
                $year = date('Y-m-d');
                $data->whereYear('created_at', $year);
            }
            elseif ($request->filter_by == '7 day'){
                $filter_option = Carbon::today()->subDays(7);
                $data->where('created_at', '>=', $filter_option);
            }
            elseif ($request->filter_by == 'date range'){
                if ($request->start_date != 0 && $request->end_date != 0){
                    $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
                }
            }
        }
        else{
            if ($request->filter_by == 'today'){
                $today = date('Y-m-d');
                $data->whereDay('orders.created_at', $today);
            }
            else if($request->filter_by == 'this month'){
                $month = date('Y-m-d');
                $data->whereMonth('created_at', $month);
            }
            else if($request->filter_by == 'this year'){
                $year = date('Y-m-d');
                $data->whereYear('created_at', $year);
            }
            elseif ($request->filter_by == '7 day'){
                $filter_option = Carbon::today()->subDays(7);
                $data->where('created_at', '>=', $filter_option);
            }
            elseif ($request->filter_by == 'date range'){
                if ($request->start_date != 0 && $request->end_date != 0){
                    $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
                }
            }
        }
        
        if($request->status_id > 0){
            $data->where('status', $request->status_id);
        }
        return DataTables::of($data)->addIndexColumn()
            ->editColumn('status', function ($row) {
                $text = '';
                if ($row->status == 1) {
                    $text = '<span class="badge badge-warning">Pending</span>';
                } else if ($row->status == 6) {
                    $text = '<span class="badge badge-success">Completed</span>';
                }

                return $text;
            })
            ->editColumn('expense_category', function ($row) {
                return $row->expenses_category->title ?? '';
            })
            ->editColumn('payment_method', function ($row) {
                return $row->payment_methods->name ?? '';
            })
            ->rawColumns(['description','expense_category', 'status'])->make(true);
    }

    public function expenseCategoryCreate(Request $request){

        $category = new ExpenseCategory();
        $category->title = $request->expense_category;
        $category->save();

        return redirect()->back()->with('success', 'Expense  category successfully created!');
    }

    public function expenseCategoryDelete($id){
        $category = ExpenseCategory::find($id);
        $category->is_deleted = 1;
        $category->save();
        
        return redirect()->back()->with('success', 'Expense  category successfully deleted!');
    }
}