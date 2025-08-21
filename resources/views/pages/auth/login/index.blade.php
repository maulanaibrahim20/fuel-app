@extends('layouts.authentication.main')
@section('content')
    <div class="container">
        <div class="media align-items-stretch justify-content-center ht-100p pos-relative">
            <div class="media-body align-items-center d-none d-lg-flex">
                <div class="mx-wd-600">
                    <img src="{{ url('/assets') }}/img/login.jpg" class="img-fluid" alt="">
                </div>
            </div><!-- media-body -->
            <div class="sign-wrapper mg-lg-l-50 mg-xl-l-60">
                <div class="wd-100p">
                    <h3 class="tx-color-01 mg-b-5">Sign In</h3>
                    <p class="tx-color-03 tx-16 mg-b-40">Welcome back! Please signin to continue.</p>
                    <form id="form-ajax" action="{{ route('login') }}" method="POST" class="form-signin">
                        @csrf
                        <div class="form-group">
                            <label>Email address Or Username</label>
                            <input type="text" name="login" class="form-control" value="superadmin"
                                placeholder="yourname@yourmail.com">
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between mg-b-5">
                                <label class="mg-b-0-f">Password</label>
                                <a href="" class="tx-13">Forgot password?</a>
                            </div>
                            <input type="password" name="password" value="password" class="form-control"
                                placeholder="Enter your password">
                        </div>
                        <button type="submit" id="btn-ajax" class="btn btn-brand-02 w-100">Sign In</button>
                        <div class="divider-text">or</div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-facebook btn-block">Sign In With Facebook</button>
                            <button class="btn btn-outline-twitter btn-block">Sign In With Twitter</button>
                        </div>
                        <div class="tx-13 mg-t-20 tx-center">Don't have an account? <a href="{{ route('register') }}">Create
                                an Account</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
