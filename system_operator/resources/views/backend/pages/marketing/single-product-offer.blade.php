@extends('backend.layouts.master')
@section('title', 'Single Product Offers - ' . config('concave.cnf_appname'))
@section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Marketing > Single Product Offers</span>
                <a class="btn btn-success float-right" href="{{ route('admin.single.product.offer.create') }}">Create New Single Product Offers</a>
            </div>
        </div>
    </div>
    <div class="grid-margin stretch-card" style="width: 100%;">
        <div class="card">
            <div class="table-responsive">
                <table id="" class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Product</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th class="text-center" data-priority="1">Action</th>
                        </tr>
                    </thead>
                    <tbody class="">
                        @foreach ($offers as $key => $offer)
                            <tr class="attribute_list">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $offer->title }}</td>
                                <td>{{ $offer->product->title ?? '' }}</td>
                                <td>{{ $offer->offer_start_date }}</td>
                                <td>{{ $offer->offer_end_date }}</td>
                                <td>{{ $offer->slug }}</td>
                                <td>
                                    <span
                                        class="badge {{ 'badge_' . strtolower(Helper::getStatusName('default', $offer->status)) }}">
                                        {{ Helper::getStatusName('default', $offer->status) }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if (Auth::user()->can('marketing.single.product.offer.edit'))
                                        <a class="text-success"
                                            href="{{ route('admin.single.product.offer.edit', $offer->id) }}"><i
                                                class="icon_btn mdi mdi-pencil-box-outline"></i></a>
                                    @endif

                                    @if (Auth::user()->can('marketing.single.product.offer.copy'))
                                        @php
                                            $link = 'https://offer.mabiyshop.com/?slug=' . $offer->slug;
                                        @endphp
                                        <a class="copyBtn" href="#"
                                            onclick="myFunction('{{ $link }}')"><i
                                                class="icon_btn text-info mdi mdi-content-copy"></i></a>
                                    @endif

                                    @if (Auth::user()->can('marketing.single.product.offer.notification'))
                                        <a class="sendFlashNotificationBtn"
                                            href="{{ route('admin.flash_deal.send.pushnotification', $offer->id) }}"><i
                                                class="icon_btn text-warning mdi mdi-bell-ring"></i></a>
                                    @endif

                                    @if (Auth::user()->can('marketing.single.product.offer.sms'))
                                        <a class="sendFlashSMSBtn text-success" data-id="{{ $offer->id }}" href="{{ route('admin.flash_deal.send.sms', $offer->id) }}"><i
                                                class=" icon_btn text-default mdi mdi-message-processing"></i></a>
                                    @endif

                                    @if (Auth::user()->can('marketing.single.product.offer.delete'))
                                        <a class="text-danger delete_btn"
                                            data-url="{{ route('admin.single.product.offer.delete', $offer->id) }}"
                                            data-toggle="modal" data-target="#deleteModal" href="#"><i
                                                class="icon_btn text-danger mdi mdi-delete"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
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
                    <p>Once you delete this item. You can not restore this item again!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a type="button" href="#" class="btn btn-danger delete_trigger">Delete</a>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('footer')
    <script>
        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);
        }

        function myFunction(url) {
            copyToClipboard(url);
            Swal.fire({
                icon: 'success',
                title: 'Copied to clipbord!',
                showConfirmButton: true,
                timer: 1500
            })
        }

        jQuery(document).on("click", ".sendFlashSMSBtn", function(e){
            e.preventDefault();
            let url = jQuery(this).attr('href'); 
            Swal.fire({
                title: 'Are you sure to send sms to all users?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url; // Check if 'url' is defined
                }
            });
        });

        jQuery(document).on("click", ".sendFlashNotificationBtn", function(e){
            e.preventDefault();
            let url = jQuery(this).attr('href'); 
            Swal.fire({
                title: 'Are you sure to send notification to all users?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Send it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url; // Check if 'url' is defined
                }
            });
        });
    </script>
@endpush
