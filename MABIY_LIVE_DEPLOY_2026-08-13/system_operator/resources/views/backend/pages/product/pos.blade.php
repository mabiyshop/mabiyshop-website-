@extends('backend.layouts.master')
@section('title','POS - '.config('concave.cnf_appname'))
@section('content')
<style>
    .overlay{
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 999;
        background: rgb(24 23 23 / 80%) url("/loading.gif") center no-repeat;
    }
    
    /* Turn off scrollbar when body element has the loading class */
    body.loading{
        overflow: hidden;   
    }
    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay{
        display: block;
    }
</style>
<section class="section-content padding-y-sm bg-default pos_section">
    <div class="container-fluid mt-50 mb-50">
        <div class="row">
            <div class="col-md-6">
                <div class="product_search">
                    <i class="mdi mdi-magnify"></i>
                    <input type="text" placeholder="Search Product..." name="serach_product_field" id="serach_product_field">
                </div> 
            </div>
            <div class="col-md-3">
                <div class="product_search">
					<input type="search" class="form-control mb-1" id="customer_search" placeholder="Search customer by name or phone">
                    <div class="input-group">
                        <select class="form-control selectpicker" data-live-search="true"  name="select_user" id="select_user" aria-describedby="button-addon2">
                            
                            
                        </select>
                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#customerAddModal" title="Add Customer"><i class="mdi mdi-plus"></i></button>
                    </div>
                    
                </div>
            </div>
            {{-- <div class="col-md-1 m-0"> 
                <button style="border-radius: 7px;" class="btn-dark" data-toggle="modal" data-target="#customerAddModal" title="Add Customer"><i style="font-size: 26px;" class="mdi mdi-account-plus"></i></button>
            </div> --}}

            <div class="col-md-3">
                <div class="product_search">
                    <input type="hidden" name="payment_method" id="payment_method" value="cash_on_delivery">
                   

                    <div class="input-group">
                        <select class="form-control selectpicker" name="shipping_address_id" id="shipping_address_id">
                            <option value="-1">-- Select Shipping Address--</option>
                            <optgroup label="Customer Address" id="customer_address_area"></optgroup>
                            <optgroup label="Pick Point Address" id="pickpoint_address">
                                @foreach($pickpoint_address as $pickpoint)
                                    <option data-address-type="pickpoint" value="{{$pickpoint->id}}">{{ $pickpoint->title ?? '' }} - {{ $pickpoint->division->title ?? '' }} -> {{ $pickpoint->district->title ?? '' }} -> {{ $pickpoint->upazila->title ?? '' }} -> {{ $pickpoint->union->title ?? '' }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#customerShippingAddressAddModal" title="Add Address"><i class="mdi mdi-plus"></i></button>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12 p-1">
				<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">Top Selling</button>
					</li>
                    <li class="nav-item ml-1" role="presentation">
						<button class="nav-link " id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="false">All Products</button>
					</li>
				</ul>
				<div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
						<div class="row" id="">
							@foreach($top_selling_products as $product)
								<div class="col-sm-6 col-12 col-lg-4 col-xl-4 mb-0 p-1">
									<div class="card mb-0">
										<div class="card-body">
											<div class="card-img-actions">
									<img src="{{'/media/thumbnail/'.$product->default_image}}" class="card-img img-fluid" width="96" height="350" loading="lazy" alt="">
											</div>
										</div>
										<div class="card-body bg-light text-center details_card">
											<div class="details_section">
												<h6 class="font-weight-semibold mb-0">
													<a href="#" class="text-primary mb-0 productViewBtn" id="{{ $product->id}}" data-abc="true">{{$product->title}}</a>
												</h6>
												@if($product->product_type != 'variable')
													<p class="mb-0" >SKU: {{$product->sku}}</p>
												@endif
												<small class="text-danger" >Seller: {{ $product->name ?? '' }}</small>
											</div>
											<p class="mb-2 font-weight-semibold">{{ 'BDT '.$product->pos_price }}</p>
											
												@if($product->product_type == 'simple')
													<button type="button" style="padding: 3px 5px;" class="btn btn-primary simple_add_to_cart" data-product-id="{{$product->id}}"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>
												@elseif($product->product_type == 'digital' || $product->product_type == 'service')
													<button type="button" style="padding: 3px 5px;" class="btn btn-primary digital_add_to_cart" data-product-type="{{$product->product_type}}" data-product-id="{{$product->id}}"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>
												@elseif($product->product_type == 'variable')
													<button type="button" style="padding: 3px 5px;" class="btn btn-primary varient_add_to_cart" data-product-id="{{$product->id}}"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>
												@endif
											
										</div>
									</div>
								</div>
							@endforeach
						</div>
						<div class="pos_pagination_area" id="">
							<div class="pos_pagination justify-content-center d-flex mt-4"> {{$top_selling_products->links()}}</div>
						</div>
					</div>
					<div class="tab-pane fade " id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
						<div class="row" id="product_list_area">
							@foreach($products as $product)
								<div class="col-sm-6 col-12 col-lg-4 col-xl-4 mb-0 p-1">
									<div class="card mb-0">
										<div class="card-body">
											<div class="card-img-actions">
									<img src="{{'/media/thumbnail/'.$product->default_image}}" class="card-img img-fluid" width="96" height="350" loading="lazy" alt="">
											</div>
										</div>
										<div class="card-body bg-light text-center details_card">
											<div class="details_section">
												<h6 class="font-weight-semibold mb-0">
													<a href="#" class="text-primary mb-0 productViewBtn" id="{{ $product->id}}" data-abc="true">{{$product->title}}</a>
												</h6>
												@if($product->product_type != 'variable')
													<p class="mb-0" >SKU: {{$product->sku}}</p>
												@endif
												<small class="text-danger" >Seller: {{ $product->seller->shopinfo->name ?? '' }}</small>
											</div>
									<p class="mb-2 font-weight-semibold">{{ 'BDT '.$product->pos_price }}</p>
											
												@if($product->product_type == 'simple')
													<button type="button" style="padding: 3px 5px;" class="btn btn-primary simple_add_to_cart" data-product-id="{{$product->id}}"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>
												@elseif($product->product_type == 'digital' || $product->product_type == 'service')
													<button type="button" style="padding: 3px 5px;" class="btn btn-primary digital_add_to_cart" data-product-type="{{$product->product_type}}" data-product-id="{{$product->id}}"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>
												@elseif($product->product_type == 'variable')
													<button type="button" style="padding: 3px 5px;" class="btn btn-primary varient_add_to_cart" data-product-id="{{$product->id}}"><i class="mdi mdi-cart mr-1"></i> Add to cart</button>
												@endif
											
										</div>
									</div>
								</div>
							@endforeach
						</div>
						<div class="pos_pagination_area" id="">
							<div class="pos_pagination justify-content-center d-flex mt-4"> {{$products->links()}}</div>
						</div>
					</div>
					
				</div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="card mt-1" id="cart_history">
                    
                </div>
            </div>

            <div class="col-md-2 col-sm-12 p-1">
                <div class="card" id="order_total_history">
                    <div class="history-elements">
                        <div>
                            <span>Subtotal: </span>
                        </div>
                        <div>
                            BDT <span id="subtotal">0</span>
                        </div>
                    </div>
                    <div class=" d-none">
                        <div>
                            <span>Grocery Shipping Cost: </span>
                        </div>
                        <div>
                            <input type="hidden" id="grocery_shipping_cost"  />
                            BDT <span id="grocery_shipping_cost_text">0</span>
                        </div>
                    </div>
                    <div class="history-elements">
                        <div>
                            {{-- <span>Shipping Cost: <br><button class="btn btn-sm btn-info" id="calculate_shipping_btn">Calculate</button> <i class="mdi mdi-pencil-box-outline" id="shipping_cost_btn"></i></span> --}}
                            <span>Shipping Cost: <br><button class="btn btn-sm btn-info" id="calculate_shipping_btn">Calculate</button> </span>
                        </div>
                        <div>
                            BDT <span id="shipping_cost">0</span>
                        </div>
                    </div>
                    <div class="history-elements">
                        <div>
                            <span>VAT/TAX: </span>
                        </div>
                        <div>
                            BDT <span id="vat_tax">0</span>
                        </div>
                    </div>
                    <div class="history-elements">
                        <div>
                            <span>Other Discount: <i class="mdi mdi-plus plus-icone" id="discount_modal_btn"></i></span>
                        </div>
                        <div>
                            BDT <span id="discount">0</span>
                        </div>
                    </div>
                    <div class="history-elements">
                        <div>
                            <span>Order Note: <i class="mdi mdi-plus plus-icone" id="othersInfoModelBtn"></i></span>
                        </div>
                        <div>
                            <input type="hidden" name="otherNote" id="otherNote">
                            <input type="hidden" name="totalOtherCharge" id="totalOtherCharge">
                            <span id="total_other_charge"></span>
                        </div>
                    </div>
                    <div class="history-elements bg-danger pl-1 pr-2 text-light">
                        <div>
                            <span>Previous Due: </span>
                        </div>
                        <div>
                            BDT <span id="previous_dues">0</span>
                        </div>
                    </div>
                    <div class="history-elements">
                        <div>
                            <span>Total Payable: </span>
                        </div>
                        <div>
                            BDT <span id="grand_total">0</span>
                        </div>
                    </div>
                    <input type="hidden" name="hidden_paid_amount" id="hidden_paid_amount" value="0">
                    <input type="hidden" name="hidden_payment_method" id="hidden_payment_method" value="0">
                    <hr>
                    <div class="history-elements">
                        <button class="btn btn-info pos-btn w-100 order_with_cash" >Cash</button>
                    </div>
                    <div class="history-elements">
                        <button class="btn btn-warning pos-btn w-100 order_partial_payment"> Partial Payment</button>
                    </div>
                    <div class="history-elements">
                        <button class="btn btn-success pos-btn w-100 cashon_order"> Cash On Delivery </button>
                    </div>
                    <div class="history-elements">
                        <button class="btn btn-danger pos-btn w-100 order_with_mfs"> MFS or Card</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="overlay"></div>
</section>

@push('footer')
{{-- <script src="{{ asset('backend/assets/js/pos.js')}}"></script> --}}
<script type="text/javascript">
    function getCustomerList(search = '') {
        let user =  localStorage.getItem("current_user_id");
        jQuery.ajax({
            type: "GET",
            url: "{{ route('admin.pos.get.customers') }}",
			data: {current_user: user, search: search},
            dataType: 'html',
            success: function (response) {
                jQuery('#select_user').html('<option value="-1">-- Select Customer --</option>' + response);
                jQuery("#select_user").selectpicker('refresh');
				if (!search) {
					updateUIWithUser(localStorage.getItem("current_user_id"));
				}
            },
            error: function (xhr, textStatus, errorThrown) {
                // Handle error if needed
                console.error("Error fetching customer list:", errorThrown);
            }
        });
    }
    // Call getCustomerList initially
    getCustomerList();
	let customerSearchTimer = null;
	jQuery(document).on('input', '#customer_search', function () {
		clearTimeout(customerSearchTimer);
		const search = this.value.trim();
		customerSearchTimer = setTimeout(function () {
			getCustomerList(search);
		}, 300);
	});

    function updateUIWithUser(userId) {
        $('#select_user').val(userId).trigger('change');
    }

    // updateUIWithUser(localStorage.getItem("current_user_id"));
    jQuery(document).on("click", "#customerAddModal button[type='submit']", function (e) {
        e.preventDefault();
        let name = $('#customerAddModal input[name="name"]').val();
        let phone = $('#customerAddModal input[name="phone"]').val();
        let email = $('#customerAddModal input[name="email"]').val();

        let form = document.getElementById('customerAddForm');
        var formData = new FormData(form);

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: jQuery('#customerAddForm').attr('action'),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                let current_user_id = response.user_id;
                localStorage.setItem("current_user_id", current_user_id);
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: true,
                    timer: 2000
                });
                localStorage.setItem("new_customer_name", name);
                localStorage.setItem("new_customer_phone", phone);
                localStorage.setItem("new_customer_email", email);
                $('#customerAddModal').modal('hide');

                $('#customerShippingAddressAddModal input[name="name"]').val(localStorage.getItem("new_customer_name"));
                $('#customerShippingAddressAddModal input[name="email"]').val(localStorage.getItem("new_customer_email"));
                $('#customerShippingAddressAddModal input[name="phone"]').val(localStorage.getItem("new_customer_phone"));

                // updateUIWithUser(current_user_id);
                getCustomerList();
            },
            error: function (xhr) {
                let errorMessage = '';
                $.each(xhr.responseJSON.errors, function (key, value) {
                    errorMessage += ('' + value + '<br>');
                });
                $('#customerAddModal .server_side_error').empty();
                $('#customerAddModal .server_side_error').html('<div class="alert alert-danger" role="alert">' + errorMessage + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            },
        });
    });

   
    $('#customerShippingAddressAddModal input[name="name"]').val(localStorage.getItem("new_customer_name"));
    $('#customerShippingAddressAddModal input[name="email"]').val(localStorage.getItem("new_customer_email"));
    $('#customerShippingAddressAddModal input[name="phone"]').val(localStorage.getItem("new_customer_phone"));

    $("#toggle_sidebar").trigger("click");
    // Search Product 
		let productSearchTimer = null;
		let productSearchRequest = null;
        jQuery(document).on("input", "#serach_product_field", function(){
			clearTimeout(productSearchTimer);
            var search_text = jQuery(this).val().trim();
            var page = 1;
            if(search_text != ''){
				productSearchTimer = setTimeout(function () {
					if (productSearchRequest) {
						productSearchRequest.abort();
					}
					productSearchRequest = jQuery.ajax({
                    type : "GET",
					url : "/admin/pos/serach/product?search_text="+encodeURIComponent(search_text)+"&page="+page,
                    success: function(response) {
                        jQuery('#product_list_area').empty();
                        jQuery('#product_list_area').html(response);
                        jQuery('.pos_pagination_area').hide();
                    }
					});
				}, 300);
            }
        });


    // auto select and function 
    // $("#shipping_address_id option:selected").prop("selected", false);
    // getCart(localStorage.getItem("current_user_id"));
    // // jQuery('#select_user').val(localStorage.getItem("current_user_id")).trigger('change');
    // if (localStorage.getItem("address_type") == 'pickpoint') {
    //     jQuery("#pickpoint_address optgroup option:first").prop('selected',true);
    // }else{
    //     jQuery("#pickpoint_address option[value='"+localStorage.getItem("current_user_address_id")+"']").prop('selected',true);
    // }
    // jQuery('#payment_method').val(localStorage.getItem("current_user_payment_method")).trigger('change');


    // Pagination 
        jQuery(document).on('click','.dynamic_pagination .page-item .page-link',function(e){
            e.preventDefault();
            var search_text = jQuery('#serach_product_field').val();
            var targetUrl = jQuery(this).attr('href');
            if(search_text != ''){
                jQuery.ajax({
                    type : "GET",
                    url : targetUrl,
                    success: function(response) {
                        jQuery('#product_list_area').empty();
                        jQuery('#product_list_area').html(response);
                        jQuery('.pos_pagination_area').hide();
                    }
                });
            }
        });
    // Quick View product
        jQuery(document).on('click','.productViewBtn',function(e){
            e.preventDefault();
            var id = jQuery(this).attr('id');
            $.ajax({
                url: "{{  url('/admin/products/view/') }}/"+id,
                type: "GET",
                dataType: "json",
                success: function (data) {
                   var em = jQuery('#vievModelBody').empty();
                   jQuery('#vievModelBody').html(data);
                   jQuery('#productViewModal').modal('show');
                }
            })
        });
    // Calculate grand total 
        function getGrandTotal(){
            var subtotal = Number(jQuery('#subtotal').text());
            var shipping_cost = Number(jQuery('#shipping_cost').text());
            var vat_tax = Number(jQuery('#vat_tax').text());
            var discount = Number(jQuery('#discount').text());
            var other_charge = Number(jQuery('#total_other_charge').text());
            var previous_dues = Number(jQuery('#previous_dues').text());
            var grand_total = Number(subtotal + shipping_cost + vat_tax + other_charge + previous_dues - discount);
            jQuery('#grand_total').text(grand_total);
        }
    //shipping cost calculation 
        function getShippingCost(customer_id, address_type, address){
            $("body").addClass("loading"); 
            $.ajax({
                url: "{{  url('/admin/pos/get/shipping/cost') }}",
                type: "POST",
                data:{
                    customer_id:customer_id,
                    address_type:address_type,
                    address:address
                },
                dataType: "JSON",
                success: function (response) {
				   if (response.status !== 1) {
					   Swal.fire({
						   icon: 'error',
						   title: response.message || 'Unable to calculate delivery charge. Please try again.',
					   });
					   $("body").removeClass("loading");
					   return;
				   }
                   jQuery('#shipping_cost').empty();
                   jQuery('#shipping_cost').html(response.shipping_cost);
                   jQuery('#grocery_shipping_cost').empty();
                   jQuery('#grocery_shipping_cost').val(response.grocery_shipping_cost);
                   jQuery('#grocery_shipping_cost_text').empty();
                   jQuery('#grocery_shipping_cost_text').text(response.grocery_shipping_cost);
                   getGrandTotal();
                   $("body").removeClass("loading");
                }
            })
        }
    // Get Cart list
        function getCart(customer_id){
            var address = localStorage.getItem("current_user_address_id");
            var address_type = localStorage.getItem("address_type");
            if(customer_id){
                $.ajax({
                    url: "{{  url('/admin/pos/get/cart/') }}/"+customer_id,
                    type: "GET",
                    data: {customer_id: customer_id},
                    dataType: "json",
                    success: function (response) {
                    jQuery('#cart_history').empty();
                    jQuery('#cart_history').html(response.html);
                    jQuery('#subtotal').html(response.subtotal);
                    jQuery('#shipping_cost').html(response.shipping_cost);
                    jQuery('#vat_tax').html(response.vat);
                    jQuery('#discount').html(response.discount);
                    jQuery('#previous_dues').html(response.balance);
                    //    getShippingCost(customer_id,address_type, address);
                    getGrandTotal();
                    }
                })
            }
        }
        jQuery(document).on('click','#calculate_shipping_btn',function(e){
            e.preventDefault();
            let customer = jQuery('#select_user').val();
            let address = localStorage.getItem("current_user_address_id");
            let address_type = localStorage.getItem("address_type");
            getShippingCost(customer,address_type, address);
        });
    //simple add to cart
        jQuery(document).on("click", ".simple_add_to_cart", function(){
            var customer = jQuery('#select_user').val();
            var product_id = jQuery(this).attr("data-product-id");
			var address = jQuery('#shipping_address_id').val();
            var address_type = jQuery('#shipping_address_id').find('option:selected').attr('data-address-type');
            var qty = 1;
            if (customer == -1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Select a customer first!',
                    showConfirmButton: true,
                    timer: 1500
                })
            }else{
                jQuery.ajax({
                    type : "POST",
                    url : "/admin/pos/simple/add-to-cart",
                    data : {user_id: customer, product_id: product_id, qty:qty},
                    success: function(response) {
                        if (response.status == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                showConfirmButton: true,
                                timer: 1500
                            })
                        }else{
                            getCart(customer);
                            new Audio('/uploads/beep-07a.mp3').play();  
							getCustomerShippingOption(customer,address,address_type);
                            Swal.fire({
                                icon: 'success',
                                title: 'Added to cart!',
                                showConfirmButton: true,
                                timer: 1500
                            })
                        }
                    }
                });
            }
        });
    //digital add to cart
        jQuery(document).on("click", ".digital_add_to_cart", function(){
            var customer = jQuery('#select_user').val();
            var product_id = jQuery(this).attr("data-product-id");
			var address = jQuery('#shipping_address_id').val();
            var address_type = jQuery('#shipping_address_id').find('option:selected').attr('data-address-type');
            var qty = 1;
            let product_type = $(this).attr('data-product-type');
            if (customer == -1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Select a customer first!',
                    showConfirmButton: true,
                    timer: 1500
                })
            }else{
                if (product_type == 'service') {
                    Swal.fire({
                        title: 'Service Informations!',
                        icon: 'info',
                        html:'<div class="form-group">'+
                                '<label>When do you want to take service from us?*</label>'+
                                '<input type="date" name="service_date" id="service_date" class="form-control" min="2022-11-29">'+
                            '</div>'+
                            '<div class="form-group">'+
                                '<label>Select your prefer time, expert will arrive by your selected time *</label>'+
                                '<select name="service_time" id="service_time" class="form-control"><option value="10-11am">10-11 am</option> <option value="11-12pm">11-12 pm</option> <option value="12-1pm">12-1 pm</option> <option value="1-2pm">1-2 pm</option> <option value="2-3pm">2-3 pm</option> <option value="3-4pm">3-4 pm</option> <option value="4-5pm">4-5 pm</option> <option value="5-6pm">5-6 pm</option> <option value="6-7pm">6-7 pm</option> <option value="7-8pm">7-8 pm</option></select>'+
                            '</div>',
                        showCancelButton: true,
                        confirmButtonColor: '#7AB001',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Add To Cart'
                        
                    })
                    .then((result) => {
                        if (result.isConfirmed) {
                            let service_date = jQuery("#service_date").val();
                            let service_time = jQuery("#service_time").val();
                            jQuery.ajax({
                                type : "POST",
                                url : "/admin/pos/digital/add-to-cart",
                                data : {user_id: customer, product_id: product_id, qty:qty,service_date:service_date,service_time:service_time},
                                success: function(response) {
                                    if (response.status == 0) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            showConfirmButton: true,
                                            timer: 1500
                                        })
                                    }else{
                                        getCart(customer);
                                        getCustomerShippingOption(customer,address,address_type);
                                        new Audio('/uploads/beep-07a.mp3').play();  
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Added to cart!',
                                            showConfirmButton: true,
                                            timer: 1500
                                        })
                                    }
                                }
                            });
                        }
                    })
                }else{
                    jQuery.ajax({
                        type : "POST",
                        url : "/admin/pos/digital/add-to-cart",
                        data : {user_id: customer, product_id: product_id, qty:qty},
                        success: function(response) {
                            if (response.status == 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    showConfirmButton: true,
                                    timer: 1500
                                })
                            }else{
                                getCart(customer);
                                getCustomerShippingOption(customer,address,address_type);
                                new Audio('/uploads/beep-07a.mp3').play();  
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Added to cart!',
                                    showConfirmButton: true,
                                    timer: 1500
                                })
                            }
                        }
                    });
                }
                
            }
        });
    //variable add to cart
        jQuery(document).on("submit", "#variable_product_form", function(e){
            e.preventDefault();
            var fromData = jQuery(this).serialize();
            var customer = jQuery('#select_user').val();
            var variable_sku = jQuery('.variable_generate_sku').text();
            var product_id = jQuery(this).find('.variable_final_add_to_cart').attr("data-product-id");
            var qty = jQuery('#variable_qty').val();
			var address = jQuery('#shipping_address_id').val();
            var address_type = jQuery('#shipping_address_id').find('option:selected').attr('data-address-type');
            fromData = fromData+"&user_id="+customer+"&product_id="+product_id+"&qty="+qty+"&variable_sku="+variable_sku;
            if (customer == -1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Select a customer first!',
                    showConfirmButton: true,
                    timer: 1500
                })
            }else{
                jQuery.ajax({
                    type : "POST",
                    url : "/admin/pos/variable/add-to-cart",
                    data : fromData,
                    success: function(response) {
                        if (response.status == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                showConfirmButton: true,
                                timer: 1500
                            })
                        }else{
                            getCart(customer);
                            new Audio('/uploads/beep-07a.mp3').play();  
                            Swal.fire({
                                icon: 'success',
                                title: 'Added to cart!',
                                showConfirmButton: true,
                                timer: 1500
                            });
                            jQuery('#posVariableProductModal').modal('hide');
							getCustomerShippingOption(customer,address,address_type);
                        }
                    }
                });
            }
        });
    //variable product modal
        jQuery(document).on("click", ".varient_add_to_cart", function(){
            var id = jQuery(this).attr("data-product-id");
			var address = jQuery('#shipping_address_id').val();
            $.ajax({
                url: "{{  url('/admin/pos/variable/product/') }}/"+id,
                type: "GET",
                dataType: "html",
                success: function (response) {
                   jQuery('#posVariableProductModalBody').empty();
                   jQuery('#posVariableProductModalBody').html(response);
                   jQuery('#posVariableProductModal').modal('show');
                }
            })
        });
    //variable sku generate from radio
        jQuery(document).on("change", ".variable_option_radio", function(){
            let generated_sku = $('.variable_generate_sku').text();
            let current_sku = $(this).attr('data-variable-sku');
            if (generated_sku != '') {
                generated_sku = generated_sku+' '+current_sku;
            }else{
                generated_sku = current_sku;
            }
            $('.variable_generate_sku').text(generated_sku);
        })
    //variable sku generate from select
        jQuery(document).on("change", ".variable_option_select", function(){
            let generated_sku = $('.variable_generate_sku').text();
            let current_sku = $(this).find(":selected").attr('data-variable-sku');
            if (generated_sku != '') {
                generated_sku = generated_sku+' '+current_sku;
            }else{
                generated_sku = current_sku;
            }
            $('.variable_generate_sku').text(generated_sku);
        })
    // variable plus 
        jQuery(document).on("click", ".variable_plus", function(){
            // alert('test');
            var qty = Number(jQuery('#variable_qty').val());
            var qty_limit = Number(jQuery('#variable_qty').attr('data-cart-limit'));
            var total_qyt = 0;
            if (qty < qty_limit) {
                total_qyt = Number(qty + 1);
            }else{
                total_qyt = Number(qty);
            }
            jQuery('#variable_qty').val(total_qyt);
        });
     // variable minus 
        jQuery(document).on("click", ".variable_minus", function(){
            // alert('test');
            var qty = Number(jQuery('#variable_qty').val());
            var total_qyt = 1;
            if (qty >= 2) {
                total_qyt = Number(qty - 1);
            }
            jQuery('#variable_qty').val(total_qyt);
        });
    // remove cart 
        jQuery(document).on("click", ".remove_cart_item", function(){
            var id = jQuery(this).attr("id");
            var customer = jQuery('#select_user').val();
			var address = jQuery('#shipping_address_id').val();
            if (customer == -1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Select a customer first!',
                    showConfirmButton: true,
                    timer: 1500
                })
            }else{
                $.ajax({
                    url: "{{  url('/admin/pos/remove/cart/') }}/"+id,
                    type: "GET",
                    data: {customer_id: customer},
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                showConfirmButton: true,
                                timer: 2000
                            })
                        }else{
                            getCart(customer);
                            new Audio('/uploads/beep-07a.mp3').play();  
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: true,
                                timer: 2000
                            })
                        }
                    }
                })
            }
        });
    // Increment cart 
        jQuery(document).on('click','.increment_cart',function(e){
            e.preventDefault();
            var id = jQuery(this).attr("id");
            var action = 'increment';
            var customer = jQuery('#select_user').val();
            jQuery.ajax({
                type : "POST",
                url : "/admin/pos/update/cart",
                data : {id: id, action: action, customer_id: customer},
                success: function(response) {
                    if (response.status == 1) {
                        getCart(customer);
                    }else if (response.status == 0) {
                        getCart(customer);
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: true,
                            timer: 1500
                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: true,
                            timer: 1500
                        })
                    }
                }
            });
        });
    // Increment cart 
        jQuery(document).on('click','.decrement_cart',function(e){
            e.preventDefault();
            var id = jQuery(this).attr("id");
            var action = 'decrement';
            var customer = jQuery('#select_user').val();
            jQuery.ajax({
                type : "POST",
                url : "/admin/pos/update/cart",
                data : {id: id, action: action, customer_id: customer},
                success: function(response) {
                    if (response.status == 1) {
                        getCart(customer);
                    }else{
                        getCart(customer);
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: true,
                            timer: 1500
                        })
                    }
                }
            });
        });
    // Get shipping option for customer address
        function getCustomerShippingOption(customer, address, address_type){
            $.ajax({
                type : "POST",
                url : "/admin/pos/customer/shipping/option",
                data : {customer_id: customer, address: address, address_type:address_type},
                success: function(response) {
                    jQuery('#shippingOptionModalBody').empty();
                    jQuery('#shippingOptionModalBody').html(response);
                }
            });
        }
    // select customer shipping address
        jQuery(document).on('click','#shipping_cost_btn',function(e){
            e.preventDefault();
            var customer = jQuery('#select_user').val();
            var address = jQuery('#shipping_address_id').val();
            var address_type = jQuery('#shipping_address_id').find('option:selected').attr('data-address-type');
            if (customer == -1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Select a customer first!',
                    showConfirmButton: true,
                    timer: 1500
                })
            }else if(address < 1 || address == ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Select a shipping address first!',
                    showConfirmButton: true,
                    timer: 1500
                })
            }else{
                getCustomerShippingOption(customer,address,address_type);
                jQuery('#shippingOptionModal').modal('show');
            }
        });
    // calculate shipping cost 
        function calculateShippingCost(){
            var shipping_cost = 0;
            let grocery_shipping_cost = jQuery('#grocery_shipping_cost').val();
            jQuery('.shipping_option_radio').each(function(key, val) {
                if (jQuery(this).is(":checked")) {
                    let qty = jQuery(this).attr('data-qty');
                    shipping_cost = shipping_cost + (Number(jQuery(this).val()) * Number(qty));
                }
            })
            jQuery('#shipping_cost').text(Number(shipping_cost) + Number(grocery_shipping_cost));
        }
    // select shipping options 
        jQuery(document).on("change", ".shipping_option_radio", function(){
            calculateShippingCost();
            getGrandTotal();
        })
        // jQuery(document).ready(function(){
        //     calculateShippingCost();
        //     getGrandTotal();
        // })
    // open discount modal 
        jQuery(document).on("click", "#discount_modal_btn", function(){
            jQuery('#discountModal').modal('show');
        })
       
    // select discount type 
        jQuery(document).on("change", "#discountModal #discount_type", function(){
            var discount_type = jQuery(this).val();
            if (discount_type == 'custom') {
                jQuery('.discount_amount_area').removeClass('d-none');
                jQuery('.discount_amount_area').addClass('d-block');
                jQuery('.coupon_code_area').removeClass('d-block');
                jQuery('.coupon_code_area').addClass('d-none');
            }else if (discount_type == 'percent') {
                jQuery('.discount_amount_area').removeClass('d-none');
                jQuery('.discount_amount_area').addClass('d-block');
                jQuery('.coupon_code_area').removeClass('d-block');
                jQuery('.coupon_code_area').addClass('d-none');
            }else{
                jQuery('.discount_amount_area').removeClass('d-block');
                jQuery('.discount_amount_area').addClass('d-none');
                jQuery('.coupon_code_area').removeClass('d-none');
                jQuery('.coupon_code_area').addClass('d-block');
            }
        })
    // discount form submit
        jQuery(document).on("click", "#discountModal #apply_discount_btn", function(e){
            e.preventDefault();
            let discount_type = jQuery('#discountModal #discount_type').val();
            if (discount_type == '-1') {
                jQuery('#discountModal .discount-error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Ops!</strong> You should select discount type first.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }else if (discount_type == 'custom') {
                let discount_amount = jQuery('#discountModal #discount_amount').val();
                if (discount_amount == '') {
                    jQuery('#discountModal .discount-error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Ops!</strong> Enter discount amount.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }else{
                    jQuery('#discountModal .discount-error').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Coupon successfully added!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    jQuery('#discount').text(Number(jQuery('#discount').text()) + Number(discount_amount));
                    getGrandTotal();
                }
            }else if(discount_type == 'percent'){
                let discount_amount = jQuery('#discountModal #discount_amount').val();
                let subtotal = jQuery('#subtotal').text();
                let discount = Number(discount_amount) / Number(100) * Number(subtotal);
                // console.log(discount);
                jQuery('#discount').text(Number(discount));
                getGrandTotal();
            }else{
                let coupon_code = jQuery('#discountModal #coupon_code').val();
                if (coupon_code == '') {
                    jQuery('#discountModal .discount-error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Ops!</strong> Enter coupon code.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }else{
                    let customer = jQuery('#select_user').val();
                    jQuery.ajax({
                        type : "POST",
                        url : "/admin/pos/check/coupon/code",
                        data : {coupon_code: coupon_code,customer_id : customer},
                        dataType: "json",
                        success: function(response) {
                            if (response.status == 0) {
                                jQuery('#discountModal .discount-error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Ops!</strong> '+response.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                            }else{
                                jQuery('#discountModal .discount-error').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Coupon successfully added!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                                jQuery('#discount').text(Number(jQuery('#discount').text()) + Number(response.coupon_amount));
                                getGrandTotal();
                            }
                        }
                    });
                }
            }
        })
        jQuery(document).on("click", "#othersInfoModelBtn", function(){
            jQuery('#posOtherInfoModal').modal('show');
        })
        jQuery('#other_info_form #other_charge').closest('.form-group').hide();
        jQuery(document).on("change", "#other_info_form #other_charge", function(e){
            e.preventDefault();
            // Other charges have no authoritative backend field; do not present them as part of the stored total.
            jQuery('#totalOtherCharge').val(0);
            jQuery('#total_other_charge').text('');
        });
        jQuery(document).on("keyup", "#other_info_form #order_note", function(e){
            e.preventDefault();
            let order_note = jQuery(this).val();
            jQuery('#otherNote').val(order_note);  
        });
    // discount att to cart 
        jQuery(document).on("click", ".cart_discount_add_btn", function(){
            let id = jQuery(this).attr('data-id');
            let unit_price = jQuery(this).attr('data-unit-price');
            jQuery('#cart_discount #cartdiscount_unit_price').val(Number(unit_price));
            jQuery('#cart_discount #cart_id').val(Number(id));
            jQuery('#cartdiscountModal').modal('show');
        })
    // update cart price
        jQuery(document).on("click", ".cart_product_price_update", function(){
            let id = jQuery(this).attr('data-id');
            let unit_price = jQuery(this).attr('data-unit-price');
            jQuery('#priceUpdateModal #current_price').val(Number(unit_price));
            jQuery('#priceUpdateModal #cart_id').val(Number(id));
            jQuery('#priceUpdateModal').modal('show');
        })
        jQuery(document).on("click", "#priceUpdateModal #apply_cartproductprice_btn", function(e){
            e.preventDefault();
            let data = $('#cart_price_update_form').serialize() + '&customer_id=' + encodeURIComponent(localStorage.getItem("current_user_id"));
            jQuery.ajax({
                type : "POST",
                url : "/admin/pos/change/cart/price",
                data : data,
                dataType: "json",
                success: function(response) {
                    if (response.status == 0) {
                        jQuery('#priceUpdateModal .cart-price-update-error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Ops!</strong> '+response.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }else{
                        jQuery('#priceUpdateModal .cart-price-update-error').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Price successfully updated!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        getCart(localStorage.getItem("current_user_id"));
                    }
                }
            });
        })
        
    // select discount type 
        jQuery(document).on("change", "#cartdiscount_type", function(){
            let discount_type = jQuery(this).val();
            if(discount_type == 'coupon'){
                jQuery('.cartdiscount_amount_area').removeClass('d-block');
                jQuery('.cartdiscount_amount_area').addClass('d-none');
                jQuery('.cartcoupon_code_area').removeClass('d-none');
                jQuery('.cartcoupon_code_area').addClass('d-block');
                jQuery('.cartdiscount_unitprice_area').removeClass('d-block');
                jQuery('.cartdiscount_unitprice_area').addClass('d-none');
            }else if(discount_type == 'percent'){
                jQuery('.cartdiscount_amount_area').removeClass('d-none');
                jQuery('.cartdiscount_amount_area').addClass('d-block');
                jQuery('.cartdiscount_unitprice_area').removeClass('d-none');
                jQuery('.cartcoupon_code_area').addClass('d-block');
                jQuery('.cartcoupon_code_area').removeClass('d-block');
                jQuery('.cartcoupon_code_area').addClass('d-none');
            }else{
                jQuery('.cartdiscount_amount_area').removeClass('d-none');
                jQuery('.cartdiscount_amount_area').addClass('d-block');
                jQuery('.coupon_code_area').removeClass('d-block');
                jQuery('.coupon_code_area').addClass('d-none');
                jQuery('.cartdiscount_unitprice_area').removeClass('d-block');
                jQuery('.cartdiscount_unitprice_area').addClass('d-none');
            }
        })
    // apply cart coupon
        jQuery(document).on("click", "#cart_discount #apply-cartdiscount-btn", function(e){
            e.preventDefault();
            let data = $('#cart_discount_form').serialize() + '&customer_id=' + encodeURIComponent(localStorage.getItem("current_user_id"));
            jQuery.ajax({
                type : "POST",
                url : "/admin/pos/apply/discount",
                data : data,
                dataType: "json",
                success: function(response) {
                    if (response.status == 0) {
                        jQuery('#cart_discount .cart-discount-error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Ops!</strong> '+response.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    }else{
                        jQuery('#cart_discount .cart-discount-error').html('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success!</strong> Coupon successfully added!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        getCart(localStorage.getItem("current_user_id"));
                    }
                }
            });
        })
    //Get customer shipping address
        function getShippingAddress(user_id, preferredAddressId){
            if(user_id){
                jQuery.ajax({
                    type : "POST",
                    url : "/admin/pos/customer/shipping/address",
                    data : {customer_id: user_id},
                    success: function(response) {
						if (String(jQuery('#select_user').val()) !== String(user_id)) return;
                        jQuery('#customer_address_area').html(response);
                        let selectedAddressId = preferredAddressId;
                        if (!selectedAddressId || !jQuery("#customer_address_area option[value='" + selectedAddressId + "']").length) {
                            selectedAddressId = jQuery('#customer_address_area option:first').val();
                        }
                        if (!selectedAddressId) {
                            selectedAddressId = jQuery('#pickpoint_address option:first').val() || -1;
                        }

                        jQuery('#shipping_address_id').val(String(selectedAddressId));
                        jQuery('#shipping_address_id').selectpicker('refresh');
                        jQuery('#shipping_address_id').trigger('change');
                    }
                });
            }
        }

        jQuery(document).on("change", "#select_user", function(){
            var nonce = jQuery(this).attr("data-nonce");
            var select_user = jQuery(this).find('option:selected').val();
            if(select_user != -1){
                localStorage.setItem("current_user_id", select_user);
				localStorage.removeItem("current_user_address_id");
				localStorage.removeItem("address_type");
				jQuery('#shipping_address_id').val('-1').selectpicker('refresh');
                getCart(select_user);
                getShippingAddress(select_user);
			}else{
				localStorage.removeItem("current_user_id");
				localStorage.removeItem("current_user_address_id");
				localStorage.removeItem("address_type");
				jQuery('#customer_address_area').empty();
				jQuery('#shipping_address_id').val('-1').selectpicker('refresh');
            }
        });

        jQuery(document).on("change", "#shipping_address_id", function(){
            var address = jQuery(this).find('option:selected').val();
            var address_type = jQuery(this).find('option:selected').attr('data-address-type');
            
            // alert(address);
            if(address != -1){
                localStorage.setItem("current_user_address_id", address);
                localStorage.setItem("address_type", address_type);
            }
            getCart(localStorage.getItem("current_user_id"));
        });
        jQuery(document).on("change", "#payment_method", function(){
            var payment_method = jQuery(this).find('option:selected').val();
            if(payment_method != -1){
                localStorage.setItem("current_user_payment_method", payment_method);
            }
        });
        
        
    // Resolve the written address with the same authoritative resolver used by checkout.
        let posAddressResolverTimer = null;
        let posAddressResolverSequence = 0;
        function loadPosDistricts(selectedDistrictId) {
            return jQuery.get('/api/v1/get-district/0').then(function (districts) {
                let options = '<option value="">--Select District--</option>';
                jQuery.each(districts || [], function (_, district) {
                    options += '<option value="' + district.id + '">' + district.title + '</option>';
                });
                jQuery('#district_id').html(options).val(selectedDistrictId || '');
            });
        }
        function loadPosUpazilas(districtId, selectedUpazilaId) {
            if (!districtId) {
                jQuery('#upazila_id').html('<option value="">--Select District First--</option>');
                return jQuery.Deferred().resolve().promise();
            }
            return jQuery.get('/api/v1/get-upazila/' + districtId).then(function (upazilas) {
                let options = '<option value="">--Select Thana / Area--</option>';
                jQuery.each(upazilas || [], function (_, upazila) {
                    options += '<option value="' + upazila.id + '">' + upazila.title + '</option>';
                });
                jQuery('#upazila_id').html(options).val(selectedUpazilaId || '');
            });
        }
        function hasUsefulPosAddressDetail(address, candidate) {
            let residual = String(address || '').toLowerCase()
                .replace(/[০-৯]/g, digit => '০১২৩৪৫৬৭৮৯'.indexOf(digit))
                .replace(/[,.;:|/\\_\-()]+/g, ' ').replace(/\s+/g, ' ').trim();
            const detailTerms = ['village','union','area','road','street','lane','house','holding','office','building','shop','market','mosque','school','college','institution','sector','block','floor','flat','para','moholla','গ্রাম','ইউনিয়ন','এলাকা','রোড','সড়ক','গলি','বাড়ি','বাসা','হোল্ডিং','অফিস','ভবন','দোকান','মার্কেট','বাজার','মসজিদ','স্কুল','কলেজ','প্রতিষ্ঠান','সেক্টর','ব্লক','তলা','ফ্লোর','ফ্ল্যাট','পাড়া','মহল্লা'];
            const districtEquivalents = {'3':['barguna','বরগুনা'],'14':['dhaka','ঢাকা'],'19':['gazipur','গাজীপুর']};
            const thanaEquivalents = {
                '67':['keraniganj','কেরানীগঞ্জ'],'81':['mirpur 10','mirpur 11','মিরপুর ১০','মিরপুর ১১'],
                '94':['mohammadpur','মোহাম্মদপুর'],'122':['savar','সাভার'],
                '142':['turag','তুরাগ','dharangartek','dharangertek','ধরঙ্গারটেক'],
                '181':['amtali','amtoli','amtoly','আমতলী'],'371':['tongi','টঙ্গি','টঙ্গী'],
                '562':['mohammadpur','মোহাম্মদপুর']
            };
            const structuredEquivalents = Array.isArray(candidate.location_evidence)
                ? candidate.location_evidence
                : [];
            const equivalents = [candidate.district_title, candidate.upazila_title]
                .concat(districtEquivalents[String(candidate.district_id)] || [])
                .concat(thanaEquivalents[String(candidate.upazila_id)] || [])
                .concat(structuredEquivalents)
                .filter(Boolean)
                .map(value => String(value).toLowerCase().replace(/[,.;:|/\\_\-()]+/g, ' ').replace(/\s+/g, ' ').trim())
                .sort((left, right) => right.length - left.length);
            equivalents.forEach(function (equivalent) {
                const escaped = equivalent.replace(/[.*+?^${}()|[\]\\]/g, '\\$&').replace(/\s+/g, '\\s+');
                residual = residual.replace(new RegExp('(^|\\s)' + escaped + '(?=\\s|$)', 'gu'), ' ');
            });
            residual = residual.replace(/\s+/g, ' ').trim();
            const addressTokens = residual.split(/\s+/).filter(Boolean);
            if (detailTerms.some(term => addressTokens.indexOf(term) !== -1)) return true;
            return /[0-9০-৯]/u.test(residual);
        }
        function showPosLocationFallback(message) {
            jQuery('#pos-address-resolver-status').removeAttr('style').attr('class', 'form-text text-warning').text(message);
            loadPosDistricts();
        }
        loadPosDistricts();
		jQuery(document).on('input', '#address_form input[name="phone"]', function () {
			this.value = this.value.replace(/[০-৯]/g, function (digit) {
				return '০১২৩৪৫৬৭৮৯'.indexOf(digit);
			});
		});
        jQuery(document).on('input', '#address_form input[name="street_address"]', function () {
            clearTimeout(posAddressResolverTimer);
            const address = jQuery(this).val().trim();
            const requestId = ++posAddressResolverSequence;
            jQuery('#district_id, #upazila_id, #union_id').val('');
            if (!address) {
                jQuery('#pos-address-resolver-status').text('');
                return;
            }
            jQuery('#pos-address-resolver-status').removeAttr('style').attr('class', 'form-text text-muted').text('Identifying delivery area...');
            posAddressResolverTimer = setTimeout(function () {
                jQuery.ajax({
                    method: 'POST',
                    url: '/api/v1/resolve-address-location',
                    data: {address: address},
                    success: function (response) {
                        if (requestId !== posAddressResolverSequence) return;
                        const candidates = Array.isArray(response.candidates) ? response.candidates : [];
                        if (response.match_type === 'strong' && candidates.length) {
                            const candidate = candidates[0];
                            jQuery.when(loadPosDistricts(candidate.district_id), loadPosUpazilas(candidate.district_id, candidate.upazila_id)).then(function () {
                            if (requestId !== posAddressResolverSequence) return;
                            if (hasUsefulPosAddressDetail(address, candidate)) {
                                jQuery('#pos-address-resolver-status').attr('class', 'mt-2').attr('style', 'display:block;width:100%;box-sizing:border-box;padding:6px 9px;border:1px solid #79bd79;border-radius:5px;background:#edf9ed;color:#267326;font-size:13px;line-height:1.35;overflow-wrap:anywhere;font-weight:600;').text('✓ এলাকা শনাক্ত হয়েছে: ' + candidate.upazila_title + ', ' + candidate.district_title);
                            } else {
                                jQuery('#pos-address-resolver-status').attr('class', 'mt-2').attr('style', 'display:block;width:100%;box-sizing:border-box;padding:7px 10px;border:1px solid #efb45a;border-radius:5px;background:#fff8e8;color:#805400;font-size:13px;line-height:1.35;overflow-wrap:anywhere;').text('⚠ এলাকা শনাক্ত হয়েছে: ' + candidate.upazila_title + ', ' + candidate.district_title + '। সঠিক ডেলিভারির জন্য গ্রাম/ইউনিয়ন/এলাকা ও বাড়ি/রোড/অফিসের তথ্য লিখুন।');
                            }
                            });
                            return;
                        }
                        if (response.match_type === 'district_only' && response.district) {
                            const district = response.district;
                            jQuery.when(loadPosDistricts(district.district_id), loadPosUpazilas(district.district_id, null)).then(function () {
                                if (requestId !== posAddressResolverSequence) return;
                                jQuery('#pos-address-resolver-status').attr('class', 'mt-2').attr('style', 'display:block;width:100%;box-sizing:border-box;padding:6px 9px;border:1px solid #7fc4d9;border-radius:5px;background:#eef9fc;color:#246477;font-size:13px;line-height:1.35;overflow-wrap:anywhere;').text('District identified. Please select Thana / Area.');
                            });
                            return;
                        }
                        showPosLocationFallback(response.match_type === 'ambiguous'
                            ? 'Please confirm the correct District and Thana / Area.'
                            : 'Area could not be identified safely. Please select District and Thana / Area.');
                    },
                    error: function () {
                        if (requestId === posAddressResolverSequence) showPosLocationFallback('Area lookup failed. Please select District and Thana / Area.');
                    }
                });
            }, 500);
        });

    //Get Customer district
        jQuery(document).on("change", "#division_id", function(){
            var nonce = jQuery(this).attr("data-nonce");
            var division_id = jQuery(this).find('option:selected').val();
            if(division_id){
                jQuery.ajax({
                    type : "POST",
                    url : "/admin/seller/get-district",
                    data : {division_id: division_id},
                    success: function(response) {
						jQuery('#district_id').empty();
                        jQuery('#district_id').append('<option value="">-- Select --</option>'+response);
                    }
                });
            }
        });
    //Get Customer upazila
        jQuery(document).on("change", "#district_id", function(){
            jQuery('.ajax_loader').show();
            var nonce = jQuery(this).attr("data-nonce");
            var district_id = jQuery(this).find('option:selected').val();
            if(district_id){
                jQuery.ajax({
                    type : "POST",
                    url : "/admin/seller/get-upazila",
                    data : {district_id: district_id},
                    success: function(response) {
						jQuery('#upazila_id').empty(); 
                        jQuery('#upazila_id').append('<option value="">-- Select --</option>'+response);
                    }
                });
            }
        });
    //Get customer union
        jQuery(document).on("change", "#upazila_id", function(){
            jQuery('.ajax_loader').show();
            var nonce = jQuery(this).attr("data-nonce");
            var upazila_id = jQuery(this).find('option:selected').val();
            if(division_id != -1){
                    jQuery.ajax({
                    type : "POST",
                    url : "/admin/seller/get-union",
                    data : {upazila_id: upazila_id},
                    success: function(response) {
						jQuery('#union_id').empty(); 
                        jQuery('#union_id').append('<option value="">-- Select --</option>'+response); 
                    }
                });
            }
        });
    // Add customer address
        jQuery('#customer-address-add-btn').click(function(event){
            event.preventDefault();
            var customer = jQuery('#select_user').val();
            if(customer == -1){
                jQuery('.address-error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Ops!</strong> You should select customer first.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }else{
                // Get form
                var form = jQuery('#address_form')[0];
                var data = new FormData(form);
                data.append('customer', customer);
                jQuery.ajax({
                    method: 'POST',
                    url: '/admin/pos/customer/shipping/address/add',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Successfully added',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        if (String(jQuery('#select_user').val()) === String(customer)) {
							getShippingAddress(customer, data.address_id);
						}
                        $('#customerShippingAddressAddModal').modal('hide');
                    },
                    error: function (xhr) {
                        var errorMessage = '<div class="card bg-danger">\n' +
                            '                        <div class="card-body text-center p-5">\n' +
                            '                            <span class="text-white">';
                        $.each(xhr.responseJSON.errors, function(key,value) {
                            errorMessage +=(''+value+'<br>');
                        });
                        errorMessage +='</span>\n' +
                            '                        </div>\n' +
                            '                    </div>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            footer: errorMessage
                        })
                    },
               })
            }
        });

        var posPaymentType = 'cash_on_delivery';
        jQuery(document).on("click", ".order_with_cash", function(e){
            e.preventDefault();
            posPaymentType = 'cash';
            let paid_amount = Number(jQuery('#grand_total').text());
            let payment_method = 1;
            jQuery('#cashPayment #modal_paid_amount').val(paid_amount);
            jQuery('#hidden_paid_amount').val(paid_amount);
            jQuery('#hidden_payment_method').val(payment_method);
            jQuery('#cashPayment').modal('show');
        })
        jQuery(document).on("keyup", "#modal_change_amount", function(e){
            e.preventDefault();
            let amount_to_pay = jQuery('#cashPayment #modal_paid_amount').val();
            let change_amount = jQuery(this).val();
            let return_amount = Number(amount_to_pay) - Number(change_amount);
            jQuery('#cashPayment #modal_return_change').val(return_amount);
        })
        jQuery(document).on("change", "#modal_change_amount", function(e){
            e.preventDefault();
            let amount_to_pay = jQuery('#cashPayment #modal_paid_amount').val();
            let change_amount = jQuery(this).val();
            let return_amount = Number(amount_to_pay) - Number(change_amount);
            jQuery('#cashPayment #modal_return_change').val(return_amount);
        })
        jQuery(document).on("click", ".order_with_mfs", function(e){
            e.preventDefault();
            posPaymentType = 'mfs_card';
            let paid_amount = Number(jQuery('#grand_total').text());
            let current_due = Number(jQuery('#previous_dues').text());
            jQuery('#paymentWithMFS #modal_paid_amount').val(paid_amount);
            jQuery('#paymentWithMFS #modal_current_due').val(current_due);
            jQuery('#hidden_paid_amount').val(paid_amount);
            jQuery('#paymentWithMFS').modal('show');
        })
        
        jQuery(document).on("click", "#paymentWithMFS #modal_mfs_type", function(e){
            e.preventDefault();
            let type = jQuery(this).val();
            if(type == 'card'){
                jQuery('#paymentWithMFS .modal_bank_field_area').removeClass('d-none');
                jQuery('#paymentWithMFS .modal_bank_field_area').addClass('d-block');
                jQuery('#paymentWithMFS .modal_bank_field_area').removeClass('d-block');
                jQuery('#paymentWithMFS .modal_mfs_field_area').addClass('d-none');
            }else if (type == 'mfs') {
                jQuery('#paymentWithMFS .modal_bank_field_area').removeClass('d-block');
                jQuery('#paymentWithMFS .modal_bank_field_area').addClass('d-none');
                jQuery('#paymentWithMFS .modal_mfs_field_area').removeClass('d-none');
                jQuery('#paymentWithMFS .modal_mfs_field_area').addClass('d-block');
            }else{
                jQuery('#paymentWithMFS .modal_bank_field_area').removeClass('d-block');
                jQuery('#paymentWithMFS .modal_bank_field_area').addClass('d-none');
                jQuery('#paymentWithMFS .modal_mfs_field_area').removeClass('d-block');
                jQuery('#paymentWithMFS .modal_mfs_field_area').addClass('d-none');
            }
        })
        jQuery(document).on("change", "#paymentWithMFS #modal_payment_method", function(e){
            e.preventDefault();
            let payment_method = jQuery(this).val();
            jQuery('#hidden_payment_method').val(payment_method);  
        })
        jQuery(document).on("click", ".order_partial_payment", function(e){
            e.preventDefault();
            posPaymentType = 'partial_payment';
            let paid_amount = Number(jQuery('#grand_total').text());
            let current_due = Number(jQuery('#previous_dues').text());
            // jQuery('#partialPaymentModal #modal_paid_amount').val(paid_amount);
            jQuery('#hidden_paid_amount').val(paid_amount);
            jQuery('#hidden_payment_method').val(1);
            jQuery('#partialPaymentModal').modal('show');
        })
        
        jQuery(document).on("change", "#partialPaymentModal #modal_paid_amount", function(e){
            e.preventDefault();
            let grand_total = Number(jQuery('#grand_total').text());
            let paid_amount = jQuery(this).val();
            let due_amount = 0;
            due_amount = grand_total - Number(paid_amount);
            jQuery('#hidden_paid_amount').val(paid_amount);
            jQuery('#hidden_payment_method').val(1);
            jQuery('#partialPaymentModal #modal_current_due').val(due_amount);
            
        })
        jQuery(document).on("keyup", "#partialPaymentModal #modal_paid_amount", function(e){
            e.preventDefault();
            let grand_total = Number(jQuery('#grand_total').text());
            let paid_amount = jQuery(this).val();
            let due_amount = 0;
            due_amount = grand_total - Number(paid_amount);
            jQuery('#hidden_paid_amount').val(paid_amount);
            jQuery('#hidden_payment_method').val(1);
            jQuery('#partialPaymentModal #modal_current_due').val(due_amount);
        })
        jQuery(document).on("click", ".cashon_order", function(e){
            e.preventDefault();
            posPaymentType = 'cash_on_delivery';
            let paid_amount = Number(jQuery('#grand_total').text());
            let current_due = Number(jQuery('#previous_dues').text());
            let shipping_charge = Number(jQuery('#shipping_cost').text());
            jQuery('#hidden_paid_amount').val(0);
            jQuery('#cashOnDeliveryModal #modal_paid_shiping_amount').val(shipping_charge);
            jQuery('#cashOnDeliveryModal #modal_current_due').val(paid_amount);
            jQuery('#hidden_payment_method').val(1);
            jQuery('#cashOnDeliveryModal').modal('show');
        })

        jQuery(document).on("change", "#cashOnDeliveryModal #modal_paid_shiping_amount", function(e){
            e.preventDefault();
            let grand_total = Number(jQuery('#grand_total').text());
            let shipping_amount = jQuery(this).val();
            let due_amount = 0;
            due_amount = grand_total + Number(shipping_amount);
            jQuery('#hidden_payment_method').val(1);
            jQuery('#cashOnDeliveryModal #modal_current_due').val(due_amount);

        })
    
        jQuery(document).on("change", "#cashOnDeliveryModal #modal_paid_amount", function(e){
            e.preventDefault();
            let grand_total = Number(jQuery('#grand_total').text());
            let paid_amount = jQuery(this).val();
            let due_amount = 0;
            due_amount = grand_total - Number(paid_amount);
            jQuery('#hidden_paid_amount').val(paid_amount);
            jQuery('#hidden_payment_method').val(1);
            jQuery('#cashOnDeliveryModal #modal_current_due').val(due_amount);
            
        })
        jQuery(document).on("keyup", "#cashOnDeliveryModal #modal_paid_amount", function(e){
            e.preventDefault();
            let grand_total = Number(jQuery('#grand_total').text());
            let paid_amount = jQuery(this).val();
            let due_amount = 0;
            due_amount = grand_total - Number(paid_amount);
            jQuery('#hidden_paid_amount').val(paid_amount);
            jQuery('#hidden_payment_method').val(1);
            jQuery('#cashOnDeliveryModal #modal_current_due').val(due_amount);
        })
        jQuery(document).on("click", ".final_order_now_btn", function(e){
            e.preventDefault();
            var customer = jQuery('#select_user').val();
            var address  = jQuery('#shipping_address_id').val();
            let address_type = localStorage.getItem("address_type");
            var payment_method  = posPaymentType;
            var order_note  = jQuery('#otherNote').val();
            var subtotal  = jQuery('#subtotal').text();
            var grand_total  = jQuery('#grand_total').text();
            var paid_amount  = jQuery('#hidden_paid_amount').val();
            var paid_by  = jQuery('#hidden_payment_method').val();
            
            var shipping_method = new Array();
            jQuery('.shipping_option_radio').each(function(key, val) {
                if (jQuery(this).is(":checked")) {
                    shipping_method.push([jQuery(this).attr("data-shippingmethod"), jQuery(this).attr("data-id"), jQuery(this).val()]);
                }
            })
            var shipping_cost  = jQuery('#shipping_cost').text();
            var discount  = jQuery('#discount').text();
            var vat  = jQuery('#vat_tax').text();
            var completed  = payment_method === 'cash' ? jQuery('#cashPayment #completed').val() : 'no';
            if (customer == -1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Select a customer first!',
                    showConfirmButton: true,
                })
            }else if(address < 1 || address == null){
                Swal.fire({
                    icon: 'error',
                    title: 'Select a shipping address first!',
                    showConfirmButton: true,
                })
            }else if (payment_method == -1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Select a payment method!',
                    showConfirmButton: true,
                })
            }else{
                var data = new FormData();
                data.append('customer', customer);
                data.append('address', address);
                data.append('address_type', address_type);
                data.append('payment_method', payment_method);
                data.append('order_note', order_note);
                data.append('shipping_method',  JSON.stringify(shipping_method));
                data.append('discount', discount);
                data.append('shipping_cost', shipping_cost);
                data.append('paid_amount', paid_amount);
                data.append('paid_by', paid_by);
                data.append('subtotal', subtotal);
                data.append('grand_total', grand_total);
                data.append('completed', completed);
                jQuery.ajax({
                    method: 'POST',
                    url: '/admin/pos/order',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (data) {
						if (data.status !== 1) {
							Swal.fire({
								icon: 'error',
								title: data.message || 'Unable to place order.',
								showConfirmButton: true,
							});
							return;
						}
                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 5000
                        })
                        setTimeout(function() {
							const defaultPrintType = "{{ Helper::getsettings('default_pos_print_invoice') ?? 'pos' }}";
                            // localStorage.setItem("current_user_id", -1);
                            localStorage.setItem("current_user_address_id", -1);
                            // getOrderSummary(data.invoice.order_id);
                            var id = data.invoice.order_id;
                            var url = "{{ route('admin.order.sold.invoice.print', [':type', ':id']) }}";
							url = url.replace(':type', defaultPrintType);
                            url = url.replace(':id', id);
                            // location.href = url;
                            window.open(
                                url,
                                '_blank'
                            );
                            location.reload();
                        }, 1000);//
                    },
                    error: function (xhr) {
                        var errorMessage = '<div class="card bg-danger">\n' +
                            '                        <div class="card-body text-center p-5">\n' +
                            '                            <span class="text-white">';
                        $.each(xhr.responseJSON.errors, function(key,value) {
                            errorMessage +=(''+value+'<br>');
                        });
                        errorMessage +='</span>\n' +
                            '                        </div>\n' +
                            '                    </div>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            footer: errorMessage
                        })
                    },
                })
            }
        })
        function getOrderSummary(order_id = null){
            jQuery.ajax({
                type : "GET",
                url : "/admin/pos/order/summary",
                data : {order_id: order_id},
                dataType: 'html',
                success: function(response) {
                    jQuery('.modal').modal('hide');
                    jQuery('#afftersalePrintModal #afftersalePrintModalbody').empty(); 
                    jQuery('#afftersalePrintModal #afftersalePrintModalbody').html(response);
                    jQuery('#afftersalePrintModal').modal('show'); 
                    jQuery('#btnPrint').hide();
                }
            });
        }

        function printDiv(print) {
            $("#afftersalePrint").html(print.html());
            window.print();
            $("#afftersalePrint").html("");
        }
</script>
@endpush
@endsection
