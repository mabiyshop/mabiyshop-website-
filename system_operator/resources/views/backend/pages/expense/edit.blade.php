@extends('backend.layouts.master')
@section('title', 'Expense Edit - ' . config('concave.cnf_appname'))
@section('content')
<div class="grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <span class="card-title">Dashboard > Expense > Edit</span>
            <a class="btn btn-success float-right" href="{{ route('admin.expense') }}">View
                Expense</a>
        </div>
    </div>
</div>
<form action="{{ route('admin.expense.update') }}" method="POST">
    @csrf
    <div class="card p-5">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">

            @if (Auth::user()->getRoleNames() != '["seller"]')
                <div class="form-group row mb-4">
                    <label class="col-sm-3 col-form-label">Select Branch <span style="color: #f00">*</span></label>
                    <div class="col-sm-9">
                        <select required name="shop_name" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select shop...">
                            @foreach($shops as  $shop)
                            <option @if($shop->id == $data->branch_id) selected="" @endif value="{{$shop->id}}">{{$shop->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @else
                <input type="hidden" name="shop_name" id="shop_name" value="{{ Auth::user()->id }}">
            @endif

            <div class=" form-group row mb-4">
                <label for="" class="col-sm-3 col-form-label">Payment Method<span
                        class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select class="form-select form-control" name="payment_method" aria-label="Default select example" required>
                        <option selected>Choose one</option>
                        @foreach(\App\Models\CurrentAsset::get() as $row)
                            <option @if($data->payment_method == $row->id) selected @endif value="{{$row->id}}">{{$row->name}} -- #{{$row->amount}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class=" form-group row mb-4">
                <label for="" class="col-sm-3 col-form-label">Title <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" name="title" value="{{ $data->title }}" class="form-control" required>
                </div>
            </div>
            <div class=" form-group row mb-4">
                <label for="" class="col-sm-3 col-form-label">Expense Category <span
                        class="text-danger">*</span></label>
                <div class="col-sm-6">
                    <select class="form-select form-control" name="expense_category" aria-label="Default select example" required>
                        <option selected>Choose one</option>
                        @foreach(\App\Models\ExpenseCategory::where('is_deleted', 0)->where('is_active', 1)->get() as $row)
                            <option @if($data->expense_category == $row->id) selected @endif value="{{$row->id}}">{{$row->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-info" type="button" id="manage_category_btn"> Manage Category</button>
                </div>
            </div>
            <div class=" form-group row mb-4">
                <label for="" class="col-sm-3 col-form-label">Amount <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="number" name="amount" value="{{ $data->amount }}" class="form-control" required>
                </div>

            </div>

            <div class="form-group row">
                <label for="" class="col-sm-3 col-form-label">Description <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <textarea type="text" name="description" class="textEditor form-control">{!! $data->title !!}</textarea>
                </div>
            </div>


            <div class=" form-group row mb-4">
                <label for="" class="col-sm-3 col-form-label">Status <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select class="form-select form-control" name="status" aria-label="Default select example" required>
                        <option selected>Choose one</option>
                        <option value="1" {{ $data->status == 1 ? 'selected':'' }}>Pending</option>
                        <option value="6" {{ $data->status == 6 ? 'selected':'' }}>Completed</option>
                    </select>
                </div>

            </div>

        </div>
        <div class="">
            <div class="float-right">
                <input type="hidden" name="id" value="{{ $data->id }}">
                <button type="submit" class="btn btn-success">Update Expense</button>
            </div>
        </div>
    </div>
</form>

    @push('footer')

    <script type="text/javascript">
        jQuery(document).on('click','#manage_category_btn',function(e){
            e.preventDefault();
            jQuery('#manage_category_modal').modal('show');
        });
    </script>
    @endpush
@endsection