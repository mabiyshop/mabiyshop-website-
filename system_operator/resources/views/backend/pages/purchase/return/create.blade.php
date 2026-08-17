@extends('backend.layouts.master')
@section('title','Purchase Return Create - '.config('concave.cnf_appname'))
@section('content')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet" />
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Return > Create Return</span>
                <a class="btn btn-success float-right" href="{{ route('admin.purchase')}}">View Return List</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <form class="form-sample" method="post" action="{{ route('admin.purchase.return.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
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

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Invoice <span style="color: #f00">*</span></label>
                                    <select name="invoice_no" class="selectpicker form-control" id="invoice_no" data-live-search="true" data-live-search-style="begins" title="Select Invoice...">
                                        
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row"> 
                            <div class="col-md-12 ">
                                <label>Select Product</label>
                                <div class="search-box input-group">
                                    <select name="product_id_select" id="product_id_select" class="selectpicker form-control" data-live-search="true"  title="Select products...">
                                        
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 mb-4">
                            <div class="col-md-12">
                                <h5>Return Table *</h5>
                                <div class="table-responsive mt-3">
                                    <table id="myTable" class="table table-hover order-list">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Quantity</th>
                                                <th>Net Unit Cost</th>
                                                <th>Unit</th>
                                                <th>Subtotal</th>
                                                <th><i class="mdi mdi-trash-can-outline"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody id="sale_product_tablle_body">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Note</label>
                                    <textarea rows="5" class="form-control" name="note"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Total Item</label>
                                    <input type="text" name="total_item" class="form-control" id="total_item" readonly="">
                                </div>
                                <div class="form-group">
                                    <label>Total Qty</label>
                                    <input type="text" name="total_qty" class="form-control" id="total_qty" readonly="">
                                </div>
                                <div class="form-group">
                                    <label>Sub Total</label>
                                    <input type="text" name="subtotal" class="form-control" id="subtotal" readonly="">
                                </div>
                                <div class="form-group">
                                    <label>Supplier Current Balance</label>
                                    <input type="text" name="current_balance" class="form-control" id="current_balance" readonly="">
                                </div>

                                <div class="form-group">
                                    <label>Supplier Balance - Invoice Current Total</label>
                                    <input type="text" name="grand_total" class="form-control" id="grand_total" readonly="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <button class="btn btn-primary" name="save" type="submit">Create Purchase</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('footer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

        <script type="text/javascript">
            jQuery(document).ready(function () {
                $('#supplier_id').on('change', function(e) {
                    e.preventDefault();
                    let id = jQuery(this).find('option:selected').val();
                    let balance = jQuery(this).find('option:selected').attr('data-balance');
                    $.ajax({
                        url: "{{  url('/admin/purchase/get/supper/invoice') }}",
                        type: "GET",
                        dataType: "html",
                        data:{
                            supplier_id:id,
                        },
                        success: function (data) {
                            jQuery('#invoice_no').empty();
                            jQuery('#invoice_no').append('<option value="-1">-- Select --</option>'+data);
                            $('.selectpicker').selectpicker('refresh');
                            jQuery('#current_balance').val(Number(balance));
                        }
                    })
                });

                $('#invoice_no').on('change', function(e) {
                    e.preventDefault();
                    let id = jQuery(this).find('option:selected').val();
                    $.ajax({
                        url: "{{  url('/admin/purchase/invoice/products') }}",
                        type: "GET",
                        dataType: "html",
                        data:{
                            invoice_id:id,
                        },
                        success: function (data) {
                            jQuery('#product_id_select').empty();
                            jQuery('#product_id_select').append('<option value="-1">-- Select --</option>'+data);
                            $('.selectpicker').selectpicker('refresh');
                        }
                    })
                });


                var row_index = 1;
                $('#product_id_select').on('change', function(e) {
                    e.preventDefault();
                    let product_id = jQuery(this).find('option:selected').val();
                    let invoice_id = jQuery(this).find('option:selected').attr('data-invoice');
                    alert(invoice_id);
                    $.ajax({
                        url: "{{  url('/admin/purchase/invoice/products/details/') }}",
                        type: "GET",
                        dataType: "json",
                        data:{
                            product_id:product_id,
                            invoice_id:invoice_id
                        },
                        success: function (data) {
                            $('#sale_product_tablle_body').append('<tr class="row_index row_index'+data.product_id+'" id="'+data.product_id+'">'+
                                        '<td>'+data.product.title+'</td>'+
                                        '<td><input type="number" class="form-control qty qty'+data.product_id+'" name="qty[]" value="'+data.qty+'" step="any" max="'+data.qty+'" required=""></td>'+
                                        '<td class="net_unit_cost"><input type="number" class="form-control net_unit_cost net_unit_cost'+data.product_id+'" name="net_unit_cost[]" value="'+data.net_unit_cost+'" step="any" required="" readonly=""></td>'+
                                        '<td class="net_unit">'+data.product.weight_unit+'</td>'+
                                        '<td class="sub-total sub-total'+data.product_id+'">'+data.net_unit_cost+'</td>'+
                                        '<td>'+
                                            '<button type="button" class="ibtnDel btn btn-md btn-danger">Delete</button>'+
                                        '</td>'+
                                        '<input type="hidden" class="product-id" name="product_id[]" value="'+data.product_id+'">'+
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


                function calculateGrandTotal(){
                    let total_item = 0;
                    let total_qty = 0;
                    let total_subtotal = 0;
                    let current_balance = jQuery('#current_balance').val();
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

                    grand_total =  Number(current_balance) - Number(total_subtotal);

                    $('#total_item').val(Number(total_item));
                    $('#total_qty').val(Number(total_qty));
                    $('#subtotal').val(Number(total_subtotal));
                    $('#grand_total').val(Number(grand_total));
                }

            })
        </script>
    @endpush
@endsection
