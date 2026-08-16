@extends('backend.layouts.master')
@section('title', 'Investor List - ' . config('concave.cnf_appname'))
@section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Accounting > Investor</span>
                <a class="btn btn-success float-right" href="" data-toggle="modal" data-target="#manage_investor_modal" >Create New Investor</a>
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
                            <th scope="col">Name</th>
                            <th scope="col">Share</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Paid</th>
                            <th scope="col">Balance</th>
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


	<div class="modal fade activity_log" id="manage_investor_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	    <div class="modal-dialog modal-md" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLongTitle">Add New Investor</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body" >
	                <form action="{{ route('admin.accounts.investor.store') }}" class="row" method="POST">
	                    @csrf
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Investor Name</label>
	                            <input type="text" name="name" class="form-control" placeholder="Name" required>
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Invest Amount</label>
	                            <input type="text" name="amount" class="form-control" placeholder="50000" required="">
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Share (%)</label>
	                            <input type="text" name="share" class="form-control" placeholder="10%" required="">
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Payment Method</label>
	                            <select class="form-select form-control" name="payment_method" aria-label="Default select example" required>
	                                <option selected>Choose one</option>
	                                @foreach(\App\Models\CurrentAsset::get() as $row)
	                                    <option value="{{$row->id}}">{{$row->name}}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <button class="btn btn-success" type="submit">Add Investor</button>
	                        </div>
	                    </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>


	<div class="modal fade activity_log" id="manage_investor_modal_edit" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	    <div class="modal-dialog modal-md" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLongTitle">Update Investor</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body" >
	                <form action="{{ route('admin.accounts.investor.update') }}" class="row" method="POST">
	                    @csrf
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Investor Name</label>
	                            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required>
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Share (%)</label>
	                            <input type="text" name="share" id="share" class="form-control" placeholder="10%" required="">
	                        </div>
	                    </div>
	                    
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                        	<input type="hidden" name="investor_id" id="investor_id">
	                            <button class="btn btn-success" type="submit">Update Investor</button>
	                        </div>
	                    </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>

    <div class="modal fade activity_log" id="manage_invest_payment_modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Pay Investor</h5>
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
                                {{-- <label><input type="radio" name="loan_invest" value="Loan" required checked> Loan</label> --}}
                                <label><input type="radio" name="loan_invest" value="Investor" required checked> Invest</label>
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
                                <input type="hidden" id="investor_id" name="investor" value="">
                                <button class="btn btn-success" type="submit">Make Payment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade activity_log" id="manage_invest_payments_modal" tabindex="-1" role="dialog"
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
                                        <th scope="col">Invest</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Payment Method</th>
                                        <th scope="col">Investor Details</th>
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
        var table = jQuery('#dataTable').DataTable({
            dom: 'Brftlip',
            buttons: ['csv', 'excel', 'pdf', 'print'],
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: true,
            ajax: {
                url: "{{ route('admin.accounts.investor.data') }}",
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
                    data: 'name'
                },
                {
                    data: 'share',
                    name: 'share',
                },
                {
                    data: 'amount',
                    name: 'amount',
                },
                {
                    data: 'paid',
                    name: 'paid',
                },
                {
                    data: 'balance',
                    name: 'balance',
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
        jQuery(document).on('click', '.edit_btn', function(e) {
            e.preventDefault();
            let id = jQuery(this).attr('data-id');
            let name = jQuery(this).attr('data-name');
            let share = jQuery(this).attr('data-share');

            jQuery('#manage_investor_modal_edit #investor_id').val(id);
            jQuery('#manage_investor_modal_edit #name').val(name);
            jQuery('#manage_investor_modal_edit #share').val(share);


            jQuery('#manage_investor_modal_edit').modal('show');
        });


        jQuery(document).on('click', '.pay_btn', function(e) {
            e.preventDefault();

            let investorid = jQuery(this).attr('data-id');
            jQuery('#manage_invest_payment_modal #investor_id').val(investorid);
            jQuery('#manage_invest_payment_modal').modal('show');
        })

        jQuery(document).on('click', '.payments', function(e) {
            e.preventDefault();

            let investid = jQuery(this).attr('data-id');
            $.ajax({
                url: "{{  url('/admin/accounts/investor/payment/data/') }}/"+investid,
                type: "GET",
                dataType: "html",
                success: function (response) {
                   jQuery('#manage_invest_payments_modal table tbody').empty();
                   jQuery('#manage_invest_payments_modal table tbody').html(response);
                   jQuery('#manage_invest_payments_modal').modal('show');
                }
            })
        })

    </script>
@endpush
