<div class="row purchase_print_div">
	<div class="col-lg-6">
		<strong>Supplier Information</strong><br>
		<span >Name: </span>  {{$purchase->supplier->name}}
        <br>
        <span>Phone: </span>  {{$purchase->supplier->phone_number}}
        <br>
        <span>E-mail: </span>  {{$purchase->supplier->email}}
        <span>Address: </span> {{$purchase->supplier->address}}
	</div>
	<div class="col-lg-6 text-right">
		<img src="/{{ Helper::getsettings('header_logo') }}"  alt="" width="70" />
        <br>
        <span></span> {{ config('concave.cnf_address') }} 
        <br>
        <span>  HP: </span> {{ config('concave.cnf_phone') }}
        <br>
        <span>  E-mail: </span> {{ config('concave.cnf_email') }}
	</div>
	<div class="col-lg-12">
		<hr>
	</div>
</div>

<div class="row" style="display: flex; -ms-flex-wrap: wrap;flex-wrap: wrap;padding: 0!important;">
    <div class="col-md-6" style="flex: 0 0 55%;max-width: 55%;position: relative;text-align: right!important;padding: 0!important;"> 
    	<span style="text-transform: uppercase;font-size: 20px;font-weight: 600;">Invoice</span>  
    </div>
    <div class="col-md-6" style="flex: 0 0 45%;max-width: 45%;position: relative;text-align: right!important;padding: 0!important;">  
    	<span style="text-transform: uppercase;font-size: 14px;font-weight: 600;">  Purchase Id: MBY{{ date("y", strtotime($purchase->created_at)) }}{{$purchase->id}} </span> 
    </div>
</div>

<div class="row">
	<div class="col-lg-12">
		<table class="table">
		   	<tbody>
		      	<tr >
		          	<td><strong>  SL</strong></td>
		          	<td><strong> Item</strong></td>
		          	<td><strong> Qty</strong></td>
		          	<td><strong> Price</strong></td>
		          	<td><strong>  Sub Total</strong></td>
		      	</tr>

		    	@foreach($purchase->purchaseProducts as $item)
		      		
			      	<tr>
			         	<td>{{$loop->index + 1}}.</td>
			         	<td>
				            {{ $item->product->title }} <br>
				            <small><b>SKU:</b> {{ $item->product->sku }}</small><br>
			          	</td>
			         	<td>
			         		<span> {{ $item->qty }} {{ $item->purchase_unit_id }}</span>
			         	</td>
			         	<td>BDT <span> {{ $item->net_unit_cost }}</span></td>
			         	<td>BDT {{ $item->total }}</td>
			      	</tr>
		    	@endforeach
		    	<tr>
		    		<td colspan="3"></td>
			      	<td class="text-right"><b>Sub Total:</b></td>
			      	<td>BDT {{ $purchase->purchaseProducts->sum('total') }}</td>
			    </tr>
			    <tr>
		    		<td colspan="3"></td>
			      	<td class="text-right"><b>Discount:</b></td>
			      	<td>BDT {{ $purchase->total_discount }}</td>
			    </tr>
			    <tr>
		    		<td colspan="3"></td>
			      	<td class="text-right"><b>Shipping Cost:</b></td>
			      	<td>BDT {{ $purchase->total_cost }}</td>
			    </tr>
			    <tr>
		    		<td colspan="3"></td>
			      	<td class="text-right"><b>Grand Total:</b></td>
			      	<td>BDT {{ $purchase->grand_total }}</td>
			    </tr>
			    <tr class="text-success">
		    		<td colspan="3"></td>
			      	<td class="text-right"><b>Paid Amount:</b></td>
			      	<td>BDT {{ $purchase->paid_amount }}</td>
			    </tr>
		   	</tbody>
		</table>
	</div>
	<div class="col-lg-12 text-center mt-2">
		<p><strong>Purchase Note: </strong>{{$purchase->note ?? ''}}</p>
        <p style="text-align: center;"><img src="data:image/png;base64, {{DNS1D::getBarcodePNG('MS'.date("y", strtotime($purchase->created_at)).$purchase->id, 'C39',5,10) }}" alt=""  width="150px" height="30px"></p>
	</div>
</div>



