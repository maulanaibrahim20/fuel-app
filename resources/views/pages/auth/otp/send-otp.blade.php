@extends('layouts.authentication.main')
@section('content')
    <div class="authentication-wrapper authentication-basic px-4">
        <div class="authentication-inner py-4">
            <!-- Two Steps Verification -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 mt-2">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <!-- Bisa ganti dengan logo perusahaan -->
                                <svg width="32" height="22" viewBox="0 0 32 22" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0 0V6.8C0 6.8 -0.1 9 2 10.8L13.7 22h6.1l-1-12.1-2.3-2.7L9.2 0H0Z"
                                        fill="#7367F0" />
                                    <path opacity="0.06" d="M7.7 16.4 12.5 3.2l4 4-8.8 9.2Z" fill="#161616" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.8 16.4 23.7 0H32v6.9c0 0-0.2 2.3-1.3 3.5L19.8 22h-6.1l-5.9-5.6Z"
                                        fill="#7367F0" />
                                </svg>
                            </span>
                            <span class="app-brand-text demo text-body fw-bold ms-1">{{ config('app.name') }}</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <h4 class="mb-1 pt-2 text-center">Two-Factor Verification 💬</h4>
                    <p class="text-center text-muted mb-4">
                        Select the method for receiving your security code (OTP).
                    </p>

                    <form id="form-ajax" action="{{ route('send-otp') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="method" id="methodWhatsapp"
                                    value="phone" checked>
                                <label class="form-check-label" for="methodWhatsapp">
                                    Send to WhatsApp Number <br>
                                    <small class="text-muted">{{ $maskedPhone ?? '+62******1234' }}</small>
                                </label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="method" id="methodEmail"
                                    value="email">
                                <label class="form-check-label" for="methodEmail">
                                    Send to Email <br>
                                    <small class="text-muted">{{ $maskedEmail ?? 'e***@example.com' }}</small>
                                </label>
                            </div>
                        </div>

                        <button id="btn-ajax" type="submit" class="btn btn-primary d-grid w-100 mb-3">
                            Send OTP Code
                        </button>
                    </form>
                </div>
            </div>
            <!-- / Two Steps Verification -->
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ url('/assets') }}/js/pages-auth-two-steps.js"></script>
@endpush
