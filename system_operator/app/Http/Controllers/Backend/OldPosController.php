<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Addresses;
use App\Models\Cart;
use App\Models\ProductMeta;
use App\Models\Admins;
use App\Models\Pickpoints;
use Auth;
use DB;
use Hash;
use Helper;

class PosController extends Controller
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
        if (is_null($this->user) || !$this->user->can('pos.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        $customers = User::orderBy('name', 'asc')->get();
        $products = Product::orderBy('id', 'desc')->where('is_deleted', 0)->with('seller')->limit(20)->paginate(8);

        $data = [
            'customers'  => $customers,
            'products'  => $products,
        ];

        return view('backend.pages.product.pos')->with($data);
    }


    public function customerCreat(Request $request)
    {

        $request->validate([
            'name' => 'required | max:191 | string',
            'email' => 'required | max:50 | email |unique:users',
            'phone' => 'required | max:15 | string |unique:users'
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->email_verified_at = now();
        $tempPass = rand(111111, 999999);
        $user->password = Hash::make($tempPass);
        $user->status = 1;
        $user->save();

        return redirect()->route('admin.pos')->with('success', 'User successfully created!');
    }

    public function customerShippingAddress()
    {
        $html = '';
        $customer_id = $_REQUEST['customer_id'];
        $addresses = Addresses::where('user_id', $customer_id)->orderBy('id', 'desc')->get();
        $pickpoints = Pickpoints::orderBy('id', 'desc')->get();
        foreach ($addresses as $address) {
            $html .= '<option value="' . $address->id . '" >' . $address->division->title . ' -> ' . $address->district->title . ' -> ' . $address->upazila->title . ' -> ' . $address->union->title . ' -> ' . $address->shipping_address . '</option>';
        }

        foreach ($pickpoints as $pickpoint) {
            $html .= '<option class="pickpoint" value="' . $pickpoint->id . '" >' . $pickpoint->division->title . ' -> ' . $pickpoint->district->title . ' -> ' . $pickpoint->upazila->title . ' -> ' . $pickpoint->union->title . ' -> ' . $pickpoint->address . '</option>';
        }




        return $html;
    }

    public function customerShippingAddressAdd(Request $request)
    {

        $request->validate([
            'customer' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'division_id' => 'required',
            'district_id' => 'required',
            'upazila_id' => 'required',
            'union_id' => 'required',
            'street_address' => 'required'
        ]);

        $addresses = new Addresses();
        $addresses->user_id = $request->customer;
        $addresses->shipping_first_name = $request->name;
        $addresses->shipping_address = $request->street_address;
        $addresses->shipping_division = $request->division_id;
        $addresses->shipping_district = $request->district_id;
        $addresses->shipping_thana = $request->upazila_id;
        $addresses->shipping_union = $request->union_id;
        $addresses->shipping_phone = $request->phone;
        $addresses->shipping_email = $request->email;
        $addresses->save();
    }

    // search product 
    public function searchProduct(Request $request)
    {
        $search_text = $request->search_text;
        $html = '';
        $products = DB::table('products')
            ->where('is_active', 1)
            ->where('title', 'like', '%' . $search_text . '%')
            ->OrWhere('products.sku', 'like', '%' . $search_text . '%')
            ->paginate(6);
        if ($products) {
            foreach ($products as $product) {
                $html .= '
                <div class="col-sm-6 col-12 col-lg-4 col-xl-4 mb-0 p-1">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="card-img-actions">
                                <img src="' . '/media/thumbnail/' . $product->default_image . '" class="card-img img-fluid" width="96" height="350" alt="">
                            </div>
                        </div>
                        <div class="card-body bg-light text-center details_card">
                            <div class="details_section">
                                <h6 class="font-weight-semibold mb-0">
                                    <a href="#" class="text-primary mb-0 productViewBtn" data-abc="true" id="' . $product->id . '">' . $product->title . '</a>
                                </h6>
                                <p class="mb-0" >SKU: ' . $product->sku . '</p>
                                <small class="text-danger" >Seller: ' . \DB::table('shop_info')->select('name')->where('seller_id', $product->seller_id)->first()->name . '</small>
                            </div>
                            <p class="mb-2 font-weight-semibold"> BDT ' . Helper::price_after_offer($product->id) . '</p>';
                
                    if ($product->product_type == 'simple') {
                        $html .= '<button type="button" style="padding: 3px 5px;" class="btn btn-primary simple_add_to_cart" data-product-id="' . $product->id . '"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>';
                    } elseif ($product->product_type == 'digital') {
                        $html .= '<button type="button" style="padding: 3px 5px;" class="btn btn-primary digital_add_to_cart" data-product-id="' . $product->id . '"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>';
                    } elseif ($product->product_type == 'variable') {
                        $html .= '<button type="button" style="padding: 3px 5px;" class="btn btn-primary varient_add_to_cart" data-product-id="' . $product->id . '"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>';
                    }
                
                $html .= '</div>
                    </div>
                </div>';
            }

            $html .= '<div class="dynamic_pos_pagination col-12" id="">
                        <div class="dynamic_pagination justify-content-center d-flex mt-4">' . $products->links() . '</div>
                    </div>';
        } else {
            $html .= '<div class="col-sm-12 p-1">
                        <b>No products found!</b>
                      </div>';
        }

        return $html;
    }


    // add to cart for pos 
    public function addToCart(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            if ($request->product_id) {
                $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->first();
                if ($product) {
                    if ($request->qty > $product->max_cart_qty) {
                        $data['status'] = 0;
                        $data['message'] = 'You can not buy more than ' . $product->max_cart_qty . ' items for this product at a time.';
                        return response()->json($data, 200);
                    } else {

                        if ($user) {
                            $already_in_cart = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->first();
                        } else {
                            $already_in_cart = Cart::where('session_key', $session_key)->where('product_id', $request->product_id)->first();
                        }

                        if ($already_in_cart) {
                            //Already Added to cart, we just need to update this product

                            if ($request->qty) {

                                $updatedQTY = $already_in_cart->qty + $request->qty;

                                if ($updatedQTY > $product->max_cart_qty) {
                                    $data['status'] = 0;
                                    $data['message'] = 'You can not buy more than ' . $product->max_cart_qty . ' items for this product at a time.';
                                    return response()->json($data, 200);
                                }

                                $available = \Helper::simple_product_stock($request->product_id, $updatedQTY);
                                if ($available == 1) {
                                    $data['qty'] = $updatedQTY;
                                } else {
                                    $d['status'] = 0;
                                    $d['message'] = 'This product out out of stock.';
                                    return response()->json($d, 200);
                                }
                            } else {
                                $data['qty'] = $already_in_cart->qty;
                            }

                            if ($user) {
                                $update = DB::table('carts')->where('user_id', $user->id)->where('product_id', $request->product_id)->update($data);
                            } else {
                                $update = DB::table('carts')->where('session_key', $session_key)->where('product_id', $request->product_id)->update($data);
                            }

                            if ($update) {
                                $cart['status'] = 1;
                            } else {
                                $cart['status'] = 0;
                                $cart['message'] = 'Something went wrong. Please try again later!';
                            }

                            return response()->json($cart, 200);
                        } else {
                            //We need to add this product to cart
                            $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->first();

                            //Validate Stock for Simple product
                            if ($request->qty) {
                                $available = \Helper::simple_product_stock($request->product_id, $request->qty);
                                if ($available == 1) {
                                    $qty = $request->qty;
                                } else {
                                    $data['status'] = 0;
                                    $data['message'] = 'This product out out of stock.';
                                    return response()->json($data, 200);
                                }
                            } else {
                                $qty = 1;
                            }

                            //Validate Price for Simple product
                            $data['price'] = \Helper::price_after_offer($product->id);
                            $data['discount'] = \Helper::discount_amount_by_IDS($product->id) * $qty;
                            $data['product_id'] = $product->id;
                            $data['seller_id'] = $product->seller_id;
                            $data['product_type'] = $product->product_type;
                            $data['user_id'] =  $user->id ?? null;
                            $data['session_key'] =  $session_key ?? null;
                            $data['qty'] = $qty;
                            $data['row_id'] = uniqid();
                            $insert_id = DB::table('carts')->insertGetId($data);
                            if ($insert_id) {
                                $cart['status'] = 1;
                                return response()->json($cart, 200);
                            } else {
                                $data['status'] = 0;
                                $data['message'] = 'Something went wrong. Please try again later!';
                                return response()->json($data, 200);
                            }
                        }
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = 'Product not found.';
                    return response()->json($data, 200);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = 'Product not found.';
                return response()->json($data, 200);
            }
        } else {
            $d['status'] = 0;
            $d['message'] = 'Failed to add product in your cart. Please try again later!';
            return response()->json($d, 200);
        }
    }

   

    //Digital Product Add to Cart
    public function digitalAddToCart(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            $d['status'] = 0;
            $d['message'] = 'Please select customer first.';
            return response()->json($d, 200);
        }
        if ($request->product_id) {
            $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->first();
            if ($product) {
                if ($request->qty > $product->max_cart_qty) {
                    $data['status'] = 0;
                    $data['message'] = 'You can not buy more than ' . $product->max_cart_qty . ' items for this product at a time.';
                    return response()->json($data, 200);
                } else {
                    $already_in_cart = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->first();
                    if ($already_in_cart) {
                        //Already Added to cart, we just need to update this product
                        if ($request->qty) {
                            $updatedQTY = $already_in_cart->qty + $request->qty;
                            if ($updatedQTY > $product->max_cart_qty) {
                                $data['status'] = 0;
                                $data['message'] = 'You can not buy more than ' . $product->max_cart_qty . ' items for this product at a time.';
                                return response()->json($data, 200);
                            }
                            $available = \Helper::simple_product_stock($request->product_id, $updatedQTY);
                            if ($available == 1) {
                                $data['qty'] = $updatedQTY;
                            } else {
                                $cart['status'] = 0;
                                $cart['message'] = 'This product out out of stock.';
                                return response()->json($cart, 200);
                            }
                        } else {
                            $data['qty'] = $already_in_cart->qty;
                        }


                        $update = DB::table('carts')->where('user_id', $user->id)->where('product_id', $request->product_id)->update($data);

                        if ($update) {
                            $cart['status'] = 1;
                        } else {
                            $cart['status'] = 0;
                            $cart['message'] = 'Something went wrong. Please try again later!';
                        }
                        return response()->json($cart, 200);
                    } else {
                        //We need to add this product to cart
                        $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->first();
                        if ($product) {

                            //Validate Stock for Simple product
                            if ($request->qty) {
                                $available = \Helper::simple_product_stock($request->product_id, $request->qty);
                                if ($available == 1) {
                                    $qty = $request->qty;
                                } else {
                                    $data['status'] = 0;
                                    $data['message'] = 'This product out out of stock.';
                                    return response()->json($data, 200);
                                }
                            } else {
                                $qty = 1;
                            }

                            //Validate Price for Simple product
                            $now_price =  \Helper::price_after_offer($product->id);

                            //Product is available to add to cart
                            $data['price'] = $now_price;
                            $data['discount'] = \Helper::discount_amount_by_IDS($product->id) * $qty;
                            $data['product_id'] = $product->id;
                            $data['seller_id'] = $product->seller_id;
                            $data['product_type'] = $product->product_type;
                            $data['user_id'] =  $user->id;
                            $data['qty'] = $qty;
                            $data['row_id'] = uniqid();
                            $data['variable_options'] = ($user->phone == -1) ? null : $user->phone;

                            $insert_id = DB::table('carts')->insertGetId($data);
                            if ($insert_id) {
                                $cart['status'] = 1;
                            } else {
                                $cart['status'] = 0;
                                $cart['message'] = 'Something went wrong. Please try again later!';
                            }
                            return response()->json($cart, 200);
                        } else {
                            $data['status'] = 0;
                            $data['message'] = 'product not found.';
                            return response()->json($data, 200);
                        }
                    }
                }
            } else {
                $data['status'] = 0;
                $data['message'] = 'Product not found.';
                return response()->json($data, 200);
            }
        } else {
            $data['status'] = 0;
            $data['message'] = 'Product not found.';
            return response()->json($data, 200);
        }

    }

    //Variable Product Add To Cart
    public function variableAddToCart(Request $request)
    {
        $variants = [];
        $shipping_option = '';
        foreach ($request->all() as $key => $value) {
            $variants[$key] =  $value;
        }

        $user = User::find($request->user_id);
        if ($user) {
            $variable_option = $request->all();
            unset($variable_option['product_id']);
            unset($variable_option['qty']);
            unset($variable_option['variable_qty']);
            unset($variable_option['user_id']);
            $variable_option = $variable_option['variable_option'];

            if ($request->product_id) {

                if ($user) {
                    $already_in_cart = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->first();
                }
                $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->first();

                if ($product) {
                    if ($request->qty > $product->max_cart_qty) {
                        $data['status'] = 0;
                        $data['message'] = 'You can not buy more than ' . $product->max_cart_qty . ' items for this product at a time.';
                        return response()->json($data, 200);
                    } else {
                        if ($already_in_cart) {
                            //Already Added to cart, we just need to update this product
                            $qty = $request->qty ? $request->qty : $already_in_cart->qty;
                            $availableOptions = \Helper::variable_product_stock($request->product_id, $qty, $variable_option);

                            if ($availableOptions) {
                                $productPrice = \Helper::price_after_offer($product->id) + $availableOptions['totalAdditional'];
                                $data['price'] = $productPrice;
                                $data['discount'] = \Helper::discount_amount_by_IDS($product->id) * $qty;
                                $data['variable_options'] = json_encode($variable_option);
                                $data['qty'] = $qty;
                            } else {
                                $d['status'] = 0;
                                $d['message'] = 'Quantity not available.';
                                return response()->json($d, 200);
                            }

                            if ($user) {
                                $update = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->update($data);
                            }


                            if ($update) {
                                $cart['status'] = 1;
                            } else {
                                $cart['status'] = 0;
                                $cart['message'] = 'Something went wrong. Please try again later!';
                            }
                            return response()->json($cart, 200);
                        } else {
                            //We need to add this product to cart
                            $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->first();
                            if ($product) {
                                $qty = $request->qty ? $request->qty : 1;
                                $availableOptions = \Helper::variable_product_stock($request->product_id, $qty, $variable_option);

                                if ($availableOptions) {
                                    //Product is available to add to cart
                                    $productPrice = \Helper::price_after_offer($product->id) + $availableOptions['totalAdditional'];
                                    $data['price'] = $productPrice;
                                    $data['discount'] = \Helper::discount_amount_by_IDS($product->id) * $qty;
                                    $data['product_id'] = $product->id;
                                    $data['product_type'] = 'variable';
                                    $data['seller_id'] = $product->seller_id;
                                    $data['user_id'] =   $user->id ?? null;
                                    $data['session_key'] =   $session_key ?? null;
                                    $data['variable_options'] = json_encode($variable_option);
                                    $data['qty'] = $qty;
                                    $data['row_id'] = uniqid();

                                    $insert_id = DB::table('carts')->insertGetId($data);

                                    if ($insert_id) {
                                        $cart['status'] = 1;
                                    } else {
                                        $cart['status'] = 0;
                                        $cart['message'] = 'Something went wrong. Please try again later!';
                                    }
                                    return response()->json($cart, 200);
                                } else {
                                    $data['status'] = 0;
                                    $data['message'] = 'Quantity not available.';
                                    return response()->json($data, 200);
                                }
                            } else {
                                $data['status'] = 0;
                                $data['message'] = 'Product not found.';
                                return response()->json($data, 200);
                            }
                        }
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = 'Product not found.';
                    return response()->json($data, 200);
                }
            } else {
                $data['status'] = 0;
                $data['message'] = 'Product not found.';
                return response()->json($data, 200);
            }
        } else {
            $d['status'] = 0;
            $d['message'] = 'Please select customer first.';
            return response()->json($d, 200);
        }

    }



    // get variable product details 
    public function getVariableProduct($id)
    {
        $html = '';

        $product = Product::where('id', $id)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->first();

        $product_meta = ProductMeta::where('product_id', $product->id)->where('meta_key', 'custom_options')->first();
        $meta_value = unserialize($product_meta->meta_value);

        $html .= '
                <form method="POST" id="variable_product_form">
                    <div class="row">
                        <div class="col-md-5">
                            <img src="' . '/media/thumbnail/' . $product->default_image . '" height="200px" width="190px">
                        </div>
                        <div class="col-md-7">
                            <div class="">
                                <div class="details_section">
                                    <h5 class="font-weight-semibold mb-0">
                                        <a href="#" class="text-primary mb-0 ">' . $product->title . '</a>
                                    </h5>
                                    <p class="mb-0" >SKU: ' . $product->sku . '</p>
                                    <small class="text-danger" >Seller: ' . \DB::table('shop_info')->select('name')->where('seller_id', $product->seller_id)->first()->name . '</small>
                                </div>
                                <p class="mb-2 font-weight-semibold"> BDT ' . Helper::price_after_offer($product->id) . '</p>';
        foreach ($meta_value as $key => $value) {
            if ($value['type'] == 'radio') {
                $html .= '
                                            <div class="">
                                                <h6 class="font-weight-semibold mb-1">
                                                    <a href="#" class="text-primary mb-0 fs-14">' . $value['title'] . ':</a>
                                                </h6>';
                foreach ($value['value'] as $row) {
                    $html .= '
                                                        <label>
                                                            <input type="radio" name="variable_option[' . $value['title'] . ']" value="' . $row['title'] . '" class="variable_option">
                                                            <span>' . $row['title'] . '</span>
                                                        </label>';
                }
                $html .= '
                                            </div>';
            }

            if ($value['type'] == 'dropdown') {
                $html .= '
                                            <div class="">
                                                <h6 class="font-weight-semibold mb-1">
                                                    <a href="#" class="text-primary mb-0 fs-14">' . $value['title'] . ':</a>
                                                </h6>
                                                <select class="form-control variable_option" name="variable_option[' . $value['title'] . ']" id="">
                                                    <option value="">Select</option>';
                foreach ($value['value'] as $row) {
                    $html .= '<option value="' . $row['title'] . '">' . $row['title'] . '</option>';
                }
                $html .= '
                                                </select>
                                            </div>';
            }
        }
        $html .= '
                                <div class="d-flex justify-content-center p-2" style="border-top: 1px solid; border-top: 1px solid #67b120;">
                                    <div class="d-flex">
                                        <button type="button" style="padding: 3px 8px;" class="btn btn-primary w-25 mr-1 variable_minus">-</button>
                                        <input type="text" name="variable_qty" id="variable_qty" value="1" class="w-10 variable_qty" style="width: 50px;border: 1px solid #67b120;border-radius: 5px;text-align: center;" readonly="" >
                                        <button type="button" style="padding: 3px 5px;" class="btn btn-primary w-25 ml-1 variable_plus">+</button>
                                    </div>
                                    <button type="submit" style="padding: 3px 5px;" class="btn btn-primary variable_final_add_to_cart ml-1" data-product-id="' . $product->id . '"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            ';
        return $html;
    }


    
    public function getCart($id)
    {
        $carts = Cart::where('user_id', $id)->orderBy('id', 'desc')->get();
        $html = '';
        if (count($carts) > 0) {

            $html .= '
                    <table class="table shopping-cart-wrap table-responsive">
                        <thead class="text-muted">
                            <tr>
                                <th style="width:50%">Product</th>
                                <th style="width:25%" class="text-center">Qty</th>
                                <th style="width:15%" class="text-center">Price</th>
                                <th style="width:10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>';

            $subtotal = 0;
            $shipping_cost = 0;
            $vat = 0;
            $discount = 0;
            $total = 0;

            foreach ($carts as $cart) {
                $product = Product::find($cart->product_id);

                $subtotal = $subtotal + ($cart->price * $cart->qty);
                $discount = $discount + $cart->discount;
                $vat = $vat + $product->vat;

                $variableOptionHtml = '';

                if ($cart->product_type == 'variable') {
                    $variableOptions = json_decode($cart->variable_options);
                    if ($variableOptions) {
                        foreach ($variableOptions as $key => $val) {
                            $variableOptionHtml .= '<span class="badge badge-primary mr-2">' . $key . ' : ' . $val . '</span>';
                        }
                    }
                }

                $html .= '
                            <tr>
                                <td>
                                    <figure class="media">
                                        <div class="img-wrap">
                                            <img src="' . '/media/thumbnail/' . $product->default_image . '" class="cart_list_image">
                                        </div>
                                        <figcaption class="media-body">
                                            <p class="title mb-0 text-primary mt-1">' . $product->title . '</p>
                                            <small> <b>SKU:</b> ' . $product->sku . ' - <b>Seller:</b> ' . $product->seller->shopinfo->name . '</small>
                                            <p>' . $variableOptionHtml . '</p>
                                        </figcaption>
                                    </figure> 
                                </td>
                                <td class="text-center"> 
                                    <div class="m-btn-group m-btn-group--pill btn-group mr-2" role="group" aria-label="...">
                                        <button type="button" class="m-btn btn btn-default decrement_cart" id="' . $cart->id . '"><i class="mdi mdi-minus"></i></button>
                                            <input type="text" class="qty_btn" value="' . $cart->qty . '">
                                        <button type="button" class="m-btn btn btn-default increment_cart" id="' . $cart->id . '"><i class="mdi mdi-plus"></i></button>
                                    </div>
                                </td>
                                <td> 
                                    <div class="price-wrap"> 
                                        <span class="price">BDT ' . $cart->price . '</span> 
                                    </div> 
                                </td>
                                <td class="text-center"> 
                                    <a href="#" class="text-danger remove_cart_item" id="' . $cart->id . '"> 
                                        <i class="text-danger mdi mdi-delete"></i>
                                    </a>
                                </td>
                            </tr>';
            }
            $html .= '   
                            <tr class="custom_tr">
                                <td colspan="4" class="pr-4"> <textarea class="form-control" name="order_note" placeholder="Order Note.." id="order_note"  rows="2"></textarea> </td>
                            </tr>

                            <tr class="custom_tr">
                                <td class="text-right" colspan="2"><b>Subtotal:</b></td>
                                
                                <td>BDT <span id="subtotal">' . $subtotal . '</span></td>
                            </tr>
                            
                            <tr class="custom_tr">
                                <td class="text-right" colspan="2"><b>Shipping Cost (+):</b> <i class="mdi mdi-pencil-box-outline" id="shipping_cost_btn"></i></td>
                                <td>BDT <span id="shipping_cost"> ' . $shipping_cost . '</span></td>
                            </tr>

                            <tr class="custom_tr">
                                <td class="text-right" colspan="2"><b>VAT/TAX (+):</b> </td>
                                <td>BDT <span id="vat_tax">' . $vat . '</span></td>
                            </tr>

                            <tr class="custom_tr">
                                <td class="text-right" colspan="2"><b>Discount (-):</b> <i class="mdi mdi-pencil-box-outline" id="discount_modal_btn"></i></td>
                                <td>BDT <span id="discount">' . $discount . '</span></td>
                            </tr>
                            
                            <tr class="custom_tr">
                                <td class="text-right" colspan="2"><b>Total:</b></td>
                                <td>BDT <span id="grand_total">' . ($subtotal + $shipping_cost - $discount + $vat) . '</span></td>
                            </tr>

                            <tr class="custom_tr">
                                <td class="text-right" colspan="3"> 
                                    <button class="btn btn-primary" id="order_now_btn">Order Now</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>';
        } else {
            $html .= '<table class="table shopping-cart-wrap table-responsive">
                        <tr class="">
                            <td> 
                                No products on cart!
                            </td>
                        </tr>
                    </table>';
        }
        return $html;
    }


    public function removeCart($id)
    {
        $cart = Cart::find($id);
        if ($cart) {
            $cart->delete();
            $data['status'] = 1;
            $data['message'] = 'Item remove from cart!';
            return response()->json($data, 200);
        } else {
            $data['status'] = 0;
            $data['message'] = 'Cart item not found!';
            return response()->json($data, 200);
        }
    }


    public function updateCart(Request $request)
    {
        $cart = Cart::find($request->id);
        if ($request->action == 'increment') {
            $cart->qty = $cart->qty + 1;
            $cart->save();
            $data['status'] = 1;
        } else {
            $quantity = $cart->qty - 1;
            if ($quantity < 1) {
                $cart->delete();
                $data['status'] = 0;
                $data['message'] = 'Item remove from cart!';
            } else {
                $cart->qty = $quantity;
                $cart->save();
                $data['status'] = 1;
            }
        }
        return response()->json($data, 200);
    }

    public function customerShippingOption(Request $request)
    {
        $carts = Cart::where('user_id', $request->customer_id)->orderBy('id', 'desc')->get();
        $address = Addresses::find($request->address);
        $html = '';
        $html .= '
            <div class="cart-calculation">
                <table width="100%" class="table-responsive">
                    <thead>
                        <tr>
                            <th width="65%">Product Details</th> 
                            <th width="25%"> Price</th> 
                            <th width="10%" class="text-center"> Quantity</th>
                        </tr>
                    </thead> 
                    <tbody>';
        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);

            $variableOptionHtml = '';

            if ($cart->product_type == 'variable') {
                $variableOptions = json_decode($cart->variable_options);
                if ($variableOptions) {
                    foreach ($variableOptions as $key => $val) {
                        $variableOptionHtml .= '<span class="badge badge-primary mr-2"><b>Color</b> ' . $key . ' : ' . $val . '</span>';
                    }
                }
            }

            $availableShippingOptions = Helper::get_shipping_cost($request->customer_id, $cart->seller_id, $cart->product_id, $address->shipping_district);

            $freeshippingHtml = '';
            if ($availableShippingOptions['free_shipping'] == 'on') {
                $freeshippingHtml .= '
                                <label class="labl">
                                    <input type="radio" class="shipping_option_radio" name="shipping_option' . $cart->id . '" value="0" data-id="' . $cart->product_id . '" data-shippingmethod="free_shipping" checked="checked" />
                                    <div class="p-1 m-1">
                                        <small>BDT 0  </small><br>
                                        <small>Free Shipping</small><br>
                                        <small> Est. Arrival: Within 4 to 7 days </small><br>
                                    </div>
                                </label>';
            }

            $html .= '
                            <tr class="">
                                <td>
                                    <div class="d-flex">
                                        <div class="pt-4">
                                            <img src="' . '/media/thumbnail/' . $product->default_image . '" alt="" class="mr-3" height="100px" width="100px"> 
                                        </div>
                                        <div class="">
                                            <h5 class="mt-0">
                                                <a href="#" class="text-primary">' . $product->title . '</a>
                                            </h5> 
                                            <p>' . $variableOptionHtml . '</p>
                                            <div class=" d-flex">';
            $html .= '
                                                ' . $freeshippingHtml . '
                                                <label class="labl">
                                                    <input type="radio" name="shipping_option' . $cart->id . '" class="shipping_option_radio" value="' . $availableShippingOptions['standard_shipping'] . '" data-id="' . $cart->product_id . '" data-shippingmethod="standard_shipping"  checked="checked"/>
                                                    <div class="p-1 m-1">
                                                        <small>BDT ' . $availableShippingOptions['standard_shipping'] . '  </small><br>
                                                        <small>Standerd Shipping</small><br>
                                                        <small> Est. Arrival: Within 4 to 7 days </small><br>
                                                    </div>
                                                </label>
                                                <label class="labl">
                                                    <input type="radio" name="shipping_option' . $cart->id . '" class="shipping_option_radio" value="' . $availableShippingOptions['express_shipping'] . '" data-id="' . $cart->product_id . '" data-shippingmethod="express_shipping" />
                                                    <div class="p-1 m-1">
                                                        <small>BDT ' . $availableShippingOptions['express_shipping'] . '  </small><br>
                                                        <small>Express Shipping</small><br>
                                                        <small> Est. Arrival: Within 1 to 3 days </small><br>
                                                    </div>
                                                </label>';

            $html .= '
                                            </div> 
                                        </div>
                                    </div>
                                </td> 
                                <td>
                                    <div class="table-item">BDT ' . $cart->price . '</div>
                                </td>
                                <td class="text-center">
                                    <div class="table-item">' . $cart->qty . '</div>
                                </td>
                            </tr>';
        }
        $html .= '   
                    </tbody>
                </table>
            </div>';
        return $html;
    }


    public function checkCouponCode(Request $request)
    {
        $coupon_amount = 0;
        if ($request->coupon_code) {
            $couponData = Helper::validateCoupon($request->coupon_code, $request->customer_id);
            if ($couponData['status'] == 1) {
                $coupon_amount = $couponData['amount'];
                $coupon_code = $couponData['code'];
                $data['status'] = 1;
                $data['coupon_amount'] = $couponData['amount'];
                $data['coupon_code'] = $couponData['code'];
            } else {
                $data['status'] = 0;
                $data['message'] = 'Invalid Coupon code!';
            }
        }
        return response()->json($data, 200);
    }

    public function order(Request $request)
    {
        $user = User::find($request->customer);
        if ($user) {

            $shipping_methods = json_decode($request->shipping_method);
            // var_dump($shipping_methods);
            // exit();

            $keyEnabledShipping = [];

            foreach ($shipping_methods as $filteredShipping) {
                array_push($keyEnabledShipping, array(
                    'shippingmethod' => $filteredShipping[0],
                    'product_id' => $filteredShipping[1],
                    'cost' => $filteredShipping[2]
                ));
            }



            $user_id = $user->id;
            $cartData = DB::table('carts')->where('user_id', $user_id)->get();
            if (!$cartData) {
                $data['status'] = 0;
                $data['message'] = 'Product not found in your cart.';
                return response()->json($data, 200);
            }

            $sub_total = 0;
            $discount_amount = 0;
            $shipping_cost = 0;

            foreach ($cartData as $cart) {
                if ($cart->product_type == 'simple' || $cart->product_type == 'digital') {
                    $price = \Helper::price_after_offer($cart->product_id);
                    $qty = \Helper::simple_product_stock($cart->product_id, $cart->qty);
                    if ($qty == 1) {
                        $sub_total += $price * $cart->qty;
                        $discount_amount += $cart->discount * $cart->qty;
                    } else {
                        $data['status'] = 0;
                        $data['message'] = 'Product out of stock.';
                        return response()->json($data, 200);
                    }
                }
                if ($cart->product_type == 'variable') {
                    if ($cart->variable_options) {
                        $availableOptions = \Helper::variable_product_stock($cart->product_id, $cart->qty, json_decode($cart->variable_options));
                        if ($availableOptions) {
                            $price = \Helper::price_after_offer($cart->product_id) + $availableOptions['totalAdditional'];
                            $sub_total += $price * $cart->qty;
                            $discount_amount += $cart->discount * $cart->qty;
                        }
                    }
                }
            }

            //Coupon Validation
            $coupon_amount = 0;
            if ($request->coupon) {
                $couponData = \Helper::validateCoupon($request->coupon, $user->id);
                if ($couponData['status'] == 1) {
                    $coupon_amount = $couponData['amount'];
                    $coupon_code = $couponData['code'];
                }
            }


            $orderData['discount_amount'] = $discount_amount;
            $orderData['paid_amount'] = 0;


            $orderData['coupon_code'] = $coupon_code ?? null;
            $orderData['coupon_amount'] = $coupon_amount ?? 0;

            $orderData['voucher_code'] = $voucher_code ?? null;
            $orderData['voucher_amount'] = $voucher_amount ?? 0;

            $orderData['payment_method'] = $request->payment_method;
            $orderData['status'] = 1;


            $userAddress = Addresses::find($request->address);
            $orderData['user_id'] = $user_id;
            $orderData['address_id'] = $userAddress->id ?? null;
            $orderData['payment_id'] = uniqid();
            $orderData['ip_address'] = request()->ip();
            $orderData['note']    = $request->order_note;

            $order_id = DB::table('orders')->insertGetId($orderData);


            $totalShippingCost = 0;
            //Insert Order details
            foreach ($cartData as $single_item) {
                $product = \Helper::get_product_by_id($single_item->product_id);
                $detailsData['user_id'] = $single_item->user_id;
                $detailsData['order_id'] = $order_id;
                $detailsData['product_id'] = $single_item->product_id;
                $detailsData['product_sku'] = $product->sku ?? null;
                $detailsData['product_qty'] = $single_item->qty;
                $detailsData['price'] = $single_item->price ?? null;
                $detailsData['is_promotion'] = $product->is_promotion;
                $detailsData['loyalty_point'] = $product->loyalty_point ?? 0;


                if ($single_item->product_type != 'digital') {

                    foreach ($keyEnabledShipping as $key => $value) {
                        if ($value['product_id'] == $single_item->product_id) {
                            $keyEnabledShippingMethod = $value['shippingmethod'];
                            $keyEnabledShippingCost = $value['cost'];
                        }
                    }

                    //Shipping Method and Shipping Cost
                    $detailsData['shipping_method'] = $keyEnabledShippingMethod;
                    $detailsData['shipping_cost'] = 0;
                    $availableShippings = \Helper::get_shipping_cost($single_item->user_id, $product->seller_id, $product->id, $userAddress->shipping_district);

                    foreach ($availableShippings as $key => $val) {

                        if ($key == $keyEnabledShippingMethod) {
                            if ($val == 'on') {
                                $val =  0;
                            } else {
                                $val =  $val;
                            }
                            $detailsData['shipping_cost'] = $val * $single_item->qty;
                            $totalShippingCost += $val * $single_item->qty;
                        }
                    }

                    // var_dump($totalShippingCost);
                    // exit();
                }



                $detailsData['product_options'] = $single_item->variable_options;

                $detailsData['seller_id'] = $product->seller_id ?? null;
                $orderData['payment_method'] = $request->payment_method;

                if ($orderDetailsId = DB::table('order_details')->insertGetId($detailsData)) {
                    if ($request->payment_method == 'cash_on_delivery') {
                        \Helper::update_product_quantity($single_item->product_id, $single_item->qty, $single_item->variable_options, 'subtraction');
                    }

                    //Set Seller Commission
                    \Helper::setSellerBalance($orderDetailsId);
                } else {
                    $data['status'] = 0;
                    $data['message'] = 'Something went wrong.';
                    return response()->json($data, 200);
                }
            }

            //Update Shipping and Total
            //$paid_amount = ($sub_total+$totalShippingCost) - ($coupon_amount + $discount_amount + $orderData['voucher_amount']);
            //Remove discout ammount
            $paid_amount = ($sub_total + $totalShippingCost) - ($coupon_amount + $orderData['voucher_amount']);

            DB::table('orders')->where('id', $order_id)->update([
                'shipping_cost' => $totalShippingCost,
                'paid_amount'   => $paid_amount
            ]);

            $invoice = DB::table('order_details')->where('order_id', $order_id)->get();
            foreach ($invoice as $key => $item) {
                $p = \Helper::get_product_by_id($item->product_id);
                $item->image = $p->default_image ?? null;
                $item->title = $p->title ?? null;
                $item->slug  = $p->slug ?? null;
            }
            $in['total'] = $orderData['paid_amount'];
            $in['sub_total'] = $sub_total;
            $in['discount_amount'] = $discount_amount;
            $in['coupon_amount'] = $coupon_amount;
            $in['order_id'] = $order_id;
            $in['products'] = $invoice;
            $in['shipping_cost'] = $shipping_cost;

            $data['status'] = 1;
            $data['message'] = 'Order placed successfully.';
            $data['invoice'] = $in;

            //Send Push notifications
            //Customer
            $message = [
                'order_id' => $order_id,
                'type' => 'order',
                'message' => 'Order placed successfully!',
            ];

            //Seller
            $sellers = DB::table('carts')->where('user_id', $user_id)->get();
            $sellers_id_for_order = array();

            foreach ($sellers as $row) {
                $sellers_id_for_order[$row->seller_id] = $row->seller_id; // Get unique seller by id.
            }

            foreach ($sellers_id_for_order as $key => $val) {
                $seller = Admins::find($val);
                // \Helper::sendPushNotification($val,2,'Order Placed','Order placed successfully!',json_encode($message),null,null);
                \Helper::sendSms($seller->phone, 'একটি নতুন অর্ডার সফলভাবে স্থাপন করা হয়েছে! অর্ডার আইডি হল  #' . $order_id);
            }

            // Customer
            \Helper::sendSms($user->phone, 'অর্ডার সফলভাবে স্থাপন করা হয়েছে! আপনার অর্ডারের জন্য আপনাকে ধন্যবাদ. আপনার অর্ডার আইডি #' . $order_id);

            //Delete Cart Data
            DB::table('carts')->where('user_id', $user_id)->delete();
            return response()->json($data, 200);
        } else {
            $data['status'] = 0;
            $data['message'] = 'You have to login first to avail this option.';
            return response()->json($data, 200);
        }
    }


}