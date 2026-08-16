@extends('backend.layouts.master')
@section('title', 'Balance Transfer - ' . config('concave.cnf_appname'))
@section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Accounting > Balance Transfer</span>
                <div class="row">
                    <div class="col-md-10">
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

                                    <label class="col-sm-1 ">
                                        <button class="btn btn-dark" type="button"id="filterBtn">Filter</button>
                                    </label>
	                            @else
	                                <input type="hidden" name="saller" id="branch_id" value="{{ Auth::user()->id }}">
	                            @endif
                            </div>
                        </form>
                    </div>
                    <div class="col-md-2">
                    	<a class="btn btn-success float-right" href="" data-toggle="modal" data-target="#manage_due_payment_modal" >New Transfer</a>
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
                            <th>S.N.</th>
                            <th scope="col">Date</th>
                            <th scope="col">Branch</th>
                            <th scope="col">Account (From)</th>
                            <th scope="col">Debit</th>
                            <th scope="col">Credit</th>
                            <th scope="col">Account (To)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>



	<div class="modal fade " id="manage_due_payment_modal" tabindex="-1" role="dialog"
	    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	    <div class="modal-dialog modal-md" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLongTitle">Manage Balance Transfer</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body" >
	                <form action="{{ route('admin.accounts.balance.transfer.store') }}" class="row" method="POST">
	                    @csrf
	                    @if (Auth::user()->getRoleNames() != '["seller"]')
                            <div class="col-sm-12">
                                <div class="form-group">
                                	<label for="">Branch</label>
                                    <select name="branch_id" id="" class="form-control">
                                        <option value="-1">--Select Shop First--</option>
                                        @foreach ($branches as $row)
                                            <option value="{{ $row->id }}">{{ $row->shopinfo->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="branch_id" id="" value="{{ Auth::user()->id }}">
                        @endif

	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Date</label>
	                            <input type="date" name="date" class="form-control" required>
	                        </div>
	                    </div>

                        <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Accounts (From)</label>
	                            <select class="selectpicker form-select form-control " name="asset_from_id" id="asset_from_id" data-show-subtext="true" data-live-search="true" required>
	                                <option selected>Select Account</option>
	                                @foreach($accounts as $row)
	                                    <option value="{{$row->id}}">{{$row->name}}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                    </div>

                        <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Current Balance</label>
	                            <input type="text" name="current_balance" id="current_balance" class="form-control" placeholder="0.00" readonly>
	                        </div>
	                    </div>

                        <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Accounts (To)</label>
	                            <select class="selectpicker form-select form-control " name="asset_to_id" id="asset_to_id" data-show-subtext="true" data-live-search="true" required>
	                                <option selected>Select Account</option>
	                                @foreach($accounts as $row)
	                                    <option value="{{$row->id}}">{{$row->name}}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                    </div>
	                    
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Amount</label>
	                            <input type="text" name="amount" id="amount" class="form-control" placeholder="50000" required="">
	                        </div>
	                    </div>

                        <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">New Balance</label>
	                            <input type="text" name="new_balance" id="new_balance" class="form-control" placeholder="0.00" readonly>
	                        </div>
	                    </div>

	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <button class="btn btn-success" type="submit">Transfer</button>
	                        </div>
	                    </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>

@endsection


@push('footer')
    <script type="text/javascript">
        function supplierPayment(branch_id = '') {
            var table = jQuery('#dataTable').DataTable({
                dom: 'Brftlip',
                buttons: ['csv', 'excel', 'pdf', 'print'],
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: true,
                ajax: {
                    url: "{{ route('admin.accounts.balance.transfer.data') }}",
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
                        data: 'date'
                    },
                    {
                        data: 'branch_id',
                        name: 'admins.name'
                    },
                    {
                        data: 'payment_method',
                        name: 'asset.name'
                    },
                    {
                        data: 'debit',
                    },
                    {
                        data: 'credit',
                    },
                    {
                        data: 'common_id',
                        name: 'account.name'
                    }
                ]
            });
        }

        jQuery('#dataTable').DataTable().destroy();
        supplierPayment($('#branch_id').val());

        jQuery(document).on('click', '#filterBtn', function(e) {
            e.preventDefault();
            jQuery('#dataTable').DataTable().destroy();
        	supplierPayment($('#branch_id').val());
        });


        function calculateCurrentBalance(){
            let current_balance = jQuery('#current_balance').val();
            let paid_amount = jQuery('#amount').val();

            let new_balance = (Number(current_balance) - Number(paid_amount));

            jQuery('#new_balance').val(new_balance);
        }

       
        jQuery(document).on('change', '#asset_from_id', function(e) {
            e.preventDefault();
            let asset_from_id = jQuery('#asset_from_id').val();
            $.ajax({
                url: "{{  url('/admin/accounts/balance-transfer/history/') }}/"+asset_from_id,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    jQuery('#manage_due_payment_modal #current_balance').val(response.balance);
                    calculateCurrentBalance();
                }
            })
        })

        jQuery(document).on('keyup', '#amount', function(e) {
            calculateCurrentBalance();
        })

    </script>
@endpush
