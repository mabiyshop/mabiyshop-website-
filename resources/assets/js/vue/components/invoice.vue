<template>
<div>


<section id="print_invoice">
<table style="width: 650px;margin:50px auto">
   <tbody>
      <tr>
         <td style="width: 375px;">
            <p><strong>{{ $t('Delivery Information') }}</strong><br>

            @php 
               
               $address = \DB::table('addresses')->where('id',$order->address_id)->first();
               $shipping_union = \DB::table('unions')->where('id',$address->shipping_union)->first();
               $shipping_thana = \DB::table('upazilas')->where('id',$address->shipping_thana)->first();
               $shipping_district = \DB::table('districts')->where('id',$address->shipping_district)->first();
               $shipping_division = \DB::table('divisions')->where('id',$address->shipping_division)->first();


            @endphp
            Address: {{$address->shipping_first_name.' '.$address->shipping_last_name}}, {{$address->shipping_address}}
            {{$shipping_union->title ?? ''}},{{$shipping_thana->title ?? ''}},{{$shipping_district->title ?? ''}},{{$shipping_division->title ?? '' }}<br>
            Call: {{$address->shipping_phone}}<br>
            Email: {{$address->shipping_email}}</p>
         </td>

         <td style="width: 375px;text-align:right;">
            <p><img @error="imageLoadError"  src="{{ asset('uploads/images/'.config('concave.cnf_logo'))}}" alt="" width="180" /> <br>{{ config('concave.cnf_address') }}<br> {{ $t('Call') }}: {{ config('concave.cnf_phone') }}<br> {{ $t('Email') }}: {{ config('concave.cnf_email') }}</p>
         </td>
      </tr>
      <tr>
         <td style="width: 375px;">&nbsp;</td>
         <td style="width: 375px;">&nbsp;</td>
      </tr>
   </tbody>
</table>

<table style="width: 650px;margin:50px auto">
   <tbody>
      <tr>
         <td style="width: 650px;">
            <h2 style="text-align: center;">{{ $t('INVOICE') }}</h2>
         <h5 style="text-align: center;"> {{ $t('Order ID') }} #{{$order->id}}</h5>
         </td>
      </tr>
   </tbody>
</table>

<table id="product" style="width: 650px;margin:50px auto">
   <tbody>
      <tr style="padding: 5px;">
         <td style="width: 70px; text-align: left;"><strong> {{ $t('Serial') }}</strong></td>
         <td style="width: 300px; text-align: left;"><strong>{{ $t('Product Description') }}</strong></td>
         <td style="width: 120px; text-align: left;"><strong> {{ $t('Price') }}</strong></td>
         <td style="width: 120px; text-align: right;"><strong> {{ $t('Sub Total') }}</strong></td>
      </tr>
      @php $subtotal = 0; $shipping_cost =0; @endphp
    @foreach($order->order_details as $item)
      @php 
         if(\Auth::user()->getRoleNames()[0] == 'seller'){
            if(\Auth::id() != $item->seller_id){
               continue;
            }
         }
         
         $subtotal += ($item->product_qty*$item->price); 
         $shipping_cost += $item->shipping_cost;
      @endphp
      <tr style="padding: 5px;">
         <td style="width: 70px; text-align: left;">{{$loop->index + 1}}.</td>
         <td style="width: 300px; text-align: left;">{{ $item->product_qty }} X {{ $item->product->title }}</td>
         <td style="width: 120px; text-align: left;">BDT {{ $item->price }}</td>
         <td style="width: 120px; text-align: right;">BDT {{ ($item->product_qty*$item->price) }}</td>
      </tr>
    @endforeach


   </tbody>
</table>



<table style="width: 650px;text-align:right; margin:50px auto">
   <tbody>
      <tr>
         <td style="width: 282px;">&nbsp;</td>
         <td style="width: 303px;">
            <p><strong>{{ $t('Sub Total') }}:&nbsp;</strong>BDT {{$subtotal}}</p>
            <p><strong>{{ $t('Shipping Cost') }}:&nbsp;</strong>BDT {{$shipping_cost}}</p>
            <p><strong> {{ $t('Paid Amount') }}:&nbsp;</strong>BDT {{ ($subtotal+$shipping_cost) }}</p>
         </td>
      </tr>
   </tbody>
</table>


<table style="width: 650px;text-align:left; margin:50px auto">
   <tbody>
      <tr>
         <td style="width: 320px;px;">
            <p><strong>{{ $t('Date/Time') }} :</strong>&nbsp;@php $dt = new DateTime('now', new DateTimezone('Asia/Dhaka'));
                echo $dt->format('F j, Y, g:i a'); @endphp</p>
         </td>
         <td style="width: 306px;">&nbsp;</td>
      </tr>
   </tbody>
</table>
</section>



</div>
</template>




<script>
    function printInvoice(div){
      var divToPrint= jQuery('#print_invoice').html();

      var newWin=window.open('','Print-Window');
      newWin.document.open();
      newWin.document.write('<html><link href=""><body onload="window.print()">'+divToPrint+'</body></html>');
      newWin.document.close();
      setTimeout(function(){newWin.close();},100);
    }
</script>


<script>
export default {
	methods:{
      methods:{
         imageLoadError(event){
            event.target.src = "/images/notfound.png";
         },
      }
	},
}
</script>
