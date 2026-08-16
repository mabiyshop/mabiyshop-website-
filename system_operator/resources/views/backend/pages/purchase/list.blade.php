@extends('backend.layouts.master')
@section('title', 'Purchase List - ' . config('concave.cnf_appname'))
@section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Purchase</span>
                <a class="btn btn-success float-right" href="{{ route('admin.purchase.create') }}">Create New Purchase</a>
            </div>
        </div>
    </div>


    <div class="grid-margin stretch-card">
        <form action="{{ route('admin.purchase.bulk.action') }}" method="POST" style="width: 100%;">
            <div class="card">
                <div class="toolbar">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">

                                <div class="col-sm-3">
                                    <label class="col-form-label">Bulk Action</label>
                                    <div class="input-group">
                                        <select name="action" id="bulkOption" class="form-control">
                                            <option value="">--Select Option First--</option>
                                            <option value="active">Change Status to Active</option>
                                            <option value="inactive">Change Status to Inactive</option>
                                            <option value="delete" onclick="confirm('Are you sure to delete ?')">Delete Selected Items</option>
                                        </select>
                                    </div>
                                </div>
                                <label class="col-sm-1 pt-2">
                                    <button class="btn btn-dark mt-4" type="submit">Apply</button></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="designed_table">
                    <table id="dataTable" class="table">
                        <thead>
                            <tr>
                                <th>Serial</th>
                                <th>
                                    <div class="form-check form-check-flat">
                                        <label class="form-check-label">
                                            <input id="select_all" type="checkbox" class="form-check-input"><i
                                                class="input-helper"></i>
                                        </label>
                                    </div>
                                </th>
                                <th>Reference No</th>
                                <th>Supplier</th>
                                <th>Shop</th>
                                <th>Items</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Pending</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th class="text-center" data-priority="1">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <!--Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Are you sure to delete this item? </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Once you delete this item, you can restore this from trash list!</p>
                    <textarea name="reason" id="reason" placeholder="Write reason, why you want to delete this item."
                        class="form-control"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a type="button" href="#" class="btn btn-danger delete_trigger">Delete</a>
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

@endsection


@push('footer')
    <script type="text/javascript">
        var table = jQuery('#dataTable').DataTable({
            dom: 'Brftlip',
            buttons: ['csv', 'excel', 'pdf', 'print'],
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: true,
            ajax: {
                url: "{{ route('admin.get.purchase.list', 0) }}",
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
                    data: 'checkbox',
                    name: 'checkbox',
                    "className": "text-center",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'reference_no'
                },
                {
                    data: 'supplier_id',
                    name: 'supplier.name'
                },
                {
                    data: 'shop_id',
                    name: 'shop.name'
                },
                {
                    data: 'item'
                },
                {
                    data: 'total_qty'
                },
                {
                    data: 'grand_total'
                },
                {
                    data: 'paid_amount'
                },
                {
                    data: 'pending_amount'
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false,
                    "className": "text-center"
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
