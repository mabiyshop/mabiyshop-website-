@extends('backend.layouts.master')
@section('title', 'Customer Due - ' . config('concave.cnf_appname'))
@section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Accounting > Customer Due</span>
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
                    	<a class="btn btn-success float-right" href="" data-toggle="modal" data-target="#manage_due_payment_modal" >Create New Payment</a>
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
                            <th scope="col">Customer</th>
                            <th scope="col">Invoice No</th>
                            <th scope="col">Paid Amount</th>
                            <th scope="col">Payment Method</th>
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
	                <h5 class="modal-title" id="exampleModalLongTitle">Manage Customer Due</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body" >
	                <form action="{{ route('admin.accounts.customer.due.store') }}" class="row" method="POST">
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
	                            <label for="">Customers</label>
	                            <select class="selectpicker form-select form-control " name="customer_id" id="customer_id" data-show-subtext="true" data-live-search="true" required>
	                                <option selected>Select customer</option>
	                                @foreach($customers as $row)
	                                    <option value="{{$row->id}}">{{$row->name}}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                    </div>

                        <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Customer Due</label>
	                            <input type="text" name="due" id="customer_due" class="form-control" placeholder="0.00" readonly>
	                        </div>
	                    </div>

                        <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Invoice Number</label>
	                            <select class="selectpicker form-select form-control "  id="invoice_number" name="invoice_number" data-show-subtext="true" data-live-search="true">
	                                <option value="NULL" selected>Choose Invoice Number</option>
	                                
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
	                            <label for="">Current Balance</label>
	                            <input type="text" name="current_balance" id="current_balance" class="form-control" placeholder="0.00" readonly>
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
	                            <button class="btn btn-success" type="submit">Make Payment</button>
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
        function customerPayment(branch_id = '') {
            var table = jQuery('#dataTable').DataTable({
                dom: 'Brftlip',
                buttons: ['csv', 'excel', 'pdf', 'print'],
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: true,
                ajax: {
                    url: "{{ route('admin.accounts.customer.due.data') }}",
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
                        data: 'common_id',
                        name: 'customer.name'
                    },
                    {
                        data: 'invoice_number'
                    },
                    {
                        data: 'amount',
                    },
                    {
                        data: 'payment_method',
                        name: 'asset.name'
                    }
                ]
            });
        }

        jQuery('#dataTable').DataTable().destroy();
        customerPayment($('#branch_id').val());

        jQuery(document).on('click', '#filterBtn', function(e) {
            e.preventDefault();
            jQuery('#dataTable').DataTable().destroy();
        	customerPayment($('#branch_id').val());
        });


        function calculateCurrentBalance(){
            let customer_balance = jQuery('#customer_due').val();
            let paid_amount = jQuery('#amount').val();

            let current_balance = (Number(customer_balance) - Number(paid_amount));

            jQuery('#current_balance').val(current_balance);
        }

       
        jQuery(document).on('change', '#customer_id', function(e) {
            e.preventDefault();
            let customer_id = jQuery('#customer_id').val();
            $.ajax({
                url: "{{  url('/admin/accounts/customer/history/') }}/"+customer_id,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    jQuery('#manage_due_payment_modal #customer_due').val(response.balance);
                    jQuery.each( response.invoice, function( key, value ) {
                        let date = new Date(value.created_at).getFullYear().toString().slice(-2);
                        jQuery('#manage_due_payment_modal #invoice_number').append(
                            '<option value="'+value.id+'">MB'+date+value.id+'</option>'
                        );
                    });
                    $('.selectpicker').selectpicker('refresh');
                    calculateCurrentBalance();
                }
            })
        })

        jQuery(document).on('keyup', '#amount', function(e) {
            calculateCurrentBalance();
        })

    </script>
@endpush
