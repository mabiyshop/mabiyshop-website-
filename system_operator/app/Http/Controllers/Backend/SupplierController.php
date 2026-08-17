<?php

namespace App\Http\Controllers\Backend;

use App\Models\Supplier;
use App\Models\ProductPurchase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Image;
use Auth;

class SupplierController extends Controller
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
        if (is_null($this->user) || !$this->user->can('supplier.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $suppliers = Supplier::latest()->get();
        return view('backend.pages.supplier.list', compact('suppliers'));
    }




    public function getSupplierList()
    {

        if (is_null($this->user) || !$this->user->can('supplier.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $data = Supplier::where('is_deleted', 0);

        return Datatables::of($data)
            ->addIndexColumn()

            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check form-check-flat">
                        <label class="form-check-label">
                            <input name="select_all[]" type="checkbox"  class="form-check-input checkbox_single_select" value="' . $row->id . '"><i class="input-helper"></i>
                        </label>
                    </div>';
            })

           

            ->editColumn('is_active', function ($row) {
                return  '<label class="badge badge_' . strtolower(\Helper::getStatusName('default', $row->is_active)) . '">' . \Helper::getStatusName('default', $row->is_active) . '</label>';
            })

            
            ->editColumn('name', function ($row) {

                if ($row->image) {
                    $image = '<img class="list_img mr-3" src="' . '/' . $row->image . '" alt="">';
                } else {
                    $image = '<img class="list_img mr-3" src="/no_image.png" alt="">';
                }

                return  '<div class="media">
                            ' . $image . '
                            <div class="media-body">
                                <p class="product_title"><a class="text-dark text-decoration-none">' . $row->name . '</a></p>
                                <span>'.$row->company_name.'</span>
                            </div>
                        </div>';
            })

            ->addColumn('action', function ($row) {
                $btn = '';

                if (Auth::user()->can('supplier.edit')) {
                    $btn = $btn . '<a class="icon_btn text-success" href="' . route('admin.supplier.edit', $row->id) . '"><i class="mdi mdi-pencil-box-outline"></i></a>';
                }

                if (Auth::user()->can('supplier.delete')) {
                    $btn = $btn . '<a class="icon_btn text-danger delete_btn" data-url="' . route('admin.supplier.delete', $row->id) . '" data-toggle="modal" data-target="#deleteModal" href="#"><i class="mdi mdi-delete"></i></a>';
                }

                if (Auth::user()->can('supplier.purchase.ledger')) {
                    $btn = $btn . '<a class="btn-sm btn btn-success" target="_blank" href="' . route('admin.supplier.view', $row->id) . '" title="Supplier Ledger">Ledger</a>';
                }

                if (Auth::user()->can('supplier.purchase.products.ledger')) {
                    $btn = $btn . '<a class="btn-sm btn btn-info ml-1" target="_blank" href="' . route('admin.supplier.product.ledger', $row->id) . '" title="Supplier Product Ledger">Product Ledger</a>';
                }

                return $btn;
            })

            ->rawColumns(['checkbox', 'name','is_active', 'action'])->make(true);
    }



    public function create()
    {
        if (is_null($this->user) || !$this->user->can('supplier.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        return view('backend.pages.supplier.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('supplier.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $supplier = new Supplier;
        $supplier->name = $request->name;
        $supplier->image = $request->image;
        $supplier->company_name = $request->company_name;
        $supplier->phone_number = $request->phone_number;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->is_active = $request->is_active ? 1 : 0;
        $supplier->opening_balance = $request->opening_balance;
        $supplier->balance = $request->opening_balance;
        $supplier->save();


        return redirect()->route('admin.supplier')->with('success', 'Supplier successfully created!');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('supplier.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $supplier = Supplier::findOrFail($id);

        return view('backend.pages.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('supplier.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $supplier = Supplier::find($id);
        $supplier->name = $request->name;
        $supplier->image = $request->image;
        $supplier->company_name = $request->company_name;
        $supplier->phone_number = $request->phone_number;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->is_active = $request->is_active ? 1 : 0;
        $supplier->save();

        return redirect()->route('admin.supplier', $supplier->id)->with('success', 'Supplier successfully updated!');
    }

    public function delete(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('supplier.delete')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $supplier = Supplier::find($id);

        //Insert Trash Data
        // $type = 'supplier';
        // $type_id = $id;
        // $reason = $request->reason ?? '';
        // $data = $supplier;
        // \Helper::setTrashInfo($type, $type_id, $reason, $data);

        $supplier->is_deleted = 1;
        $supplier->save();

        return redirect()->route('admin.supplier')->with('success', 'Supplier successfully deleted!');
    }

    public function view($id)
    {
        $supplier = Supplier::find($id);

        return view('backend.pages.supplier.view')->with(
            array(
                'supplier' => $supplier,
            )
        );
    }


    public function viewProducts($id)
    {
        $supplier = Supplier::find($id);
        $products = ProductPurchase::leftJoin('purchases', 'purchases.id', '=', 'product_purchases.purchase_id')
                    ->where('purchases.supplier_id', $id)
                    ->select('product_purchases.*')
                    ->groupBy('product_purchases.product_id')
                    ->get();

        return view('backend.pages.supplier.product-ledger')->with(
            array(
                'supplier' => $supplier,
                'products' => $products,
            )
        );
    }

    public function action(Request $request)
    {

        if (empty($request->select_all)) {
            session()->flash('success', 'You have to select supplier!');
            return back();
        }

        if ($request->action ==  "active") {
            foreach ($request->select_all as $id) {
                Supplier::where('id', $id)->update(['is_active' => 1]);
            }
            session()->flash('success', 'Supplier successfully activated !');
            return back();
        }

        if ($request->action ==  "inactive") {
            foreach ($request->select_all as $id) {
                Supplier::where('id', $id)->update(['is_active' => 0]);
            }
            session()->flash('success', 'Supplier successfully inctivated !');
            return back();
        }

        if ($request->action ==  "delete") {
            foreach ($request->select_all as $id) {
                Supplier::where('id', $id)->update(['is_deleted' => 1]);
                $supplier = Supplier::find($id);
                //Insert Trash Data
                $type = 'supplier';
                $type_id = $id;
                $reason = $request->reason ?? 'Bulk Delete';
                $data = $supplier;
                \Helper::setTrashInfo($type, $type_id, $reason, $data);
            }
            session()->flash('success', 'Supplier successfully deleted !');
            return back();
        }
    }
}