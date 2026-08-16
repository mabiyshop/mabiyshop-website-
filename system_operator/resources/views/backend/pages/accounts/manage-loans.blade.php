@extends('backend.layouts.master')
@section('title', 'Loan List - ' . config('concave.cnf_appname'))
@section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Accounting > Loan</span>
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
                    	<a class="btn btn-success float-right" href="" data-toggle="modal" data-target="#manage_loan_modal" >Create New Loan</a>
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
                            <th scope="col">Loan From</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Interest</th>
                            <th scope="col">Last Payment Date</th>
                            <th scope="col">Payment Method</th>
                            <th scope="col">Payable</th>
                            <th scope="col">Paid</th>
                            <th scope="col">Due</th>
                            <th class="text-center" data-priority="1">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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


	<div class="modal fade " id="manage_loan_modal" tabindex="-1" role="dialog"
	    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	    <div class="modal-dialog modal-md" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLongTitle">Manage Loan</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body" >
	                <form action="{{ route('admin.accounts.loans.store') }}" class="row" method="POST">
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
	                            <label for="">Loan From</label>
	                            <input type="text" name="loan_from" class="form-control" placeholder="Bank name" required>
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Amount</label>
	                            <input type="text" name="amount" class="form-control" placeholder="50000" required="">
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Interest (%)</label>
	                            <input type="text" name="interest" class="form-control" placeholder="10%" required="">
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Last Payment Date</label>
	                            <input type="date" name="payment_last_date" class="form-control" placeholder="10%" >
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Payment Method</label>
	                            <select class="form-select form-control" name="payment_method" aria-label="Default select example" required>
	                                <option selected>Choose one</option>
	                                @foreach($accounts as $row)
	                                    <option value="{{$row->id}}">{{$row->name}}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <button class="btn btn-success" type="submit">Add Loan</button>
	                        </div>
	                    </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>


    <div class="modal fade activity_log" id="manage_loan_payment_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Pay Loan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" >
                    <form action="{{ route('admin.accounts.loan.investor.payment') }}" class="row" method="POST">
                        @csrf
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Loan/Investor</label><br>
                                <label><input type="radio" name="loan_invest" value="Loan" required checked> Loan</label>
                                {{-- <label><input type="radio" name="loan_invest" value="Investor"> Invest</label> --}}
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Amount</label>
                                <input type="text" name="amount" class="form-control" placeholder="50000" required="">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Payment Method</label>
                                <select class="form-select form-control" name="payment_method" aria-label="Default select example" required>
                                    <option selected>Choose one</option>
                                    @foreach($accounts as $row)
	                                    <option value="{{$row->id}}">{{$row->name}}</option>
	                                @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input type="hidden" id="loan_id" name="loan" value="">
                                <button class="btn btn-success" type="submit">Make Payment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade activity_log" id="manage_investloan_payments_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Payment History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Loan</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Payment Method</th>
                                        <th scope="col">Loan Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


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
                    url: "{{ route('admin.accounts.loans.data') }}",
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
                        name: 'branch.name'
                    },
                    {
                        data: 'loan_from'
                    },
                    {
                        data: 'amount',
                    },
                    {
                        data: 'interest',
                    },
                    {
                        data: 'payment_last_date'
                    },
                    {
                        data: 'payment_method',
                        name: 'asset.name'
                    },
                    {
                        data: 'payable'
                    },
                    {
                        data: 'paid'
                    },
                    {
                        data: 'pending'
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
        }

        jQuery('#dataTable').DataTable().destroy();
        cashflow($('#branch_id').val());

        jQuery(document).on('click', '#filterBtn', function(e) {
            e.preventDefault();
            jQuery('#dataTable').DataTable().destroy();
        	cashflow($('#branch_id').val());
        });

        jQuery(document).on('click', '.pay_btn', function(e) {
            e.preventDefault();

            let loanid = jQuery(this).attr('data-id');
            jQuery('#manage_loan_payment_modal #loan_id').val(loanid);
            jQuery('#manage_loan_payment_modal').modal('show');
        })

        jQuery(document).on('click', '.payments', function(e) {
            e.preventDefault();

            let loanid = jQuery(this).attr('data-id');
            $.ajax({
                url: "{{  url('/admin/accounts/loans/payment/data/') }}/"+loanid,
                type: "GET",
                dataType: "html",
                success: function (response) {
                   jQuery('#manage_investloan_payments_modal table tbody').empty();
                   jQuery('#manage_investloan_payments_modal table tbody').html(response);
                   jQuery('#manage_investloan_payments_modal').modal('show');
                }
            })
        })

    </script>
@endpush
