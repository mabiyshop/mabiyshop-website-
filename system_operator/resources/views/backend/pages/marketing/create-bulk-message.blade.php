@extends('backend.layouts.master')
@section('title', 'Send Bulk Message - ' . config('concave.cnf_appname'))
@section('content')
    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Matrketing > Send Bulk Message</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <form class="form-sample" method="post" action="{{ route('admin.marketing.send.bulk.message') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Title<span style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="title" placeholder="Ex: New Year Gretings"
                                            class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Select Customer<span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <select name="customer[]" class="form-control selectpicker" data-live-search="true"
                                            multiple required>
                                            <option value="-1">All Customer - {{ count($customers) }}</option>
                                            @foreach ($customers as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name . ' - ' . $item->phone . ' - ' . $item->email }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Channel <span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-check form-check-flat font_small_11">
                                                    <label class="form-check-label">
                                                        <input type="radio" name="channel" value="sms"
                                                            class="form-check-input" required>SMS<i
                                                            class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-check form-check-flat font_small_11">
                                                    <label class="form-check-label">
                                                        <input type="radio" name="channel" value="email"
                                                            class="form-check-input" required>EMAIL<i
                                                            class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 sms_item">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">SMS Gateway</label>
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-check form-check-flat font_small_11">
                                                    <label class="form-check-label">
                                                        <input type="radio" name="gateway_option" value="musking"
                                                            class="form-check-input">Musking<i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-check form-check-flat font_small_11">
                                                    <label class="form-check-label">
                                                        <input type="radio" value="nonmusking" name="gateway_option" class="form-check-input"
                                                            checked>Non Musking<i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="row email_item">

                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Email Header<span
                                            style="color: #f00">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="email_header" placeholder="Ex: Happy New Year"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 email_item">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email Body</label>
                                    <div class="col-sm-10">
                                        <textarea type="text" name="email_body" placeholder="Description" class="form-control textEditor"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row sms_item">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Message <br> <small class="text-danger">You have
                                            to send message in Bengali language</small> </label>
                                    <div class="col-sm-10">
                                        <textarea type="text" name="message_body" placeholder="Message Body" class="form-control sms_message_body"></textarea>
                                        <div class="d-flex">
                                            <label for="total-charecter" class="p-2 bg-secondary ">Total: <span>0</span></label>
                                            <label for="total-parts" class="ml-1 mr-1 p-2 bg-secondary">Parts: <span>0</span></label>
                                            <label for="total-remaining" class="p-2 bg-secondary">Remaining: <span>0</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p class="text-right">
                                        <button class="btn btn-primary" name="save" type="submit">Send Bulk
                                            Message</button>
                                    </p>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('footer')
        <script>
            jQuery(document).on('change', 'input[name="channel"]', function() {
                if (jQuery(this).val() == 'sms') {
                    jQuery('.email_item').hide();
                    jQuery('.sms_item').show();
                } else if (jQuery(this).val() == 'email') {
                    jQuery('.sms_item').hide();
                    jQuery('.email_item').show();
                }
            });

            jQuery(document).on('input', '.sms_message_body', function(){
                let sms = jQuery(this).val();
                let result = calculateSMSLength(sms);
            console.log("Total Characters:", result.totalCharacters);
            console.log("Total Parts:", result.totalParts);
            console.log("Remaining Characters in Last Part:", result.remaining);
                // let total = countCharacters(sms);
                // let remaining = total > 71 ? 67 : 70; // Default remaining for Unicode
                // let parts = 1;

                // if (total > 160) {
                //     parts = Math.ceil(total / 153);
                //     remaining = total > 71 ? 67 - (total % 67) : 153 - (total % 153);
                // }

                jQuery('label[for="total-charecter"] span').text(result.totalCharacters);
                jQuery('label[for="total-remaining"] span').text(result.remaining);
                jQuery('label[for="total-parts"] span').text(result.totalParts);
            });

            function calculateSMSLength(message) {
                let totalCharacters = message.length;
                let gsmLimit = 160;
                let unicodeLimit = 70;
                let gsmPartLimit = 153;
                let unicodePartLimit = 67;

                let containsUnicode = false;
                for (let i = 0; i < message.length; i++) {
                    if (message.charCodeAt(i) > 127) { // Check for Unicode characters
                        containsUnicode = true;
                        break;
                    }
                }

                let totalParts = Math.ceil(totalCharacters / (containsUnicode ? unicodeLimit : gsmLimit));
                let remaining = containsUnicode ? unicodeLimit - (totalCharacters % unicodeLimit) : gsmLimit - (totalCharacters % gsmPartLimit);

                return {
                    totalCharacters: totalCharacters,
                    totalParts: totalParts,
                    remaining: remaining
                };
            }

            
        </script>
    @endpush


@endsection
