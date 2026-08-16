@extends('backend.layouts.master')
@section('title', 'Order List - ' . config('concave.cnf_appname'))
@section('content')

    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Sales > Orders</span>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="POST">
                            <div class="form-group row">
                                <div class="col-sm-2 " id="start_date_area">
                                    <label>From Date</label>
                                    <div class="input-group">
                                        <input type="date" name="start_date" id="start_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2 " id="end_date_area">
                                    <label>To Date</label>
                                    <div class="input-group">
                                        <input type="date" name="end_date" id="end_date" class="form-control">
                                    </div>
                                </div>

                                <label class="col-sm-1 mt-4"><button class="btn btn-dark" type="button"
                                        id="filterBtn">Filter</button></label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-margin">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    @can('order.edit')
                        <div class="card-body pb-0">
                            <button type="button" id="bulkAcceptButton" class="btn btn-primary" disabled>Accept Selected</button>
                            <span id="bulkSelectedCount" class="ml-2">0 orders selected</span>
                            <div id="bulkAcceptSummary" class="mt-3" style="display: none;"></div>
                        </div>
                    @endcan
                    <div class="designed_table table-responsive">

                        <table id="dataTable" class="table">
                            <thead>
                                <tr>
                                    <th class="details-control control" style="width: 24px;"></th>
                                    <th class="text-center" data-priority="1">
                                        @can('order.edit')
                                            <input type="checkbox" id="bulkSelectAll" aria-label="Select all eligible orders on this page">
                                        @endcan
                                    </th>
                                    <th>Order Id</th>
                                    <th>Order From</th>
                                    <th>Statistics</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Shipping Name</th>
                                    <th>Shipping Phone</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th data-priority="2">Payment Status</th>
                                    <th>Payment Method</th>
                                    <th class="text-center" data-priority="1">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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

@endsection

@push('footer')
    <script type="text/javascript">
        function orderList(start_date, end_date) {
            var table = jQuery('#dataTable').DataTable({
                dom: 'Brftlip',
                buttons: ['csv', 'excel', 'pdf', 'print'],
                searchable: true,
                responsive: {
                    details: {
                        type: 'column',
                        target: 'td.details-control'
                    }
                },
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ url('admin/orders/get-order-list') }}/" + start_date + "/" + end_date,
                    type: 'GET',
                },
                aLengthMenu: [
                    [10, 25],
                    [10, 25]
                ],
                iDisplayLength: 25,
                "order": [
                    [2, 'desc']
                ],
                "language": {
                    "processing": '<span style="color:#4eb9fa;"><i class=" mdi mdi-spin mdi-settings"></i> LOADING...</span>'
                },
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        orderable: false,
                        searchable: false,
                        className: 'details-control control'
                    },
                    {
                        data: 'bulk_select',
                        name: 'bulk_select',
                        orderable: false,
                        searchable: false,
                        "className": "text-center"
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'order_from',
                        data: 'order_from',
                        "className": "text-capitalize text-center"
                    }, 
                    {
                        data: 'statistics',
                        searchable: false,
                        orderable: false,
                    }, 
                    // {
                    //     data: 'parent_order_id',
                    //     searchable: false,
                    // }, 
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'user_id',
                        name: 'user.name'
                    },
                    // {
                    //     data: 'payment_id'
                    // },
                    {
                        data: 'shipping_name',
                        name: 'address.shipping_first_name'
                    },
                    {
                        data: 'shipping_phone',
                        name: 'address.shipping_phone'
                    },
                    
                    {
                        data: 'product_qty',
                        name: 'product_qty'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'paid_amount',
                        name: 'paid_amount'
                    },
                    {
                        data: 'status',
                        name: 'statuses.title'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        "className": "text-center"
                    },
                ],
                drawCallback: function() {
                    resetBulkSelection();
                }
            });
        }

        function selectedBulkOrderIds() {
            return $('#dataTable tbody .bulk-order-checkbox:checked').map(function() {
                return parseInt(this.value, 10);
            }).get().filter(function(id) {
                return Number.isInteger(id) && id > 0;
            });
        }

        function updateBulkSelectionState() {
            var eligible = $('#dataTable tbody .bulk-order-checkbox');
            var selected = selectedBulkOrderIds();
            $('#bulkSelectedCount').text(selected.length + (selected.length === 1 ? ' order selected' : ' orders selected'));
            $('#bulkAcceptButton').prop('disabled', selected.length === 0);
            $('#bulkSelectAll').prop('checked', eligible.length > 0 && selected.length === eligible.length);
            $('#bulkSelectAll').prop('indeterminate', selected.length > 0 && selected.length < eligible.length);
        }

        function resetBulkSelection() {
            $('#bulkSelectAll').prop({ checked: false, indeterminate: false });
            $('#dataTable tbody .bulk-order-checkbox').prop('checked', false);
            updateBulkSelectionState();
        }

        $(document).on('click', '#bulkSelectAll, .bulk-order-checkbox, #bulkAcceptButton', function(e) {
            e.stopPropagation();
        });

        $(document).on('change', '#bulkSelectAll', function() {
            $('#dataTable tbody .bulk-order-checkbox').prop('checked', this.checked);
            updateBulkSelectionState();
        });

        $(document).on('change', '.bulk-order-checkbox', updateBulkSelectionState);

        $(document).on('click', '#bulkAcceptButton', function() {
            var button = $(this);
            var orderIds = selectedBulkOrderIds();
            if (orderIds.length === 0) {
                return;
            }
            if (!window.confirm('Accept ' + orderIds.length + ' selected orders and send eligible orders to Pathao?')) {
                return;
            }

            button.prop('disabled', true).text('Processing...');
            $('#bulkSelectAll, #dataTable tbody .bulk-order-checkbox').prop('disabled', true);
            $('#bulkAcceptSummary').hide().empty();

            $.ajax({
                url: "{{ route('admin.orders.bulk.accept') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { order_ids: orderIds },
                success: function(response) {
                    var summary = response.summary || {};
                    var html = '<strong>Processed: ' + (summary.processed || 0) + '</strong>' +
                        ' &nbsp; Accepted: ' + (summary.accepted || 0) +
                        ' &nbsp; Failed: ' + (summary.failed || 0) +
                        ' &nbsp; Needs review: ' + (summary.uncertain || 0) +
                        ' &nbsp; Skipped: ' + (summary.skipped || 0);
                    var reviewItems = (response.results || []).filter(function(result) {
                        return result.result === 'failed' || result.result === 'uncertain';
                    });
                    if (reviewItems.length) {
                        html += '<ul class="mb-0 mt-2">';
                        reviewItems.forEach(function(result) {
                            html += '<li>' + $('<div>').text(result.order_reference + ' — ' + result.message).html() + '</li>';
                        });
                        html += '</ul>';
                    }
                    $('#bulkAcceptSummary').html(html).show();
                    $('#dataTable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Bulk acceptance could not be completed.';
                    $('#bulkAcceptSummary').text(message).show();
                    resetBulkSelection();
                },
                complete: function() {
                    button.text('Accept Selected');
                    $('#bulkSelectAll, #dataTable tbody .bulk-order-checkbox').prop('disabled', false);
                    updateBulkSelectionState();
                }
            });
        });

        orderList(0, 0);

        $(document).on('click', '#filterBtn', function(e) {
            e.preventDefault();
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            if (start_date == '') {
                alert('Start Date Required!');
            } else if (end_date == '') {
                alert('End Date Required!');
            } else {
                $('#dataTable').DataTable().destroy();
                orderList(start_date, end_date);
            }
        });
    </script>
@endpush
