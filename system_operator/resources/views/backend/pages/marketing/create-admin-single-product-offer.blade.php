@extends('backend.layouts.master')
@section('title', 'Flash Deal Create - ' . config('concave.cnf_appname'))
@section('content')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet" />
    <style type="text/css">
        .bootstrap-tagsinput .tag {
            margin-right: 2px;
            color: white !important;
            background-color: #5daf21;
            padding: 0.2rem;
            line-height: 28px;
        }
    </style>

    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Marketing >Single Product Offers > Create</span>
                <a class="btn btn-success float-right" href="{{ route('admin.single.product.offer') }}">View Single Product
                    Offers List</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <form class="form-sample" method="post" action="{{ route('admin.single.product.offer.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Title</label>
                                    <div class="col-sm-9">
                                        <input type="text" data-slugable-model="single_product_offer" name="title"
                                            id="title" value="{{ old('title') }}" placeholder="Title"
                                            class="form-control slug_maker" />
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Slug</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="slug" id="slug" placeholder="Slug"
                                            class="form-control slug_taker" value="{{ old('slug') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Select Products</label>
                                    <div class="col-sm-9">
                                        <select name="product_id" id="select_deal_products" data-show-subtext="true"
                                            data-live-search="true" class="selectpicker form-control" required>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    @if (old('product_id') == $product->id) selected @endif>{{ $product->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Start Date</label>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" name="offer_start_date" value="{{ old('offer_start_date') }}"
                                            placeholder="Start Date" class="form-control" />
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">End Date</label>
                                    <div class="col-sm-9">
                                        <input type="datetime-local" name="offer_end_date" value="{{ old('offer_end_date') }}"
                                            placeholder="End Date" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Special Price</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend bg-primary border-primary">
                                                <span
                                                    class="input-group-text bg-transparent text-white">{{ Helper::getDefaultCurrency()->currency_symbol }}</span>
                                            </div>
                                            <input type="number" step="0.01" min="1"
                                                name="discount" value="{{ old('discount') }}"
                                                class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Special Price Type</label>
                                    <div class="col-sm-9">
                                        <select name="discount_type" class="form-control">
                                            <option @if (old('discount_type') == 1) selected @endif
                                                value="1">Fixed</option>
                                            <option @if (old('discount_type') == 2) selected @endif
                                                value="2">Percent</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Status</label>
                                    <div class="col-sm-9">
                                        <div class="form-check form-check-flat">
                                            <label class="form-check-label">
                                                <label class="switch">
                                                    <input name="status" type="checkbox" @if (old('status')) checked @endif>
                                                    <span class="slider round"></span>
                                                </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title">Banners</p>
                                <div class="banners-area">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <td>Image</td>
                                                <td>Title</td>
                                                <td>Details</td>
                                                <td class="text-center">
                                                    <a href="#" type="button" onclick="incrementRow('banners-area', 'itwillbecoppy'); return false;" class="btn btn-sm btn-primary">
                                                        <i class="mdi mdi-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="itwillbecoppy" data-row-no="1">
                                                <td>
                                                    <button type="button" data-image-width="800" data-image-height="800"
                                                        data-input-name="banner_images" data-input-type="multiple"
                                                        class="btn btn-success initConcaveMedia">Select File
                                                    </button>
                                                    
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="banner_title[]" placeholder="Features">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="banner_details[]" placeholder="Value">
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" type="button" class="btn btn-sm btn-danger" onclick="removeRow(event); return false;"><i class="mdi mdi-delete"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title"> Package</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label">Heading</label>
                                            <input type="text" name="package_heading"  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="col-form-label">Details</label>
                                            <textarea type="text" name="package_details" class="textEditor form-control">{{ old('package_details') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-4 d-flex align-items-center">
                                        <div class="form-group">
                                            <label class="">Image</label><br>
                                            <button type="button" data-image-width="1000" data-image-height="1000"
                                                data-input-name="package_image" data-input-type="single"
                                                class="btn btn-success initConcaveMedia">Select File
                                            </button>
                                            @if (old('package_image'))
                                                <p class="selected_images_gallery">
                                                    <span>
                                                        <input type="hidden" value="{{ old('package_image') }}"
                                                            name="package_image">
                                                        <img src="{{ '/' . old('package_image') }}">
                                                        <b data-file-url="{{ old('package_image') }}"
                                                            class="selected_image_remove">X</b>
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title">Why is our perfume unique?</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label">Title</label>
                                            <input type="text" name="why_our_perfume_is_unique_heading"  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label">Heading</label>
                                            <input type="text" name="why_our_perfume_is_unique" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="why_our_perfume_is_unique-area">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <td>Icon</td>
                                                        <td>Title</td>
                                                        <td>Details</td>
                                                        <td class="text-center">
                                                            <a href="#" type="button" onclick="incrementRow('why_our_perfume_is_unique-area', 'why_our_perfume_is_uniquecoppy'); return false;" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-plus"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="why_our_perfume_is_uniquecoppy" data-row-no="1">
                                                        <td>
                                                            <input type="text" class="form-control" name="why_our_perfume_is_unique_icon[]" placeholder="Icon ">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="why_our_perfume_is_unique_title[]" placeholder="Title">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="why_our_perfume_is_unique_details[]" placeholder="Details">
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="#" type="button" class="btn btn-sm btn-danger" onclick="removeRow(event); return false;"><i class="mdi mdi-delete"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title">Product Video</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label">Heading</label>
                                            <input type="text" name="product_video_deading" class="form-control" value="{{ old('product_video_deading') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-form-label">Video Link</label>
                                            <input type="text" name="product_video_link" class="form-control" value="{{ old('product_video_link') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title">Price & Size</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label">Thumbnail</label>
                                            <button type="button" data-image-width="1000" data-image-height="1000"
                                                data-input-name="price_size_image" data-input-type="single"
                                                class="btn btn-success initConcaveMedia">Select File
                                            </button>
                                            @if (old('price_size_image'))
                                                <p class="selected_images_gallery">
                                                    <span>
                                                        <input type="hidden" value="{{ old('price_size_image') }}"
                                                            name="price_size_image">
                                                        <img src="{{ '/' . old('price_size_image') }}">
                                                        <b data-file-url="{{ old('price_size_image') }}"
                                                            class="selected_image_remove">X</b>
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="product_size_and_price-area">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <td>Size</td>
                                                        <td>Price</td>
                                                        <td class="text-center">
                                                            <a href="#" type="button" onclick="incrementRow('product_size_and_price-area', 'product_size_and_pricecoppy'); return false;" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-plus"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="product_size_and_pricecoppy" data-row-no="1">
                                                        <td>
                                                            <input type="text" class="form-control" name="product_size_and_price_size[]" placeholder="Size ">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" name="product_size_and_price_price[]" placeholder="Price">
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="#" type="button" class="btn btn-sm btn-danger" onclick="removeRow(event); return false;"><i class="mdi mdi-delete"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title">Why we are best?</p>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-form-label">Our History</label>
                                            <input type="text" name="our_history" class="form-control" value="{{ old('our_history') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-form-label">Refund/Exchange</label>
                                            <input type="text" name="refund_exchange_facility" class="form-control" value="{{ old('refund_exchange_facility') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-form-label">Cash on delivery</label>
                                            <input type="text" name="cash_on_delivery_facility" class="form-control" value="{{ old('cash_on_delivery_facility') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-form-label">Our Support</label>
                                            <input type="text" name="our_support" class="form-control" value="{{ old('our_support') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-form-label">Thumbnail</label>
                                            <button type="button" data-image-width="1000" data-image-height="1000"
                                                data-input-name="why_we_best_image" data-input-type="single"
                                                class="btn btn-success initConcaveMedia">Select File
                                            </button>
                                            @if (old('why_we_best_image'))
                                                <p class="selected_images_gallery">
                                                    <span>
                                                        <input type="hidden" value="{{ old('why_we_best_image') }}"
                                                            name="why_we_best_image">
                                                        <img src="{{ '/' . old('why_we_best_image') }}">
                                                        <b data-file-url="{{ old('why_we_best_image') }}"
                                                            class="selected_image_remove">X</b>
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title">Who can use the product</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label">Heading</label>
                                            <input type="text" name="who_can_use_the_product_heading"  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label">Thumbnail</label>
                                            <button type="button" data-image-width="1000" data-image-height="1000"
                                                data-input-name="who_can_use_the_product_image" data-input-type="single"
                                                class="btn btn-success initConcaveMedia">Select File
                                            </button>
                                            @if (old('who_can_use_the_product_image'))
                                                <p class="selected_images_gallery">
                                                    <span>
                                                        <input type="hidden" value="{{ old('who_can_use_the_product_image') }}"
                                                            name="who_can_use_the_product_image">
                                                        <img src="{{ '/' . old('who_can_use_the_product_image') }}">
                                                        <b data-file-url="{{ old('who_can_use_the_product_image') }}"
                                                            class="selected_image_remove">X</b>
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="who_can_use_the_product-area">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <td>Points</td>
                                                        <td class="text-center">
                                                            <a href="#" type="button" onclick="incrementRow('who_can_use_the_product-area', 'who_can_use_the_productcoppy'); return false;" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-plus"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="who_can_use_the_productcoppy" data-row-no="1">
                                                        <td>
                                                            <input type="text" class="form-control" name="who_can_use_the_product_point[]" placeholder="Points ">
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="#" type="button" class="btn btn-sm btn-danger" onclick="removeRow(event); return false;"><i class="mdi mdi-delete"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title">Customer Review</p>
                                <div class="customer_review-area">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <td>Image</td>
                                                <td>Name</td>
                                                <td>Details</td>
                                                <td>Stars</td>
                                                <td class="text-center">
                                                    <a href="#" type="button" onclick="incrementRow('customer_review-area', 'customer_reviewcoppy'); return false;" class="btn btn-sm btn-primary">
                                                        <i class="mdi mdi-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="customer_reviewcoppy" data-row-no="1">
                                                <td>
                                                    <button type="button" data-image-width="800" data-image-height="800"
                                                        data-input-name="reviewer_image" data-input-type="multiple"
                                                        class="btn btn-success initConcaveMedia">Select File
                                                    </button>
                                                    
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="reviewer_name[]" placeholder="Name">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="reviewer_details[]" placeholder="Review">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="reviewer_star[]" placeholder="Stars">
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" type="button" class="btn btn-sm btn-danger" onclick="removeRow(event); return false;"><i class="mdi mdi-delete"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2 mt-2">
                                <p class="content_title">FAQ's</p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label">Thumbnail</label>
                                            <button type="button" data-image-width="1000" data-image-height="1000"
                                                data-input-name="faq_section_image" data-input-type="single"
                                                class="btn btn-success initConcaveMedia">Select File
                                            </button>
                                            @if (old('faq_section_image'))
                                                <p class="selected_images_gallery">
                                                    <span>
                                                        <input type="hidden" value="{{ old('faq_section_image') }}"
                                                            name="faq_section_image">
                                                        <img src="{{ '/' . old('faq_section_image') }}">
                                                        <b data-file-url="{{ old('faq_section_image') }}"
                                                            class="selected_image_remove">X</b>
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="faqs-area">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <td>Question</td>
                                                        <td>Answare</td>
                                                        <td class="text-center">
                                                            <a href="#" type="button" onclick="incrementRow('faqs-area', 'faqscoppy'); return false;" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-plus"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="faqscoppy" data-row-no="1">
                                                        <td>
                                                            <input type="text" class="form-control" name="faqscoppy_question[]" placeholder="Question ">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="faqscoppy_answare[]" placeholder="Answare">
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="#" type="button" class="btn btn-sm btn-danger" onclick="removeRow(event); return false;"><i class="mdi mdi-delete"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <button class="btn btn-primary" name="save" type="submit">Add</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

    <script type="text/javascript">
        jQuery(document).ready(function() {
            $(".tag_field").tagsinput('items');

            jQuery(document).on('change', '#select_deal_products', function(e) {
                e.preventDefault();

                var ids = jQuery(this).val();
                if (ids != '') {
                    jQuery.ajax({
                        url: "/admin/flash-deals/get/products/" + ids,
                        type: "get",
                        data: {},
                        success: function(response) {
                            jQuery('#all_selected_products_area').html(response);
                        }
                    });
                } else {
                    jQuery('#all_selected_products_area').html('');
                }

            })
        });

        function incrementRow(first_div, second_div, copy_single = null){
            console.log(copy_single);
                if (copy_single == null) {
                    var maindiv = $('.' + first_div);
                }else{
                    var maindiv = $(copy_single).closest('.' + first_div);
                }
                var copydiv = maindiv.find('.' + second_div + ':last');
                var clonedDiv = copydiv.clone(true);
                var rowNumber = parseInt(copydiv.attr('data-row-no')) + 1;
                clonedDiv.attr('data-row-no', rowNumber);
                clonedDiv.insertAfter(copydiv);
        }

        function removeRow(event){
            event.preventDefault();
            var row = event.target.closest('tr');
            row.remove();
        }

    </script>
@endpush
