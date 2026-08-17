<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Pos Print |  {{ config('concave.cnf_appname') }}</title>
        
        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
                text-indent: 0;
            }
            h1 {
                color: black;
                font-family: "Times New Roman", serif;
                font-style: normal;
                font-weight: bold;
                text-decoration: none;
                font-size: 11pt;
            }
            .p,
            p {
                color: black;
                font-family: "Times New Roman", serif;
                font-style: normal;
                font-weight: normal;
                text-decoration: none;
                font-size: 9.5pt;
                margin: 0pt;
            }
            a {
                color: black;
                font-family: "Times New Roman", serif;
                font-style: normal;
                font-weight: normal;
                text-decoration: none;
                font-size: 9.5pt;
            }
            h2 {
                color: black;
                font-family: "Times New Roman", serif;
                font-style: normal;
                font-weight: bold;
                text-decoration: none;
                font-size: 9.5pt;
            }
            .s1 {
                color: black;
                font-family: "Times New Roman", serif;
                font-style: normal;
                font-weight: bold;
                text-decoration: none;
                font-size: 9.5pt;
            }
            .s2 {
                color: black;
                font-family: "Times New Roman", serif;
                font-style: normal;
                font-weight: normal;
                text-decoration: none;
                font-size: 9.5pt;
            }
            .s3 {
                color: black;
                font-family: "Times New Roman", serif;
                font-style: normal;
                font-weight: normal;
                text-decoration: none;
                font-size: 12pt;
            }
            .s4 {
                color: black;
                font-family: "Times New Roman", serif;
                font-style: normal;
                font-weight: normal;
                text-decoration: none;
                font-size: 1pt;
            }
            table,
            tbody {
                vertical-align: top;
                overflow: visible;
            }
        </style>
    </head>
    <body>
        <p style="text-indent: 0pt; text-align: left;">
            <span>
                <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
                    <tr>
                        <td><img width="205" height="65" src="{{ asset('uploads/images/frontendlogo.png') }}" /></td>
                        <td>
                            <h1 style="padding-top: 3pt; text-indent: 0pt; text-align: right;">{{ config('concave.cnf_appname') }}</h1>
                            <p style="text-indent: 0pt; text-align: right;">{{ config('concave.cnf_address') }}</p>
                            <p style="text-indent: 0pt; text-align: right;">{{ config('concave.cnf_phone') }}</p>
                            <p style="text-indent: 0pt; text-align: right;"><a href="mailto:{{ config('concave.cnf_email') }}">{{ config('concave.cnf_email') }}</a></p>
                            <p style="text-indent: 0pt; text-align: right;">mabiyshop.com</p>
                        </td>
                    </tr>
                </table>
            </span>
        </p>
        <div style="display: flex; justify-content: space-between;">
            <div>
                <h2 style="padding-left: 9pt; text-indent: 0pt; text-align: left;">Bill To, 
                    <br>
                    @if($order->is_pickpoint == 1)
                        <span style="font-weight: 600;"> Name: </span>  {{$order->user->name ?? ''}}
                        <br>
                        <span style="font-weight: 600;">  Phone: </span>  {{ $order->user->phone ?? ''}}
                        <br>
                        <span style="font-weight: 600;">  E-mail: </span>  {{$order->user->email ?? ''}}
                        <br>
                        <span style="font-weight: 600;">  Address: </span> 
                        {{$order->pickpoint_address->address}}, <br>{{$order->pickpoint_address->union->title ?? ''}}, {{$order->pickpoint_address->upazila->title ?? ''}},{{$order->pickpoint_address->district->title ?? ''}},{{$order->pickpoint_address->division->title ?? ''}}
                    @else
                        <span style="font-weight: 600;"> Name: </span>  {{$order->historical_shipping_address->shipping_first_name ?? ''.' '.$order->historical_shipping_address->shipping_last_name ?? ''}}
                        <br>
                        <span style="font-weight: 600;">  Phone: </span>  {{ $order->historical_shipping_address->shipping_phone ?? ''}}
                        <br>
                        <span style="font-weight: 600;">  E-mail: </span>  @if(filter_var($order->user->phone, FILTER_VALIDATE_EMAIL)) {{$order->user->shipping_email ?? $order->user->phone ?? ''}} @endif
                        <br>
                        <span style="font-weight: 600;">  Address: </span> 
                        {{$order->historical_shipping_address->shipping_address}}, <br>{{$order->historical_shipping_address->union->title ?? ''}}, 
                        {{$order->historical_shipping_address->upazila->title ?? ''}},
                        {{$order->historical_shipping_address->district->title ?? ''}},
                        {{$order->historical_shipping_address->division->title ?? ''}}
                    @endif
                </h2>
                
            </div>
            <div>
                <h2 style="padding-top: 4pt; text-indent: 0pt; text-align: right;">Invoice #MS{{ date('Y', strtotime($order->created_at))}}{{$order->id}}</h2>
                <h2 style="text-indent: 0pt; text-align: right;">Date: {{ date('d-m-Y', strtotime($order->created_at))}}</h2>
                <h2 style="padding-left: 42pt; text-indent: 0pt; text-align: right;">Voucher:</h2>
            </div>
        </div>
        <table style="border-collapse: collapse; margin-left: 6.125pt;" cellspacing="0">
            <tr style="height: 20pt;">
                <td
                    style="
                        width: 37pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                    bgcolor="#DDDDDD"
                >
                    <p class="s1" style="padding-top: 4pt; padding-right: 11pt; text-indent: 0pt; text-align: right;">SN</p>
                </td>
                <td
                    style="
                        width: 271pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                    bgcolor="#DDDDDD"
                >
                    <p class="s1" style="padding-top: 4pt; padding-left: 4pt; text-indent: 0pt; text-align: left;">Product Name</p>
                </td>
                <td
                    style="
                        width: 64pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                    bgcolor="#DDDDDD"
                >
                    <p class="s1" style="padding-top: 4pt; padding-left: 16pt; padding-right: 16pt; text-indent: 0pt; text-align: center;">Qty</p>
                </td>
                <td
                    style="
                        width: 86pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                    bgcolor="#DDDDDD"
                >
                    <p class="s1" style="padding-top: 4pt; padding-left: 31pt; padding-right: 30pt; text-indent: 0pt; text-align: center;">Price</p>
                </td>
                <td
                    style="
                        width: 99pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                    bgcolor="#DDDDDD"
                >
                    <p class="s1" style="padding-top: 4pt; padding-left: 24pt; padding-right: 23pt; text-indent: 0pt; text-align: center;">Subtotal</p>
                </td>
            </tr>
            @php 
                $subtotal = 0;
            @endphp
            @foreach($order->order_details as $details)
                @php
                    if(\Auth::user()->getRoleNames()[0] == 'seller'){
                        if(\Auth::id() != $details->seller_id){
                        continue;
                        }
                    }
                @endphp
                <tr style="height: 20pt;">
                    <td
                        style="
                            width: 37pt;
                            border-top-style: solid;
                            border-top-width: 1pt;
                            border-left-style: solid;
                            border-left-width: 1pt;
                            border-bottom-style: solid;
                            border-bottom-width: 1pt;
                            border-right-style: solid;
                            border-right-width: 1pt;
                        "
                    >
                        <p class="s1" style="padding-top: 4pt; padding-right: 15pt; text-indent: 0pt; text-align: right;">{{ $loop->iteration }}</p>
                    </td>
                    <td
                        style="
                            width: 271pt;
                            border-top-style: solid;
                            border-top-width: 1pt;
                            border-left-style: solid;
                            border-left-width: 1pt;
                            border-bottom-style: solid;
                            border-bottom-width: 1pt;
                            border-right-style: solid;
                            border-right-width: 1pt;
                        "
                    >
                        <p class="s2" style="padding-top: 4pt; padding-left: 4pt; text-indent: 0pt; text-align: left;">
                            {{$details->product->title ?? '' }}
                            @if($details->product->product_type == 'variable' || $details->product->product_type == 'service')
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
                        </p>
                    </td>
                    <td
                        style="
                            width: 64pt;
                            border-top-style: solid;
                            border-top-width: 1pt;
                            border-left-style: solid;
                            border-left-width: 1pt;
                            border-bottom-style: solid;
                            border-bottom-width: 1pt;
                            border-right-style: solid;
                            border-right-width: 1pt;
                        "
                    >
                        <p class="s2" style="padding-top: 4pt; padding-left: 16pt; padding-right: 16pt; text-indent: 0pt; text-align: center;">
                            
                            {{ $details->product_qty }}
                            
                        </p>
                    </td>
                    <td
                        style="
                            width: 86pt;
                            border-top-style: solid;
                            border-top-width: 1pt;
                            border-left-style: solid;
                            border-left-width: 1pt;
                            border-bottom-style: solid;
                            border-bottom-width: 1pt;
                            border-right-style: solid;
                            border-right-width: 1pt;
                        "
                    >
                        <p class="s2" style="padding-top: 4pt; padding-left: 23pt; text-indent: 0pt; text-align: left;"> 
                            @if($details->discount)
                                <del>{{ number_format((float) $details->discount , 2, '.', '') }} Tk</del>
                            @endif 
                            {{ number_format((float)$details->price , 2, '.', '') }} Tk</p>
                    </td>
                    <td
                        style="
                            width: 99pt;
                            border-top-style: solid;
                            border-top-width: 1pt;
                            border-left-style: solid;
                            border-left-width: 1pt;
                            border-bottom-style: solid;
                            border-bottom-width: 1pt;
                            border-right-style: solid;
                            border-right-width: 1pt;
                        "
                    >
                        <p class="s2" style="padding-top: 4pt; padding-left: 24pt; padding-right: 23pt; text-indent: 0pt; text-align: center;"> {{ number_format((float)$details->product_qty *  $details->price , 2, '.', '') }} Tk</p>
                        @php $subtotal +=  ($details->product_qty *  $details->price); @endphp
                    </td>
                </tr>
            @endforeach
        </table>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <table style="border-collapse: collapse; margin-left: 6.125pt;" cellspacing="0">
            <tr style="height: 20pt;">
                <td
                    style="
                        width: 436pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-top-color: #ffffff;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-left-color: #ffffff;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-bottom-color: #ffffff;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s1" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">Sub Total</p>
                </td>
                <td
                    style="
                        width: 121pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s2" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">{{ number_format((float)$subtotal , 2, '.', '')}} Tk</p>
                </td>
            </tr>
            <tr style="height: 20pt;">
                <td style="width: 436pt; border-bottom-style: solid; border-bottom-width: 1pt; border-bottom-color: #ffffff; border-right-style: solid; border-right-width: 1pt;">
                    <p class="s1" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">Shipping Cost</p>
                </td>
                <td
                    style="
                        width: 121pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s2" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">{{ number_format((float)$order->shipping_cost , 2, '.', '')}} Tk</p>
                </td>
            </tr>
            <tr style="height: 20pt;">
                <td style="width: 436pt; border-bottom-style: solid; border-bottom-width: 1pt; border-bottom-color: #ffffff; border-right-style: solid; border-right-width: 1pt;">
                    <p class="s1" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">Discount</p>
                </td>
                <td
                    style="
                        width: 121pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s2" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">{{ number_format((float)$order->discount_amount , 2, '.', '')}} Tk</p>
                </td>
            </tr>
            <tr style="height: 20pt;">
                <td style="width: 436pt; border-bottom-style: solid; border-bottom-width: 1pt; border-bottom-color: #ffffff; border-right-style: solid; border-right-width: 1pt;">
                    <p class="s1" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">Total Gross</p>
                </td>
                <td
                    style="
                        width: 121pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s2" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">{{ number_format((float)$order->total_amount , 2, '.', '')}} Tk</p>
                </td>
            </tr>

            <tr style="height: 20pt;">
                <td
                    style="
                        width: 436pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-top-color: #ffffff;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-left-color: #ffffff;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s1" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">Paid</p>
                </td>
                <td
                    style="
                        width: 121pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s2" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">{{ number_format((float)$order->paid_amount , 2, '.', '')}} TK</p>
                </td>
            </tr>
            
            <tr style="height: 20pt;">
                <td
                    style="
                        width: 436pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-top-color: #ffffff;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-left-color: #ffffff;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-bottom-color: #ffffff;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s1" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">Due</p>
                </td>
                <td
                    style="
                        width: 121pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                    "
                >
                    <p class="s2" style="padding-top: 4pt; padding-right: 3pt; text-indent: 0pt; text-align: right;">{{ number_format((float)$order->total_amount - $order->paid_amount , 2, '.', '')}} Tk</p>
                </td>
            </tr>
            
        </table>
        <p style="text-indent: 0pt; text-align: left;"><br /></p>
        <h2 style="padding-top: 4pt; padding-left: 5pt; text-indent: 0pt; text-align: left;">Note: {{ $order->note }}</h2>
        <h2 style="padding-left: 5pt; text-indent: 0pt; text-align: left;">*Payment By: <span class="p">()</span></h2>
        <h2 style="padding-top: 2pt; padding-left: 5pt; text-indent: 0pt; text-align: left;">Created By: <span class="p">{{ config('concave.cnf_appname') }}</span></h2>
        <p class="s4" style="padding-left: 195pt; text-indent: 0pt; line-height: 1pt; text-align: left;"></p>
        <div style="display: flex; justify-content: space-between;">
            <div>
                <p style="padding-left: 9pt; text-indent: 0pt; line-height: 11pt; text-align: left;">1 [Sold Invoice #MS{{ date('Y', strtotime($order->created_at))}}{{$order->id}} ]</p>
            </div>
            <div>
                --------------------------------
                <p style="padding-left: 9pt; text-indent: 0pt; line-height: 11pt; text-align: center;">Authorized Signature </p>
            </div>
            <div>
                --------------------------------
                <p style="padding-left: 9pt; text-indent: 0pt; line-height: 11pt; text-align: right;">Customer Signature</p>
            </div>
        </div>

        <table style="width: 650px;text-align:left; margin:50px auto;">
            <tbody style="text-align: center;">
                <p style="font-size:12px;text-align: center;">Thank you for being with us. Stay connected with <b>mabiyshop.com</b> </p>
                <p style="text-align: center;"><img src="data:image/png;base64, {{DNS1D::getBarcodePNG('MS'.date("y", strtotime($order->created_at)).$order->id, 'C39',5,10) }}" alt=""  width="150px" height="30px"></p>
            </tbody>
        </table>
    </body>
    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</html>
