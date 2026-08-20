@extends('backend.layouts.master')
@section('title', 'Fraud & Security - ' . config('concave.cnf_appname'))
@section('content')

    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Fraud & Security</span>
            </div>
        </div>
    </div>

    <div class="grid-margin">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="designed_table table-responsive">
                        <table id="dataTable" class="table">
                            <thead>
                                <tr>
                                    <th>Order Id</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Shipping Name</th>
                                    <th>Shipping Phone</th>
                                    <th>Total Amount</th>
                                    <th>Risk Level</th>
                                    <th>Fraud Status</th>
                                    <th>Courier %</th>
                                    <th>OTP Required</th>
                                    <th>Reason</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer')
    <script type="text/javascript">
        function fraudList() {
            var table = jQuery('#dataTable').DataTable({
                dom: 'Brftlip',
                buttons: ['csv', 'excel', 'pdf', 'print'],
                searchable: true,
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: "{{ url('admin/fraud-security/get-list') }}",
                aLengthMenu: [
                    [10, 25, 50],
                    [10, 25, 50]
                ],
                iDisplayLength: 25,
                "order": [
                    [1, 'desc']
                ],
                "language": {
                    "processing": '<span style="color:#4eb9fa;"><i class=" mdi mdi-spin mdi-settings"></i> LOADING...</span>'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'user_name', name: 'user.name' },
                    { data: 'shipping_name', name: 'address.shipping_first_name' },
                    { data: 'shipping_phone', name: 'address.shipping_phone' },
                    { data: 'total_amount', name: 'total_amount' },
                    { data: 'risk_level', name: 'risk_level' },
                    { data: 'fraud_status', name: 'fraud_status', orderable: false, searchable: false },
                    { data: 'courier_success_rate', name: 'courier_success_rate', orderable: false, searchable: false },
                    { data: 'otp_required', name: 'otp_required', orderable: false, searchable: false },
                    { data: 'fraud_reason', name: 'fraud_reason', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, "className": "text-center" },
                ]
            });
        }

        fraudList();

        $(document).on('click', '.release-btn', function(e) {
            e.preventDefault();
            var button = $(this);
            var orderId = button.data('id');
            if (!confirm('Release this order back to Main Orders?')) {
                return;
            }
            $.ajax({
                url: "{{ url('admin/fraud-security') }}/" + orderId + "/release",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 1) {
                        $('#dataTable').DataTable().ajax.reload(null, false);
                    } else {
                        alert(response.message || 'Release failed.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Release failed.');
                }
            });
        });
    </script>
@endpush
