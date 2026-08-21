<p class="content_title">General</p>

<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Site Favicon</label></div>
    <div class="col-sm-9">

        <button type="button" data-image-width="32" data-image-height="32" data-input-name="favicon"
            data-input-type="single" class="btn btn-success initConcaveMedia">Select Image
        </button>

        @if ($x = Helper::getsettings('favicon'))
            <p class="selected_images_gallery">
                <span>
                    <input type="hidden" value="{{ $x }}" name="image">
                    <img src="{{ '/' . $x }}">
                    <b data-file-url="{{ $x }}" class="selected_image_remove">X</b>
                </span>
            </p>
        @endif


    </div>
</div>

<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Payment Method
            Image</label></div>
    <div class="col-sm-9">


        <button type="button" data-image-width="405" data-image-height="82" data-input-name="accepted_payment_image"
            data-input-type="single" class="btn btn-success initConcaveMedia">Select Image
        </button>

        @if ($x = Helper::getsettings('accepted_payment_image'))
            <p class="selected_images_gallery">
                <span>
                    <input type="hidden" value="{{ $x }}" name="image">
                    <img src="{{ '/' . $x }}">
                    <b data-file-url="{{ $x }}" class="selected_image_remove">X</b>
                </span>
            </p>
        @endif

    </div>
</div>

@php $default_currencyList =  Helper::getsettings('default_currency'); @endphp

<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Default Currency</label>
    </div>
    <div class="col-sm-9">
        <select class="form-control" name="default_currency">
            @foreach (App\Models\Currency::orderBy('id', 'desc')->get() as $currency)
                <option value="{{ $currency->id }}" @if ($default_currencyList == $currency->id) selected @endif>
                    {{ $currency->title }}</option>
            @endforeach
        </select>
    </div>
</div>


<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Phone Number</label></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="phone_number"
            value="{{ Helper::getsettings('phone_number') }}">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Welcome Text</label></div>
    <div class="col-sm-9">
        <input type="text" class="form-control " name="welcome_text"
            value="{{ Helper::getsettings('welcome_text') }}">
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Copyright Text</label>
    </div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="copyright_text"
            value="{{ Helper::getsettings('copyright_text') }}">
    </div>
</div>



<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Flagship Banner</label>
    </div>
    <div class="col-sm-9">


        <button type="button" data-image-width="290" data-image-height="45" data-input-name="flagship_banner"
            data-input-type="single" class="btn btn-success initConcaveMedia">Select Image
        </button>

        @if ($x = Helper::getsettings('flagship_banner'))
            <p class="selected_images_gallery">
                <span>
                    <input type="hidden" value="{{ $x }}" name="image">
                    <img src="{{ '/' . $x }}">
                    <b data-file-url="{{ $x }}" class="selected_image_remove">X</b>
                </span>
            </p>
        @endif

    </div>
</div>

@php
$vendors = DB::table('admins')
    ->select('admins.id', 'shop_info.name')
    ->join('shop_info', 'shop_info.seller_id', '=', 'admins.id')
    ->get();

$settings_ids = Helper::getsettings('flagship_ids');
$ids = explode(',', $settings_ids);
@endphp
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Flagship Stores</label>
    <div class="col-sm-9">
        <select name="flagship_ids[]" data-max-options="20" class="selectpicker form-control" data-show-subtext="true"
            data-live-search="true" multiple>
            @foreach ($vendors as $key => $seller)
                <option value="{{ $seller->id }}" @if (in_array($seller->id, $ids)) selected @endif>
                    {{ $seller->name ?? '' }}</option>
            @endforeach
        </select>
    </div>
</div>


<div class="form-group row">
    <label class="col-sm-3 col-form-label">Default Branch</label>
    <div class="col-sm-9">
        <select name="default_branch_id" data-max-options="20" class="selectpicker form-control" data-show-subtext="true"
            data-live-search="true" >
            @foreach (\App\Models\Admins::where('is_deleted', 0)->where('status', 1)->where('is_branch', 1)->get() as $branch)
                <option value="{{ $branch->id }}" @if (Helper::getsettings('default_branch_id') == $branch->id) selected @endif>
                    {{ $branch->shopinfo->name ?? '' }}</option>
            @endforeach
        </select>
    </div>
</div>

@php

$return_policyList = Helper::getsettings('return_policy');
$terms_of_useList = Helper::getsettings('terms_of_use');
$privacy_policyList = Helper::getsettings('privacy_policy');
$warranty_policyList = Helper::getsettings('warranty_policy');
$pages = \DB::table('pages')
    ->where('status', 1)
    ->get();

@endphp


<!-- Pages links -->
<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Return Policy Page</label>
    </div>
    <div class="col-sm-9">
        <select class="form-control" name="return_policy">
            @foreach ($pages as $page)
                <option value="{{ $page->slug }}" @if ($return_policyList == $page->slug) selected @endif>
                    {{ $page->title }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Terms Of Use Page</label>
    </div>
    <div class="col-sm-9">

        <select class="form-control" name="terms_of_use">
            @foreach ($pages as $page)
                <option value="{{ $page->slug }}" @if ($terms_of_useList == $page->slug) selected @endif>
                    {{ $page->title }}</option>
            @endforeach
        </select>

    </div>
</div>
<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Warranty Policy
            Page</label></div>
    <div class="col-sm-9">

        <select class="form-control" name="warranty_policy">
            @foreach ($pages as $page)
                <option value="{{ $page->slug }}" @if ($warranty_policyList == $page->slug) selected @endif>
                    {{ $page->title }}</option>
            @endforeach
        </select>

    </div>
</div>

<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Privacy Policy
            Page</label></div>
    <div class="col-sm-9">

        <select class="form-control" name="privacy_policy">
            @foreach ($pages as $page)
                <option value="{{ $page->slug }}" @if ($privacy_policyList == $page->slug) selected @endif>
                    {{ $page->title }}</option>
            @endforeach
        </select>

    </div>
</div>


<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Meta Title</label></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="site_meta_title"
            value="{{ Helper::getsettings('site_meta_title') }}">
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Meta Keyword</label></div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="site_meta_keyword"
            value="{{ Helper::getsettings('site_meta_keyword') }}">
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Meta Description</label>
    </div>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="site_meta_description"
            value="{{ Helper::getsettings('site_meta_description') }}">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Payment/Bank Details </label>
    </div>
    <div class="col-sm-9">
        <textarea type="text" name="company_bank_information" class="textEditor form-control"
                placeholder="Description.."> {{ Helper::getSettings('company_bank_information') }} </textarea>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Loyalty Point Validity (Days) </label>
    </div>
    <div class="col-sm-9">
        <input type="number" min="0" name="loyalty_point_validity_days" class="form-control" value="{{ Helper::getSettings('loyalty_point_validity_days') }}">
    </div>
</div>

<div class="form-group row">
    <div class="col-sm-3"><label class="col-form-label">Low Stock Alert</label>
    </div>
    <div class="col-sm-9">
        <input type="number" min="0" name="low_stock_alert" class="form-control" value="{{ Helper::getSettings('low_stock_alert') }}">
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Social Login (ON/OFF)</label>
    <div class="col-sm-9">
        <div class="form-check form-check-flat">
            <label class="form-check-label">
                <input type="hidden" name="social_login" value="0">
                <input name="social_login" type="checkbox" class="form-check-input" value="1"
                    @if (Helper::getsettings('social_login') == 1) checked @endif>Eanbled<i class="input-helper"></i><i
                    class="input-helper"></i></label>
        </div>
    </div>
</div>


<!-- Partil Payment start -->
<p class="content_title">Partial Payment</p>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Enable</label>
    <div class="col-sm-9">
        <div class="form-check form-check-flat">
            <label class="form-check-label">
                <input type="hidden" name="partial_payment_enable" value="0">
                <input name="partial_payment_enable" type="checkbox" class="form-check-input" value="1"
                    @if (Helper::getsettings('partial_payment_enable') == 1) checked @endif>Eanbled<i class="input-helper"></i><i
                    class="input-helper"></i></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-3 col-form-label">Fixed / Percentage Type</label>
    <div class="col-sm-9">
        <select name="partial_payment_type" class="selectpicker form-control">
            <option data-tokens="fixed" value="fixed" @if (Helper::getSettings('partial_payment_type') == 'fixed') selected @endif> Fixed</option>
            <option data-tokens="percentage" value="percentage" @if (Helper::getSettings('partial_payment_type') == 'percentage') selected @endif> Percentage</option>
        </select>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Fixed / Percentage Amount</label>
    <div class="col-sm-9">
        <input type="text" min="0" name="partial_payment_fixed_or_percentage_amount" class="form-control" value="{{ Helper::getSettings('partial_payment_fixed_or_percentage_amount') }}">
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Minimum Amount</label>
    <div class="col-sm-9">
        <input type="text" min="0" name="partial_payment_minimum_amount" class="form-control" value="{{ Helper::getSettings('partial_payment_minimum_amount') }}">
    </div>
</div>
<!-- Partil Payment End -->

<p class="content_title">Checkout Offer</p>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Enable Offer</label>
    <div class="col-sm-9">
        <div class="form-check form-check-flat">
            <label class="form-check-label">
                <input type="hidden" name="checkout_offer_enabled" value="0">
                <input name="checkout_offer_enabled" type="checkbox" class="form-check-input" value="1"
                    @if (Helper::getsettings('checkout_offer_enabled') == 1) checked @endif>Enabled<i class="input-helper"></i>
            </label>
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Discount Percentage</label>
    <div class="col-sm-9">
        <div class="input-group">
            <input type="number" min="0" max="100" step="0.01" name="checkout_offer_discount_percent" class="form-control"
                value="{{ Helper::getSettings('checkout_offer_discount_percent') ?? 0 }}">
            <div class="input-group-append"><span class="input-group-text">%</span></div>
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Offer Message</label>
    <div class="col-sm-9">
        <input type="text" name="checkout_offer_message" class="form-control"
            value="{{ Helper::getSettings('checkout_offer_message') ?: 'অফার চলছে — আপনার প্রয়োজন হলে এখনই কনফার্ম করুন' }}">
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Countdown Minutes</label>
    <div class="col-sm-9">
        <input type="number" min="1" name="checkout_offer_countdown_minutes" class="form-control"
            value="{{ Helper::getSettings('checkout_offer_countdown_minutes') ?: 60 }}">
    </div>
</div>

<!-- Partil Payment start -->
<p class="content_title">Shipping Options</p>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Enable</label>
    <div class="col-sm-9">
        <div class="form-check form-check-flat">
            <label class="form-check-label">
                <input type="hidden" name="default_shipping_enable" value="0">
                <input name="default_shipping_enable" type="checkbox" class="form-check-input" value="1"
                    @if (Helper::getsettings('default_shipping_enable') == 1) checked @endif>Eanbled<i class="input-helper"></i><i
                    class="input-helper"></i></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-3 col-form-label">Pathao Shipping Enable</label>
    <div class="col-sm-9">
        <div class="form-check form-check-flat">
            <label class="form-check-label">
                <input type="hidden" name="pathao_shipping_enable" value="0">
                <input name="pathao_shipping_enable" type="checkbox" class="form-check-input" value="1"
                    @if (Helper::getsettings('pathao_shipping_enable') == 1) checked @endif>Eanbled<i class="input-helper"></i><i
                    class="input-helper"></i></label>
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-3 col-form-label">Inside Origin</label>
    <div class="col-sm-9">
        <input type="text" min="0" name="default_shipping_inside_origin" class="form-control" value="{{ Helper::getSettings('default_shipping_inside_origin') }}">
    </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Outside Origin</label>
    <div class="col-sm-9">
        <input type="text" min="0" name="default_shipping_outside_origin" class="form-control" value="{{ Helper::getSettings('default_shipping_outside_origin') }}">
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-3 col-form-label">Increase /KG</label>
    <div class="col-sm-9">
        <input type="text" min="0" name="default_shipping_increase_per_kg" class="form-control" value="{{ Helper::getSettings('default_shipping_increase_per_kg') }}">
    </div>
</div>

@php
$districts = DB::table('districts')->get();

$settings_default_shipping_inside_location_ids = Helper::getsettings('default_shipping_inside_location');
$default_shipping_inside_location_ids = explode(',', $settings_default_shipping_inside_location_ids);
@endphp
<div class="form-group row">
    <label class="col-sm-3 col-form-label">Inside Location</label>
    <div class="col-sm-9">
        <select name="default_shipping_inside_location[]" data-max-options="20" class="selectpicker form-control" data-show-subtext="true"
            data-live-search="true" multiple>
            @foreach ($districts as $key => $district)
                <option value="{{ $district->id }}" @if (in_array($district->id, $default_shipping_inside_location_ids)) selected @endif>
                    {{ $district->title ?? '' }}</option>
            @endforeach
        </select>
    </div>
</div>
<!-- Partil Payment End -->

<div class="form-group row">
    <label class="col-sm-3 col-form-label">Default POS Print</label>
    <div class="col-sm-9">
        <select name="default_pos_print_invoice[]" class="form-control" >
            <option value="pos" @if (Helper::getsettings('default_pos_print_invoice') == 'pos') selected @endif>Pos</option>
            <option value="courier" @if (Helper::getsettings('default_pos_print_invoice') == 'courier') selected @endif>Courier</option>
            <option value="invoice" @if (Helper::getsettings('default_pos_print_invoice') == 'invoice') selected @endif>Invoice</option>
        </select>
    </div>
</div>
