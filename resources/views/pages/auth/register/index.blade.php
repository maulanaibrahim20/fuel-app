@extends('layouts.authentication.main')
@section('content')
    <div class="container">
        <div class="media align-items-stretch justify-content-center ht-100p">
            <div class="sign-wrapper mg-lg-r-50 mg-xl-r-60">
                <div class="pd-t-20 wd-100p">
                    <h4 class="tx-color-01 mg-b-5">Create New Account</h4>
                    <p class="tx-color-03 tx-16 mg-b-40">It's free to signup and only takes a minute.</p>

                    <!-- FORM AJAX -->
                    <form id="form-ajax" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Name">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Enter Username">
                        </div>
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" name="email" class="form-control"
                                placeholder="Enter your email address">
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between mg-b-5">
                                <label class="mg-b-0-f">Password</label>
                            </div>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password">
                        </div>
                        <button type="submit" class="btn btn-brand-02 w-100">Create Account</button>
                    </form>

                    <div class="divider-text">or</div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-facebook btn-block">Sign Up With Facebook</button>
                        <button class="btn btn-outline-twitter btn-block">Sign Up With Twitter</button>
                    </div>
                    <div class="tx-13 mg-t-20 tx-center">
                        Already have an account? <a href="{{ route('login') }}">Sign In</a>
                    </div>
                </div>
            </div>
            <div class="media-body pd-y-30 pd-lg-x-50 pd-xl-x-60 align-items-center d-none d-lg-flex pos-relative">
                <div class="mx-lg-wd-500 mx-xl-wd-550">
                    <img src="{{ url('/assets') }}/img/register.jpg" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection
