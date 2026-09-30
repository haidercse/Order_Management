<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title> @yield('title') </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('backend.layouts.partials.style')
</head>

<body>

    <div id="preloader">
        <div class="loader"></div>
    </div>


    <div class="login-area">
        <div class="container">
            @include('backend.layouts.partials.message')
            <div class="login-box ptb--100">
                <form id="loginForm" method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="login-form-head">
                        <img style="border-radius: 50%; object-fit: cover; height: 80px; width: 80px;"
                            src="{{ asset('admin/assets/images/icon/logo.png') }}" alt="logo">
                        <h4>Sign In</h4>
                        <p>Hello there, Sign in and Welcome to our Elite Express</p>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" name="email" id="exampleInputEmail1" required
                                value="{{ old('email') }}">
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                            <i class="ti-email"></i>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" name="password" id="exampleInputPassword1" required
                                value="{{ old('password') }}">
                            @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            @endif
                            <i class="ti-lock"></i>
                        </div>

                        {{-- <div class="row mb-4 rmber-area">
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" class="custom-control-input" id="customControlAutosizing"
                                        name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="customControlAutosizing">Remember
                                        Me</label>
                                </div>
                            </div>
                             <div class="col-6 text-right">
                                <a href="#">Forgot Password?</a>
                            </div> 
                        </div> --}}

                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Submit <i class="ti-arrow-right"></i></button>
                            <div class="login-other row mt-4">
                                <div class="col-6">
                                    <a class="fb-login" href="#">Log in with <i class="fa fa-facebook"></i></a>
                                </div>
                                <div class="col-6">
                                    <a class="google-login" href="#">Log in with <i class="fa fa-google"></i></a>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-footer text-center mt-5">
                            <p class="text-muted">Don't have an account? <a href="#">Sign up</a></p>
                        </div> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>


    @include('backend.layouts.partials.offset')

    <!-- jquery latest version -->

    @include('backend.layouts.partials.scripts')


</body>

</html>
