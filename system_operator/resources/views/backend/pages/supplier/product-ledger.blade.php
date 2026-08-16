@extends('backend.layouts.master')

@section('title', 'Supplier Product Ledger - ' . config('concave.cnf_appname'))

    @section('content')

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <p class="content_title">Supplier Product Ledger</p>
                    </div>
                    <div class="">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bb-1">
                                    <th scope="col"><b>SN.</b></th>
                                    <th scope="col"><b>Product Info</b></th>
                                    <th scope="col"><b>Purchase Quantity</b></th>
                                    <th scope="col"><b>Purchase Price</b></th>
                                    <th scope="col"><b>Return Quantity</th>
                                    <th scope="col"><b>Return Price</b></th>
                                </tr>
                            </thead>
                            <tbody>
                            	@foreach($products as $row)
	                                <tr>
	                                    <td>{{ $loop->iteration}}</td>
	                                    <td class="text-left">
	                                    	{{ $row->product->title }}
	                                    </td>
	                                    <td>{{ \App\Models\ProductPurchase::where('invoice_no', NULL)->where('product_id', $row->product_id)->sum('qty') }}</td>
	                                    <td>{{ \App\Models\ProductPurchase::where('invoice_no', NULL)->where('product_id', $row->product_id)->sum('total') }}</td>
	                                    <td>{{ \App\Models\ProductPurchase::where('invoice_no', '!=', NULL)->where('product_id', $row->product_id)->sum('total') }}</td>
	                                    <td>{{ \App\Models\ProductPurchase::where('invoice_no', '!=', NULL)->where('product_id', $row->product_id)->sum('total') }}</td>
	                                </tr>
	                                
	                            @endforeach
                                <tr>
                                    <td class="text-right" colspan="4"><b>Total Sold = {{ number_format(\App\Models\ProductPurchase::where('invoice_no', NULL)->sum('total'),2) }}</b></td>
                                    <td class="text-right" colspan="2"><b>Total Return = {{ number_format(\App\Models\ProductPurchase::where('invoice_no', '!=', NULL)->sum('total'),2) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('footer')
        <script type="text/javascript">
            
        </script>
    @endpush
@endsection