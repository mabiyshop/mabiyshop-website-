@include('auth.admin.header')
            <div class="col-lg-4 mx-auto">
              <h4 class="text-center p-2 text-white">
                <img src="{{ asset('uploads/images/backend-logo.png') }}" width="120px" alt="Admin Logo">
              </h4>
              <div class="auto-form-wrapper">
                @if(session()->has('failed'))
                    <p class="text-danger">{!! session()->get('failed') !!}</p>
                @endif
                @if(session('success'))
                    <div class="row">
                      <span class="alert alert-success col-md-12" role="alert">
                        <strong class="text-">{{ session('success') }}</strong>
                      </span>
                    </div>
                @endif
                <form action="{{ route('admin.login') }}" method="post">
					       @csrf
                  <div class="form-group">
                    <label class="label">Email / Mobile Number</label>
                    <div class="input-group">
                       <input id="email" type="text" placeholder="Enter email or mobile number" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                        @enderror
						
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="label">Password</label>
                    <div class="input-group">
                      <input id="password" placeholder="Enter password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
							@error('password')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror

                    </div>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary submit-btn btn-block">Login</button>
                  </div>
                  <div class="text-block text-center my-3">
                    <a href="{{ route('admin.password.request')}}" class=" text-small"> Forgot Password ?</a>
                  </div>
                </form>
              </div>
           
            </div>

@include('auth.admin.footer')
