@extends('backend.layouts.master')
@section('title', 'Fraud & Security - Order ' . ($order->id ?? '') . ' - ' . config('concave.cnf_appname'))
@section('content')
@php($historicalAddress = $order->historical_shipping_address)
<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <span class="card-title">Dashboard > Fraud & Security > Order Information</span>
            <a href="{{ route('admin.fraud-security') }}" class="btn btn-dark float-right">Back to Fraud & Security</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body formated_para">
                <h4 class="card-title text-uppercase mb-4">Order Information</h4>
                <hr>
                <p><strong>Order ID: </strong> MS{{ date('y', strtotime($order->created_at)) }}{{ $order->id }}</p>
                <p><strong>Order Date: </strong> {{ $order->created_at->format('d M, Y, g:i a') }}</p>
                <p><strong>Payment Method: </strong> {{ $order->payment_method }}</p>
                <p><strong>Total Amount: </strong> {{ Helper::getDefaultCurrency()->currency_symbol . ' ' . ($order->total_amount ?? 0) }}</p>
                <p><strong>Paid Amount: </strong> {{ Helper::getDefaultCurrency()->currency_symbol . ' ' . ($order->paid_amount ?? 0) }}</p>

                <hr>
                <h4 class="card-title text-uppercase mb-4">Fraud Information</h4>
                <hr>
                <p><strong>Risk Level: </strong> {{ $order->risk_level ?? '-' }}</p>
                <p><strong>Fraud Status: </strong>
                    @if ($order->fraud_status === 'BLOCKED')
                        <span class="badge text-light" style="background-color: #e62e2d;">Blocked</span>
                    @elseif ($order->fraud_status === 'REVIEW')
                        <span class="badge text-light" style="background-color: #ec8b23;">Review</span>
                    @else
                        <span class="badge text-light" style="background-color: #3b8104;">Normal</span>
                    @endif
                </p>
                <p><strong>Manual Review: </strong> {{ $order->manual_review ? 'Yes' : 'No' }}</p>
                <p><strong>OTP Required: </strong> {{ $order->otp_required ? 'Yes' : 'No' }}</p>
                <p><strong>Fraud Reason: </strong> {{ $order->fraud_reason ?? '-' }}</p>
                <p><strong>Risk Reasons: </strong>
                    @php
                        $reasons = is_array($order->risk_reasons) ? $order->risk_reasons : (json_decode($order->risk_reasons, true) ?: []);
                    @endphp
                    {{ !empty($reasons) ? implode(', ', $reasons) : '-' }}
                </p>

                @if ($order->courier_success_rate !== null)
                    <p><strong>Courier Success Rate: </strong>
                        <span class="badge text-light" style="background-color: 
                            @php
                                $rate = (float) $order->courier_success_rate;
                                if ($rate < 40) echo '#e62e2d';
                                elseif ($rate < 60) echo '#ec8b23';
                                else echo '#3b8104';
                            @endphp;">
                            {{ round($rate) }}%
                        </span>
                    </p>
                @endif

                <hr>
                <h4 class="card-title text-uppercase mb-4">Actions</h4>
                <hr>
                @if ($order->fraud_status !== 'NORMAL')
                    <button type="button" class="btn btn-success btn-block mb-2" id="releaseBtn" data-id="{{ $order->id }}">Release to Main Orders</button>
                @endif
                @if ($order->fraud_status !== 'BLOCKED')
                    <button type="button" class="btn btn-danger btn-block" id="keepBlockedBtn" data-id="{{ $order->id }}">Keep Blocked</button>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card min-h-330">
            <div class="card-body formated_para">
                <div class="row">
                    <div class="col-12">
                        <h4 class="card-title text-uppercase mb-2">Shipping Information</h4>
                        <hr>
                        @if($order->is_pickpoint == 1)
                            <p><strong>Title: </strong> {{ $order->pickpoint_address->title }}</p>
                            <p><strong>Address: </strong> {{ $order->pickpoint_address->address }},
                                {{ $order->pickpoint_address->union->title ?? '' }}, {{ $order->pickpoint_address->upazila->title ?? '' }},
                                {{ $order->pickpoint_address->district->title ?? '' }}, {{ $order->pickpoint_address->division->title ?? '' }}
                            </p>
                            <p><strong>Postcode: </strong> {{ $order->pickpoint_address->postcode }}</p>
                            <p><strong>Phone Number: </strong> {{ $order->pickpoint_address->phone }}</p>
                        @else
                            <p><strong>Full Name: </strong> {{ $historicalAddress->shipping_first_name }}
                                {{ $historicalAddress->shipping_last_name ?? '' }}</p>
                            <p><strong>Address: </strong> {{ $historicalAddress->shipping_address }},
                                {{ $historicalAddress->union->title ?? '' }}, {{ $historicalAddress->upazila->title ?? '' }},
                                {{ $historicalAddress->district->title ?? '' }}, {{ $historicalAddress->division->title ?? '' }}
                            </p>
                            <p><strong>Postcode: </strong> {{ $historicalAddress->shipping_postcode }}</p>
                            <p><strong>Phone Number: </strong> {{ $historicalAddress->shipping_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card min-h-330">
            <div class="card-body formated_para">
                <div class="row">
                    <div class="col-12">
                        <h4 class="card-title text-uppercase mb-2">Customer Block Status</h4>
                        <hr>
                        @if ($customerBlock)
                            <p><strong>Status: </strong>
                                @if ($customerBlock['status'] === 'BLOCKED')
                                    <span class="badge text-light" style="background-color: #e62e2d;">Blocked</span>
                                @elseif ($customerBlock['status'] === 'WATCH')
                                    <span class="badge text-light" style="background-color: #ec8b23;">Watch</span>
                                @else
                                    <span class="badge text-light" style="background-color: #3b8104;">Normal</span>
                                @endif
                            </p>
                            <p><strong>Reason: </strong> {{ $customerBlock['reason'] ?? '-' }}</p>
                            <p><strong>Expires At: </strong> {{ $customerBlock['expires_at'] ?? 'Never' }}</p>
                        @else
                            <p>No customer block record found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($order->courier_history_snapshot)
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-uppercase mb-3">Courier History</h4>
                    @php
                        $snapshot = is_array($order->courier_history_snapshot) ? $order->courier_history_snapshot : json_decode($order->courier_history_snapshot, true);
                    @endphp
                    @if (!empty($snapshot['combined']))
                        <p><strong>Combined Success Rate: </strong>{{ $snapshot['combined']['success_rate'] ?? 0 }}%
                            (Total: {{ $snapshot['combined']['total'] ?? 0 }},
                            Success: {{ $snapshot['combined']['successful'] ?? 0 }},
                            Failed: {{ $snapshot['combined']['failed'] ?? 0 }})
                        </p>
                    @endif
                    @if (!empty($snapshot['couriers']))
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Courier</th>
                                        <th>Total</th>
                                        <th>Success</th>
                                        <th>Cancel</th>
                                        <th>Rate</th>
                                        <th>Rating</th>
                                        <th>Risk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($snapshot['couriers'] as $name => $courier)
                                        @if ($courier['available'] ?? false)
                                            <tr>
                                                <td>{{ ucfirst($name) }}</td>
                                                <td>{{ $courier['total'] ?? '-' }}</td>
                                                <td>{{ $courier['successful'] ?? '-' }}</td>
                                                <td>{{ $courier['failed'] ?? '-' }}</td>
                                                <td>{{ $courier['success_rate'] ?? '-' }}%</td>
                                                <td>{{ $courier['rating'] ?? '-' }}</td>
                                                <td>{{ $courier['risk_level'] ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-uppercase mb-3">Order Items</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->order_details as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ optional($item->product)->title ?? 'N/A' }}</td>
                                    <td>{{ $item->product_qty }}</td>
                                    <td>{{ Helper::getDefaultCurrency()->currency_symbol . ' ' . $item->price }}</td>
                                    <td>{{ Helper::getDefaultCurrency()->currency_symbol . ' ' . ($item->product_qty * $item->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if (count($orderLog) > 0)
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-uppercase mb-3">Order Log</h4>
                    <div class="wrapper">
                        <ul class="sessions">
                            @foreach ($orderLog as $oLog)
                                <li>
                                    <div class="time">{{ date('d M, Y h:ia', strtotime($oLog->created_at)) }}</div>
                                    <div class="detail">{{ $oLog->generated_text }}</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Release Modal -->
<div class="modal fade" id="releaseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Release Order to Main Orders</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <textarea name="note" id="releaseNote" class="form-control" placeholder="Optional release note..." rows="5"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmRelease">Release</button>
            </div>
        </div>
    </div>
</div>

<!-- Keep Blocked Modal -->
<div class="modal fade" id="keepBlockedModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Keep Order Blocked</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <textarea name="reason" id="keepBlockedReason" class="form-control" placeholder="Reason for keeping blocked..." rows="5" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirmKeepBlocked">Keep Blocked</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer')
    <script type="text/javascript">
        var releaseOrderId = null;
        var keepBlockedOrderId = null;

        $(document).on('click', '#releaseBtn', function() {
            releaseOrderId = $(this).data('id');
            $('#releaseModal').modal('show');
        });

        $(document).on('click', '#confirmRelease', function() {
            if (!releaseOrderId) return;
            var note = $('#releaseNote').val();
            $.ajax({
                url: "{{ url('admin/fraud-security') }}/" + releaseOrderId + "/release",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { note: note },
                success: function(response) {
                    if (response.status == 1) {
                        window.location.href = "{{ route('admin.fraud-security') }}";
                    } else {
                        alert(response.message || 'Release failed.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Release failed.');
                }
            });
        });

        $(document).on('click', '#keepBlockedBtn', function() {
            keepBlockedOrderId = $(this).data('id');
            $('#keepBlockedModal').modal('show');
        });

        $(document).on('click', '#confirmKeepBlocked', function() {
            if (!keepBlockedOrderId) return;
            var reason = $('#keepBlockedReason').val();
            if (!reason.trim()) {
                alert('Please provide a reason.');
                return;
            }
            $.ajax({
                url: "{{ url('admin/fraud-security') }}/" + keepBlockedOrderId + "/keep-blocked",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { reason: reason },
                success: function(response) {
                    if (response.status == 1) {
                        window.location.reload();
                    } else {
                        alert(response.message || 'Action failed.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Action failed.');
                }
            });
        });
    </script>
@endpush
