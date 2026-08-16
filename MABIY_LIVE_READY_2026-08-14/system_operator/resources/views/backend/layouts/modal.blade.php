@if (Route::currentRouteName() == 'admin.pos')
    {{-- Customer add modal start --}}
    <div class="modal fade" id="customerAddModal" tabindex="-1" role="dialog" aria-labelledby="customerAddModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerAddModalLabel">Add New Customer </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="server_side_error">

                    </div>
                    <form class="form-sample" id="customerAddForm" method="post" action="{{ route('admin.pos.customer.create') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-5 col-form-label">Customer Name <span
                                            class="required">*</span></label>
                                    <div class="col-sm-7">
                                        <input type="text" name="name" class="form-control" required=""
                                            placeholder="Customer Name" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-5 col-form-label">Customer Phone <span
                                            class="required">*</span></label>
                                    <div class="col-sm-7">
                                        <input type="text" name="phone" class="form-control" required=""
                                            placeholder="Customer Phone" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-5 col-form-label">Customer Email </label>
                                    <div class="col-sm-7">
                                        <input type="email" name="email" class="form-control"
                                            placeholder="Customer Email" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <button class="btn btn-primary" name="save" type="submit">Add
                                            Customer</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Customer add modal end --}}

    {{-- Customer address add modal start --}}
    <div class="modal fade" id="customerShippingAddressAddModal" tabindex="-1" role="dialog"
        aria-labelledby="customerShippingAddressAddModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerShippingAddressAddModalLabel">Add New Shipping Address </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="address-error">

                    </div>
                    <form class="form-sample" method="post" action="#" enctype="multipart/form-data"
                        id="address_form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Full Name <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" class="form-control"
                                            @error('name') is-invalid @enderror value="{{ old('name') }}" required
                                            autocomplete="name" autofocus placeholder="Full Name" id="address-name">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label"> Email</label>
                                    <div class="col-sm-8">
                                        <input id="email" type="text"
                                            class="form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" autocomplete="email" autofocus
                                            placeholder="Email Address" id="address-email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Phone <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="phone" type="text"
                                            class="form-control @error('phone') is-invalid @enderror" name="phone"
                                            value="{{ old('phone') }}" required autocomplete="name" autofocus
                                            placeholder="Phone Number" id="address-phone">
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Address<span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="street_address" class="form-control" required
                                            autocomplete="name" autofocus placeholder="Address">
                                        <small id="pos-address-resolver-status" class="form-text"></small>
                                        <div id="pos-address-incomplete-warning" class="mt-2" style="display:none;width:100%;box-sizing:border-box;padding:7px 10px;border:1px solid #efb45a;border-radius:5px;background:#fff8e8;color:#805400;font-size:13px;line-height:1.35;overflow-wrap:anywhere;">⚠️ জেলা-থানা সিলেক্ট হয়েছে। সঠিক ডেলিভারির জন্য বাড়ি/রোড/গ্রাম অথবা কাছাকাছি পরিচিত স্থানের তথ্য লিখুন</div>
                                        @error('street_address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12 d-none" id="pos-legacy-division-field">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Division<span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <select class=" form-control" @error('division_id') is-invalid @enderror
                                            name="division_id" value="{{ old('division_id') }}"
                                            data-show-subtext="true" data-live-search="true" name="division_id"
                                            id="division_id">
                                            <option value="">Select Division</option>
                                            @foreach (App\Models\Division::orderBy('title', 'asc')->get() as $division)
                                                <option value="{{ $division->id }}">{{ $division->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('division_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12 pos-location-fallback">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">District<span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <select class=" form-control" id="district_id"
                                            @error('district_id') is-invalid @enderror name="district_id"
                                            value="{{ old('district_id') }}" data-show-subtext="true"
                                            data-live-search="true" required="">
                                            <option value="" selected disabled>--Select District--</option>
                                        </select>

                                        @error('district_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 pos-location-fallback">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Upazila / Thana<span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <select class=" form-control" @error('upazila_id') is-invalid @enderror
                                            name="upazila_id" value="{{ old('upazila_id') }}"
                                            data-show-subtext="true" data-live-search="true" id="upazila_id"
                                            required="">
                                            <option selected disabled>--Select District First--</option>
                                        </select>
                                        @error('upazila_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group row">
									<label class="col-sm-4 col-form-label">Specific Area (Optional)</label>
                                    <div class="col-sm-8">
                                        <select class=" form-control" @error('union_id') is-invalid @enderror
                                            name="union_id" value="{{ old('union_id') }}" data-show-subtext="true"
											data-live-search="true" id="union_id">
                                            <option selected disabled>--Select Upazila First--</option>
                                        </select>
                                        @error('union_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <button class="btn btn-primary" name="save" id="customer-address-add-btn"
                                            type="submit">Add Address</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Customer address add modal end --}}



    <style type="text/css">
        /* HIDE RADIO */
        .img-radio {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* IMAGE STYLES */
        .img-radio+img {
            cursor: pointer;
        }

        /* CHECKED STYLES */
        .img-radio:checked+img {
            outline: 2px solid #f00;
        }

        .fs-14 {
            font-size: 14px !important;
        }
    </style>

    {{-- product quick view modal --}}
    <div class="modal fade" id="posVariableProductModal" tabindex="-1" role="dialog"
        aria-labelledby="posVariableProductModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="posVariableProductModalCenterTitle">Product Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="posVariableProductModalBody">

                </div>
            </div>
        </div>
    </div>
    <style type="text/css">
        .labl {
            display: block;
            /*width: 400px;*/
        }

        .labl>input {
            /* HIDE RADIO */
            visibility: hidden;
            /* Makes input not-clickable */
            position: absolute;
            /* Remove input from document flow */
        }

        .labl>input+div {
            /* DIV STYLES */
            cursor: pointer;
            border: 2px solid transparent;
            border: 1px solid #67b120;
        }

        .labl>input:checked+div {
            /* (RADIO CHECKED) DIV STYLES */
            background-color: #67b120;
            border: 1px solid #67b120;
            color: #ffff;
            font-weight: 400;
        }
    </style>
    {{-- product quick view modal --}}
    <div class="modal fade" id="shippingOptionModal" tabindex="-1" role="dialog"
        aria-labelledby="shippingOptionModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shippingOptionModalCenterTitle">Shipping Option</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="shippingOptionModalBody">

                </div>
            </div>
        </div>
    </div>

    {{-- discount modal  --}}
    <div class="modal fade" id="cartdiscountModal" tabindex="-1" role="dialog"
        aria-labelledby="cartdiscountModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartdiscountModalCenterTitle">Discount Option</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="cart_discount">
                    <div class="cart-discount-error">

                    </div>
                    <form class="" method="post" action="#" enctype="multipart/form-data"
                        id="cart_discount_form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Discount Type <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="cartdiscount_type" id="cartdiscount_type"
                                            required="">
                                            <option value="-1">-- Select Discount Type --</option>
                                            <option value="custom">Custom</option>
                                            <option value="coupon">Coupon </option>
                                            <option value="percent">Percent </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 cartdiscount_amount_area">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Discount Amount <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="cartdiscount_amount" id="cartdiscount_amount"
                                            class="form-control" required="" placeholder="Discount Amount">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 cartdiscount_unitprice_area d-none">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Unit Price <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="cartdiscount_unit_price" id="cartdiscount_unit_price"
                                            class="form-control" required="" placeholder="Unit Price">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 cartcoupon_code_area d-none">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Coupon Code <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="cartcoupon_code" id="cartcoupon_code"
                                            class="form-control" placeholder="Coupon Code">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 ">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Vat</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="vat" id="vat"
                                            class="form-control" placeholder="vat">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <input type="hidden" name="cart_id" id="cart_id" value="">
                                        <button class="btn btn-primary" id="apply-cartdiscount-btn" type="submit">Apply
                                            Discount</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- discount modal  --}}
    <div class="modal fade" id="discountModal" tabindex="-1" role="dialog"
        aria-labelledby="discountModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalCenterTitle">Discount Option</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="">
                    <div class="discount-error">

                    </div>
                    <form class="" method="post" action="#" enctype="multipart/form-data"
                        id="discount_form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Discount Type <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="discount_type" id="discount_type"
                                            required="">
                                            <option value="-1">-- Select Discount Type --</option>
                                            <option value="custom">Custom</option>
                                            <option value="coupon">Coupon </option>
                                            <option value="percent">Percent </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 discount_amount_area">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Discount Amount <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="discount_amount" id="discount_amount"
                                            class="form-control" required="" placeholder="Discount Amount">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 coupon_code_area d-none">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Coupon Code <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="coupon_code" id="coupon_code"
                                            class="form-control" placeholder="Coupon Code">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <button class="btn btn-primary" id="apply_discount_btn" type="submit">Apply
                                            Discount</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="priceUpdateModal" tabindex="-1" role="dialog"
        aria-labelledby="priceUpdateModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="priceUpdateModalCenterTitle">Price Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="cart_price_update">
                    <div class="cart-price-update-error">

                    </div>
                    <form class="" method="post" action="#" enctype="multipart/form-data"
                        id="cart_price_update_form">
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Current Price<span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="current_price" id="current_price"
                                            class="form-control" required="" placeholder="Current Price" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">New Price<span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="update_cart_product_price" id="update_cart_product_price"
                                            class="form-control" required="" placeholder="New Price" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <input type="hidden" name="cart_id" id="cart_id" value="">
                                        <button class="btn btn-primary" id="apply_cartproductprice_btn" type="submit">Apply
                                            Changes</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="posOtherInfoModal" tabindex="-1" role="dialog"
        aria-labelledby="discountModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalCenterTitle">Other Informations</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="">
                    <div class="discount-error">

                    </div>
                    <form class="" method="post" action="#" enctype="multipart/form-data"
                        id="other_info_form">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Others Charge</label>
                                    <div class="col-sm-8">
                                        <input type="number" min="0" name="other_charge" id="other_charge" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Note</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="order_note" placeholder="Order Note.." id="order_note"  rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cashPayment" tabindex="-1" role="dialog"
        aria-labelledby="discountModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalCenterTitle">Take Full Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="">Paid Amount</label>
                                <input type="number" min="0" name="modal_paid_amount" id="modal_paid_amount" class="form-control" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="">Change Amount</label>
                                <input type="number" min="0" name="modal_change_amount" id="modal_change_amount" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="">Return Change</label>
                                <input type="number" min="0" name="modal_return_change" id="modal_return_change" class="form-control" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label class=""> Is Complete?</label>
                                <select id="completed" name="completed" class="form-control">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="bg-dark rounded text-center text-light">
                                <span><b class="text-secondary">Note:</b> If you not interested to use change amount feature, click Submit Button</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary final_order_now_btn">Submit Order</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentWithMFS" tabindex="-1" role="dialog"
        aria-labelledby="discountModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalCenterTitle">Take MFS Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row" id="">
                    <div class="col-lg-6 row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="">Paid Amount</label>
                                <input type="number" min="0" name="modal_paid_amount" id="modal_paid_amount" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="">Current Due</label>
                                <input type="number" min="0" name="modal_current_due" id="modal_current_due" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="">Card / Cheque or Mobile Banking (মোবাইল ব্যাংকিং)</label>
                                <select name="" id="modal_mfs_type" class="form-control">
                                    <option value="-1">-- Select --</option>
                                    <option value="card">Card / Cheque</option>
                                    <option value="mfs">Mobile Banking / মোবাইল ব্যাংকিং</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 modal_bank_field_area d-none">
                            <div class="form-group">
                                <label class="">Card Name / Cheque Bank Name</label>
                                <input type="text" name="" id="modal_checkbank_name" class="form-control" >
                            </div>
                        </div>

                        <div class="col-md-12 modal_bank_field_area d-none">
                            <div class="form-group">
                                <label class="">Card Number / Cheque Number</label>
                                <input type="text" name="" id="modal_cheque_number" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12 modal_bank_field_area d-none">
                            <div class="form-group">
                                <label class="">Cheque Date</label>
                                <input type="date" name="" id="modal_cheque_date" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12 modal_bank_field_area d-none">
                            <div class="form-group">
                                <label class="">Cheque Diposit Date</label>
                                <input type="date" name="" id="modal_cheque_diposit_date" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12 modal_mfs_field_area d-none">
                            <div class="form-group">
                                <label class="">Sender Number</label>
                                <input type="text" name="" id="modal_sender_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12 modal_mfs_field_area d-none">
                            <div class="form-group">
                                <label class="">Mobile Banking Acc Type</label>
                                <select name="modal_fbs_account_type" id="modal_fbs_account_type" class="form-control">
                                    <option value="">Select One</option>
                                    <option value="Bkash">Bkash</option>
                                    <option value="Rocket">Rocket</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Upay">Upay</option>
                                    <option value="MCash">MCash</option>
                                    <option value="Trust Axiata Pay (Tap)">Trust Axiata Pay (Tap)</option>
                                    <option value="EasyCash">EasyCash</option>
                                    <option value="Mobile Money">Mobile Money</option>
                                    <option value="SureCash">SureCash</option>
                                    <option value="T-Cash">T-Cash</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="">Cheque / Card / Mobile Banking Diposit to</label>
                                <select name="modal_payment_method" id="modal_payment_method" class="form-control" required>
                                    <option value="">Select One</option>
                                    @foreach($mfs_and_bank as $mfs)
                                        <option value="{{ $mfs->id }}">{{ $mfs->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary final_order_now_btn">Submit Order</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="partialPaymentModal" tabindex="-1" role="dialog"
        aria-labelledby="discountModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalCenterTitle">Take Partial Payment </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="">Paid Amount</label>
                                <input type="number" min="0" name="modal_paid_amount" id="modal_paid_amount" class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="">Current Due</label>
                                <input type="number" min="0" name="modal_current_due" id="modal_current_due" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary final_order_now_btn">Submit Order</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="cashOnDeliveryModal" tabindex="-1" role="dialog"
        aria-labelledby="discountModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalCenterTitle">Take Partial Payment </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="">Shipping charge</label>
                                <input type="number" min="0" name="modal_paid_shiping_amount" id="modal_paid_shiping_amount" class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="">Shipped By</label>
                                <select name="" id="modal_shipped_by" class="form-control">
                                    <option value="pathao">Pathao</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="">Paid Amount</label>
                                <input type="number" min="0" name="modal_paid_amount" id="modal_paid_amount" class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="">Current Due</label>
                                <input type="number" min="0" name="modal_current_due" id="modal_current_due" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary final_order_now_btn">Submit Order</button>
                </div>
            </div>
        </div>
    </div>

    <div id="afftersalePrint" class="d-none"></div>

    <div class="modal fade" id="afftersalePrintModal" tabindex="-1" role="dialog"
        aria-labelledby="discountModalModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalCenterTitle">Order Summary</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="afftersalePrintModalbody">

                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-inverse btn-small" aria-hidden="true" onclick="printDiv($('#afftersalePrintModalbody'))"><i class="icon-white icon-print"></i> Print</a>&nbsp;
                    <button data-dismiss="modal" aria-hidden="true" class="btn btn-inverse btn-small"><i class="icon-white icon-remove"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

@endif

<!--View Modal -->
<div class="modal fade bd-example-modal-lg" id="productViewModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Product Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="vievModelBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--Seller quick View Modal -->
<div class="modal fade bd-example-modal-lg" id="sellerQuickViewModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Seller Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="sellerQuickViewModalBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bd-example-modal-lg" id="customerQuickViewModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Customer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="customerQuickViewModalBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade activity_log" id="activityLogViewModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Manipulated Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="activityLogViewModalBody">
            </div>
        </div>
    </div>
</div>


<div id="live_notofication"></div>


<div class="modal fade activity_log" id="manage_category_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Manage Expense Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" >
                <form action="{{ route('admin.expense.category.create') }}" class="row" method="POST">
                    @csrf
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="">Category Name</label>
                            <input type="text" name="expense_category" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <button class="btn btn-success" type="submit">Add Category</button>
                        </div>
                    </div>
                </form>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Category List</label>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\ExpenseCategory::where('is_deleted', 0)->where('is_active', 1)->get() as $row)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration}}</th>
                                        <td>{{ $row->title}}</td>
                                        <td>
                                            <a class="icon_btn text-danger" href="{{ route('admin.expense.category.delete', $row->id) }}"><i class="mdi mdi-delete"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="modal fade activity_log" id="manage_loan_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Manage Loan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" >
                <form action="" class="row" method="POST">
                    @csrf
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Loan From</label>
                            <input type="text" name="loan_from" class="form-control" placeholder="Bank name" required>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="text" name="amount" class="form-control" placeholder="50000" required="">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Interest (%)</label>
                            <input type="text" name="interest" class="form-control" placeholder="10%" required="">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Last Payment Date</label>
                            <input type="date" name="payment_last_date" class="form-control" placeholder="10%" >
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Payment Method</label>
                            <select class="form-select form-control" name="payment_method" aria-label="Default select example" required>
                                <option selected>Choose one</option>
                                @foreach(\App\Models\CurrentAsset::get() as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <button class="btn btn-success" type="submit">Add Loan</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Loan List</label>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Loan From</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Interest</th>
                                    <th scope="col">Last Payment Date</th>
                                    <th scope="col">Payment Method</th>
                                    <th scope="col">Payable</th>
                                    <th scope="col">Paid</th>
                                    <th scope="col">Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Loan::get() as $row)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration}}</th>
                                        <td>{{ $row->date}}</td>
                                        <td>{{ $row->loan_from}}</td>
                                        <td>৳ {{ $row->amount}}</td>
                                        <td>{{ $row->interest }} %</td>
                                        <td>{{ $row->payment_last_date }}</td>
                                        <td>{{ $row->asset->name ?? '' }}</td>
                                        <td>৳ {{ $row->payable ?? 0 }}</td>
                                        <td>৳ {{ $row->paid ?? 0 }}</td>
                                        <td>৳ {{ ($row->payable - $row->paid) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade activity_log" id="manage_investloan_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Pay Loan Or Investor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" >
                <form action="" class="row" method="POST">
                    @csrf
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Loan/Investor</label><br>
                            <label><input type="radio" name="loan_invest" value="Loan" required> Loan</label>
                            <label><input type="radio" name="loan_invest" value="Investor"> Invest</label>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="text" name="amount" class="form-control" placeholder="50000" required="">
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Payment Method</label>
                            <select class="form-select form-control" name="payment_method" aria-label="Default select example" required>
                                <option selected>Choose one</option>
                                @foreach(\App\Models\CurrentAsset::get() as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Loan</label>
                            <select class="form-select form-control" name="loan" aria-label="Default select example" >
                                <option selected value="0">Choose one</option>
                                @foreach(\App\Models\Loan::get() as $row)
                                    <option value="{{$row->id}}">{{ $row->loan_from }} -- {{ $row->amount }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            <label for="">Investor</label>
                            <select class="form-select form-control" name="investor" aria-label="Default select example" >
                                <option selected value="0">Choose one</option>
                                @foreach(\App\Models\Investor::get() as $row)
                                    <option value="{{$row->id}}">{{$row->name}} -- {{ (\Helper::getBusinessBalance() * $row->share) / 100 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-12">
                        <div class="form-group">
                            <button class="btn btn-success" type="submit">Make Payment</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-12">
                        <label>Payment History</label>
                    </div>
                    <div class="col-sm-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Loan/Investor</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Payment Method</th>
                                    <th scope="col">Loan/Investor Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\LoanInvestorPayment::get() as $row)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration}}</th>
                                        <td>{{ $row->date}}</td>
                                        <td>{{ $row->loan_invest }}</td>
                                        <td>৳ {{ $row->amount}}</td>
                                        <td>{{ $row->asset->name ?? '' }}</td>
                                        <td>
                                            @if($row->loan_invest == 'Loan')
                                                {{ $row->loans->loan_from }}<br>
                                                {{ $row->loans->amount }}<br>
                                                {{ $row->loans->interest }} %
                                            @else
                                                {{ $row->investors->name }}<br>
                                                {{ $row->investors->amount }}<br>
                                                {{ $row->investors->share }} %
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
