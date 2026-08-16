@extends('backend.layouts.master')
@section('title', 'Accounts List - ' . config('concave.cnf_appname'))
@section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Accounting > Accounts</span>
                <a class="btn btn-success float-right" href="" data-toggle="modal" data-target="#manage_current_asset_modal" >Create New Account</a>
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
                            <th scope="col">Title</th>
                            <th scope="col">Branch</th>
                            <th scope="col">Type</th>
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


	<div class="modal fade activity_log" id="manage_current_asset_modal" tabindex="-1" role="dialog"
	    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	    <div class="modal-dialog modal-md" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLongTitle">Manage Current Assets</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body" >
	                <form action="{{ route('admin.accounts.asset.store') }}" class="row" method="POST">
	                    @csrf
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Name</label>
	                            <input type="text" name="name" class="form-control" placeholder="Cash/Bkash/Bank" required>
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Branch</label>
	                            <select name="branch_id" id="" class="form-control" required>
	                                <option value="-1">-- Branch --</option>
	                                @foreach($branches as $row)
	                                	<option value="{{$row->id}}">{{ $row->shopinfo->name }}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                    </div>

	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Type</label>
	                            <select name="type" id="" class="form-control" required>
	                                <option value="-1">-- Select --</option>
	                                <option value="cash">Cash</option>
	                                <option value="bank">Bank</option>
	                                <option value="mfs">MFS</option>
	                            </select>
	                        </div>
	                    </div>

	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Amount</label>
	                            <input type="text" name="amount" class="form-control" placeholder="50000" >
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <button class="btn btn-success" type="submit">Add Asset</button>
	                        </div>
	                    </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="modal fade" id="manage_current_asset_modal_edit" tabindex="-1" role="dialog"
	    aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	    <div class="modal-dialog modal-md" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLongTitle">Edit Current Assets</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body" >
	                <form action="{{ route('admin.accounts.asset.update') }}" class="row" method="POST">
	                    @csrf
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Name</label>
	                            <input type="text" name="name" id="name" class="form-control" placeholder="Cash/Bkash/Bank" required>
	                        </div>
	                    </div>
	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Branch</label>
	                            <select name="branch_id" id="branch_id" class="form-control" required>
	                                <option value="-1">-- Branch --</option>
	                                @foreach($branches as $row)
	                                	<option value="{{$row->id}}">{{ $row->shopinfo->name }}</option>
	                                @endforeach
	                            </select>
	                        </div>
	                    </div>

	                    <div class="col-lg-12">
	                        <div class="form-group">
	                            <label for="">Type</label>
	                            <select name="type" id="type" class="form-control" required>
	                                <option value="-1">-- Select --</option>
	                                <option value="cash">Cash</option>
	                                <option value="bank">Bank</option>
	                                <option value="mfs">MFS</option>
	                            </select>
	                        </div>
	                    </div>

	                    <div class="col-lg-12">
	                        <div class="form-group">
	                        	<input type="hidden" name="account_id" id="account_id" value="">
	                            <button class="btn btn-success" type="submit">Update Asset</button>
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
        var table = jQuery('#dataTable').DataTable({
            dom: 'Brftlip',
            buttons: ['csv', 'excel', 'pdf', 'print'],
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: true,
            ajax: {
                url: "{{ route('admin.accounts.assets.data') }}",
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
                    data: 'branch_id',
                    name: 'branch_id',
                },
                {
                    data: 'type'
                },
                {
                    data: 'amount'
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
            let type = jQuery(this).attr('data-type');
            let branch_id = jQuery(this).attr('data-branch');

            jQuery('#manage_current_asset_modal_edit #account_id').val(id);
            jQuery('#manage_current_asset_modal_edit #name').val(name);

            jQuery("#manage_current_asset_modal_edit #type option[value='"+type+"']")[0].selected = true;
            if (branch_id > 0) {
            	jQuery("#manage_current_asset_modal_edit #branch_id option[value='"+branch_id+"']")[0].selected = true;
            }

            jQuery('#manage_current_asset_modal_edit').modal('show');
        });
    </script>
@endpush
