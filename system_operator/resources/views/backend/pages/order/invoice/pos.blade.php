<html lang="en"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/invoice/pos.css') }}">
    <title>Pos Print |  {{ config('concave.cnf_appname') }}</title>
    <style>
        *{
            font-size: 8px !important;
            font-family: Arial, Helvetica;
        }
    </style>
</head>
<body>
    <div class="ticket" style="margin-top: -5px;">
        
        <p class="centered">
            <img style="width: 100px;" src="{{ asset('uploads/images/frontendlogo.png') }}">
            <br><span style="font-size: 20px; font-weight: bold;">{{ config('concave.cnf_appname') }}</span>
            <br>{{ config('concave.cnf_address') }}
            <br>{{ config('concave.cnf_phone') }}
            <br>{{ config('concave.cnf_email') }}
        </p>
            <div>
                <table>
                    <tbody><tr style="">
                        <td style="font-size: 8px; text-align: left; width: 100%; border-top: 1px solid white;">
                            Bill To, {{$order->historical_shipping_address->shipping_first_name ?? ''.' '.$order->historical_shipping_address->shipping_last_name ?? ''}}<br>
                            {{ $order->user->phone ?? '' }} <br>
                            @if($order->is_pickpoint == 1)
                                {{$order->pickpoint_address->title}}, {{$order->pickpoint_address->union->title ?? ''}}, {{$order->pickpoint_address->upazila->title ?? ''}},{{$order->pickpoint_address->district->title ?? ''}},{{$order->pickpoint_address->division->title ?? ''}}
                            @else
                                {{ $order->historical_shipping_address->shipping_address ?? ''}}
                            @endif
                            <br>Inv #MS{{ date('Y', strtotime($order->created_at))}}{{$order->id}}
                            <br>Date: {{ date('d-m-Y', strtotime($order->created_at))}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            
        <table>
            <thead>
                <tr>
                    <th style="text-align: left;" class="description">P Name</th>
                    <th class="quantity">Qty.</th>
                    <th class="quantity">Unit P</th>
                    <th class="price">Total</th>
                </tr>
            </thead>
            
            <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach($order->order_details as $details)
                        <tr>
                            <td class="description">
                                {{$details->product->title ?? '' }}
                                @if($details->product->product_type == 'variable' || $details->product->product_type == 'service')
                                    <br>
                                    <small><b>SKU:</b> {{ $details->product_sku }}</small><br>
                                    @php 
                                    $variable_option = json_decode($details->product_options);
                                    @endphp
                                    @if($variable_option)
                                    @foreach($variable_option as $key=> $val)
                                        <span style="margin-right: 5px;"> <b>{{ $key }}: </b> {{ $val }}</span>
                                    @endforeach
                                    @endif
                                    <br>
                                @else
                                    <small><b>SKU:</b> {{ $details->product_sku }}</small><br>
                                @endif
                            </td>
                            <td class="quantity">
                                
                                {{ $details->product_qty }}
                                
                            </td>
                            <td class="quantity">{{ number_format((float)$details->price , 2, '.', '') }} </td>
                            <td style="text-align: center;"><span style="font-size: 8px;">{{ number_format((float)$details->product_qty *  $details->price , 2, '.', '') }}</span></td>
                            @php $subtotal += ($details->product_qty *  $details->price); @endphp
                        </tr>
                    @endforeach
                
                
                
                <tr>
                    <td class="quantity"></td>
                    <td colspan="2" style="font-size: 9px;" class="description">Subtotal: </td>
                    <td style="font-size: 10px;text-align: center;" class="">{{ number_format((float)$subtotal , 2, '.', '')}}</td>
                </tr>   
                <tr style="border: 1px dashed white !important;">
                    <td style="border: 1px dashed white !important;" class="quantity"></td>
                    <td colspan="2" style="font-size: 9px; border: 1px dashed white !important;" class="description">Shipping Cost: </td>
                    <td style="text-align: center;font-size: 10px; border: 1px dashed white !important;" class="">{{ number_format((float)$order->shipping_cost , 2, '.', '')}}</td>
                </tr>     
                <tr style="border: 1px dashed white !important;">
                    <td style="border: 1px dashed white !important;" class="quantity"></td>
                    <td colspan="2" style="font-size: 9px; border: 1px dashed white !important;" class="description">Discount: </td>
                    <td style="text-align: center;font-size: 10px; border: 1px dashed white !important;" class="">{{ number_format((float)$order->discount_amount , 2, '.', '')}}</td>
                </tr>    
                <tr style="border: 1px dashed white !important;">
                    <td style="border: 1px dashed white !important;" class="quantity"></td>
                    <td colspan="2" style="font-size: 9px; border: 1px dashed white !important;" class="description">Total Gross: </td>
                    <td style="text-align: center;font-size: 10px; border: 1px dashed white !important;" class="">{{ number_format((float)$order->total_amount , 2, '.', '')}}</td>
                </tr>         
                    
                <tr style="border: 1px dashed white !important;">
                    <td style="border: 1px dashed white !important;" class="quantity"></td>
                    <td colspan="2" style="font-size: 9px; border: 1px dashed white !important;" class="description">Total Payable</td>
                    <td style="text-align: center;font-size: 10px; border: 1px dashed white !important;" class="">{{ number_format((float)$order->total_amount - $order->paid_amount , 2, '.', '')}}</td>
                </tr>
                <tr style="border: 1px dashed white !important;">
                    <td style="border: 1px dashed white !important;" class="quantity"></td>
                    <td colspan="2" style="font-size: 9px; border: 1px dashed white !important;" class="description">Paid</td>
                    <td style="text-align: center;font-size: 10px; border: 1px dashed white !important;" class="">{{ number_format((float)$order->paid_amount , 2, '.', '')}}</td>
                </tr>
            </tbody>
        </table>
        
            <p style="line-height: 3px;"><b>*Payment By:</b>&nbsp;&nbsp; no ()</p>
            <p style="line-height: 3px;"><b>Created By:</b>&nbsp; {{ config('concave.cnf_appname') }}</p>
            <p style="font-size: 8px; background-color: #060709; border-radius: 10px; padding: 3px 1px; color: #ffffff; font-weight: bold;" class="centered">Varclone IT Solution</p>
        <div>
            .................................................................................
            .................................................................................
        </div>
    </div>
    <button id="btnPrint" class="hidden-print">Print</button>
    <script>
        const $btnPrint = document.querySelector("#btnPrint");
        $btnPrint.addEventListener("click", () => {
            window.print();
        });
    </script>
    <script>
        window.onload = function () {
            window.print();
        }
    </script>

</body></html>
