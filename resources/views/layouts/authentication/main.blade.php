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

    <title>DashForge Responsive Bootstrap 5 Dashboard Template</title>

    <!-- vendor css -->
    <link href="{{ url('/assets') }}/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="{{ url('/assets') }}/lib/remixicon/fonts/remixicon.css" rel="stylesheet">

    <!-- DashForge CSS -->
    <link rel="stylesheet" href="{{ url('/assets') }}/css/dashforge.css">
    <link rel="stylesheet" href="{{ url('/assets') }}/css/dashforge.auth.css">
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
    <div class="content content-fixed content-auth">
        @yield('content')
    </div>

    <script src="{{ url('/assets') }}/lib/jquery/jquery.min.js"></script>
    <script src="{{ url('/assets') }}/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('/assets') }}/lib/feather-icons/feather.min.js"></script>
    <script src="{{ url('/assets') }}/lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>

    <script src="{{ url('/assets') }}/js/dashforge.js"></script>

    <!-- append theme customizer -->
    <script src="{{ url('/assets') }}/lib/js-cookie/js.cookie.js"></script>
    <script src="{{ url('/assets') }}/js/dashforge.settings.js"></script>
    <script>
        $(function() {
            'use script'

            window.darkMode = function() {
                $('.btn-white').addClass('btn-dark').removeClass('btn-white');
            }

            window.lightMode = function() {
                $('.btn-dark').addClass('btn-white').removeClass('btn-dark');
            }

            var hasMode = Cookies.get('df-mode');
            if (hasMode === 'dark') {
                darkMode();
            } else {
                lightMode();
            }
        })
    </script>
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
                        // kembalikan tombol ke semula
                        $btn.prop("disabled", false).html(btnText);
                    },
                });
            });
        });
    </script>
</body>

</html>
