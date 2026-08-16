@extends('backend.layouts.master')
@section('title', 'Expense Report - ' . config('concave.cnf_appname'))
@section('content')
<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <span class="card-title">Dashboard > Expense > Report</span>
            <!-- <a class="btn btn-success float-right" href="{{ route('admin.expense.create') }}">Create New
                Expense</a> -->
            <div class="row">
                <div class="col-md-12">

                    <div class="form-group row">
                        <!-- @if (Auth::user()->getRoleNames() != '["seller"]') -->
                        <div class="col-sm-2">
                            <div class="input-group">
                                <select name="expense_category" id="expense_category" class="selectpicker form-control"
                                    data-show-subtext="true" data-live-search="true">
                                    <option value="-1">--Select Category First--</option>
                                    @foreach(\App\Models\ExpenseCategory::where('is_deleted', 0)->where('is_active', 1)->get() as $row)
                                        <option value="{{$row->id}}">{{$row->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- @else
                                    <input type="hidden" name="saller" id="seller_id" value="{{Auth::user()->id}}">
                                @endif -->

                        <div class="col-sm-2">
                            <div class="input-group">
                                <select name="filter_option" id="filter_option" class="form-control">
                                    <option value="-1">--Select Option First--</option>
                                    <option value="today">Today</option>
                                    <option value="7 day">7 Day</option>
                                    <option value="this month">This Month</option>
                                    <option value="this year">This Year</option>
                                    <option value="date range">Date Range</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <select name="status_id" id='status_id' class="form-control">
                                    <option value="-1">--Select Option First--</option>
                                    <option value="1">Pending</option>
                                    <option value="6">Completed</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-2 d-none" id="start_date_area">
                            <div class="input-group">
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-2 d-none" id="end_date_area">
                            <div class="input-group">
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>

                        <label class="col-sm-1"><button class="btn btn-dark" type="submit"
                                id="filter_data">Filter</button></label>
                    </div>
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
                        <th>SL</th>
                        <th>Title</th>
                        <th>Expense Category</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
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


<div class="modal fade" id="career_quick_view_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="">Career Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class="card">
                    <div class="card-body career_form_element">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@endsection

@push('footer')
<script type="text/javascript">
function fetchData(expense_category = '', filter_by = '', start_date = '', end_date = '', status_id = '') {
    var table = jQuery('#dataTable').DataTable({
        dom: 'Brftlip',
        buttons: ['csv', 'excel', 'pdf', 'print'],
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,

        ajax: {
            url: "{{ route('admin.get.expense.report') }}",
            type: 'GET',
            data: {
                'expense_category': expense_category,
                'filter_by': filter_by,
                'start_date': start_date,
                'end_date': end_date,
                'status_id': status_id,
            }
        },
        aLengthMenu: [
            [25, 50, 100, 500, 5000, -1],
            [25, 50, 100, 500, 5000, "All"]
        ],
        iDisplayLength: 25,
        "language": {
            "processing": '<span style="color:#4eb9fa;"><i class=" mdi mdi-spin mdi-settings"></i> LOADING...</span>'
        },
        "order": [
            [0, 'desc']
        ],
        columns: [{
                data: 'id',
                "className": "text-center",
                orderable: false,
                searchable: false,
            },
            {
                data: 'title',
                name: 'title',
            },
            {
                data: 'expense_category',
                name: 'expense_category',
            },
            {
                data: 'amount',
                name: 'amount',
            },
            {
                data: 'payment_method',
                name: 'payment_method',
            },
            {
                data: 'status',
                name: 'status',
            },
            {
                data: 'description',
                name: 'description',
            },

        ]
    });
}
fetchData();

// Quick View customer
jQuery(document).on('click', '.career_quick_view_btn', function(e) {
    e.preventDefault();
    jQuery.ajax({
        url: "/admin/career/view/" + jQuery(this).attr('data-id'),
        type: "get",
        data: {

        },
        success: function(response) {
            jQuery('.career_form_element').html(response);
            jQuery('#career_quick_view_modal').modal('show');
        }
    });
});



jQuery(document).on('change', '#filter_option', function(e) {
    e.preventDefault();
    var val = $(this).val();
    if (val == 'date range') {
        $('#start_date_area').removeClass('d-none');
        $('#start_date_area').addClass('d-block');

        $('#end_date_area').removeClass('d-none');
        $('#end_date_area').addClass('d-block');
    } else {
        $('#start_date_area').removeClass('d-block');
        $('#start_date_area').addClass('d-none');

        $('#end_date_area').removeClass('d-block');
        $('#end_date_area').addClass('d-none');
    }
});

$('#filter_data').click(function(e) {
    e.preventDefault();

    jQuery('#dataTable').DataTable().destroy();
    fetchData($('#expense_category').val(), $('#filter_option').val(), $('#start_date').val(), $('#end_date').val(), $('#status_id').val());

});
</script>
@endpush