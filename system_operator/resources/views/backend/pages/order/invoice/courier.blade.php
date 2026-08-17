<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sticker | {{ config('concave.cnf_appname') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .sticker {
            width: 280px;
            height: 370px;
            background: white;
            border: 2px solid #000;
            padding-left: 30px;
            position: relative;
			padding-top: 10px;
			padding-bottom:10px;
        }
        
        .header {
            display: flex;
            align-items: flex-start;
            gap: 5px;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 4px;
        }
        
        .logo {
            flex-shrink: 0;
        }
        
        .logo img {
            width: 50px;
            height: auto;
            display: block;
        }
        
        .shop-info {
            flex: 1;
            min-width: 0;
        }
        
        .shop-name {
            font-size: 12px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 1px;
        }
        
        .shop-location {
            font-size: 10px;
            color: #333;
            line-height: 1.2;
        }
        
        .barcode-section {
            margin: 4px 0;
            text-align: center;
        }
        
        .barcode {
            width: 100%;
            height: 30px;
            background: repeating-linear-gradient(
                90deg,
                #000 0px,
                #000 1.5px,
                #fff 1.5px,
                #fff 3px
            );
            margin: 2px 0;
        }
        
        .date-section {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 3px;
            text-align: left;
        }
        
        .cod-main-section {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;
            flex-wrap: wrap;
        }
        
        .merchant-label {
            font-size: 10px;
            font-weight: bold;
            color: #000;
        }
        
        .cod-section {
            background: #000;
            color: white;
            padding: 2px 5px;
            font-size: 10px;
            white-space: nowrap;
            flex: 1;
            min-width: 140px;
        }
        
        .cod-label {
            font-weight: bold;
        }
        
        .customer-section {
            background: #f5f5f5;
            padding: 4px;
            margin-bottom: 4px;
            border: 1px solid #ddd;
        }
        
        .order-info {
            font-size: 10px;
            line-height: 1.3;
        }
        
        .order-info p {
            margin-bottom: 1px;
        }
        
        .order-info strong {
            font-weight: bold;
        }
        
        .products-section {
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px dashed #000;
        }
        
        .products-list {
            font-size: 10px;
            line-height: 1.3;
            
            overflow: hidden;
        }
        
        .product-item {
            margin-bottom: 2px;
            padding-bottom: 2px;
            border-bottom: 1px dotted #ddd;
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .footer {
            position: absolute;
            bottom: 6px;
            left: 6px;
            right: 6px;
            border-top: 1px solid #000;
            padding-top: 2px;
            font-size: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .sticker {
                border: none;
                margin: 0;
                width: 3in;
                height: 4in;
            }
            
            #btnPrint {
                display: none !important;
            }
        }
        
        #btnPrint {
            margin-top: 20px;
            padding: 10px 30px;
            font-size: 14px;
            cursor: pointer;
            background: #e63946;
            color: white;
            border: none;
            border-radius: 5px;
        }
        
        #btnPrint:hover {
            background: #d62828;
        }
        
        @page {
            size: 3in 4in;
            margin: 0;
        }
    </style>
</head>
<body>
    <div>
        <div class="sticker">
            <!-- Header with Logo and Shop Info -->
            <div class="header">
                <div class="logo">
                    <img src="{{ asset('uploads/images/frontendlogo.png') }}" alt="Logo">
                </div>
                <div class="shop-info">
                    <div class="shop-name">{{ config('concave.cnf_appname') }}</div>
                    <div class="shop-location">{{ config('concave.cnf_address') }}</div>
                    <div class="shop-location">{{ config('concave.cnf_phone') }}</div>
                </div>
            </div>
            
            <!-- Barcode Section -->
            <div class="barcode-section">
                <p style="text-align: center;"><img src="data:image/png;base64, {{DNS1D::getBarcodePNG('MS'.date("y", strtotime($order->created_at)).$order->id, 'C39',5,10) }}" alt=""  width="150px" height="30px"></p>
            </div>
            
            <!-- Date and Invoice -->
            <div class="date-section">
                {{ date('d-m-Y', strtotime($order->created_at)) }}
            </div>
            
            <!-- Invoice and COD Amount -->
            <div class="cod-main-section">
                <span class="merchant-label">MS{{ date('Y', strtotime($order->created_at))}}{{$order->id}}</span>
                <span class="cod-section">
                    <span class="cod-label">COD: BDT {{ number_format((float)$order->total_amount - $order->paid_amount, 2, '.', '') }}</span>
                </span>
            </div>
            
            <!-- Customer Information -->
            <div class="customer-section">
                <div class="order-info">
                    <p><strong>{{$order->historical_shipping_address->shipping_first_name ?? ''}} {{$order->historical_shipping_address->shipping_last_name ?? ''}}</strong></p>
                    <p><strong>Phone:</strong> {{$order->historical_shipping_address->shipping_phone ?? $order->user->phone ?? ''}}</p>
                    <p><strong>Address:</strong>
                        @if($order->is_pickpoint == 1)
                            {{$order->pickpoint_address->title}}, {{$order->pickpoint_address->union->title ?? ''}}, {{$order->pickpoint_address->upazila->title ?? ''}}, {{$order->pickpoint_address->district->title ?? ''}}
                        @else
                            {{ $order->historical_shipping_address->shipping_address ?? ''}}
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Products List -->
            <div class="products-section">
                <div class="products-list">
                    @php $count = 1; @endphp
                    @foreach($order->order_details as $details)
                        @if($count <= 3)
                        <div class="product-item">
                            <strong>{{ $count }}. {{$details->product->title ?? ''}}</strong>
                            @if($details->product->product_type == 'variable')
                                @php $variable_option = json_decode($details->product_options); @endphp
                                @if($variable_option)
                                    <br>
                                    @foreach($variable_option as $key=> $val)
                                        <span style="margin-right: 3px;">{{ $key }}: {{ $val }}</span>
                                    @endforeach
                                @endif
                            @endif
                            <br>Price: ৳{{ number_format((float)$details->price, 2) }} × {{ $details->product_qty }} = ৳{{ number_format((float)$details->price * $details->product_qty, 2) }}
                        </div>
                        @php $count++; @endphp
                        @endif
                    @endforeach
                    @if($order->order_details->count() > 3)
                        <div style="font-style: italic; margin-top: 2px;">...and {{ $order->order_details->count() - 3 }} more items</div>
                    @endif
					<p style="color:black;text-align:right;">Shiping Cost: {{ number_format((float)$order->shipping_cost , 2, '.', '')}}  </p>
					<p style="color:black;text-align:right;">Total Gross: {{ number_format((float)$order->total_amount , 2, '.', '')}}  </p>
					<p style="color:black;text-align:left;font-size: 8px;">Note: {{ $order->note }}  </p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                Powered by Varclone IT Solution
            </div>
        </div>
        
        <button id="btnPrint">Print Sticker</button>
    </div>
    
    <script>
        const $btnPrint = document.querySelector("#btnPrint");
        $btnPrint.addEventListener("click", () => {
            window.print();
        });
        
        // Auto-print on page load
        window.onload = function () {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
