<!DOCTYPE html>

<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ url('/assets') }}/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ config('app.name') }}</title>

    <meta name="description" content="" />

    @include('components.admin.css')

    <link rel="stylesheet" href="{{ url('/assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />

    <!-- Page -->
    <link rel="stylesheet" href="{{ url('/assets') }}/vendor/css/pages/page-auth.css" />

    @stack('styles')
</head>

<body>
    <!-- Content -->
    @yield('content')
    <!-- / Content -->

    @include('components.admin.js')

    <!-- Vendors JS -->
    <script src="{{ url('/assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ url('/assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ url('/assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>

    <!-- Page JS -->
    <script src="{{ url('/assets') }}/js/pages-auth.js"></script>
    @stack('scripts')
</body>

</html>
