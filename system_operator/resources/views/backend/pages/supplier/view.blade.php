@extends('backend.layouts.master')

@section('title', 'Supplier Ledger - ' . config('concave.cnf_appname'))

    @section('content')

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <p class="content_title">Supplier Ledger</p>
                    </div>
                    <div class="">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center bb-1">
                                    <th colspan="2"><p><b>Balance Sheet</b></p></th>
                                </tr>
                                <tr class="bb-1">
                                    <th scope="col"><b>Info</b></th>
                                    <th scope="col"><b>Amount</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Opening Balance</b></td>
                                    <td class="text-right">{{ number_format($supplier->opening_balance,2) }}</td>
                                </tr>
                                <tr>
                                    <td><b>Total Sell</b></td>
                                    <td class="text-right">{{ number_format($supplier->purchases->sum('grand_total'),2) }}</td>
                                </tr>
                                <tr>
                                    <td><b>Instant Paid</b></td>
                                    <td class="text-right">{{ number_format($supplier->purchases->sum('paid_amount'),2) }}</td>
                                </tr>
                                <tr>
                                    <td><b>Due Payments</b></td>
                                    <td class="text-right">{{ number_format($supplier->purchases->sum('grand_total') - $supplier->purchases->sum('paid_amount'),2) }}</td>
                                </tr>
                                <tr>
                                    <td><b>Total Return<b></td>
                                    <td class="text-right">{{ number_format($supplier->return->sum('grand_total'),2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="2"><b>Balance = {{ number_format($supplier->balance,2) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <p class="content_title">Supplier Info</p>
                    </div>
                    <div class="">
                        <ul class="list-group">
                            <li class="nav-item mb-2 d-flex ustify-content-between align-items-center">
                                <small class="p-1 pl-2 pr-2 bg-dark fs-18 text-light"><i class="mdi mdi-account-circle"></i></small>
                                <b class="fs-18 ml-2">{{ $supplier->name }}</b>
                            </li>
                            <li class="nav-item mb-2 d-flex ustify-content-between align-items-center">
                                <small class="p-1 pl-2 pr-2 bg-dark fs-18 text-light"><i class="mdi mdi-briefcase-account"></i></small>
                                <b class="fs-18 ml-2">{{ $supplier->company_name }}</b>
                            </li>
                            <li class="nav-item mb-2 d-flex ustify-content-between align-items-center">
                                <small class="p-1 pl-2 pr-2 bg-dark fs-18 text-light"><i class="mdi mdi-phone"></i></small>
                                <b class="fs-18 ml-2">{{ $supplier->phone_number }}</b>
                            </li>
                            <li class="nav-item mb-2 d-flex ustify-content-between align-items-center">
                                <small class="p-1 pl-2 pr-2 bg-dark fs-18 text-light"><i class="mdi mdi-email"></i></small>
                                <b class="fs-18 ml-2">{{ $supplier->email }}</b>
                            </li>
                            <li class="nav-item mb-2 d-flex ustify-content-between align-items-center">
                                <small class="p-1 pl-2 pr-2 bg-dark fs-18 text-light"><i class="mdi mdi-map-marker-radius"></i></small>
                                <b class="fs-18 ml-2">{{ $supplier->address }}</b>
                            </li>
                            <li class="nav-item mb-2 d-flex ustify-content-between align-items-center">
                                <small class="p-1 pl-2 pr-2 bg-dark fs-18 text-light"><i class="mdi mdi-cash-multiple"></i></small>
                                <b class="fs-18 ml-2">{{ number_format($supplier->balance,2) }}</b>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link b-0 active" id="invoiceBtn" onclick="trigerDiv('invoice', 'invoiceBtn');">Supplier Invoice</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link ml-1 b-0" id="paymentsBtn" onclick="trigerDiv('payments', 'paymentsBtn');">Supplier Payments</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link ml-1 b-0" id="returnedBtn" onclick="trigerDiv('returned', 'returnedBtn');">Return Products</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="invoice" role="tabpanel" aria-labelledby="pills-home-tab">
                            <div class="">
                                <p class="content_title"><b>Supplier Invoice</b></p>
                            </div>
                            <div class="designed_table">
                                <table id="invoiceTable" class="table">
                                    <thead>
                                        <tr>
                                            <th>Serial</th>
                                            <th>Date</th>
                                            <th>Reference No</th>
                                            <th>Total</th>
                                            <th>Paid</th>
                                            <th>Payment Status</th>
                                            <th class="text-center" data-priority="1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="">
                                <p class="content_title"><b>Supplier Payments</b></p>
                            </div>
                            <div class="designed_table">
                                <table id="paymentTable" class="table">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Date</th>
                                            <th>Pay By</th>
                                            <th>Paid Amount</th>
                                            <th>Voucher No</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($supplier->payments as $row)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td>{{ date('d M, Y', strtotime($row->created_at)) }}</td>
                                                <td>{{ $row->payment_method->name ?? '' }}</td>
                                                <td>{{ $row->amount }}</td>
                                                <td>{{ $row->references_no }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="returned" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="">
                                <p class="content_title"><b>Return Products</b></p>
                            </div>
                            <div class="designed_table">
                                <table id="returnTable" class="table">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>Date</th>
                                            <th>Invoice No</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($supplier->return as $row)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td>{{ date('d M, Y', strtotime($row->created_at)) }}</td>
                                                <td>{{ $row->invoice_no }}</td>
                                                <td>{{ $row->total_cost }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="purchase_quick_view_modal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="">Purchase Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body purchase_form_element" id="purchase_form_element">

                        </div>
                        
                    </div>
                    <button class="btn btn-info mt-2" id="printBtn" onclick='printDiv();'>Print</button>
                </div>

            </div>
        </div>
    </div>



    <div class="modal fade" id="due_payment_modal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="">Due Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <form class="row" action="{{ route('admin.purchase.due.payment')}}" method="post">
                                @csrf
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Payment Method</label>
                                        <select class="form-select form-control" name="payment_method" aria-label="Default select example" required>
                                            <option selected>Choose one</option>
                                            @foreach(\App\Models\CurrentAsset::get() as $row)
                                                <option value="{{$row->id}}">{{$row->name}} -- #{{$row->amount}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Paid Amount</label>
                                        <input type="text" name="paid_amount" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" name="purchase_id" id="purchase_id">
                                        <button class="btn btn-success" type="submit">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    @push('footer')
        <script type="text/javascript">
            function trigerDiv(triger_div_id, btn){
                jQuery('.tab-pane').removeClass('show active');
                jQuery('.nav-link').removeClass('active');

                
                jQuery('#'+btn).addClass('active');
                jQuery('#'+triger_div_id).addClass('show active');
            }

            // Purchase table 
            var table = jQuery('#invoiceTable').DataTable({
                dom: 'Brftlip',
                buttons: ['csv', 'excel', 'pdf', 'print'],
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: true,
                ajax: {
                    url: "{{ route('admin.get.purchase.list', $supplier->id) }}",
                    type: 'GET',
                },
                "order": [
                    [0, 'desc']
                ],
                "language": {
                    "processing": '<span style="color:#4eb9fa;"><i class=" mdi mdi-spin mdi-settings"></i> LOADING...</span>'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        "className": "text-center",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'reference_no'
                    },
                    {
                        data: 'grand_total'
                    },
                    {
                        data: 'paid_amount'
                    },
                    {
                        data: 'payment_status',
                        orderable: false,
                        searchable: false,
                        "className": "text-center"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        "className": "text-center"
                    },
                ]
            });

            // Payment Table 
            // let payments = jQuery('#paymentTable').DataTable({
            //     dom: 'Brftlip',
            //     buttons: ['csv', 'excel', 'pdf', 'print'],
            //     responsive: true,
            //     processing: true,
            //     serverSide: true,
            //     autoWidth: true,
            //     ajax: {
            //         url: "{{ route('admin.get.payment.list', $supplier->id) }}",
            //         type: 'GET',
            //     },
            //     "order": [
            //         [0, 'desc']
            //     ],
            //     "language": {
            //         "processing": '<span style="color:#4eb9fa;"><i class=" mdi mdi-spin mdi-settings"></i> LOADING...</span>'
            //     },
            //     columns: [{
            //             data: 'DT_RowIndex',
            //             "className": "text-center",
            //             orderable: false,
            //             searchable: false,
            //         },
            //         {
            //             data: 'created_at',
            //             name: 'created_at',
            //         },
            //         {
            //             data: 'payment_method',
            //             name: 'payment_method.name'
            //         },
            //         {
            //             data: 'reference_no'
            //         },
            //         {
            //             data: 'amount'
            //         },
            //         {
            //             data: 'references_no'
            //         },
            //     ]
            // });

            // Quick View customer
            jQuery(document).on('click', '.purchase_quick_view_btn', function(e) {
                e.preventDefault();
                jQuery.ajax({
                    url: "/admin/purchase/view/" + jQuery(this).attr('data-id'),
                    type: "get",
                    data: {},
                    success: function(response) {
                        jQuery('.purchase_form_element').html(response);
                        jQuery('#purchase_quick_view_modal').modal('show');
                    }
                });
            });

            // Due Payment
            jQuery(document).on('click', '.purchase_due_payment_btn', function(e) {
                e.preventDefault();
                let id = jQuery(this).attr('data-id');
                jQuery('#purchase_id').val(id);
                jQuery('#due_payment_modal').modal('show');
            });

            function printDiv() {

                var divToPrint=document.getElementById('purchase_form_element');

                var newWin=window.open('','Print-Window');

                newWin.document.open();

                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

                newWin.document.close();

                setTimeout(function(){newWin.close();},10);

            }
        </script>
    @endpush
@endsection