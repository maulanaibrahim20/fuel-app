<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Twitter -->
    <meta name="twitter:site" content="@themepixels">
    <meta name="twitter:creator" content="@themepixels">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="DashForge">
    <meta name="twitter:description" content="Responsive Bootstrap 5 Dashboard Template">
    <meta name="twitter:image" content="http://themepixels.me/dashforge/img/dashforge-social.png">

    <!-- Facebook -->
    <meta property="og:url" content="http://themepixels.me/dashforge">
    <meta property="og:title" content="DashForge">
    <meta property="og:description" content="Responsive Bootstrap 5 Dashboard Template">

    <meta property="og:image" content="http://themepixels.me/dashforge/img/dashforge-social.png">
    <meta property="og:image:secure_url" content="http://themepixels.me/dashforge/img/dashforge-social.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">

    <!-- Meta -->
    <meta name="description" content="Responsive Bootstrap 5 Dashboard Template">
    <meta name="author" content="ThemePixels">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('/assets') }}/img/favicon.png">

    <title>{{ config('app.name') }}</title>

    @include('components.admin.css')
    @stack('css')
</head>

<body>
    <div id="global-loader"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(255,255,255,0.7); z-index:9999;
            display:flex; justify-content:center; align-items:center;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    @include('layouts.admin.sidebar')

    <div class="content ht-100v pd-0">
        <div class="content-header">
            <div class="content-search">
                <i data-feather="search"></i>
                <input type="search" class="form-control" placeholder="Search...">
            </div>
            <nav class="nav">
                <a href="" class="nav-link"><i data-feather="help-circle"></i></a>
                <a href="" class="nav-link"><i data-feather="grid"></i></a>
                <a href="" class="nav-link"><i data-feather="align-left"></i></a>
            </nav>
        </div>

        <div class="content-body">
            @yield('content')
        </div>
    </div>

    @include('components.admin.js')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ajaxStart(function() {
            $("#global-loader").fadeIn(200);
        });

        $(document).ajaxStop(function() {
            $("#global-loader").fadeOut(200);
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on("submit", "#form-ajax", function(e) {
                e.preventDefault();

                let $form = $(this);
                let action = $form.attr("action");
                let method = $form.attr("method") || "POST";
                let formData = new FormData(this);

                // cari tombol submit di dalam form
                let $btn = $form.find("#btn-ajax");
                let btnText = $btn.html(); // simpan text asli (pakai html biar support icon juga)

                // ubah state jadi loading
                $btn.prop("disabled", true).html(
                    `<span class="spinner-border spinner-border-sm"></span> Loading...`);

                $.ajax({
                    url: action,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.redirect) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil",
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = res.redirect;
                            });
                            return;
                        }

                        if (res.code === 200 || res.code === 201) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil",
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal",
                                text: res.message || "Proses gagal"
                            });
                        }
                    },
                    error: function(xhr) {
                        let res = xhr.responseJSON;
                        let msg = "Terjadi kesalahan.";

                        if (res) {
                            if (res.code === 422 && res.data) {
                                let errorMessages = [];
                                Object.keys(res.data).forEach(function(key) {
                                    errorMessages.push(res.data[key][0]);
                                });
                                msg = res.message + "\n" + errorMessages.join("\n");
                            } else if (res.message) {
                                msg = res.message;
                            }
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: msg
                        });
                    },
                    complete: function() {
                        $btn.prop("disabled", false).html(btnText);
                    },
                });
            });
        });

        $(document).on("click", "#btn-logout", function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Yakin ingin logout?",
                text: "Anda akan keluar dari aplikasi.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, logout",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: "Logout berhasil",
                        text: "Anda akan diarahkan ke halaman login....",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $("#form-logout").submit();
                    });
                }
            });
        });
    </script>
    @stack('scripts')

</body>

</html>
