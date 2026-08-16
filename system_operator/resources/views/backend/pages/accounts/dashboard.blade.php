@extends('backend.layouts.master')

@section('title', 'Accounts Dashboard - ' . config('concave.cnf_appname'))

    @section('content')
    {{-- <div class="row mb-2">
        @if (Auth::user()->can('accounts.manage.assets'))
            <div class="col-lg-3">
                <button class="btn btn-info w-100" id="manage_current_asset_btn">Manage Current Assets</button>
            </div>
        @endif

        @if (Auth::user()->can('accounts.manage.investor'))
            <div class="col-lg-3">
                <button class="btn btn-success w-100" id="manage_investor_btn">Manage Investor</button>
            </div>
        @endif

        @if (Auth::user()->can('accounts.manage.loan'))
            <div class="col-lg-3">
                <button class="btn btn-danger w-100" id="manage_loan_btn">Manage Loan</button>
            </div>
        @endif

        @if (Auth::user()->can('accounts.manage.investloan'))
            <div class="col-lg-3">
                <button class="btn btn-warning w-100" id="manage_investloan_btn">Pay Invest & Loan</button>
            </div>
        @endif

    </div> --}}

    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">Total Purchases</h4>
                        <p class="font-weight-semibold mb-0"></p>
                    </div>

                    @if (Auth::user()->getRoleNames() == '["seller"]') 
                        <h3 class="font-weight-medium mb-4">৳ {{ \Helper::getBusinessPurchase(Auth::user()->id) }}</h3>
                    @else
                        <h3 class="font-weight-medium mb-4">৳ {{ \Helper::getBusinessPurchase() }}</h3>
                    @endif
                    
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">Total Sale</h4>
                        <p class="font-weight-semibold mb-0"></p>
                    </div>

                    @if (Auth::user()->getRoleNames() == '["seller"]') 
                        <h3 class="font-weight-medium mb-4">৳ {{ \Helper::getBusinessSale(Auth::user()->id) }}</h3>
                    @else
                        <h3 class="font-weight-medium mb-4">৳ {{ \Helper::getBusinessSale() }}</h3>
                    @endif

                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">Total Expense</h4>
                        <p class="font-weight-semibold mb-0"></p>
                    </div>

                    @if (Auth::user()->getRoleNames() == '["seller"]') 
                        <h3 class="font-weight-medium mb-4">৳ {{ \Helper::getBusinessExpense(Auth::user()->id) }}</h3>
                    @else
                        <h3 class="font-weight-medium mb-4">৳ {{ \Helper::getBusinessExpense() }}</h3>
                    @endif

                </div>
            </div>
        </div>

       
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-0">Total Balance</h4>
                        <p class="font-weight-semibold mb-0"></p>
                    </div>

                    @if (Auth::user()->getRoleNames() == '["seller"]') 
                        <h3 class="font-weight-medium mb-4">৳ {{ \Helper::getBusinessBalance(Auth::user()->id) }}</h3>
                    @else
                        <h3 class="font-weight-medium mb-4">৳ {{ \Helper::getBusinessBalance() }}</h3>
                    @endif

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
            jQuery(document).on('click','#manage_current_asset_btn',function(e){
                e.preventDefault();
                jQuery('#manage_current_asset_modal').modal('show');
            });

            jQuery(document).on('click','#manage_investor_btn',function(e){
                e.preventDefault();
                jQuery('#manage_investor_modal').modal('show');
            });

            jQuery(document).on('click','#manage_loan_btn',function(e){
                e.preventDefault();
                jQuery('#manage_loan_modal').modal('show');
            });

            jQuery(document).on('click','#manage_investloan_btn',function(e){
                e.preventDefault();
                jQuery('#manage_investloan_modal').modal('show');
            });

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

        </script>
    @endpush
@endsection