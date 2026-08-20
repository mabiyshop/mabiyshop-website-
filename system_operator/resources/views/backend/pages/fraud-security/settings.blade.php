@extends('backend.layouts.master')
@section('title', 'Fraud & Security - Settings - ' . config('concave.cnf_appname'))
@section('content')

    <div class="grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <span class="card-title">Dashboard > Fraud & Security > Settings</span>
                <a href="{{ route('admin.fraud-security') }}" class="btn btn-dark float-right">Back to Fraud & Security</a>
            </div>
        </div>
    </div>

    <div class="grid-margin">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form id="fraudSettingsForm">
                            @csrf
                            <h4 class="card-title text-uppercase mb-3">Courier Risk Rules</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Normal if success rate >= (%)</label>
                                        <input type="number" name="good_courier_success_rate" class="form-control" value="{{ $settings['good_courier_success_rate'] ?? 60 }}" min="0" max="100" step="0.1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Review if success rate >= (%) and below Normal</label>
                                        <input type="number" name="high_risk_courier_success_below" class="form-control" value="{{ $settings['high_risk_courier_success_below'] ?? 40 }}" min="0" max="100" step="0.1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Trusted if success rate >= (%)</label>
                                        <input type="number" name="trusted_courier_success_rate" class="form-control" value="{{ $settings['trusted_courier_success_rate'] ?? 80 }}" min="0" max="100" step="0.1" required>
                                    </div>
                                </div>
                            </div>

                            <h4 class="card-title text-uppercase mb-3 mt-4">Customer History Rules</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Trusted after completed orders >=</label>
                                        <input type="number" name="trusted_mabiy_completed_orders" class="form-control" value="{{ $settings['trusted_mabiy_completed_orders'] ?? 2 }}" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Review if return/cancel rate >= (%)</label>
                                        <input type="number" name="high_mabiy_return_rate" class="form-control" value="{{ $settings['high_mabiy_return_rate'] ?? 50 }}" min="0" max="100" step="0.1" required>
                                    </div>
                                </div>
                            </div>

                            <h4 class="card-title text-uppercase mb-3 mt-4">New Customer Rules</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Require OTP</label>
                                        <select name="new_customer_otp_required" class="form-control">
                                            <option value="1" {{ ($settings['new_customer_otp_required'] ?? 1) ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ !($settings['new_customer_otp_required'] ?? 1) ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Manual Review</label>
                                        <select name="new_customer_manual_review" class="form-control">
                                            <option value="1" {{ ($settings['new_customer_manual_review'] ?? 0) ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ !($settings['new_customer_manual_review'] ?? 0) ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h4 class="card-title text-uppercase mb-3 mt-4">Checkout OTP Rules</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>OTP bypass after delivered orders >=</label>
                                        <input type="number" name="otp_bypass_min_delivered_orders" class="form-control" value="{{ $settings['otp_bypass_min_delivered_orders'] ?? 1 }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>OTP bypass if MabiY delivery success >= (%)</label>
                                        <input type="number" name="otp_bypass_mabiy_success_rate" class="form-control" value="{{ $settings['otp_bypass_mabiy_success_rate'] ?? 50 }}" min="0" max="100" step="0.1" required>
                                    </div>
                                </div>
                            </div>

                            <h4 class="card-title text-uppercase mb-3 mt-4">High Risk Action</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>When order is HIGH_RISK</label>
                                        <select name="high_risk_action" class="form-control">
                                            <option value="review" {{ ($settings['high_risk_action'] ?? 'review') === 'review' ? 'selected' : '' }}>Auto Review</option>
                                            <option value="block" {{ ($settings['high_risk_action'] ?? 'review') === 'block' ? 'selected' : '' }}>Auto Block</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h4 class="card-title text-uppercase mb-3 mt-4">Provider Unavailable</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>When courier API is unavailable</label>
                                        <select name="provider_unavailable_action" class="form-control">
                                            <option value="allow" {{ ($settings['provider_unavailable_action'] ?? 'allow') === 'allow' ? 'selected' : '' }}>Allow order</option>
                                            <option value="review" {{ ($settings['provider_unavailable_action'] ?? 'allow') === 'review' ? 'selected' : '' }}>Review order</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success" id="saveSettingsBtn">Save Settings</button>
                                    <span id="settingsMessage" class="ml-3"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('footer')
    <script type="text/javascript">
        $(document).on('submit', '#fraudSettingsForm', function(e) {
            e.preventDefault();
            var button = $('#saveSettingsBtn');
            button.prop('disabled', true).text('Saving...');
            $('#settingsMessage').text('');

            $.ajax({
                url: "{{ url('admin/fraud-security/settings') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status == 1) {
                        $('#settingsMessage').html('<span style="color: green;">' + response.message + '</span>');
                    } else {
                        $('#settingsMessage').html('<span style="color: red;">' + (response.message || response.error || 'Save failed.') + '</span>');
                    }
                },
                error: function(xhr) {
                    var message = xhr.responseJSON?.error || xhr.responseJSON?.message || 'Validation failed.';
                    $('#settingsMessage').html('<span style="color: red;">' + message + '</span>');
                },
                complete: function() {
                    button.prop('disabled', false).text('Save Settings');
                }
            });
        });
    </script>
@endpush
