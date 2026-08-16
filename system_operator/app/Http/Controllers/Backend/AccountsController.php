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
use App\Models\Supplier;
use App\Models\Purchase;

use Hash,Image,Auth,DB,Helper;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class AccountsController extends Controller
{

      /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware(function($request,$next){
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function user(){
        $users = User::orderBy('id','desc')->where('is_deleted',0)->get();
        return view('backend.pages.users.user',compact('users'));  
    }


    public function getCustomerList(){

        if(is_null($this->user) || !$this->user->can('customer.view')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $data = User::where('is_deleted',0)->orderBy('id','desc');

        return Datatables::of($data)->addIndexColumn()

        ->addColumn('checkbox', function($row){
            return '<div class="form-check form-check-flat">
                        <label class="form-check-label">
                            <input name="select_all[]" type="checkbox"  class="form-check-input checkbox_single_select" value="'.$row->id.'"><i class="input-helper"></i>
                        </label>
                    </div>';
        })

        ->addColumn('customer', function($row){
            $html = '';
            $imageHtml = '';

            if($row->avatar){
                $imageHtml = '<img class="list_img mr-3" src="'.asset('/'.$row->avatar).'">';
            }else{
                $imageHtml = '<img class="list_img mr-3" src="'.asset('uploads/images/default/no-image.png').'">';
            }
            
            $html = '<div class="media">'.$imageHtml.'<div class="media-body"><p class="product_title text-capitalize">'.$row->name.'</p></div>';
            return $html;
        })
        ->addColumn('status', function($row){
            return  '<label class="badge badge_'.strtolower(\Helper::getStatusName('default',$row->status)).'">'.\Helper::getStatusName('default',$row->status).'</label>';
        })

        ->addColumn('action', function($row){
            $btn = '';

            if(Auth::user()->can('customer.accounts')){
                $btn = '<a class="icon_btn text-default" href="'.route('admin.user.accounts',$row->id).'"><i class="mdi mdi-chart-bar"></i></a>';
            }

            if(Auth::user()->can('customer.view')){
                $btn = $btn.'<a class="icon_btn text-info customer_quick_view_btn" href="" data-id="'.$row->id.'" ><i class="mdi mdi-eye"></i></a>';
            }

            if(Auth::user()->can('customer.edit')){
                $btn = $btn.'<a class="icon_btn text-success" href="'.route('admin.user.edit',$row->id).'"><i class="mdi mdi-pencil-box-outline"></i></a>';
            }
            
            if(Auth::user()->can('customer.delete')){
                $btn = $btn.'<a class="icon_btn text-danger delete_btn" data-url="'.route('admin.user.delete',$row->id).'" data-toggle="modal" data-target="#deleteModal" href="#"><i class="mdi mdi-delete"></i></a>';
            }

            return $btn;
        })

        ->rawColumns(['checkbox','customer','status','action'])->make(true);
    }

    public function user_accountsOrders($customer_id){

        $data = Order::where('user_id', $customer_id)
              ->with('statuses')
              ->orderBy('id','desc');

        return Datatables::of($data->get())->addIndexColumn()
        ->addColumn('id', function($row){
             return 'MS'.date('y',strtotime($row->created_at)).$row->id;
         })

        ->addColumn('order_date', function($row){
            return date('d M, Y h:ia',strtotime($row->created_at));
        })

        ->addColumn('user', function($row){
            return $row->user->name;
        })


        ->addColumn('shipping_name', function($row){
            return optional($row->historical_shipping_address)->shipping_first_name.' '.optional($row->historical_shipping_address)->shipping_last_name;
        })

        ->addColumn('shipping_phone', function($row){
            return optional($row->historical_shipping_address)->shipping_phone;
        })


        ->addColumn('product_qty', function($row){
            if(Auth::user()->getRoleNames() == '["seller"]'){
                $product_qty = 0;
                foreach(OrderDetails::where('order_id', $row->id)->get() as $odtls){
                    if($odtls->seller_id == Auth::user()->id){
                        $product_qty += $odtls->product_qty;
                    }
                }
                return $product_qty;
            }else{
                return $row->order_details->sum('product_qty');
            }
        })

        ->addColumn('paid_amount', function($row){
            return Helper::getDefaultCurrency()->currency_symbol.' '.$row->paid_amount;
        })

        ->addColumn('status', function($row){
            return  '<label class="badge text-light" style="background-color: '.$row->statuses->color_code.';">'.$row->statuses->title.'</label>';
        })

        ->addColumn('action', function($row){
            $btn = '';
            if(Auth::user()->can('order.view')){
                $btn = '<a class="icon_btn text-success" href="'.route('admin.order.show',$row->id).'"><i class="mdi mdi-eye"></i></a>';
            }
            if(Auth::user()->can('order.edit')){
                $btn = $btn.'<a class="icon_btn text-info" href="'.route('admin.order.edit',$row->id).'"><i class="mdi mdi-playlist-edit"></i></a>';
            }
            if(Auth::user()->can('order.delete')){
                $btn = $btn.'<a class="icon_btn text-danger delete_btn" data-url="'.route('admin.order.delete',$row->id).'" data-toggle="modal" data-target="#deleteModal" href="#"><i class="mdi mdi-delete"></i></a>';
            }

            return $btn;
        })

        ->rawColumns(['order_date','user','shipping_name','shipping_phone','product_qty','paid_amount','promotion','status','action'])->make(true);
    }

    public function user_edit($id){
        $divisions = Division::orderBy('title','asc')->get();
        $districts = District::orderBy('title','asc')->get();
        $user = User::find($id);
        $address = Addresses::where('user_id',$id)->get();
        foreach($address as $row){
            if ($user->default_address_id == $row->id) {
                $is_defualt = 'Yes';
            }else{
                $is_defualt = 'No';
            }
            $row->is_defualt = $is_defualt;
        }
        return view('backend.pages.users.user_edit',compact('user','divisions','districts','address'));  
    }

    public function user_create(){
        $divisions = Division::orderBy('title','asc')->get();
        $districts = District::orderBy('title','asc')->get();
        
        return view('backend.pages.users.user_create',compact('divisions','districts'));  
    }

    public function user_store(Request $request){

        $request->validate([
            'name' => 'required | max:191 | string',
            // 'email'=> 'required | max:50 | email',
            'phone'=> 'required | max:15 | string |unique:users',
        ]);
    
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->avatar = $request->avatar;
        $user->email_verified_at = now();
        $tempPass = rand(111111,999999);
        $user->password = Hash::make($tempPass);
        $user->status = $request->status ? 1 : 0;
        $user->save();

        $address = new Addresses;
        $address->user_id = $user->id;
        $address->shipping_first_name = $request->name;
        $address->shipping_last_name = '';
        $address->shipping_phone = $request->phone;
        $address->shipping_email = $request->email;
        $address->shipping_division = $request->division_id;
        $address->shipping_district = $request->district_id;
        $address->shipping_thana = $request->upazila_id;
        $address->shipping_union = $request->union_id;
        $address->shipping_postcode = null;
        $address->shipping_address = $request->street_address;
        $address->save();

        return redirect()->route('admin.user')->with('success', 'User successfully created!');
    }

    public function user_update(Request $request,$id){
        $request->validate([
            'name' => 'required | max:191 | string',
            'password'=> 'nullable | min:6 | confirmed',
        ]);


        $user = User::find($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email_verified_at = now();
        if(!empty($request->password)){
            $user->password = Hash::make($request->password);
        }
  
        $user->ip_address = $request->ip();
        $user->status = $request->status ? 1 : 0;
        $user->avatar = $request->avatar ? $request->avatar : $request->avatar_old;
        $user->save();
        return redirect()->route('admin.user')->with('success', 'User Successfully Updated!');
    }

    public function user_address_update(Request $request){

        $address = Addresses::find($request->address_id);
        $address->shipping_address = $request->street_address;
        $address->shipping_division = $request->division_id;
        $address->shipping_district = $request->district_id;
        $address->shipping_thana = $request->upazila_id;
        $address->shipping_union = $request->union_id;
        $address->save();

        $user = User::find($request->user_id);
        if($user && $request->defualt){
            $user->default_address_id = $address->id;
            $user->save();
        }


        return redirect()->back()->with('success', 'User Address Successfully Updated!');
    }

    public function user_destroy( Request $request,$id){
        $user = User::find($id);

        //Insert Trash Data
        $type = 'customer'; $type_id = $id; $reason = $request->reason ?? ''; $data = $user;
        \Helper::setTrashInfo($type,$type_id,$reason,$data);

        $user->is_deleted = 1;
        $user->save();

        return redirect()->route('admin.user')->with('success', 'User Successfully deleted!');
    }

    public function user_view($id){
        $user = User::find($id);
        $address = Addresses::where('user_id',$id)->get();

        $html = '';

        $html .= '<div class="row">
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                          <p class="content_title">Personal Information</p>
                            <div class="row">
                            <div class="col-md-6">
                              <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Full Name </label>
                                <div class="col-sm-8">
                                   '.$user->name.'
                                </div>
                             </div>
                            </div>

                              <div class="col-md-6">
                                <div class="form-group row">
                                  <label class="col-sm-4 col-form-label"> Email</label>
                                  <div class="col-sm-8">
                                    '.$user->email.'
                                  </div>
                               </div>
                              </div>
            
                              <div class="form-group col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Phone </label>
                                    <div class="col-sm-8">
                                      '.$user->phone.'
                                    </div>
                                </div>
                              </div>
        
                              
                              <div class="col-md-6">
                                <div class="form-group row">
                                  <label class="col-sm-4 col-form-label">Status </label>
                                  <div class="col-sm-8">
                                     <div class="form-check form-check-flat">';
                                        if ($user->status == 1) {
                                          $html .= '<label class="">Active</label>';
                                        }else{
                                           $html .= '<label class="">In active</label>';
                                        }
                                      $html .='
                                     </div>
                                  </div>
                               </div>
                              </div>
        
                              <div class="form-group col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Profile Image</label>
                                    <div class="col-sm-8">';
                                      if($user->avatar){
                                        $html .='<p class="selected_images_gallery">
                                          <img src="/'.$user->avatar.'" style="height:100px; width:100px;"> 
                                        </p>';
                                      }
                                      $html .='
                                    </div>
                                </div>
                              </div>
        
                            </div>
                  </div>
                </div>
              </div>
      </div>

      <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <p class="content_title">Address</p>
                    <div class="row">
                      <div class="designed_table table-responsive">
                        <table id="listTable" class="table">
                            <thead>
                              <tr>
                                  <th>Division</th>
                                  <th>District</th>
                                  <th>Upazila</th>
                                  <th>Union / Area</th>
                                  <th>Address</th>
                              </tr>
                            </thead>
                            <tbody>';

                            foreach ($address as $row){
                              $html .='
                              <tr>
                                  <td >'.$row->division->title.'</td>
                                  <td >'.$row->district->title.'</td>
                                  <td >'.$row->upazila->title .'</td>
                                  <td >'.$row->union->title .'</td>
                                  <td >'.$row->shipping_address.'</td>
                              </tr>';
                            }
                            $html .='
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
      </div>';
      
      return $html ;
    }

    public function user_action(Request $request){
        if (empty($request->select_all)) {
            session()->flash('success', 'You have to select user!');
            return back();
        }

        if($request->action ==  "active"){
            foreach($request->select_all as $id){
                User::where('id', $id)->update(['status' => 1]);
            }
            session()->flash('success', 'User successfully activated !');
            return back();
        }

        if($request->action ==  "inactive"){
            foreach($request->select_all as $id){
              User::where('id', $id)->update(['status' => 0]);
            }
            session()->flash('success', 'User successfully inctivated !');
            return back();
        }

        if($request->action ==  "delete"){
            foreach($request->select_all as $id){
                User::where('id', $id)->update(['is_deleted' => 1]);
                $user = User::find($id);
                //Insert Trash Data
                $type = 'customer'; $type_id = $id; $reason = $request->reason ?? 'Bulk Delete'; $data = $user;
                \Helper::setTrashInfo($type,$type_id,$reason,$data);
            }
            session()->flash('success', 'User successfully deleted !');
            return back();
        }
    }


    public function user_accounts($id){
      if(is_null($this->user) || !$this->user->can('customer.accounts')){
        return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
      }

      $user = User::find($id);

      $carts = Cart::where('user_id', $id)->get();
      $wishlists = Wishlist::where('user_id', $id)->get();

      return view('backend.pages.users.accounts',compact('user','carts', 'wishlists')); 
    }


    /**
     * 
     * Administrator management section for admin panel
     * 
     */

    public function administrator(){

        if(is_null($this->user) || !$this->user->can('admin.view')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $administrators = Admins::orderBy('id','desc')->where('is_deleted',0)->get();
        $administratorsArray = [];
		foreach($administrators as $administrator){
			if(!$administrator->hasRole('seller')){
				$administratorsArray[] = $administrator;
			}
		}
		$administrators = $administratorsArray;

        return view('backend.pages.users.administrator',compact('administrators'));  
    }


    public function administrator_edit($id){

        if(is_null($this->user) || !$this->user->can('admin.edit')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $administrator = Admins::find($id);
        $roles = Role::all();
        return view('backend.pages.users.administrator_edit',compact('administrator','roles'));  
    }

    public function administrator_create(){
        if(is_null($this->user) || !$this->user->can('admin.create')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $roles = Role::all();
        return view('backend.pages.users.administrator_create',compact('roles'));  
    }

    public function administrator_store(Request $request){

        if(is_null($this->user) || !$this->user->can('admin.create')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

       $currentUserLevel = Auth::user()->admin_level;

        $request->validate([
            'name' => 'required | max:191 | string',
            'email'=> 'required | max:50 | email | unique:admins',
            'phone'=> 'required | max:15 | string',
            'roles' => 'required',
            'password' => 'required',
        ],[
            'email.unique' => 'This email has already been taken!'
        ]);
        
        $administrator = new Admins();
        $administrator->name = $request->name;
        $administrator->email = $request->email;
        $administrator->phone = $request->phone;
        // $tempPass = rand(111111,999999);
        $administrator->password = Hash::make($request->password);
        $administrator->status = $request->status ? 1 : 0;
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = round(microtime(true)).'.'.$image->getClientOriginalExtension();
            $location = public_path('uploads/images/administrators/'.$imageName);
            Image::make($image)->save($location);
            $administrator->avatar = $imageName;
        }


        if($request->roles){
            $administrator->assignRole($request->roles);
        }
        
        $administrator->save();
        return redirect()->route('admin.administrator')->with('success', 'Account Successfully Created!');

    }

    public function administrator_update(Request $request,$id){
       
        if(is_null($this->user) || !$this->user->can('admin.edit')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }


        $request->validate([
            'name' => 'required | max:191 | string',
        ],[
            'email.unique' => 'This email has already been taken!'
        ]);

        $administrator = Admins::find($id);
        $administrator->name = $request->name;
        $administrator->avatar = $request->avatar;
        $administrator->status = $request->status ? 1 : 0;

        if($request->roles){
            $administrator->syncRoles($request->roles);
        }

        $administrator->save();
        return redirect()->route('admin.administrator')->with('success', 'Account successfully Updated!');
    }

   public function administrator_destroy(Request $request,$id){

        if(is_null($this->user) || !$this->user->can('admin.delete')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
      
        if(Auth::id() == $id){
            session()->flash('failed', 'You can not delete yourself!');
            return back();
        }
    
        $administrator = Admins::find($id);
        $isDeletable = true;

        foreach($administrator->roles as $role){
            if($role->name == 'superadmin'){
                $isDeletable = false;
            }
        }

        if($isDeletable){
            

            //Insert Trash Data
            $type = 'admin'; $type_id = $id; $reason = $request->reason ?? ''; $data = $administrator;
            \Helper::setTrashInfo($type,$type_id,$reason,$data);

            $administrator->is_deleted = 1;
            $administrator->save();

            return redirect()->route('admin.administrator')->with('success', 'Account successfully deleted!');
        }else{
            return redirect()->route('admin.index')->with('failed', 'You can not delete a superadmin user!');
        }


       
    }


    public function permission(){
        return view('backend.pages.users.permission');
    }
    
    
    public function dashboard(){
        if(is_null($this->user) || !$this->user->can('accounts.view')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        return view('backend.pages.accounts.dashboard');  
    }


    public function accountHistry(Request $request){
        if(isset($request->branch_id) && $request->branch_id > 0){
            $data = Account::where('branch_id', $request->branch_id)->get();
        }else{
            $data = Account::get();
        }
        return DataTables::of($data)->addIndexColumn()
        ->editColumn('date', function ($row) {
            return date('d F, Y', strtotime($row->date));
        })
        ->editColumn('manage_type', function ($row) {
            return str_replace("_"," ",$row->manage_type);
        })

        ->editColumn('branch_id', function ($row) {
            return $row->branch->shopinfo->name ?? '';
        })

        ->editColumn('payment_method', function ($row) {
            return $row->asset->name ?? '';
        })
        ->rawColumns(['date', 'manage_type','branch_id','payment_method'])->make(true);
    }

    public function cashflow(){
        if(is_null($this->user) || !$this->user->can('accounts.view')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->get();
        return view('backend.pages.accounts.cash-flow', compact('branches'));  
    }
    
   
    // public function loanAndInvestorPayment(Request $request){
    //     $payment = new LoanInvestorPayment();
    //     $payment->date = $request->date;
    //     $payment->loan_invest = $request->loan_invest;
    //     $payment->amount = $request->amount;
    //     $payment->payment_method = $request->payment_method;
    //     $payment->loan = $request->loan ?? null;
    //     $payment->investor = $request->investor ?? null;
    //     $payment->save();

    //     Helper::debitAssetBalance($payment->payment_method, $payment->amount);

    //     Helper::accountHistory('', $request->loan_invest , $request->amount, 0, $request->amount, $request->payment_method, $request->date, $payment->id, Helper::getBusinessBalance());

    //     if($request->loan){
    //         $loan = Loan::find($request->loan);
    //         $loan->paid = ($loan->paid + $payment->amount);
    //         $loan->save();
    //     }

    //     if($request->investor){
    //         $investor = Investor::find($request->investor);
    //         $investor->paid = ($loan->paid + $payment->amount);
    //         $investor->save();
    //     }
        
    //     return redirect()->back()->with('success', 'Payment Successfully!');
    // }

    public function customerDue(){
        if(is_null($this->user) || !$this->user->can('accounts.manage.customerdue')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        if (Auth::user()->getRoleNames() == '["seller"]') {
            $accounts = CurrentAsset::where('branch_id', Auth::user()->id)->all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->where('branch_id', Auth::user()->id)->get();
        }else{
            $accounts = CurrentAsset::all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->get();
        }

        $customers = User::where('is_deleted', 0)->get();
        
        return view('backend.pages.accounts.manage-customer-due', compact('accounts', 'branches', 'customers'));  
    }

    public function customerDuePaymentData(Request $request){
        if(isset($request->branch_id) && $request->branch_id > 0){
            $data = Account::where('branch_id', $request->branch_id)->where('manage_type', 'customer_payment');
        }else{
            $data = Account::where('manage_type', 'customer_payment');
        }
        return DataTables::of($data)->addIndexColumn()

        ->editColumn('date', function ($row) {
            return date('d F, Y', strtotime($row->date));
        })

        ->editColumn('branch_id', function ($row) {
            return $row->branch->shopinfo->name ?? '';
        })

        ->editColumn('payment_method', function ($row) {
            return $row->asset->name ?? '';
        })

        ->editColumn('common_id', function ($row) {
            return $row->customer->name ?? '';
        })

        ->rawColumns(['date', 'common_id','branch_id','payment_method'])->make(true);
    }

    public function getCustomerHistory($id){
        $balance = User::find($id)->due_balance;

        $invoice = Order::where('user_id', $id)->get();
        $data['balance'] = $balance;
        $data['invoice'] = $invoice;
        return json_encode($data);
    }

    public function customerDueStore(Request $request){
        Helper::creditAssetBalance($request->payment_method, $request->amount);
        Helper::customerBalanceCredit($request->customer_id, $request->amount);
        Helper::accountHistory($request->branch_id,'','customer_payment' , 0, $request->amount, $request->amount, $request->payment_method, $request->date, $request->customer_id, Helper::getBusinessBalance($request->branch_id), $request->invoice_number);
        return redirect()->back()->with('success', 'Customer payment done!');
    }

    // supplier 

    public function supplierDue(){
        if(is_null($this->user) || !$this->user->can('accounts.manage.paysupplier')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        if (Auth::user()->getRoleNames() == '["seller"]') {
            $accounts = CurrentAsset::where('branch_id', Auth::user()->id)->all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->where('branch_id', Auth::user()->id)->get();
        }else{
            $accounts = CurrentAsset::all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->get();
        }

        $supplier = Supplier::where('is_deleted', 0)->where('is_active', 1)->get();
        
        return view('backend.pages.accounts.manage-supplier-due', compact('accounts', 'branches', 'supplier'));  
    }


    public function getSupplierHistory($id){
        $balance = Supplier::find($id)->balance;

        $invoice = Purchase::where('user_id', $id)->get();
        $data['balance'] = $balance;
        $data['invoice'] = $invoice;
        return json_encode($data);
    }

    public function supplierDueStore(Request $request){
        Helper::debitAssetBalance($request->payment_method, $request->amount);
        Helper::debitSupplierBalance($request->supplier_id, $request->amount);
        Helper::supplierPayments($request->supplier_id, $request->payment_method,$request->amount, $request->invoice_number);
        Helper::accountHistory($request->branch_id,'','supplier_payment' , $request->amount, 0, $request->amount, $request->payment_method, $request->date, $request->supplier_id, Helper::getBusinessBalance(), $request->invoice_number);

        if($request->invoice_number){
            $purchase = Purchase::where('reference_no', $request->invoice_number)->first();
            if($purchase){
                $purchase->paid_amount = ($purchase->paid_amount + $request->amount);
                if (($purchase->paid_amount + $request->amount) >= $purchase->grand_total) {
                    $purchase->payment_status = 1;
                }
                $purchase->save();
            }
        }

        return redirect()->back()->with('success', 'Supplier payment done!');
    }


    public function supplierDuePaymentData(Request $request){
        if(isset($request->branch_id) && $request->branch_id > 0){
            $data = Account::where('branch_id', $request->branch_id)->where('manage_type', 'supplier_payment');
        }else{
            $data = Account::where('manage_type', 'supplier_payment');
        }
        return DataTables::of($data)->addIndexColumn()

        ->editColumn('date', function ($row) {
            return date('d F, Y', strtotime($row->date));
        })

        ->editColumn('branch_id', function ($row) {
            return $row->branch->shopinfo->name ?? '';
        })

        ->editColumn('payment_method', function ($row) {
            return $row->asset->name ?? '';
        })

        ->editColumn('common_id', function ($row) {
            return $row->supplier->name ?? '';
        })

        ->rawColumns(['date', 'common_id','branch_id','payment_method'])->make(true);
    }


    // Balance transfer 
    public function balanceTransfer(){
        if(is_null($this->user) || !$this->user->can('accounts.manage.balancetransfer')){
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        if (Auth::user()->getRoleNames() == '["seller"]') {
            $accounts = CurrentAsset::where('branch_id', Auth::user()->id)->all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->where('branch_id', Auth::user()->id)->get();
        }else{
            $accounts = CurrentAsset::all();
            $branches = Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->get();
        }

        $supplier = Supplier::where('is_deleted', 0)->where('is_active', 1)->get();
        
        return view('backend.pages.accounts.manage-balance-transfer', compact('accounts', 'branches', 'supplier'));  
    }


    public function getBalanceTransferHistory($id){
        $balance = CurrentAsset::find($id)->amount;
        $data['balance'] = $balance;
        return json_encode($data);
    }


    public function balanceTransferStore(Request $request){

        Helper::debitAssetBalance($request->asset_from_id, $request->amount);
        Helper::creditAssetBalance($request->asset_to_id, $request->amount);

        Helper::accountHistory($request->branch_id,'','balance_transfer' , $request->amount, 0, $request->amount, $request->asset_from_id, $request->date, $request->asset_to_id, Helper::getBusinessBalance());

        Helper::accountHistory($request->branch_id,'','balance_transfer' , 0, $request->amount, $request->amount, $request->asset_from_id, $request->date, $request->asset_to_id, Helper::getBusinessBalance());

        return redirect()->back()->with('success', 'Transfer Completed!');
    }


    public function balanceTransferData(Request $request){
        if(isset($request->branch_id) && $request->branch_id > 0){
            $data = Account::where('branch_id', $request->branch_id)->where('manage_type', 'balance_transfer');
        }else{
            $data = Account::where('manage_type', 'balance_transfer');
        }
        return DataTables::of($data)->addIndexColumn()

        ->editColumn('date', function ($row) {
            return date('d F, Y', strtotime($row->date));
        })

        ->editColumn('branch_id', function ($row) {
            return $row->branch->shopinfo->name ?? '';
        })

        ->editColumn('payment_method', function ($row) {
            return $row->asset->name ?? '';
        })

        ->editColumn('common_id', function ($row) {
            return $row->account->name ?? '';
        })

        ->rawColumns(['date', 'common_id','branch_id','payment_method'])->make(true);
    }

}
