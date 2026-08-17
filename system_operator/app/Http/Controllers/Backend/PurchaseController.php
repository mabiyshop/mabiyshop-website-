<?php

namespace App\Http\Controllers\Backend;

use App\Models\Purchase;
use App\Models\ProductPurchase;
use App\Models\ShopInfo;
use App\Models\Admins;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\PurchasesReturn;
use App\Models\ProductMeta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Image;
use Auth;
use Helper;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('supplier.purchase.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $purchases = Purchase::latest()->get();
        return view('backend.pages.purchase.list', compact('purchases'));
    }

    public function getPurchaseList($supplier_id = null)
    {

        if (is_null($this->user) || !$this->user->can('supplier.purchase.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $data = Purchase::with('supplier','shop');

        if (Auth::user()->getRoleNames() == '["seller"]') {
            $data->where('shop_id' , Auth::user()->id);
        }
        
        if ($supplier_id != 0) {
            $data->where('supplier_id', $supplier_id);
        }

        return Datatables::of($data)
            ->addIndexColumn()

            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check form-check-flat">
                        <label class="form-check-label">
                            <input name="select_all[]" type="checkbox"  class="form-check-input checkbox_single_select" value="' . $row->id . '"><i class="input-helper"></i>
                        </label>
                    </div>';
            })

            ->editColumn('created_at', function ($row) {
                return date('d M, Y', strtotime($row->created_at));
            })


            ->editColumn('supplier_id', function ($row) {
                return  optional($row->supplier)->name;
            })

            ->editColumn('shop_id', function ($row) {
                return  optional($row->shop)->name;
            })

            ->editColumn('pending_amount', function ($row) {
                return  $row->grand_total - $row->paid_amount;
            })
           
            ->editColumn('status', function ($row) {
                if ($row->status == 1) {
                    return  '<label class="badge badge-info">Recieved</label>';
                }else if ($row->status == 2) {
                    return  '<label class="badge badge-info">Partial</label>';
                }elseif ($row->status == 3) {
                    return  '<label class="badge badge-info">Pending</label>';
                }else{
                    return  '<label class="badge badge-info">Ordered</label>';
                }
                
            })

            ->editColumn('payment_status', function ($row) {
                if ($row->payment_status == 1) {
                    return  '<label class="badge badge-success text-light">Paid</label>';
                }else{
                    return  '<label class="badge badge-danger text-light">Pending</label>';
                }
                
            })

            ->addColumn('action', function ($row) {
                $btn = '';

                // if (Auth::user()->can('supplier.purchase.edit')) {
                //     $btn = $btn . '<a class="icon_btn text-success" href="' . route('admin.purchase.edit', $row->id) . '"><i class="mdi mdi-pencil-box-outline"></i></a>';
                // }
                
                if(Auth::user()->can('supplier.purchase.payment') && $row->payment_status == 0){
                    $btn .= '<a title="Due Payment" data-id="'.$row->id.'" class="icon_btn purchase_due_payment_btn text-info" href="javascript:void(0)"><i class="mdi mdi-cash"></i></a>';
                }

                if(Auth::user()->can('supplier.purchase.view')){
                    $btn .= '<a title="Quick View" data-id="'.$row->id.'" class="icon_btn purchase_quick_view_btn text-success" href="javascript:void(0)"><i class="mdi mdi-eye"></i></a>';
                }

                if (Auth::user()->can('supplier.purchase.delete')) {
                    $btn = $btn . '<a class="icon_btn text-danger delete_btn" data-url="' . route('admin.purchase.delete', $row->id) . '" data-toggle="modal" data-target="#deleteModal" href="#"><i class="mdi mdi-delete"></i></a>';
                }

                return $btn;
            })

            ->rawColumns(['checkbox', 'status', 'action', 'payment_status','pending_amount'])->make(true);
    }



    public function create()
    {
        if (is_null($this->user) || !$this->user->can('supplier.purchase.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $shops = Admins::where('status',1)->where('is_branch', 1)->get();
        $suppliers = Supplier::where('is_deleted', 0)->where('is_active', 1)->get();
        $products = Product::where('is_deleted', 0)->where('product_qc', 1)->where('is_active', 1)->get();
        return view('backend.pages.purchase.create', compact('shops','suppliers','products'));
    }

    public function productDetails($id){
        $product = Product::find($id);
        return json_encode($product);
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('supplier.purchase.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $request->validate([
            'shop_name' => 'required',
            'supplier_id' => 'required',
            'product_id' => 'required',
        ]);

        $reference_no = uniqid();

        $purchase = new Purchase;
        $purchase->reference_no  =   $reference_no;
        $purchase->user_id = $this->user->id;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->shop_id = $request->shop_name;
        $purchase->item    = $request->item;
        $purchase->total_qty  = $request->total_qty; 
        $purchase->total_discount  = $request->order_discount ?? 0;
        $purchase->total_cost  = $request->shipping_cost ?? 0;
        $purchase->order_discount  = $request->order_discount ?? 0;
        $purchase->grand_total = $request->grand_total;
        $purchase->paid_amount = $request->paid_amount ?? 0;
        $purchase->status  = $request->status;
        $purchase->payment_status  = ($request->status == 1) ? 1 : 0;
        $purchase->payment_method  = $request->payment_method;

        if($request->hasFile('document')){
            $uploadtedFiles = [];
            $allowedfileExtension=['pdf','jpg','JPG','jpeg','png','PNG','docx','doc','csv'];
            $files = $request->file('document');
            
            $filename = $files->getClientOriginalName();
            $extension = $files->getClientOriginalExtension();
            $check = in_array($extension,$allowedfileExtension);
    
           
            $fileName = round(microtime(true)).rand(1111,9999).'.'.$files->getClientOriginalExtension();
            $location = public_path('uploads/purchase/');
            $files->move($location, $fileName);
        
            $purchase->document    = $fileName;
        }
        $purchase->note = $request->note;
        $purchase->save();

        if ($request->payment_method && $request->paid_amount > 0) {
            Helper::debitAssetBalance($request->payment_method, $request->paid_amount);

            Helper::accountHistory($request->shop_name,'', 'Purchase from supplier', $purchase->paid_amount, 0, $purchase->paid_amount, $purchase->payment_method, '', $purchase->id, Helper::getBusinessBalance(), '');

            Helper::supplierPayments($purchase->supplier_id, $request->payment_method,$purchase->paid_amount, $purchase->reference_no);
        }

        $supplier = Supplier::find($purchase->supplier_id);
        $new_balance = 0;
        $new_balance = $request->grand_total - $request->paid_amount;
        $supplier->balance = ($supplier->balance + $new_balance);
        $supplier->save();


        if (isset($request->product_id)) {
            for ($i=0; $i < count($request->product_id); $i++) { 
                $product = Product::find($request->product_id[$i]);

                $product_purchase = new ProductPurchase();
                $product_purchase->purchase_id = $purchase->id;
                $product_purchase->product_id  = $request->product_id[$i];
                $product_purchase->qty = $request->qty[$i];
                $product_purchase->recieved  =   $request->recieved[$i] ?? $request->qty[$i];
                $product_purchase->purchase_unit_id  = $product->weight_unit;  
                $product_purchase->net_unit_cost   = $request->net_unit_cost[$i];
                $product_purchase->discount    = 0;
                $product_purchase->total = ($product->product_cost * $request->qty[$i]);
                $product_purchase->save();

                // if($product->product_type != 'variable'){
                    $product->qty = ($product->qty + $product_purchase->qty);
                    $product->in_stock = 1;
                    $product->product_cost = $request->net_unit_cost[$i];
                // }else{
                //     $variable_options = \DB::table('product_metas')->where('product_id',$product->id)->where('meta_key','custom_options')->first();
                //     $variableOptions = '';
                //     $result = (array) unserialize($variable_options->meta_value);
                    
                //     foreach($result as $key =>$data ){
                        
                //         if($data['value']){
                //             foreach($data['value'] as $k => $v){
                //                 $v['qty'] = ($v['qty'] + $request->qty[$i]);
                //                 // var_dump($v);
                //                 // exit;
                //                 // if($k == 'qty'){
                //                 //     $data['value'][$k] = (array) ($v + $request->qty[$i]);
                //                 // }else{
                //                     $data['value'][$k] = (array) $v ;
                //                 // }
                                
                //             }
                //         }
                //         $result[$key] = (array) $data;
                //     }
                //     $variableOptions = serialize($result);
                //     // var_dump($variableOptions);
                //     if($variableOptions){
                //         ProductMeta::updateOrCreate(
                //             ['product_id' =>  $product->id, 'meta_key' =>  'custom_options'],
                //             [
                //                 'meta_key' =>  'custom_options',
                //                 'meta_value' =>   $variableOptions
                //             ]
                //         );
                //     }

                //     $product->in_stock = 1;
                //     $product->product_cost = $request->net_unit_cost[$i];
                // }
                $product->product_cost = $request->net_unit_cost[$i];
                $product->save();
            }
            
        }
        
        return redirect()->route('admin.purchase')->with('success', 'Purchase successfully created!');
    }

    public function duePayment(Request $request){
        if (is_null($this->user) || !$this->user->can('supplier.purchase.payment')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        } 
        $request->validate([
            'payment_method' => 'required',
            'paid_amount' => 'required',
        ]);

        $purchase = Purchase::find($request->purchase_id);
        $purchase->paid_amount = ($purchase->paid_amount + $request->paid_amount);

        if (($purchase->paid_amount + $request->paid_amount) >= $purchase->grand_total) {
            $purchase->payment_status = 1;
        }

        $purchase->save();


        if ($request->payment_method) {
            Helper::debitAssetBalance($request->payment_method, $request->paid_amount);
            Helper::accountHistory('', 'Purchase from supplier (due payment)', $request->paid_amount, 0, $request->paid_amount, $request->payment_method, '', $purchase->id, Helper::getBusinessBalance());

            Helper::supplierPayments($purchase->supplier_id, $request->payment_method,$request->paid_amount, $purchase->reference_no);
        }

        $supplier = Supplier::find($purchase->supplier_id);
        $new_balance = 0;
        $new_balance = $request->paid_amount;
        $supplier->balance = ($supplier->balance - $new_balance);
        $supplier->save();

        return redirect()->route('admin.purchase')->with('success', 'Purchase payment successfully done!');
    }


    public function getPaymentList($supplier_id = null){

        if ($supplier_id != 0) {
            $data = SupplierPayment::where('amount', '!=', 'null');
        }else{
            $data = SupplierPayment::where('supplier_id', $supplier_id);
        }
        

        return Datatables::of($data)
            ->addIndexColumn()

            ->editColumn('created_at', function ($row) {
                return date('d M, Y', strtotime($row->created_at));
            })

            ->editColumn('supplier_id', function ($row) {
                return  optional($row->supplier)->name;
            })

            ->editColumn('payment_method', function ($row) {
                return  optional($row->payment_method)->name;
            })

            ->rawColumns(['created_at', 'supplier_id', 'payment_method'])->make(true);
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('supplier.purchase.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $purchase = Purchase::findOrFail($id);

        return view('backend.pages.purchase.edit', compact('purchase'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('supplier.purchase.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $purchase = Purchase::find($id);
        $purchase->name = $request->name;
        $purchase->image = $request->image;
        $purchase->company_name = $request->company_name;
        $purchase->phone_number = $request->phone_number;
        $purchase->email = $request->email;
        $purchase->address = $request->address;
        $purchase->is_active = $request->is_active ? 1 : 0;
        $purchase->save();

        return redirect()->route('admin.purchase', $purchase->id)->with('success', 'Purchase successfully updated!');
    }

    public function delete(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('supplier.purchase.delete')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $purchase = Purchase::find($id);

        //Insert Trash Data
        // $type = 'purchase';
        // $type_id = $id;
        // $reason = $request->reason ?? '';
        // $data = $purchase;
        // \Helper::setTrashInfo($type, $type_id, $reason, $data);

        $purchase->is_deleted = 1;
        $purchase->save();

        return redirect()->route('admin.purchase')->with('success', 'Purchase successfully deleted!');
    }

    public function view($id)
    {

        $purchase = Purchase::find($id);

        return view('backend.pages.purchase.view')->with(
            array(
                'purchase' => $purchase,
            )
        );
    }

    public function action(Request $request)
    {

        if (empty($request->select_all)) {
            session()->flash('success', 'You have to select purchase!');
            return back();
        }

        if ($request->action ==  "active") {
            foreach ($request->select_all as $id) {
                Purchase::where('id', $id)->update(['is_active' => 1]);
            }
            session()->flash('success', 'Purchase successfully activated !');
            return back();
        }

        if ($request->action ==  "inactive") {
            foreach ($request->select_all as $id) {
                Purchase::where('id', $id)->update(['is_active' => 0]);
            }
            session()->flash('success', 'Purchase successfully inctivated !');
            return back();
        }

        if ($request->action ==  "delete") {
            foreach ($request->select_all as $id) {
                Purchase::where('id', $id)->update(['is_deleted' => 1]);
                $purchase = Purchase::find($id);
                //Insert Trash Data
                $type = 'purchase';
                $type_id = $id;
                $reason = $request->reason ?? 'Bulk Delete';
                $data = $purchase;
                \Helper::setTrashInfo($type, $type_id, $reason, $data);
            }
            session()->flash('success', 'Purchase successfully deleted !');
            return back();
        }
    }



    // return products 

    public function return()
    {
        $purchases = Purchase::latest()->get();
        return view('backend.pages.purchase.return.list', compact('purchases'));
    }

    public function returncreate()
    {
        $shops = ShopInfo::where('status',1)->get();
        $suppliers = Supplier::where('is_deleted', 0)->where('is_active', 1)->get();
        $products = Product::where('is_deleted', 0)->where('product_qc', 1)->where('is_active', 1)->get();
        return view('backend.pages.purchase.return.create', compact('shops','suppliers','products'));
    }

    public function getInvoices(Request $request){
        $purchase = Purchase::where('supplier_id', $request->supplier_id)->get();
        $html = '';
        foreach ($purchase as $row) {
            $html .= '<option value="'.$row->reference_no.'">'.$row->reference_no.'</option>';
        }

        return $html;
    }

    public function getInvoicesProducts(Request $request){
        $purchase = Purchase::where('reference_no', $request->invoice_id)->first();
        $html = '';
        foreach ($purchase->purchaseProducts as $row) {
            $html .= '<option value="'.$row->product->id.'" data-invoice="'.$purchase->reference_no.'">'.$row->product->barcode .' ('. $row->product->title .')'.'</option>';
        }
        return $html;
    }

    public function getInvoicesProductsDetails(Request $request){
        $purchase = Purchase::where('reference_no', $request->invoice_id)->first();
        $product = ProductPurchase::where('purchase_id', $purchase->id)->where('product_id', $request->product_id)->with('product')->first();
        return json_encode($product);
    }

    public function getPurchaseReturnList($supplier_id = null){

        $data = PurchasesReturn::with('supplier');
        if ($supplier_id != 0) {
            $data->where('supplier_id', $supplier_id);
        }

        return Datatables::of($data)
            ->addIndexColumn()

            ->editColumn('created_at', function ($row) {
                return date('d M, Y', strtotime($row->created_at));
            })


            ->editColumn('supplier_id', function ($row) {
                return  optional($row->supplier)->name;
            })

            ->editColumn('pending_amount', function ($row) {
                return  $row->grand_total - $row->paid_amount;
            })
           
            ->editColumn('status', function ($row) {
                if ($row->status == 1) {
                    return  '<label class="badge badge-warning">Reture Requested</label>';
                }else{
                    return  '<label class="badge badge-danger">Returned</label>';
                }
                
            })

            ->addColumn('action', function ($row) {
                $btn = '';
                
                if(Auth::user()->can('supplier.purchase.payment') && $row->payment_status == 0){
                    $btn .= '<a class="btn-sm btn btn-success mr-1" href="'.route('admin.purchase.return.confirm', $row->id).'" title="Return Confirmed">Return</a>';
                }

                if(Auth::user()->can('supplier.purchase.view')){
                    $btn .= '<a title="Quick View" data-id="'.$row->id.'" class="icon_btn purchase_quick_view_btn text-success" href="javascript:void(0)"><i class="mdi mdi-eye"></i></a>';
                }

                if (Auth::user()->can('supplier.purchase.delete')) {
                    $btn = $btn . '<a class="icon_btn text-danger delete_btn" data-url="' . route('admin.purchase.delete', $row->id) . '" data-toggle="modal" data-target="#deleteModal" href="#"><i class="mdi mdi-delete"></i></a>';
                }

                return $btn;
            })

            ->rawColumns(['status', 'action'])->make(true);
    }

    public function returnstore(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'invoice_no' => 'required',
            'product_id' => 'required',
        ]);

        $reference_no = uniqid();
        $return = new PurchasesReturn;
        $return->reference_no  =   $reference_no;
        $return->invoice_no  =  $request->invoice_no;
        $return->user_id = $this->user->id;
        $return->supplier_id = $request->supplier_id;
        $return->item    = $request->total_item;
        $return->total_qty  = $request->total_qty; 
        $return->total_cost = $request->subtotal;
        $return->grand_total = $request->subtotal;
        $return->paid_amount = 0;
        $return->status  = 1;
        $return->payment_status  = ($request->status == 1) ? 1 : 0;
        $return->payment_method  = 0;
        $return->note = $request->note;
        $return->save();

        if (isset($request->product_id)) {
            for ($i=0; $i < count($request->product_id); $i++) { 
                $product = Product::find($request->product_id[$i]);

                $product_purchase = new ProductPurchase();
                $product_purchase->purchase_id = $return->id;
                $product_purchase->invoice_no = $reference_no;
                $product_purchase->product_id  = $request->product_id[$i];
                $product_purchase->qty = $request->qty[$i];
                $product_purchase->purchase_unit_id  = $product->weight_unit;  
                $product_purchase->net_unit_cost   = $request->net_unit_cost[$i];
                $product_purchase->discount    = 0;
                $product_purchase->total = ($request->net_unit_cost[$i] * $request->qty[$i]);
                $product_purchase->save();

                $product->qty = ($product->qty - $product_purchase->qty);
                $product->save();
            }
        }
        
        return redirect()->route('admin.purchase.return')->with('success', 'Purchase return successfully created!');
    }


    public function returnConfirm($id){
        $return = PurchasesReturn::find($id);
        $previous_invoice = Purchase::where('reference_no', $return->invoice_no)->first();

        if ($previous_invoice->payment_method) {
            Helper::creditAssetBalance($previous_invoice->payment_method, $return->total_cost);

            Helper::accountHistory('', 'Product Return', 0, $return->total_cost, $return->total_cost, $previous_invoice->payment_method, '', $return->id, Helper::getBusinessBalance());

            Helper::supplierPayments($return->supplier_id, $previous_invoice->payment_method,$return->total_cost, $return->invoice_no);

            Helper::debitSupplierBalance($return->supplier_id, $return->total_cost);
        }

        $return->status = 0;
        $return->payment_status = 1;
        $return->payment_method = $previous_invoice->payment_method;
        $return->save();

        return redirect()->route('admin.purchase.return')->with('success', 'Returned successfully done!');
    }


    public function returnview($id){
        $return = PurchasesReturn::find($id);

        return view('backend.pages.purchase.return.view')->with(
            array(
                'purchase' => $return,
            )
        );
    }
}