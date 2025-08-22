@extends('layouts.admin.main')
@section('content')
    <div class="container pd-x-0">
        <!-- Breadcrumb -->
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Edit Profile</h4>
            </div>
        </div>
        <div class="row row-xs">
            <!-- Profile Photo Card -->
            <div class="col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-header pd-t-20 pd-b-15">
                        <h6 class="mg-b-0">Profile Photo</h6>
                    </div>
                    <div class="card-body pd-20 text-center">
                        <div class="position-relative d-inline-block mg-b-20">
                            <div class="avatar avatar-xxl">
                                <img src="{{ Storage::url($user->avatar) ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                                    class="rounded-circle" alt="{{ Auth::user()->name }}"></a>
                            </div>
                            <div class="position-absolute" style="bottom: 0; right: 0;">
                                <label for="avatar" class="btn btn-sm btn-primary rounded-circle pd-2"
                                    style="width: 35px; height: 35px;">
                                    <i data-feather="camera" class="wd-15 ht-15"></i>
                                </label>
                            </div>
                        </div>

                        <h5 class="tx-medium mg-b-5">{{ $user->name }}</h5>
                        <p class="tx-color-03 tx-12 mg-b-15">{{ $user->email }}</p>

                        <div class="d-flex justify-content-center">
                            <div class="text-center mg-r-20">
                                {{-- <h6 class="tx-normal tx-rubik mg-b-0">{{ $user->vehicles()->count() }}</h6> --}}
                                <small class="tx-10 tx-uppercase tx-spacing-1 tx-color-03">Vehicles</small>
                            </div>
                            <div class="text-center mg-l-20">
                                {{-- <h6 class="tx-normal tx-rubik mg-b-0">{{ $user->fuelRecords()->count() }}</h6> --}}
                                <small class="tx-10 tx-uppercase tx-spacing-1 tx-color-03">Fuel Records</small>
                            </div>
                        </div>

                        <div class="alert alert-info mg-t-20 tx-12" role="alert">
                            <i data-feather="info" class="wd-12 mg-r-5"></i>
                            Upload JPG, JPEG, or PNG. Max size 2MB.
                        </div>
                    </div>
                </div>

                <!-- Account Status Card -->
                <div class="card mt-2">
                    <div class="card-header pd-t-20 pd-b-15">
                        <h6 class="mg-b-0">Account Status</h6>
                    </div>
                    <div class="card-body pd-20">
                        <div class="d-flex align-items-center mg-b-15">
                            <div
                                class="wd-40 ht-40 bg-success tx-white mg-r-10 d-flex align-items-center justify-content-center rounded">
                                <i data-feather="check" class="wd-20"></i>
                            </div>
                            <div>
                                <p class="tx-medium mg-b-0">Account Verified</p>
                                <small class="tx-color-03">Email verified successfully</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mg-b-15">
                            <div
                                class="wd-40 ht-40 bg-{{ $user->subscription_type === 'premium' ? 'warning' : 'secondary' }} tx-white mg-r-10 d-flex align-items-center justify-content-center rounded">
                                <i data-feather="{{ $user->subscription_type === 'premium' ? 'crown' : 'user' }}"
                                    class="wd-20"></i>
                            </div>
                            <div>
                                <p class="tx-medium mg-b-0 text-capitalize">{{ $user->subscription_type }} Account
                                </p>
                                <small class="tx-color-03">
                                    @if ($user->subscription_type === 'premium')
                                        Expires:
                                        {{ $user->subscription_expires_at ? $user->subscription_expires_at->format('M d, Y') : 'Never' }}
                                    @else
                                        <a href="#" class="link-03">Upgrade to Premium</a>
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div
                                class="wd-40 ht-40 bg-info tx-white mg-r-10 d-flex align-items-center justify-content-center rounded">
                                <i data-feather="clock" class="wd-20"></i>
                            </div>
                            <div>
                                <p class="tx-medium mg-b-0">Last Login</p>
                                <small
                                    class="tx-color-03">{{ $user->last_login_at ? $user->last_login_at : 'Never' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Form Card -->
            <div class="col-lg-8 col-xl-8 mg-t-10 mg-lg-t-0">
                <form id="form-ajax" action="{{ route('admin.profile.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Personal Information -->
                    <div class="card">
                        <div class="card-header pd-t-20 pd-b-15">
                            <h6 class="mg-b-0">Personal Information</h6>
                        </div>
                        <div class="card-body pd-20">
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="d-none"
                                onchange="previewImage(this)">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mg-b-20">
                                        <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Full
                                            Name <span class="tx-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter your full name" value="{{ old('name', $user->name) }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mg-b-20">
                                        <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Username
                                            <span class="tx-danger">*</span></label>
                                        <input type="text" name="username" class="form-control"
                                            placeholder="Enter your Username"
                                            value="{{ old('username', $user->username) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mg-b-20">
                                        <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Phone
                                            Number</label>
                                        <input type="tel" name="phone" class="form-control"
                                            placeholder="Enter your phone number"
                                            value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mg-b-20">
                                        <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Email
                                            Address <span class="tx-danger">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="Enter your email" value="{{ old('email', $user->email) }}"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="card mt-2">
                        <div class="card-header pd-t-20 pd-b-15">
                            <h6 class="mg-b-0">Security Settings</h6>
                        </div>
                        <div class="card-body pd-20">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mg-b-20">
                                        <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Current
                                            Password</label>
                                        <input type="password" name="current_password"
                                            class="form-control password-field" placeholder="Enter current password">
                                        <small class="form-text tx-color-03">Leave blank if you don't want to change
                                            password</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mg-b-20">
                                        <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">New
                                            Password</label>
                                        <input type="password" name="password" class="form-control password-field"
                                            placeholder="Enter new password">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mg-b-0">
                                        <label class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">Confirm
                                            New Password</label>
                                        <input type="password" name="password_confirmation"
                                            class="form-control password-field" placeholder="Confirm new password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mg-b-0">
                                        <label
                                            class="form-label tx-medium tx-12 tx-uppercase tx-sans tx-spacing-1">&nbsp;</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="showPassword"
                                                onchange="togglePassword()">
                                            <label class="custom-control-label" for="showPassword">Show passwords</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card mt-2">
                        <div class="card-body pd-20 text-right">
                            <button type="button" class="btn btn-white btn-uppercase pd-x-25"
                                onclick="window.history.back()">
                                <i data-feather="x" class="wd-10 mg-r-5"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-secondary btn-uppercase pd-x-25 mg-l-10"
                                onclick="resetForm()">
                                <i data-feather="refresh-cw" class="wd-10 mg-r-5"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary btn-uppercase pd-x-25 mg-l-10">
                                <i data-feather="save" class="wd-10 mg-r-5"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        // Preview uploaded image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePreview');
                    if (preview) {
                        preview.src = e.target.result;
                    } else {
                        // If no img element exists, replace the avatar span with img
                        const avatarContainer = document.querySelector('.avatar-xxl');
                        avatarContainer.innerHTML =
                            `<img src="${e.target.result}" class="rounded-circle" alt="Profile Photo" id="profilePreview">`;
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Toggle password visibility
        function togglePassword() {
            const checkbox = document.getElementById('showPassword');
            const passwordInputs = document.querySelectorAll('.password-field');

            passwordInputs.forEach(input => {
                input.type = checkbox.checked ? 'text' : 'password';
            });
        }

        // Reset form to original values
        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                document.getElementById('profileForm').reset();
                // Reset image preview
                location.reload();
            }
        }

        // Form submission with loading state
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i data-feather="loader" class="wd-10 mg-r-5"></i> Saving...';
            submitBtn.disabled = true;
        });

        // Initialize Feather Icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>
@endpush
