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
use App\Models\ShopInfo;
use App\Models\Order;
use App\Models\CollectedVoucher;
use App\Models\District;
use App\Models\Upazila;
use App\Models\Union;
use App\Models\CurrentAsset;
use App\Services\AddressLocationResolver;
use App\Support\BangladeshMobile;

use Carbon\Carbon;
use Auth;
use DB;
use Hash;
use Helper;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

			if (is_null($this->user) || !$this->user->can('pos.view')) {
				abort(403, 'You do not have permission to access POS.');
			}

            return $next($request);
        });
    }


    public function index()
    {
        if (is_null($this->user) || !$this->user->can('pos.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        
        
		$products = Product::orderBy('id', 'desc')
			->where('is_active', 1)
			->where('product_qc', 1)
			->where('is_deleted', 0)
			->with('seller.shopinfo')
			->simplePaginate(8);

        $pickpoint_address = Pickpoints::where('status', 1)->with('division', 'district', 'upazila', 'union')->get();

        if (Auth::user()->getRoleNames() == '["seller"]') {
            $asset = CurrentAsset::where('status', 0)->where('branch_id', Auth::user()->id)->get();
            $mfs_and_bank = CurrentAsset::where('status', 0)->where('branch_id', Auth::user()->id)->Where(function($query) {
                $query->where('type', 'mfs')
                      ->orwhere('type', 'bank');
            })->get();
        }else{
            $asset = CurrentAsset::where('status', 0)->get();
            $mfs_and_bank = CurrentAsset::where('status', 0)->Where(function($query) {
                $query->where('type', 'mfs')
                      ->orwhere('type', 'bank');
            })->get();
        }
		
		$topSales = DB::table('order_details')
			->select(
				'product_id',
				DB::raw('MAX(seller_id) as seller_id'),
				DB::raw('SUM(product_qty) as sold_quantity')
			)
			->groupBy('product_id');
		$top_selling_products = DB::query()
			->fromSub($topSales, 'top_sales')
			->join('admins', 'top_sales.seller_id', '=', 'admins.id')
			->join('products', 'top_sales.product_id', '=', 'products.id')
			->select('products.*', 'admins.name')
			->orderByDesc('top_sales.sold_quantity')
			->simplePaginate(9);

		foreach ($products as $product) {
			$product->pos_price = $this->getPosDisplayPrice($product);
		}
		foreach ($top_selling_products as $product) {
			$product->pos_price = $this->getPosDisplayPrice($product);
		}

        $data = [
            // 'customers'  => $customers,
            'products'  => $products,
            'top_selling_products'  => $top_selling_products,
            'pickpoint_address'  => $pickpoint_address,
            'mfs_and_bank'  => $mfs_and_bank,
        ];

        return view('backend.pages.product.pos')->with($data);
    }

	private function getPosDisplayPrice($product)
	{
		$now = Carbon::now();
		$starts = $product->special_price_start ? Carbon::parse($product->special_price_start) : null;
		$ends = $product->special_price_end ? Carbon::parse($product->special_price_end) : null;
		if ($starts && $ends && $starts <= $now && $ends >= $now && $product->special_price > 0) {
			return (int) $product->special_price_type === 1
				? $product->price - $product->special_price
				: $product->price - ($product->price * ($product->special_price / 100));
		}

		return $product->price;
	}

    public function getCustomers(Request $request){
		$search = trim((string) $request->search);
		$customers = User::select('id', 'name', 'phone')
			->when($search !== '', function ($query) use ($search) {
				$query->where(function ($query) use ($search) {
					$query->where('name', 'like', '%' . $search . '%')
						->orWhere('phone', 'like', '%' . $search . '%');
				});
			})
			->when($search === '' && $request->current_user, function ($query) use ($request) {
				$query->where('id', $request->current_user);
			})
			->orderBy('name', 'asc')
			->limit(50)
			->get();
        $html = '';
        foreach($customers as $customer){
            // $isSelected = ($customer->id == $request->current_user) ? "selected" : "";
            $isSelected = "";
            $html .= '<option value="'.$customer->id.'" ' . $isSelected . '>'.$customer->name.' - '.$customer->phone.'</option>';
        }
        return $html;
    }


    public function customerCreat(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required | max:191 | string',
            'email' => 'nullable | unique:users',
            'phone' => 'required | max:15 | string | unique:users'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'Error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->email_verified_at = now();
        $tempPass = rand(111111, 999999);
        $user->password = Hash::make($tempPass);
        $user->status = 1;
        $user->save();

        return response()->json([
            'type' => 'success',
            'message' => 'User successfully created!',
            'user_id' => $user->id,
        ]);

        // session(['added_user' => $user->id]);
        // return redirect()->route('admin.pos')->with('success', 'User successfully created!');
    }

    public function customerShippingAddress()
    {
        $html = '';
        $customer_id = $_REQUEST['customer_id'];
		$addresses = Addresses::where('user_id', $customer_id)
			->where('is_deleted', 0)
			->with('district', 'upazila', 'union')
			->orderBy('id', 'desc')
			->get();
        foreach ($addresses as $address) {
			$location = array_filter([
				optional($address->upazila)->title,
				optional($address->district)->title,
				optional($address->union)->title,
			]);
			$html .= '<option data-address-type="user_address" value="'.$address->id.'">'
				. implode(', ', $location) . ' — ' . $address->shipping_address . '</option>';
        }

        // foreach ($pickpoints as $pickpoint) {
        //     $html .= '<option data-address-type="pickpoint" class="pickpoint" value="' . $pickpoint->id . '" >' . $pickpoint->division->title . ' -> ' . $pickpoint->district->title . ' -> ' . $pickpoint->upazila->title . ' -> ' . $pickpoint->union->title . ' -> ' . $pickpoint->address . '</option>';
        // }

        return $html;
    }

    public function customerShippingAddressAdd(Request $request, AddressLocationResolver $resolver)
    {
		$request->merge([
			'phone' => BangladeshMobile::normalize($request->input('phone')),
		]);

        $request->validate([
            'customer' => 'required',
            'name' => 'required',
            'phone' => BangladeshMobile::validationRules(),
            'district_id' => 'required',
            'upazila_id' => 'required',
            // 'union_id' => 'required',
            'street_address' => 'required'
        ], [
			'phone.required' => 'Phone field is required.',
			'phone.regex' => 'Enter a valid 11-digit Bangladesh mobile number starting with 01.',
		]);

		$district = District::find($request->district_id);
		$upazila = Upazila::find($request->upazila_id);
		$union = $request->union_id ? Union::find($request->union_id) : null;
		$isLegacyDetailed = $request->filled('division_id') || $request->filled('union_id');
		$resolution = $resolver->resolve((string) $request->street_address);
		$resolvedPairIsValid = collect($resolution['candidates'] ?? [])->contains(function ($candidate) use ($district, $upazila) {
			return $district && $upazila
				&& (int) $candidate['district_id'] === (int) $district->id
				&& (int) $candidate['upazila_id'] === (int) $upazila->id;
		});
		$manualFallbackAllowed = $isLegacyDetailed
			|| in_array($resolution['match_type'] ?? 'none', ['none', 'district_only', 'ambiguous'], true);

		if (
			!$district
			|| !$upazila
			|| (int) $upazila->district_id !== (int) $district->id
			|| ($request->filled('division_id') && (int) $request->division_id !== (int) $district->division_id)
			|| !is_numeric($district->city_id) || (int) $district->city_id <= 0
			|| !is_numeric($upazila->zone_id) || (int) $upazila->zone_id <= 0
			|| (!$resolvedPairIsValid && !$manualFallbackAllowed)
			|| ($request->union_id && (!$union || (int) $union->upazila_id !== (int) $upazila->id))
		) {
			return response()->json([
				'message' => 'The selected delivery location is invalid.',
				'errors' => ['location' => ['The selected delivery location is invalid.']],
			], 422);
		}

        $addresses = new Addresses();
        $addresses->user_id = $request->customer;
        $addresses->shipping_first_name = $request->name;
        $addresses->shipping_address = $request->street_address;
        $addresses->shipping_division = $district->division_id;
		$addresses->shipping_district = $district->id;
		$addresses->shipping_thana = $upazila->id;
		$addresses->shipping_union = $union->id ?? null;
        $addresses->shipping_phone = $request->phone;
        $addresses->shipping_email = $request->email;
        $addresses->save();

        if($addresses){
            $user = User::find($addresses->user_id);
            if ($user) {
                $user->default_address_id = $addresses->id;
                $user->save();
            }
        }

		return response()->json([
			'status' => 1,
			'message' => 'Shipping address added successfully.',
			'address_id' => $addresses->id,
		]);
    }

    // search product 
    public function searchProduct(Request $request)
    {
        $search_text = $request->search_text;
        $html = '';
		$products = Product::with('shopinfo')
            ->where('is_active', 1)
            ->where('product_qc',1)
            ->where('is_deleted', 0)
			->where(function ($query) use ($search_text) {
				$query->where('title', 'like', '%' . $search_text . '%')
					->orWhere('sku', 'like', '%' . $search_text . '%');
			})
			->simplePaginate(6);
        if ($products) {
            foreach ($products as $product) {
                $html .= '
                <div class="col-sm-6 col-12 col-lg-4 col-xl-4 mb-0 p-1">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="card-img-actions">
								<img src="' . '/media/thumbnail/' . $product->default_image . '" class="card-img img-fluid" width="96" height="350" loading="lazy" alt="">
                            </div>
                        </div>
                        <div class="card-body bg-light text-center details_card">
                            <div class="details_section">
                                <h6 class="font-weight-semibold mb-0">
                                    <a href="#" class="text-primary mb-0 productViewBtn" data-abc="true" id="' . $product->id . '">' . $product->title . '</a>
                                </h6>';
                                if ($product->product_type != 'variable') {
                                    $html .='<p class="mb-0" >SKU: ' . $product->sku . '</p>';
                                }
                                $html .='
								<small class="text-danger" >Seller: ' . optional($product->shopinfo)->name . '</small>
                            </div>
							<p class="mb-2 font-weight-semibold"> BDT ' . $this->getPosDisplayPrice($product) . '</p>';
                
                    if ($product->product_type == 'simple') {
                        $html .= '<button type="button" style="padding: 3px 5px;" class="btn btn-primary simple_add_to_cart" data-product-id="' . $product->id . '"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>';
                    } elseif ($product->product_type == 'digital' || $product->product_type == 'service') {
                        $html .= '<button type="button" style="padding: 3px 5px;" class="btn btn-primary digital_add_to_cart" data-product-type="'.$product->product_type.'" data-product-id="' . $product->id . '"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>';
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
                $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->first();
                if ($product) {

                    //Conditional Cart Validation Starts
                    if ($user) {
                        $cartSum  = Cart::where('user_id', $user->id)->sum('price');
                    } else {
                        $cartSum = Cart::where('session_key', $session_key)->sum('price');
                    }

                    if ($cartSum < $product->minimum_cart_value) {
                        $data['status'] = 0;
                        $data['message'] = 'You can buy this item when you have a minimum amount of BDT ' . $product->minimum_cart_value . ' in your cart.';
                        return response()->json($data, 200);
                    }

                    //Conditional Cart Validation Ends



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
                            $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->first();

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
                            $data['base_price'] = $product->price ?? 0.00;
                            $data['price'] = \Helper::price_after_offer($product->id);
                            // $data['discount'] = \Helper::discount_amount_by_IDS($product->id) * $qty;
                            $data['discount'] = 0;
                            $data['product_id'] = $product->id;
                            $data['seller_id'] = $product->seller_id;
                            $data['product_type'] = $product->product_type;

                            $data['packaging_cost'] = $product->packaging_cost ?? 0;
                            $data['security_charge'] = $product->security_charge ?? 0;

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
            $d['message'] = 'Please select customer first.';
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
            $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->first();
            if ($product) {


                //Conditional Cart Validation Starts
                if ($user) {
                    $cartSum  = Cart::where('user_id', $user->id)->sum('price');
                } else {
                    $cartSum = Cart::where('session_key', $session_key)->sum('price');
                }

                if ($cartSum < $product->minimum_cart_value) {
                    $data['status'] = 0;
                    $data['message'] = 'You can buy this item when you have a minimum amount of BDT ' . $product->minimum_cart_value . ' in your cart.';
                    return response()->json($data, 200);
                }

                //Conditional Cart Validation Ends


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
                        $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->first();
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
                            $data['base_price'] = $product->price ?? 0.00;
                            $data['price'] = $now_price;
                            // $data['discount'] = \Helper::discount_amount_by_IDS($product->id) * $qty;
                            $data['discount'] = 0;
                            $data['product_id'] = $product->id;
                            $data['seller_id'] = $product->seller_id;
                            $data['product_type'] = $product->product_type;
                            $data['user_id'] =  $user->id;
                            $data['qty'] = $qty;
                            $data['row_id'] = uniqid();

                            if($request->service_date){
                                $serviceOption = [
                                    'service_date' => $request->service_date,
                                    'service_time' => $request->service_time,
                                ];

                                $data['variable_options'] = json_encode($serviceOption);
                            }else{
                                $data['variable_options'] = ($request->phone_number == -1) ? null : $request->phone_number;
                            }

                            

                            $data['packaging_cost'] = $product->packaging_cost ?? 0;
                            $data['security_charge'] = $product->security_charge ?? 0;

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
            if ($key == 'shipping_option') {
                $shipping_option =  $value;
            } else {
                $variants[$key] =  $value;
            }
        }

        $session_key = $request->session_key;
        $user = $user = User::find($request->user_id);
        if ($user) {
            $variable_option = $request->all();
            unset($variable_option['product_id']);
            unset($variable_option['qty']);
            unset($variable_option['shipping_option']);
            unset($variable_option['session_key']);
            unset($variable_option['variable_sku']);
            $variable_option = $variable_option['variable_option'];

            if ($request->product_id) {

                if ($user) {
                    $already_in_cart = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->where('variable_options', json_encode($variable_option))->first();
                } else {
                    $already_in_cart = Cart::where('session_key', $session_key)->where('product_id', $request->product_id)->where('variable_options', json_encode($variable_option))->first();
                }

                $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->first();

                if ($product) {


                    //Conditional Cart Validation Starts
                    if ($user) {
                        $cartSum  = Cart::where('user_id', $user->id)->sum('price');
                    } else {
                        $cartSum = Cart::where('session_key', $session_key)->sum('price');
                    }

                    if ($cartSum < $product->minimum_cart_value) {
                        $data['status'] = 0;
                        $data['message'] = 'You can buy this item when you have a minimum amount of BDT ' . $product->minimum_cart_value . ' in your cart.';
                        return response()->json($data, 200);
                    }

                    //Conditional Cart Validation Ends



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
                                // $data['price'] = $productPrice;
                                // $data['discount'] = \Helper::discount_amount_by_IDS($product->id) * $qty;
                                $data['discount'] = 0;
                                // $data['variable_options'] = json_encode($variable_option);
                                $data['qty'] = $qty;
                                // $data['variable_sku'] = $request->variable_sku;
                            } else {
                                $d['status'] = 0;
                                $d['message'] = 'Quantity not available.';
                                return response()->json($d, 200);
                            }

                            if ($user) {
                                $update = Cart::where('user_id', $user->id)->where('product_id', $request->product_id)->update($data);
                            } else {
                                $update = Cart::where('session_key', $session_key)->where('product_id', $request->product_id)->update($data);
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
                            $product = Product::where('id', $request->product_id)->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->first();
                            if ($product) {
                                $qty = $request->qty ? $request->qty : 1;
                                $availableOptions = \Helper::variable_product_stock($request->product_id, $qty, $variable_option);

                                if ($availableOptions) {
                                    
                                    //Product is available to add to cart
                                    $data['base_price'] = ($availableOptions['totalAdditional'] == 0) ? $product->price : ($product->price + $availableOptions['totalAdditional']);

                                    // $productPrice = \Helper::price_after_offer($product->id) + \Helper::getDiscountByAditionalPrice($product->special_price_type, $product->special_price, $availableOptions['totalAdditional']);
                                    $productPrice = \Helper::price_after_offer($product->id) + $availableOptions['totalAdditional'];
                                    
                                    $data['price'] = $productPrice;
                                    // $data['discount'] = \Helper::discount_amount_by_IDS($product->id) * $qty;
                                    $data['discount'] = 0;
                                    $data['product_id'] = $product->id;
                                    $data['product_type'] = 'variable';
                                    $data['seller_id'] = $product->seller_id;
                                    $data['user_id'] =   $user->id ?? null;
                                    $data['session_key'] =   $session_key ?? null;
                                    $data['variable_options'] = json_encode($variable_option);
                                    $data['qty'] = $qty;
                                    $data['row_id'] = uniqid();
                                    $data['variable_sku'] = $request->variable_sku;

                                    $data['packaging_cost'] = $product->packaging_cost ?? 0;
                                    $data['security_charge'] = $product->security_charge ?? 0;

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
                                    <p class="mb-0" >SKU: <span class="variable_generate_sku"></span></p>
                                    <small class="text-danger" >Seller: ' . \DB::table('shop_info')->select('name')->where('seller_id', $product->seller_id)->first()->name . '</small>
                                </div>
                                <p class="mb-2 font-weight-semibold"> BDT ' . Helper::price_after_offer($product->id) . '</p>';
                                    if ($product_meta) {
                                        $meta_value = unserialize($product_meta->meta_value);
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
                                                                <input type="radio" data-variable-sku="'.$row['sku'].'" name="variable_option[' . $value['title'] . ']" value="' . $row['title'] . '" class="variable_option variable_option_radio">
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
                                                    <select class="form-control variable_option variable_option_select" name="variable_option[' . $value['title'] . ']" id="">
                                                        <option value="">Select</option>';
                                                        foreach ($value['value'] as $row) {
                                                            $html .= '<option data-variable-sku="'.$row['sku'].'" value="' . $row['title'] . '">' . $row['title'] . '</option>';
                                                        }
                                                        $html .= '
                                                    </select>
                                                </div>';
                                            }
                                        }
                                    }else{
                                        $html .= 'No variants available';
                                    }
                                    $html .= '
                                <div class="d-flex justify-content-center p-2" style="border-top: 1px solid; border-top: 1px solid #67b120;">
                                    <div class="d-flex">
                                        <button type="button" style="padding: 3px 8px;" class="btn btn-primary w-25 mr-1 variable_minus">-</button>
                                        <input type="text" data-cart-limit="'.$product->max_cart_qty.'" name="variable_qty" id="variable_qty" value="1" class="w-10 variable_qty" style="width: 50px;border: 1px solid #67b120;border-radius: 5px;text-align: center;" readonly="" >
                                        <button type="button" style="padding: 3px 5px;" class="btn btn-primary w-25 ml-1 variable_plus">+</button>
                                    </div>';
                                    if ($product_meta) {
                                        $html .= '
                                        <button type="submit" style="padding: 3px 5px;" class="btn btn-primary variable_final_add_to_cart ml-1" data-product-id="' . $product->id . '"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>';
                                    }
                                    $html .='
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
		$carts = Cart::where('user_id', $id)->with('product.seller.shopinfo')->orderBy('id', 'desc')->get();
        if (Auth::user()->getRoleNames() == '["seller"]') {
            $asset = CurrentAsset::where('status', 0)->where('branch_id', Auth::user()->id)->get();
            $mfs_and_bank = CurrentAsset::where('status', 0)->where('branch_id', Auth::user()->id)->where('type', 'mfs')->get();
        }else{
            $asset = CurrentAsset::where('status', 0)->get();
            $mfs_and_bank = CurrentAsset::where('status', 0)->where('type', 'mfs')->get();
        }
        $html = '';
        $subtotal = 0;
        $shipping_cost = 0;
        $vat = 0;
        $discount = 0;
        $total = 0;
        $balance = User::find($id)->balance ?? 0;
        if (count($carts) > 0) {
            $html .= '
                    <table class="table shopping-cart-wrap table-responsive">
                        <thead class="text-muted">
                            <tr>
                                <th style="width:60%">Product</th>
                                <th style="width:15%" class="text-center">Price</th>
                                <th style="width:15%" class="text-center">Subtotal</th>
                                <th style="width:10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>';

            
            foreach ($carts as $cart) {
                $product = $cart->product;
                if (!$product) {
                    continue;
                }

                $subtotal = $subtotal + (($cart->price) * $cart->qty);
                $discount = $discount + $cart->discount;
                $vat = $vat + $cart->vat + $product->vat ;

                $variableOptionHtml = '';

                if ($cart->product_type == 'variable') {
                    $variableOptions = json_decode($cart->variable_options);
                    if ($variableOptions) {
                        foreach ($variableOptions as $key => $val) {
                            $variableOptionHtml .= '<span class="badge badge-primary mr-2">' . $key . ' : ' . $val . '</span>';
                        }
                    }
                }

                if ($cart->product_type == 'service') {
                    $variableOptions = json_decode($cart->variable_options);
                    if ($variableOptions) {

                        $variableOptionHtml .= '<span class="badge badge-primary mr-2">Service Date : ' . $variableOptions->service_date . '</span> <span class="badge badge-primary mr-2">Service Time : ' . $variableOptions->service_time . '</span>';
                        
                    }
                }

                $html .= '
                    <tr>
                        <td>
                            <figure class="media mb-1">
                                <div class="img-wrap">
                                    <img src="' . '/media/thumbnail/' . $product->default_image . '" class="cart_list_image">
                                </div>
                                <figcaption class="media-body">
                                    <p class="title mb-0 text-primary mt-1">' . $product->title . ' <button type="button" class="btn-info cart_discount_add_btn " title="Add Discount" data-unit-price="'.$cart->price.'" data-id="'.$cart->id.'"><i class="mdi mdi-plus text-light"></i></button></p>
                                    <small> 
                                        <b>SKU:</b> ';
                                        if ($cart->product_type == 'variable') {
                                            $html .= $cart->variable_sku;
                                        }else{
                                            $html .= $product->sku;
                                        }
                                        $html .='
                                        , <b>
                                        Seller:</b> ' . $product->seller->shopinfo->name . '</small>
                                    <p class="mb-0">' . $variableOptionHtml . '</p>
                                    <div class="m-btn-group m-btn-group--pill btn-group m-0" role="group" aria-label="...">
                                        <button type="button" class="m-btn btn btn-default decrement_cart" id="' . $cart->id . '"><i class="mdi mdi-minus"></i></button>
                                            <input type="text" class="qty_btn" value="' . $cart->qty . '">
                                        <button type="button" class="m-btn btn btn-default increment_cart" id="' . $cart->id . '"><i class="mdi mdi-plus"></i></button>
                                    </div>
                                </figcaption>
                            </figure> 
                            
                        </td>
                        
                        <td> 
                            <div class="price-wrap"> ';
                                    if($cart->base_price > $cart->price){
                                        $html .='<b> BDT <del> '.$cart->base_price.'</del></b>';
                                    }
                                    $html .='
                                    <b class="price">BDT ' . $cart->price . '</b> <button type="button" class="btn-info cart_product_price_update " title="Update Price" data-unit-price="'.$cart->price.'" data-id="'.$cart->id.'"><i class="mdi mdi-plus text-light"></i></button> </br> 
                                    <b class="price">BDT ' . $cart->discount . ' (Discount)</b><br>
                                    <b class="price">BDT ' . $cart->vat . ' (Vat)</b>
                            </div> 
                        </td>
                        <td> 
                            <div class="price-wrap"> 
                            <b class="price">BDT ' . (($cart->price + $cart->packaging_cost + $cart->security_charge + $cart->vat) * $cart->qty) . '</b>
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

        $data['status'] = 1;
        $data['html'] = $html;
        $data['subtotal'] = $subtotal;
        $data['shipping_cost'] = $shipping_cost;
        $data['vat'] = $vat;
        $data['discount'] = $discount;
        $data['total'] = $total;
        $data['balance'] = $balance;
        return response()->json($data, 200);
        return $html;
    }


    public function removeCart($id)
    {
        $cart = Cart::where('id', $id)->where('user_id', request('customer_id'))->first();
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


    public function changeCartProductPrice(Request $request){
        $cart = Cart::where('id', $request->cart_id)->where('user_id', $request->customer_id)->first();
        if($cart){
            if (!is_numeric($request->update_cart_product_price) || (float) $request->update_cart_product_price < 0) {
                return response()->json(['status' => 0, 'message' => 'Please enter a valid non-negative price.'], 200);
            }
            $cart->price = $request->update_cart_product_price;
            $cart->save();
            $data['status'] = 1; 
        }else{
            $data['status'] = 0; 
            $data['message'] = 'Cart price not found!';
        }

        return response()->json($data, 200);
    }


    public function updateCart(Request $request)
    {
        $cart = Cart::where('id', $request->id)->where('user_id', $request->customer_id)->first();
        if (!$cart) {
            return response()->json(['status' => 0, 'message' => 'Cart item not found!'], 200);
        }
        if ($request->action == 'increment') {
            $product = Product::where('id', $cart->product_id)->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->first();
            if (!$product) {
                return response()->json(['status' => 0, 'message' => 'This product is no longer available.'], 200);
            }

            if ($cart->qty < $product->max_cart_qty ) {
                $newQuantity = $cart->qty + 1;
                if ($cart->product_type === 'simple' && \Helper::simple_product_stock($cart->product_id, $newQuantity) != 1) {
                    return response()->json(['status' => 2, 'message' => 'Product is out of stock.'], 200);
                }
                if ($cart->product_type === 'variable') {
                    $options = json_decode($cart->variable_options);
                    if (!$options || json_last_error() !== JSON_ERROR_NONE || !\Helper::variable_product_stock($cart->product_id, $newQuantity, $options)) {
                        return response()->json(['status' => 2, 'message' => 'Selected product variant is out of stock.'], 200);
                    }
                }
                $cart->qty = $newQuantity;
                $cart->save();
                $data['status'] = 1;            
            }else{
                $data['status'] = 2; 
                $data['message'] = 'You can not buy this product more!';
            }
            
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
        if ($request->address_type == 'pickpoint') {
            $address =  Pickpoints::find($request->address);
        }else{
            $address = Addresses::find($request->address);
        }
        
        $html = '';
        $html .= '
            <div class="cart-calculation">
                <table width="100%" class="">
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
                                                    <input type="radio" class="shipping_option_radio shipping_radio" name="shipping_option' . $cart->id . '" value="0" data-id="' . $cart->product_id . '" data-shippingmethod="free_shipping" data-qty="'.$cart->qty.'" checked="checked" />
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
                                                            <h5 class="mt-5">
                                                                <a href="#" class="text-primary">' . $product->title . '</a>
                                                            </h5> 
                                                            <p>' . $variableOptionHtml . '</p>
                                                            <div class=" d-flex">';
                                                            if ($request->address_type == 'pickpoint' || (\Helper::getSettings('pathao_shipping_enable')) == 1) {
                                                                if((\Helper::getSettings('pathao_shipping_enable')) == 1){
                                                                    $html .= '<span class="badge badge-danger">Pathao</span>';
                                                                }else{
                                                                    $html .= '<span class="badge badge-danger">Pickup Point</span>';
                                                                }
                                                                    
                                                            }else{
                                                                if ($product->product_type != 'digital' && $product->product_type != 'service' && $product->is_grocery != 'grocery') {
                                                                    
                                                                    $html .= '
                                                                        ' . $freeshippingHtml . '
                                                                        <label class="labl">
                                                                            <input type="radio" name="shipping_option' . $cart->id . '" class="shipping_option_radio shipping_radio" value="' . $availableShippingOptions['standard_shipping'] . '" data-id="' . $cart->product_id . '" data-shippingmethod="standard_shipping"  checked="checked" data-qty="'.$cart->qty.'"/>
                                                                            <div class="p-1 m-1">
                                                                                <small>BDT ' . $availableShippingOptions['standard_shipping'] . '  </small><br>
                                                                                <small>Standerd Shipping</small><br>
                                                                                <small> Est. Arrival: Within 4 to 7 days </small><br>
                                                                            </div>
                                                                        </label>
                                                                        <label class="labl">
                                                                            <input type="radio" name="shipping_option' . $cart->id . '" class="shipping_option_radio shipping_radio" value="' . $availableShippingOptions['express_shipping'] . '" data-id="' . $cart->product_id . '" data-shippingmethod="express_shipping" data-qty="'.$cart->qty.'"/>
                                                                            <div class="p-1 m-1">
                                                                                <small>BDT ' . $availableShippingOptions['express_shipping'] . '  </small><br>
                                                                                <small>Express Shipping</small><br>
                                                                                <small> Est. Arrival: Within 1 to 3 days </small><br>
                                                                            </div>
                                                                        </label>';
                                                                }
                                                            }
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


	private function getValidatedPosAddress($customerId, $addressId, $addressType)
	{
		if ($addressType === 'pickpoint') {
			$pickpoint = Pickpoints::where('id', $addressId)->where('status', 1)->first();
			if (!$pickpoint) {
				return ['status' => 0, 'message' => 'The selected pickup point is invalid.'];
			}

			$district = $pickpoint->district;
			$upazila = $pickpoint->upazila;
			$union = $pickpoint->union;
			$unionIsValid = $pickpoint->union_id === null || $union;
			$divisionIsValid = $district && (int) $pickpoint->division_id === (int) $district->division_id;
		} else {
			$address = Addresses::where('id', $addressId)
				->where('user_id', $customerId)
				->where('is_deleted', 0)
				->first();
			if (!$address) {
				return ['status' => 0, 'message' => 'The selected delivery address is invalid for this customer.'];
			}

			$district = $address->district;
			$upazila = $address->upazila;
			$union = $address->union;
			$unionIsValid = $address->shipping_union === null || $union;
			$divisionIsValid = $district && (int) $address->shipping_division === (int) $district->division_id;
		}

		if (!$district || !$upazila || !$divisionIsValid || (int) $upazila->district_id !== (int) $district->id) {
			return ['status' => 0, 'message' => 'The selected District and Thana combination is invalid.'];
		}
		if (!$unionIsValid || ($union && (int) $union->upazila_id !== (int) $upazila->id)) {
			return ['status' => 0, 'message' => 'The selected Union does not belong to the selected Thana.'];
		}

		return [
			'status' => 1,
			'address' => $address ?? null,
			'pickpoint' => $pickpoint ?? null,
			'district' => $district,
			'upazila' => $upazila,
			'union' => $union,
		];
	}

	private function calculatePosShipping($user, $cartData, $addressId, $addressType, array $selectedMethods = [])
	{
		$location = $this->getValidatedPosAddress($user->id, $addressId, $addressType);
		if ($location['status'] !== 1) {
			return $location;
		}

		$hasGrocery = false;
		$groceryTotal = 0;
		$totalWeight = 0;
		$products = Product::whereIn('id', $cartData->pluck('product_id')->unique())->get()->keyBy('id');
		foreach ($cartData as $cart) {
			$product = $products->get($cart->product_id);
			if (!$product) {
				return ['status' => 0, 'message' => 'A cart product is no longer available.'];
			}
			if ($product->is_grocery === 'grocery') {
				$hasGrocery = true;
				$groceryTotal += $cart->price * $cart->qty;
			}
			$totalWeight += $this->getPosProductWeightKg($product) * $cart->qty;
		}

		if ($hasGrocery) {
			$union = $location['union'];
			if (!$union || (int) $union->grocery_shipping_allowed !== 1) {
				return ['status' => 0, 'message' => 'Please select a grocery delivery address with an eligible Union / Area.'];
			}
		}
		$groceryShippingCost = 0;
		if ($hasGrocery && $addressType !== 'pickpoint') {
			$validationAmount = \Helper::getsettings('shipping_validation_amount') ?? 500;
			$groceryShippingCost = $groceryTotal >= $validationAmount
				? (int) (\Helper::getsettings('shipping_minimum_amount') ?? 30)
				: (int) (\Helper::getsettings('shipping_maximum_amount') ?? 50);
		}

		if ($addressType === 'pickpoint') {
			return ['status' => 1, 'shipping_cost' => 0, 'grocery_shipping_cost' => 0, 'location' => $location];
		}

		$shippingCost = 0;
		if ((int) \Helper::getSettings('pathao_shipping_enable') === 1) {
			$district = $location['district'];
			$upazila = $location['upazila'];
			if (!is_numeric($district->city_id) || (int) $district->city_id <= 0 || !is_numeric($upazila->zone_id) || (int) $upazila->zone_id <= 0) {
				return ['status' => 0, 'message' => 'The selected address does not have a valid Pathao location mapping.'];
			}

			$groups = [];
			foreach ($cartData as $cart) {
				if ($cart->product_type === 'simple' || $cart->product_type === 'variable') {
					$groups[$cart->seller_id][] = $cart;
				}
			}
			$defaultSeller = Admins::find(\Helper::getSettings('default_branch_id'));
			$sellers = Admins::whereIn('id', array_keys($groups))->get()->keyBy('id');
			foreach ($groups as $sellerId => $items) {
				$seller = $sellers->get($sellerId);
				$storeId = $seller->pathao_store_id ?? optional($defaultSeller)->pathao_store_id;
				if (!$storeId) {
					return ['status' => 0, 'message' => 'Unable to calculate delivery charge. Please try again.'];
				}
				$itemWeight = 0;
				foreach ($items as $item) {
					$itemWeight += max(0.1, $this->getPosProductWeightKg($products->get($item->product_id))) * $item->qty;
				}
				$price = \Helper::calculateShippingCost($storeId, 2, 48, max(0.1, $itemWeight), $district->city_id, $upazila->zone_id);
				if (!is_object($price) || ($price->type ?? null) !== 'success' || !isset($price->data->price, $price->data->discount, $price->data->additional_charge, $price->data->promo_discount)) {
					return ['status' => 0, 'message' => 'Unable to calculate delivery charge. Please try again.'];
				}
				$shippingCost += ($price->data->price - $price->data->discount) + $price->data->additional_charge - $price->data->promo_discount;
			}
		} elseif ((int) \Helper::getSettings('default_shipping_enable') === 1) {
			$insideIds = explode(',', (string) \Helper::getSettings('default_shipping_inside_location'));
			$base = in_array((string) $location['district']->id, array_map('trim', $insideIds), true)
				? \Helper::getSettings('default_shipping_inside_origin')
				: \Helper::getSettings('default_shipping_outside_origin');
			$shippingCost = $totalWeight > 1
				? $base + ($totalWeight * \Helper::getSettings('default_shipping_increase_per_kg'))
				: $base;
		} else {
			foreach ($cartData as $cart) {
				$product = $products->get($cart->product_id);
				if ($cart->product_type === 'digital' || $cart->product_type === 'service' || $product->is_grocery === 'grocery') {
					continue;
				}
				$options = \Helper::get_shipping_cost($user->id, $cart->seller_id, $cart->product_id, $location['district']->id);
				$method = $selectedMethods[$cart->product_id] ?? 'standard_shipping';
				if (!array_key_exists($method, $options) || $method === 'free_shipping' && ($options['free_shipping'] ?? null) !== 'on') {
					$method = 'standard_shipping';
				}
				$shippingCost += $method === 'free_shipping' ? 0 : ((float) ($options[$method] ?? 0) * $cart->qty);
			}
		}

		return ['status' => 1, 'shipping_cost' => $shippingCost + $groceryShippingCost, 'grocery_shipping_cost' => $groceryShippingCost, 'location' => $location];
	}

	private function getPosProductWeightKg($product)
	{
		if (!$product) {
			return 0;
		}
		if ($product->weight_unit === 'kg' || $product->weight_unit === 'l') {
			return (float) $product->weight;
		}
		if ($product->weight_unit === 'gram' || $product->weight_unit === 'ml') {
			return (float) $product->weight / 1000;
		}

		return 0;
	}

    public function getShippingCost(Request $request){
		$user = User::find($request->customer_id);
		$cartData = Cart::where('user_id', $request->customer_id)->get();
		if (!$user || $cartData->isEmpty()) {
			return response()->json(['status' => 0, 'message' => 'Customer cart is empty.'], 200);
		}

		$result = $this->calculatePosShipping($user, $cartData, $request->address, $request->address_type);
		return response()->json($result, 200);
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

    public function applyDiscount(Request $request)
    {
        $cart = Cart::where('id', $request->cart_id)->where('user_id', $request->customer_id)->first();
        if (!$cart) {
            return response()->json(['status' => 0, 'message' => 'Cart item not found!'], 200);
        }
        if($request->cartdiscount_type == 'custom'){
            if ($request->cartdiscount_amount) {
                if (!is_numeric($request->cartdiscount_amount) || (float) $request->cartdiscount_amount < 0 || (float) $request->cartdiscount_amount > ((float) $cart->price * (int) $cart->qty)) {
                    return response()->json(['status' => 0, 'message' => 'Please enter a valid discount within the cart item total.'], 200);
                }
                $cart->discount = $request->cartdiscount_amount;
                $cart->vat = $request->vat ?? 0.00;
                $cart->save();
                $data['status'] = 1;
                $data['message'] = 'Dsicount added successfully!';
                return response()->json($data, 200);
            }else{
                $data['status'] = 0;
                $data['message'] = 'Please enter discount amount!';
                return response()->json($data, 200);
            }
        }elseif ($request->cartdiscount_type == 'coupon') {
            if ($request->cartcoupon_code) {
                $couponData = Helper::validateCoupon($request->cartcoupon_code, $cart->user_id);
                if ($couponData['status'] == 1) {
                    $cart->discount = $couponData['amount'];
                    $cart->vat = $request->vat ?? 0.00;
                    $cart->save();
                    $data['status'] = 1;
                    $data['message'] = 'Dsicount added successfully!';
                    return response()->json($data, 200);
                } else {
                    $data['status'] = 0;
                    $data['message'] = 'Invalid Coupon code!';
                    return response()->json($data, 200);
                }
            }else{
                $data['status'] = 0;
                $data['message'] = 'Please enter coupon code!';
                return response()->json($data, 200);
            }
        }elseif($request->cartdiscount_type == 'percent'){
            if ($request->cartdiscount_amount) {
                if(is_numeric($request->cartdiscount_amount) && (float) $request->cartdiscount_amount >= 0 && (float) $request->cartdiscount_amount <= 100){
                    $percent = ((float) $request->cartdiscount_amount / 100) * ((float) $cart->price * (int) $cart->qty);
                    $cart->discount = $percent;
                    $cart->vat = $request->vat ?? 0.00;
                    $cart->save();
                    $data['status'] = 1;
                    $data['message'] = 'Dsicount added successfully!';
                    return response()->json($data, 200);
                }else{
                    $data['status'] = 0;
                    $data['message'] = 'Please enter unit price!';
                    return response()->json($data, 200);
                }
                
            }else{
                $data['status'] = 0;
                $data['message'] = 'Please enter discount amount!';
                return response()->json($data, 200);
            }
        }else{
            if($request->vat > 0){
                $cart->vat = $request->vat ?? 0.00;
                $cart->save();
                $data['status'] = 1;
                $data['message'] = 'Vat added successfully!';
                return response()->json($data, 200);
            }else{
                $data['status'] = 0;
                $data['message'] = 'Please select discount type!';
                return response()->json($data, 200);
            }

        }        
    }

    public function order_function(Request $request){
        $user = User::find($request->customer);
        $pickpoint =  Pickpoints::where('id', $request->address)->first();
        if ($user) {

            $user_id = $user->id;
			$cartData = DB::table('carts')->where('user_id', $user_id)->get();
			if ($cartData->isEmpty()) {
				$data['status'] = 0;
				$data['message'] = 'Product not found in your cart.';
				return response()->json($data, 200);
			}

			$selectedMethods = [];
			foreach ((array) json_decode($request->shipping_method, true) as $selection) {
				if (is_array($selection) && isset($selection[0], $selection[1])) {
					$selectedMethods[$selection[1]] = $selection[0];
				}
			}
			$shippingResult = $this->calculatePosShipping($user, $cartData, $request->address, $request->address_type, $selectedMethods);
			if ($shippingResult['status'] !== 1) {
				return response()->json(['status' => 0, 'message' => $shippingResult['message']], 200);
			}

            $sub_total = 0;
			$vat = 0;
			if (!is_numeric($request->discount) || (float) $request->discount < 0) {
				return response()->json(['status' => 0, 'message' => 'Please enter a valid non-negative discount.'], 200);
			}
			$discount_amount = (float) $request->discount;
			$coupon_amount =  0;
			$shipping_cost = (float) $shippingResult['shipping_cost'];
			$packaging_cost = 0;
			$security_charge = 0;
			$aff_commission_amount = 0;
			$productIds = [];
			$validatedProducts = Product::whereIn('id', $cartData->pluck('product_id')->unique())
				->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->get()->keyBy('id');
			$hasPhysicalDeliveryProduct = false;
			$sellerIdsForNotification = [];

            foreach ($cartData as $cart) {
				$sProduct = $validatedProducts->get($cart->product_id);
				if (!$sProduct) {
					return response()->json(['status' => 0, 'message' => 'A cart product is no longer available for POS ordering.'], 200);
				}
				if (!is_numeric($cart->qty) || (int) $cart->qty < 1 || (int) $cart->qty != $cart->qty || (int) $cart->qty > (int) $sProduct->max_cart_qty) {
					return response()->json(['status' => 0, 'message' => 'A cart product has an invalid quantity.'], 200);
				}
				// POS intentionally permits an administrator-entered unit-price override.
				if (!is_numeric($cart->price) || (float) $cart->price < 0) {
					return response()->json(['status' => 0, 'message' => 'A cart product has an invalid manual price.'], 200);
				}
				$vat += (($cart->price * $sProduct->vat) / 100) * $cart->qty;
				$sub_total += $cart->price * $cart->qty;
				$hasPhysicalDeliveryProduct = $hasPhysicalDeliveryProduct || in_array($cart->product_type, ['simple', 'variable'], true);
				$sellerIdsForNotification[$cart->seller_id] = $cart->seller_id;

                if ($sProduct->aff_commission_amount) {
                    $aff_commission_amount += $sProduct->aff_commission_amount * $cart->qty;
                    $productIds[] = $sProduct->id;
                }

                if ($cart->product_type == 'simple' || $cart->product_type == 'digital' || $cart->product_type == 'service') {
					$price = \Helper::price_after_offer($cart->product_id);

					//Validate Price Update
	
					// if ($price != $cart->price) {
					// 	$newDiscount =  \Helper::discount_amount_by_IDS($cart->product_id) * $cart->qty;
					// 	DB::table('carts')->where('id', $cart->id)->update(['price' => $price, 'discount' => $newDiscount]);

					// 	$data['status'] = 2;
					// 	$data['message'] = 'Product price has been changed. Please review new price and place order again!';
					// 	return response()->json($data, 200);
					// }

					$qty = $cart->product_type === 'simple'
						? \Helper::simple_product_stock($cart->product_id, $cart->qty)
						: 1;
					if ($qty == 1) {
						$packaging_cost += $cart->packaging_cost * $cart->qty;
						$security_charge += $cart->security_charge * $cart->qty;
						// $discount_amount += $discount_amount + $cart->discount;
					} else {
						$data['status'] = 0;
						$data['message'] = 'Product is out of stock.';
						return response()->json($data, 200);
					}
				}

				if ($cart->product_type == 'variable') {
					$options = json_decode($cart->variable_options);
					if (!$cart->variable_options || json_last_error() !== JSON_ERROR_NONE || !$options) {
						return response()->json(['status' => 0, 'message' => 'A selected product variant is invalid or no longer available.'], 200);
					}
					$availableOptions = \Helper::variable_product_stock($cart->product_id, $cart->qty, $options);
						
					if ($availableOptions) {
							$price = \Helper::price_after_offer($cart->product_id) + $availableOptions['totalAdditional'];
							//Validate Price Update
							// if ($price != $cart->price) {
							// 	$newDiscount =  \Helper::discount_amount_by_IDS($cart->product_id) * $cart->qty;
							// 	DB::table('carts')->where('id', $cart->id)->update(['price' => $price, 'discount' => $newDiscount]);
							// 	$data['status'] = 2;
							// 	$data['message'] = 'Product price has been changed. Please review new price and place order again!';
							// 	return response()->json($data, 200);
							// }
					} else {
						return response()->json(['status' => 0, 'message' => 'A selected product variant is out of stock or no longer available.'], 200);
					}
				}
            }

			if ($discount_amount > $sub_total) {
				return response()->json(['status' => 0, 'message' => 'Discount cannot exceed the authoritative subtotal.'], 200);
			}
			$previousDue = max(0, (float) ($user->balance ?? 0));
			$total_amount = ($sub_total + $shipping_cost + $packaging_cost + $security_charge + $vat + $previousDue) - $discount_amount;
			if (!is_numeric($request->paid_amount) || (float) $request->paid_amount < 0) {
				return response()->json(['status' => 0, 'message' => 'Please enter a valid non-negative paid amount.'], 200);
			}
			$paidAmount = min((float) $request->paid_amount, $total_amount);
			$allowedPaymentMethods = ['cash', 'partial_payment', 'cash_on_delivery', 'mfs_card'];
			if (!in_array($request->payment_method, $allowedPaymentMethods, true)) {
				return response()->json(['status' => 0, 'message' => 'The selected payment method is invalid.'], 200);
			}
			$paymentAsset = null;
			if (isset($request->paid_by) && $paidAmount > 0) {
				$paymentAsset = CurrentAsset::find($request->paid_by);
				if (!$paymentAsset) {
					return response()->json(['status' => 0, 'message' => 'The selected payment account is invalid.'], 200);
				}
			}

            $orderData['total_amount'] = $total_amount;
			$orderData['paid_amount'] = $paidAmount;
			$orderData['is_partial_payment'] = ($paidAmount > 0 && $paidAmount < $total_amount) ? 1 : 0;
			$orderData['order_from'] = $request->order_from ?? 'pos';
			$orderData['coupon_code'] = $coupon_code ?? null;
			$orderData['coupon_amount'] = $coupon_amount ?? 0;
			$orderData['voucher_code'] = $voucher_code ?? null;
			$orderData['voucher_amount'] = $voucher_amount ?? 0;
			$orderData['discount_amount'] = $discount_amount;
			$orderData['payment_method'] = $request->payment_method ?? 'cash_on_delivery';
			$orderData['pay_by'] = $paidAmount > 0 ? $request->paid_by : null;
			$directCompletionAllowed = $request->address_type === 'pickpoint' || !$hasPhysicalDeliveryProduct;
            if ($directCompletionAllowed && $request->completed === 'yes') {
                $orderData['status'] = 6;
            }else{
                $orderData['status'] = 1;
            }

			$orderData['vat'] = $vat;
			$orderData['user_id'] = $user_id;
			$orderData['address_id'] = $request->address;
			$orderData['shipping_address_snapshot'] = $request->address_type !== 'pickpoint'
				? json_encode(Order::shippingAddressSnapshot($shippingResult['location']['address']), JSON_UNESCAPED_UNICODE)
				: null;
			$orderData['is_pickpoint'] = ($request->address_type == 'pickpoint') ? 1 : 0;
			$orderData['payment_id'] = uniqid();
			$orderData['ip_address'] = request()->ip();
			$orderData['note']    = $request->order_note;
			$orderData['shipping_cost']    = $shipping_cost;
			$orderData['total_packaging_cost']  = $packaging_cost;
			$orderData['total_security_charge'] = $security_charge;
			DB::beginTransaction();
			try {
			$order_id = DB::table('orders')->insertGetId($orderData);

            
            //Insert Order details
			foreach ($cartData as $single_item) {
				$product = \Helper::get_product_by_id($single_item->product_id);
				$detailsData['user_id'] = $single_item->user_id;
				$detailsData['order_id'] = $order_id;
				$detailsData['product_id'] = $single_item->product_id;
				$detailsData['product_sku'] = ($product->sku) ? $product->sku : $single_item->variable_sku;
				$detailsData['vat'] = ($single_item->price*$product->vat)/100 * $single_item->qty;
				$detailsData['product_qty'] = $single_item->qty;
				$detailsData['base_price'] = $single_item->base_price ?? 0.00;
				$detailsData['price'] = $single_item->price ?? 0.00;
				$detailsData['discount'] = $single_item->discount ?? 0.00;
				$detailsData['is_promotion'] = $product->is_promotion;
				$detailsData['loyalty_point'] = $product->loyalty_point ?? 0;
                $detailsData['shipping_method'] = 'pick_point';
				$detailsData['shipping_cost'] = $shipping_cost;
				$detailsData['product_options'] = $single_item->variable_options;
				$detailsData['seller_id'] = $product->seller_id ?? null;
				$orderData['payment_method'] = $request->payment_method;
                $detailsData['packaging_cost'] = $single_item->packaging_cost ?? 0;
                $detailsData['security_charge'] = $single_item->security_charge ?? 0;
				if ($directCompletionAllowed && $request->completed === 'yes') {
                    $detailsData['status'] = 10;
                }else{
                    $detailsData['status'] = 1;
                }
				if ($orderDetailsId = DB::table('order_details')->insertGetId($detailsData)) {
					if (in_array($single_item->product_type, ['simple', 'variable'], true)) {
						\Helper::update_product_quantity($single_item->product_id, $single_item->qty, $single_item->variable_options, 'subtraction');
                    }

					//Set Seller Commission
					\Helper::setSellerBalance($orderDetailsId);
				} else {
					throw new \RuntimeException('Unable to insert POS order details.');
				}
			}

            // if customer pay 
			if($paymentAsset && $paidAmount > 0){
				$asset = $paymentAsset;
				\Helper::creditAssetBalance($asset->id, $paidAmount);
				\Helper::accountHistory($asset->branch_id,'','customer_order' , 0, $paidAmount, $paidAmount, $request->paid_by, '', $user_id, Helper::getBusinessBalance($asset->branch_id), $request->invoice_number);
				$due_amount = $total_amount - $paidAmount;
				if($due_amount > 0){
					\Helper::customerBalanceDebit($user_id, $due_amount);
				}
			}else{
				\Helper::customerBalanceDebit($user_id, $total_amount - $paidAmount);
            }

            //Affiliate Commision
			if ($user->affiliate_referer && $productIds && $aff_commission_amount > 0) {
				$affData['user_id'] = $user->affiliate_referer;
				$affData['buyer_id'] = $user->id;
				$affData['product_ids'] = implode(',', $productIds);
				$affData['order_id'] = $order_id;
				$affData['commission_amount'] = $aff_commission_amount;
				$affData['note'] = '';
				$affData['status'] = 1;
				DB::table('affiliate_history')->insert($affData);
			}

			DB::table('carts')->where('user_id', $user_id)->delete();
			DB::commit();
			} catch (\Throwable $exception) {
				DB::rollBack();
				\Log::error('POS order transaction failed.', ['user_id' => $user_id, 'error' => $exception->getMessage()]);
				return response()->json(['status' => 0, 'message' => 'Unable to place the order. No changes were saved.'], 200);
			}

			$invoice = DB::table('order_details')->where('order_id', $order_id)->get();
			foreach ($invoice as $key => $item) {
				$p = \Helper::get_product_by_id($item->product_id);
				$item->image = $p->default_image ?? null;
				$item->title = $p->title ?? null;
				$item->slug  = $p->slug ?? null;
			}

			$in['total'] = $total_amount;
			$in['sub_total'] = $sub_total;
			$in['discount_amount'] = $discount_amount;
			$in['coupon_amount'] = $coupon_amount;
			$in['order_id'] = $order_id;
			$in['products'] = $invoice;
			$in['shipping_cost'] = $shipping_cost;

			$data['status'] = 1;
			$data['message'] = 'Order placed successfully.';
			$data['invoice'] = $in;

			//Send Email
			$order = Order::find($order_id);

			if ($request->server('HTTP_HOST') != '127.0.0.1:8000') {
				if ($email = $user->email) {
					try {
						\Helper::sendEmail($email, 'Order Placed', $order, 'invoice');
					} catch (\Throwable $exception) {
						\Log::warning('POS order confirmation email failed.', ['order_id' => $order_id]);
					}
				}
			}

			//Send Push notifications
			//Customer
			$message = [
				'order_id' =>  'MS' . date('y', strtotime(Carbon::now())) . $order_id,
				'type' => 'order',
				'message' => 'Order placed successfully!',
			];

			//Seller
			foreach ($sellerIdsForNotification as $val) {
				$seller = Admins::find($val);
				try {
					\Helper::sendPushNotification($val, 2, 'Order Placed', 'Order placed successfully!', json_encode($message));
				} catch (\Throwable $exception) {
					\Log::warning('POS seller push notification failed.', ['order_id' => $order_id, 'seller_id' => $val]);
				}
				// \Helper::sendSms($seller->phone, 'একটি নতুন অর্ডার সফলভাবে স্থাপন করা হয়েছে! অর্ডার আইডি হল  #' . 'MS' . date('y', strtotime(Carbon::now())) . $order_id);
			}

			// Customer
			try {
				\Helper::sendSms($user->phone, 'অর্ডার সফলভাবে স্থাপন করা হয়েছে! আপনার অর্ডারের জন্য আপনাকে ধন্যবাদ. আপনার অর্ডার আইডি #' . 'MS' . date('y', strtotime(Carbon::now())) . $order_id);
			} catch (\Throwable $exception) {
				\Log::warning('POS customer SMS failed.', ['order_id' => $order_id, 'user_id' => $user->id]);
			}

            return response()->json($data, 200);

        }else {
            $data['status'] = 0;
            $data['message'] = 'You have to select customer.';
            return response()->json($data, 200);
        }
    }

    public function order_function1(Request $request){

        $user = User::find($request->customer);
        $pickpoint =  Pickpoints::where('id', $request->address)->first();

        $partial_payment = false;
		if($request->payment_method == 'online_payment'){
			$partial_payment = $request->partial_payment;
		}

        if ($user) {
			$validForGroceryShipping = false;
			$userAddress = Addresses::find($user->default_address_id);

			if ($request->address_type != 'pickpoint') {
				if (!$userAddress) {
					$data['status'] = 0;
					$data['message'] = 'You do not have any default shipping address! Please add address first to place an order.';
					return response()->json($data, 200);
				} else {

					$grocery_allowed_shipping_location = \DB::table('unions')->where('grocery_shipping_allowed', 1)->pluck('id')->toArray();
					$shipping_union = $userAddress->shipping_union;

					if (in_array($shipping_union, $grocery_allowed_shipping_location)) {
						$validForGroceryShipping = true;
					}
				}
			}


            if ($request->address_type != 'pickpoint') {
			    $shipping_methods =  $request->shipping_method;
			    $keyEnabledShipping = [];
            
                foreach ($shipping_methods as $filteredShipping) {
                    $keyEnabledShipping[$filteredShipping['product_id']] = $filteredShipping['shipping_method'];
                }
            }else{
                $keyEnabledShipping = [];
            }
			

			$user_id = $user->id;
			$cartData = DB::table('carts')->where('user_id', $user_id)->get();
			if (!$cartData) {
				$data['status'] = 0;
				$data['message'] = 'Product not found in your cart.';
				return response()->json($data, 200);
			}

			$sub_total = 0;
			$vat = 0;
			$discount_amount = 0;
			$shipping_cost = 0;
			$packaging_cost = 0;
			$security_charge = 0;
			$aff_commission_amount = 0;
			$productIds = [];

			foreach ($cartData as $cart) {
				//Update packaging_cost & security_charge
				$sProduct = Product::select('is_grocery', 'packaging_cost', 'security_charge', 'aff_commission_amount', 'id','vat')->where('id', $cart->product_id)->first();
				if ($sProduct) {

					$vat +=  (($cart->price*$sProduct->vat)/100) * $cart->qty;

					if ($request->address_type != 'pickpoint') {

						if ($cart->packaging_cost != $sProduct->packaging_cost) {
							DB::table('carts')->where('id', $cart->id)->update(['packaging_cost' => $sProduct->packaging_cost]);
							$data['status'] = 2;
							$data['message'] = 'Product packaging cost has been changed. Please review new packaging cost and place order again!';
							return response()->json($data, 200);
						}

						if ($cart->security_charge != $sProduct->security_charge) {
							DB::table('carts')->where('id', $cart->id)->update(['security_charge' => $sProduct->security_charge]);
							$data['status'] = 2;
							$data['message'] = 'Product security charge has been changed. Please review new security charge and place order again!';
							return response()->json($data, 200);
						}

						if ($sProduct->is_grocery == 'grocery') {
							if (!$validForGroceryShipping) {
								$data['status'] = 0;
								$data['message'] = 'Sorry! We are not currently delivery grocery product in your area! Please check grocery help center for further details.';
								return response()->json($data, 200);
							}
						}
					}

					if ($sProduct->aff_commission_amount) {
						$aff_commission_amount += $sProduct->aff_commission_amount * $cart->qty;
						$productIds[] = $sProduct->id;
					}
				}


				if ($cart->product_type == 'simple' || $cart->product_type == 'digital' || $cart->product_type == 'service') {
					$price = \Helper::price_after_offer($cart->product_id);

					//Validate Price Update
	
					if ($price != $cart->price) {
						$newDiscount =  \Helper::discount_amount_by_IDS($cart->product_id) * $cart->qty;
						DB::table('carts')->where('id', $cart->id)->update(['price' => $price, 'discount' => $newDiscount]);

						$data['status'] = 2;
						$data['message'] = 'Product price has been changed. Please review new price and place order again!';
						return response()->json($data, 200);
					}

					$qty = \Helper::simple_product_stock($cart->product_id, $cart->qty);
					if ($qty == 1) {
						$sub_total += $price * $cart->qty;
						$packaging_cost += $cart->packaging_cost * $cart->qty;
						$security_charge += $cart->security_charge * $cart->qty;
					} else {
						$data['status'] = 0;
						$data['message'] = 'Product is out of stock.';
						return response()->json($data, 200);
					}
				}

				if ($cart->product_type == 'variable') {

					if ($cart->variable_options) {
						$availableOptions = \Helper::variable_product_stock($cart->product_id, $cart->qty, json_decode($cart->variable_options));
						
						if ($availableOptions) {
							$price = \Helper::price_after_offer($cart->product_id) + $availableOptions['totalAdditional'];


							//Validate Price Update
							if ($price != $cart->price) {
								$newDiscount =  \Helper::discount_amount_by_IDS($cart->product_id) * $cart->qty;
								DB::table('carts')->where('id', $cart->id)->update(['price' => $price, 'discount' => $newDiscount]);
								$data['status'] = 2;
								$data['message'] = 'Product price has been changed. Please review new price and place order again!';
								return response()->json($data, 200);
							}

							$sub_total += $price * $cart->qty;
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


			//Voucher Validation for Collection and Activation
			$collected = CollectedVoucher::where('user_id', $user->id)->where('status', 0)->first();
			if ($collected) {
				$voucherForActivation = $collected->voucher_id;
				$voucherData = \Helper::validateVoucher($voucherForActivation, $user->id);
				if ($voucherData['status'] == 1) {
					CollectedVoucher::where('voucher_id', $voucherData['code'])->where('user_id', $user->id)->update(['status' => 1]);
				}
			}


			//Voucher Validation for Checkout
			$voucher_amount = 0;
			if ($request->usedVoucher) {
				$usedVoucher = $request->usedVoucher;
				$voucherData = \Helper::validateVoucher($request->usedVoucher, $user->id);

				if ($voucherData['status'] == 1) {
					$voucher_amount = $voucherData['amount'];
					$voucher_code = $voucherData['code'];
					CollectedVoucher::where('voucher_id', $voucherData['code'])
					->where('user_id', $user->id)
					->first()
					->update(['status' => 2]);
				}
			}


			//Partial Payment validation for Checkout
			if($partial_payment){
				$partial_payment_validation = \Helper::validatePartialPayment($partial_payment,$sub_total);
				if($partial_payment_validation['status'] == 0){
					$data['status'] = 0;
					$data['message'] = $partial_payment_validation['message'];
					return response()->json($data, 200);
				}
			}

			$orderData['total_amount'] = 0;
			$orderData['paid_amount'] = 0;
			$orderData['is_partial_payment'] = 0;
			$orderData['order_from'] = $request->order_from ?? 'web';
			$orderData['coupon_code'] = $coupon_code ?? null;
			$orderData['coupon_amount'] = $coupon_amount ?? 0;
			$orderData['voucher_code'] = $voucher_code ?? null;
			$orderData['voucher_amount'] = $voucher_amount ?? 0;
			$orderData['discount_amount'] = $orderData['coupon_amount'] + $orderData['voucher_amount'];
			$orderData['payment_method'] = $request->payment_method;
			$orderData['status'] = 1;
			$orderData['vat'] = $vat;
			$orderData['user_id'] = $user_id;
			$orderData['address_id'] = $user->default_address_id ?? null;
			$orderData['payment_id'] = uniqid();
			$orderData['ip_address'] = request()->ip();
			$orderData['note']    = $request->note;
			$orderData['total_packaging_cost']  = $packaging_cost;
			$orderData['total_security_charge'] = $security_charge;
			$order_id = DB::table('orders')->insertGetId($orderData);
			$totalShippingCost = 0;
			$grocery_total_price = 0;
			$shipping_cost_grocery = 0;


			//Insert Order details
			foreach ($cartData as $single_item) {
				$product = \Helper::get_product_by_id($single_item->product_id);

				$detailsData['user_id'] = $single_item->user_id;
				$detailsData['order_id'] = $order_id;
				$detailsData['product_id'] = $single_item->product_id;

				$detailsData['product_sku'] = ($product->sku) ? $product->sku : $single_item->variable_sku;
				$detailsData['vat'] = ($single_item->price*$product->vat)/100 * $single_item->qty;
				$detailsData['product_qty'] = $single_item->qty;
				$detailsData['price'] = $single_item->price ?? null;
				$detailsData['is_promotion'] = $product->is_promotion;
				$detailsData['loyalty_point'] = $product->loyalty_point ?? 0;


				if ($single_item->product_type != 'digital' && $single_item->product_type != 'service' && $product->is_grocery != 'grocery') {
					//Shipping Method and Shipping Cost

					if ($request->address_type != 'pickpoint') {
						// $detailsData['shipping_method'] = $keyEnabledShipping[$single_item->product_id];
						// $detailsData['shipping_cost'] = 0;
						// $availableShippings = \Helper::get_shipping_cost($single_item->user_id, $product->seller_id, $product->id, $userAddress->shipping_district);

						// foreach ($availableShippings as $key => $val) {
						// 	if ($key == $keyEnabledShipping[$single_item->product_id]) {
						// 		$val = (int) $val;
						// 		$detailsData['shipping_cost'] = $val * $single_item->qty;
						// 		$totalShippingCost += $val * $single_item->qty;
						// 	}
						// }
						$totalShippingCost = $request->shipping_cost;
						$detailsData['shipping_cost'] = 0;
					} else {
						$detailsData['shipping_method'] = 'pick_point';
						$detailsData['shipping_cost'] = $request->shipping_cost;
					}
				}

				if ($product->is_grocery == 'grocery') {
					$grocery_total_price += $single_item->price * $single_item->qty;
				}

				$detailsData['product_options'] = $single_item->variable_options;
				$detailsData['seller_id'] = $product->seller_id ?? null;
				$orderData['payment_method'] = $request->payment_method;

				if ($user->default_pickpoint_address != 1) {
					$detailsData['packaging_cost'] = $single_item->packaging_cost ?? 0;
					$detailsData['security_charge'] = $single_item->security_charge ?? 0;
				}


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



			if ($grocery_total_price > 0) {
				$settingAmountForGrocery = \Helper::getsettings('shipping_validation_amount') ?? 500;
				$minimumShippingAmount = \Helper::getsettings('shipping_minimum_amount') ?? 30;
				$maximumShippingAmount = \Helper::getsettings('shipping_maximum_amount') ?? 50;

				if ($grocery_total_price >= $settingAmountForGrocery) {
					$shipping_cost_grocery = (int)$minimumShippingAmount;
				} else {
					$shipping_cost_grocery = (int)$maximumShippingAmount;
				}
			}


			if ($request->address_type != 'pickpoint') { //Pick Point Order
				$pData = [];
				$pData['grocery_shipping_cost'] = 0;
				$pData['shipping_cost'] = $totalShippingCost + $shipping_cost_grocery;
				$pData['is_pickpoint'] = 1;

				if ($pickpoint) {
					if ($pickpoint->discount_type == 1) { //Fixed
						$pData['shipping_cost'] = $pickpoint->discount;
					} else {
						$pData['shipping_cost'] =  ($sub_total * $pickpoint->discount) / 100;
					}
				}

				$total_amount = ($sub_total + $pData['shipping_cost']+$vat) - ($coupon_amount + $orderData['voucher_amount']);

				$pData['total_amount'] = $total_amount;
				$pData['paid_amount'] = 0;
				$pData['is_partial_payment'] = $partial_payment ? 1 :0;

				DB::table('orders')->where('id', $order_id)->update($pData);
			} else {
				$total_amount = ($sub_total + $totalShippingCost + $shipping_cost_grocery + $packaging_cost + $security_charge+$vat) - ($coupon_amount + $orderData['voucher_amount']);
				DB::table('orders')->where('id', $order_id)->update([
					'shipping_cost' => $totalShippingCost + $shipping_cost_grocery,
					'grocery_shipping_cost' => $shipping_cost_grocery,
					'total_amount'		=> $total_amount,
					'paid_amount'		=> 0,
					'is_partial_payment'=> $partial_payment ? 1 :0
				]);
			}



			//Affiliate Commision
			if ($user->affiliate_referer && $productIds && $aff_commission_amount > 0) {
				$affData['user_id'] = $user->affiliate_referer;
				$affData['buyer_id'] = $user->id;
				$affData['product_ids'] = implode(',', $productIds);
				$affData['order_id'] = $order_id;
				$affData['commission_amount'] = $aff_commission_amount;
				$affData['note'] = '';
				$affData['status'] = 1;
				DB::table('affiliate_history')->insert($affData);
			}

			$invoice = DB::table('order_details')->where('order_id', $order_id)->get();
			foreach ($invoice as $key => $item) {
				$p = \Helper::get_product_by_id($item->product_id);
				$item->image = $p->default_image ?? null;
				$item->title = $p->title ?? null;
				$item->slug  = $p->slug ?? null;
			}
			$in['total'] = $total_amount;
			$in['sub_total'] = $sub_total;
			$in['discount_amount'] = $discount_amount;
			$in['coupon_amount'] = $coupon_amount;
			$in['order_id'] = $order_id;
			$in['products'] = $invoice;
			$in['shipping_cost'] = $shipping_cost;

			$data['status'] = 1;
			$data['message'] = 'Order placed successfully.';
			$data['invoice'] = $in;

			//Send Email
			$order = Order::find($order_id);

			if ($request->server('HTTP_HOST') != '127.0.0.1:8000') {
				if ($email = $user->email) {
					\Helper::sendEmail($email, 'Order Placed', $order, 'invoice');
				}
			}

			//Send Push notifications
			//Customer
			$message = [
				'order_id' =>  'MS' . date('y', strtotime(Carbon::now())) . $order_id,
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
				\Helper::sendPushNotification($val, 2, 'Order Placed', 'Order placed successfully!', json_encode($message));
				\Helper::sendSms($seller->phone, 'একটি নতুন অর্ডার সফলভাবে স্থাপন করা হয়েছে! অর্ডার আইডি হল  #' . 'MS' . date('y', strtotime(Carbon::now())) . $order_id);
			}

			// Customer
			\Helper::sendSms($user->phone, 'অর্ডার সফলভাবে স্থাপন করা হয়েছে! আপনার অর্ডারের জন্য আপনাকে ধন্যবাদ. আপনার অর্ডার আইডি #' . 'MS' . date('y', strtotime(Carbon::now())) . $order_id);

			//Delete Cart Data
			DB::table('carts')->where('user_id', $user_id)->delete();

            return response()->json($data, 200);
		}else {
            $data['status'] = 0;
            $data['message'] = 'You have to select customer.';
            return response()->json($data, 200);
        }
    }

    public function getOrderSummary($order_id, Request $request){
        if ($order_id) {
            $order = Order::find($order_id);
        }else{
            $order = Order::find($request->order_id);
        }
        
        return view('backend.pages.order.invoice.invoice', compact('order'));
    }

}
