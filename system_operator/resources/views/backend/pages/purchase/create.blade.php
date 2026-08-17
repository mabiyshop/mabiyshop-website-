@extends('backend.layouts.master')
@section('title','Purchase Create - '.config('concave.cnf_appname'))
@section('content')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet" />
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Purchase > Create Purchase</span>
                <a class="btn btn-success float-right" href="{{ route('admin.purchase')}}">View Purchase List</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <form class="form-sample" method="post" action="{{ route('admin.purchase.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            @if (Auth::user()->getRoleNames() != '["seller"]')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Shop Name <span style="color: #f00">*</span></label>
                                        <select required name="shop_name" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select shop...">
                                            @foreach($shops as  $shop)
                                            <option value="{{$shop->id}}" @if($shop->id == Helper::getsettings('default_branch_id')) selected="" @endif>{{$shop->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="shop_name" id="shop_name" value="{{ Auth::user()->id }}">
                            @endif

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier <span style="color: #f00">*</span></label>
                                    <select name="supplier_id" id="supplier_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select supplier...">
                                        @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}" data-balance="{{ $supplier->balance }}">{{$supplier->name .' ('. $supplier->company_name .')'}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Purchase Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1">Recieved</option>
                                        <option value="2">Partial</option>
                                        <option value="3">Pending</option>
                                        <option value="4">Ordered</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Payment Method</label>
                                    <select class="form-select form-control" name="payment_method" id="payment_method" aria-label="Default select example" required>
                                        <option value="0" selected>Choose one</option>
                                        @foreach(\App\Models\CurrentAsset::get() as $row)
                                            <option value="{{$row->id}}" data-balance="{{ $row->amount }}">{{$row->name}} -- #{{$row->amount}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Attach Document</label> <i class="dripicons-question" data-toggle="tooltip" title="Only jpg, jpeg, png, gif, pdf, csv, docx, xlsx and txt file is supported"></i>
                                    <input type="file" name="document" class="form-control" >
                                    @if($errors->has('extension'))
                                        <span>
                                           <strong>{{ $errors->first('extension') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <label>Select Product</label>
                                <div class="search-box input-group">
                                    <select name="product_id_select" id="product_id_select" class="selectpicker form-control" data-live-search="true"  title="Select products...">
                                        @foreach($products as $product)
                                            <option value="{{$product->id}}">{{$product->barcode .' ('. $product->title .')'}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Order Table *</h5>
                                <div class="table-responsive mt-3">
                                    <table id="myTable" class="table table-hover order-list">
                                        <thead>
                                            <tr>
                                                <th>name</th>
                                                <th>Code</th>
                                                <th>Quantity</th>
                                                <th class="recieved-product-qty d-none">Recieved</th>
                                                <th>Net Unit Cost</th>
                                                <th>Unit</th>
                                                <th>Subtotal</th>
                                                <th><i class="mdi mdi-trash-can-outline"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody id="sale_product_tablle_body">
                                            
                                        </tbody>
                                        <tfoot class="tfoot active">
                                            <th colspan="2">Total</th>
                                            <th id="total-qty">0</th>
                                            <th class="recieved-product-qty d-none"></th>
                                            <th></th>
                                            <th></th>
                                            <th id="total">0.00</th>
                                            <th><i class="mdi mdi-trash-can-outline"></i></th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="hidden" name="total_qty" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="hidden" name="total_discount" />
                                </div>
                            </div>
                           
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="hidden" name="total_cost" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="hidden" name="item" />
                                    <input type="hidden" name="order_tax" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="hidden" name="grand_total" />
                                    <input type="hidden" name="paid_amount" value="0.00" />
                                    <input type="hidden" name="payment_status" value="1" />
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-8 row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Note</label>
                                        <textarea rows="5" class="form-control" name="note"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            <strong>Previous Due / Balance</strong>
                                        </label>
                                        <input type="number" name="current_balance" id="current_balance" class="form-control" step="any" readonly="" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            <strong>Discount</strong>
                                        </label>
                                        <input type="number" name="order_discount" id="order_discount_field" class="form-control" step="any" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            <strong>Shipping Cost</strong>
                                        </label>
                                        <input type="number" name="shipping_cost" id="shipping_cost_field" class="form-control" step="any" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            <strong>Payable</strong>
                                        </label>
                                        <input type="number" name="payable_amount" id="payable_amount" class="form-control" step="any" readonly="" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            <strong>Paid Amount</strong>
                                        </label>
                                        <input type="number" name="paid_amount" id="paid_amount" class="form-control" step="any" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>
                                            <strong>Supplier New Balance</strong>
                                        </label>
                                        <input type="number" name="new_balance" id="new_balance" class="form-control" step="any" readonly="" />
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <button class="btn btn-primary " id="purchase_save_btn" name="save" type="submit">Create Purchase</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="container-fluid">
                        <table class="table table-bordered table-condensed totals">
                            <td><strong>Items</strong>
                                <span class="pull-right" id="item">0.00</span>
                            </td> 

                            <td><strong>Total</strong>
                                <span class="pull-right" id="subtotal">0.00</span>
                            </td>
                            
                            <td><strong>Order Discount</strong>
                                <span class="pull-right" id="order_discount">0.00</span>
                            </td>
                            <td><strong>Shipping Cost</strong>
                                <span class="pull-right" id="shipping_cost">0.00</span>
                            </td>
                            <td><strong>grand total</strong>
                                <span class="pull-right" id="grand_total">0.00</span>
                            </td>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('footer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

        <script type="text/javascript">
            jQuery(document).ready(function () {

                function calculateBalance(){
                    let current_balance = jQuery('#current_balance').val();
                    let grand_total = jQuery('#grand_total').text();
                    let paid_amount = jQuery('#paid_amount').val();
                    let new_balance = (Number(current_balance) + Number(grand_total)) - Number(paid_amount);
                    let payable = (Number(current_balance) + Number(grand_total));

                    jQuery('#payable_amount').val(Number(payable));

                    jQuery('#new_balance').val(Number(new_balance));

                    let balance = $('#payment_method').children("option:selected").attr('data-balance');
                    
                    if(Number(balance) < Number($('#payable_amount').val())){
                        $('#purchase_save_btn').prop('disabled', true);
                        //alert(balance);
                    }else{
                        $('#purchase_save_btn').prop('disabled', false);
                    }
                }

                $('#supplier_id').on('change', function(e) {
                    e.preventDefault();
                    let id = jQuery(this).find('option:selected').val();
                    let balance = jQuery(this).find('option:selected').attr('data-balance');
                    jQuery('#current_balance').val(Number(balance));

                    calculateBalance();
                });

                $('#paid_amount').on('change', function(e) {
                    e.preventDefault();
                    calculateBalance();
                });

                $('#paid_amount').on('keyup', function(e) {
                    e.preventDefault();
                    calculateBalance();
                });

                $('#payment_method').on('change', function(e) {
                    e.preventDefault();
                    let balance = $(this).children("option:selected").attr('data-balance');
                    
                    if(Number(balance) < Number($('#payable_amount').val())){
                        $('#purchase_save_btn').prop('disabled', true);
                        //alert(balance);
                    }else{
                        $('#purchase_save_btn').prop('disabled', false);
                    }
                });



                $('select[name="status"]').on('change', function() {
                    if($('select[name="status"]').val() == 2){
                        $(".recieved-product-qty").removeClass("d-none");
                        $(".qty").each(function() {
                            rowindex = $(this).closest('tr').index();
                            $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.recieved').val($(this).val());
                        });

                    }
                    else if(($('select[name="status"]').val() == 3) || ($('select[name="status"]').val() == 4)){
                        $(".recieved-product-qty").addClass("d-none");
                        $(".recieved").each(function() {
                            $(this).val(0);
                        });
                    }
                    else {
                        $(".recieved-product-qty").addClass("d-none");
                        $(".qty").each(function() {
                            rowindex = $(this).closest('tr').index();
                            $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ')').find('.recieved').val($(this).val());
                        });
                    }
                });

                var row_index = 1;
                $('#product_id_select').on('change', function(e) {
                    e.preventDefault();

                    let product_id = jQuery(this).find('option:selected').val();
                    $.ajax({
                        url: "{{  url('/admin/purchase/product/details/') }}/"+product_id,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $('#sale_product_tablle_body').append('<tr class="row_index row_index'+data.id+'" id="'+data.id+'">'+
                                        '<td>'+data.title+'</td>'+
                                        '<td>'+data.barcode+'</td>'+
                                        '<td><input type="number" class="form-control qty qty'+data.id+'" name="qty[]" value="1" step="any" required=""></td>'+
                                        '<td class="recieved-product-qty d-none">'+
                                            '<input type="number" class="form-control recieved" name="recieved[]" value="1" step="any">'+
                                        '</td>'+
                                        '<td class="net_unit_cost"><input type="number" class="form-control net_unit_cost net_unit_cost'+data.id+'" name="net_unit_cost[]" value="'+data.product_cost+'" step="any" required=""></td>'+
                                        '<td class="net_unit">'+data.weight_unit+'</td>'+
                                        '<td class="sub-total sub-total'+data.id+'">'+data.product_cost+'</td>'+
                                        '<td>'+
                                            '<button type="button" class="ibtnDel btn btn-md btn-danger">Delete</button>'+
                                        '</td>'+
                                        '<input type="hidden" class="product-code" name="product_code[]" value="'+data.barcode+'">'+
                                        '<input type="hidden" class="product-id" name="product_id[]" value="'+data.id+'">'+
                                        '<input type="hidden" class="purchase-unit" name="purchase_unit[]" value="'+data.weight_unit+'">'+
                                        // '<input type="hidden" class="net_unit_cost" name="net_unit_cost[]" value="'+data.product_cost+'">'+
                                        '<input type="hidden" class="subtotal-value" name="subtotal[]" value="'+data.product_cost+'">'+
                                    '</tr>');
                            row_index = row_index + 1;
                        }
                    })
                    setTimeout(function() {
                        calculateGrandTotal();
                    }, 1000);
                    
                });

                

                //Delete product
                $("#sale_product_tablle_body").on("click", ".ibtnDel", function(event) {
                    rowindex = $(this).closest('tr').attr('id');
                    $(this).closest("tr").remove();
                    setTimeout(function() {
                        calculateGrandTotal();
                    }, 1000);
                });

                $("#sale_product_tablle_body").on("click", ".qty", function(event) {
                    rowindex = $(this).closest('tr').attr('id');
                    let qty = $(this).val();
                    let cost = $('.net_unit_cost'+rowindex).val();
                    let sub_total = Number(cost) * Number(qty);
                    $(this).closest('tr').find('td.sub-total').text(Number(sub_total));
                    $(this).closest('tr').find('input.subtotal-value').val(Number(sub_total));

                    setTimeout(function() {
                        calculateGrandTotal();
                    }, 1000);
                });

                $("#sale_product_tablle_body").on("keyup", ".qty", function(event) {
                    rowindex = $(this).closest('tr').attr('id');
                    let qty = $(this).val();
                    let cost = $('.net_unit_cost'+rowindex).val();
                    let sub_total = Number(cost) * Number(qty);
                    $(this).closest('tr').find('td.sub-total').text(Number(sub_total));
                    $(this).closest('tr').find('input.subtotal-value').val(Number(sub_total));

                    setTimeout(function() {
                        calculateGrandTotal();
                    }, 1000);
                });

                // unit cost change 
                $("#sale_product_tablle_body").on("click", ".net_unit_cost", function(event) {
                    setTimeout(function() {
                        calculateGrandTotal();
                    }, 1000);
                });

                $("#sale_product_tablle_body").on("keyup", ".net_unit_cost", function(event) {
                    setTimeout(function() {
                        calculateGrandTotal();
                    }, 1000);
                });

                $("#order_discount_field, #shipping_cost_field").keyup(function(){
                    // alert('test');
                    setTimeout(function() {
                        calculateGrandTotal();
                    }, 1000);
                })


                function calculateGrandTotal(){
                    let total_item = 0;
                    let total_qty = 0;
                    let total_subtotal = 0;
                    let total_discount = $('input[name="order_discount"]').val();
                    let total_shipping_cost = $('input[name="shipping_cost"]').val();
                    let grand_total = 0;

                    
                    $('.row_index').each(function(key, val) {
                        let id = $(this).attr('id');
                        let qty = $('.row_index'+id).find('.qty'+id).val();
                        let cost = $('.net_unit_cost'+id).val();
                        let sub_total = Number(cost) * Number(qty);

                        total_qty = total_qty + Number(qty);
                        total_subtotal = total_subtotal + Number(sub_total);
                        
                        total_item = Number(total_item) + Number(1);

                        $('.row_index'+id).find('.qty'+id).val();
                        $('.row_index'+id).find('td.sub-total').text(sub_total);
                    })

                    grand_total = Number(total_subtotal) + Number(total_shipping_cost) - Number(total_discount);

                    
                    $('#total-qty').text(Number(total_qty));
                    $('#total').text(Number(total_subtotal));

                    $('#item').text(Number(total_item));
                    $('#subtotal').text(Number(total_subtotal));
                    $('#order_discount').text(Number(total_discount));
                    $('#shipping_cost').text(Number(total_shipping_cost));
                    $('#grand_total').text(Number(grand_total));


                    $('input[name="item"]').val(Number(total_item));
                    $('input[name="total_qty"]').val(Number(total_qty));
                    $('input[name="total_cost"]').val(Number(total_subtotal));
                    $('input[name="total_discount"]').val(Number(total_discount));
                    $('input[name="grand_total"]').val(Number(grand_total));

                    calculateBalance();

                    let balance = $('#payment_method').children("option:selected").attr('data-balance');
                    
                    if(Number(balance) < Number($('#payable_amount').val())){
                        $('#purchase_save_btn').prop('disabled', true);
                        //alert(balance);
                    }else{
                        $('#purchase_save_btn').prop('disabled', false);
                    }

                }

            })
        </script>
    @endpush
@endsection
