@extends('backend.layouts.master')

@section('title', 'Accounts Dashboard - ' . config('concave.cnf_appname'))

    @section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Accounting > Cash Flow</span>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="POST">
                            <div class="form-group row">
                                @if (Auth::user()->getRoleNames() != '["seller"]')
	                                <div class="col-sm-3">
	                                    <div class="input-group">
	                                        <select name="saller" id="branch_id" class="selectpicker form-control"
	                                            data-show-subtext="true" data-live-search="true">
	                                            <option value="-1">--Select Shop First--</option>
	                                            @foreach ($branches as $row)
	                                                <option value="{{ $row->id }}">{{ $row->shopinfo->name }}</option>
	                                            @endforeach
	                                        </select>
	                                    </div>
	                                </div>
	                            @else
	                                <input type="hidden" name="saller" id="branch_id" value="{{ Auth::user()->id }}">
	                            @endif

                                <label class="col-sm-1 ">
                                	<button class="btn btn-dark" type="button"id="filterBtn">Filter</button>
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="designed_table">
                <table id="dataTable" class="table">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Branch</th>
                            <th>Transaction</th>
                            <th>Date</th>
                            <th>Purpose</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Method</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @push('footer')

        <script type="text/javascript">
        function cashflow(branch_id = '') {
            var table = jQuery('#dataTable').DataTable({
                dom: 'Brftlip',
                buttons: ['csv', 'excel', 'pdf', 'print'],
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: true,
                ajax: {
                    url: "{{ route('admin.accounts.history') }}",
                    type: 'GET',
                    data:{
                    	'branch_id' : branch_id,
                    },
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
                        data: 'branch_id',
                        name: 'branch.name'
                    },
                    {
                        data: 'transaction_id'
                    },
                    {
                        data: 'date',
                    },
                    {
                        data: 'manage_type',
                    },
                    {
                        data: 'debit'
                    },
                    {
                        data: 'credit'
                    },
                    {
                        data: 'payment_method'
                    },
                    {
                        data: 'balance'
                    },
                ]
            });
        }

        jQuery('#dataTable').DataTable().destroy();
        cashflow($('#branch_id').val());

        jQuery(document).on('click', '#filterBtn', function(e) {
            e.preventDefault();
            jQuery('#dataTable').DataTable().destroy();
        	cashflow($('#branch_id').val());
        });
        </script>
    @endpush
@endsection