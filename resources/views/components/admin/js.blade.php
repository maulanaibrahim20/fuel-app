<script src="{{ url('assets') }}/vendor/libs/jquery/jquery.js"></script>
<script src="{{ url('assets') }}/vendor/libs/popper/popper.js"></script>
<script src="{{ url('assets') }}/vendor/js/bootstrap.js"></script>
<script src="{{ url('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ url('assets') }}/vendor/libs/node-waves/node-waves.js"></script>

<script src="{{ url('assets') }}/vendor/libs/hammer/hammer.js"></script>
<script src="{{ url('assets') }}/vendor/libs/i18n/i18n.js"></script>
<script src="{{ url('assets') }}/vendor/libs/typeahead-js/typeahead.js"></script>

<script src="{{ url('assets') }}/vendor/js/menu.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ url('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="{{ url('assets') }}/vendor/libs/swiper/swiper.js"></script>
<script src="{{ url('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

<!-- Main JS -->
<script src="{{ url('assets') }}/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ url('/global') }}/global.js"></script>

<script>
    $(document).ajaxStart(function() {
        $("#global-loader").fadeIn(200);
    });

    $(document).ajaxStop(function() {
        $("#global-loader").fadeOut(200);
    });
</script>
