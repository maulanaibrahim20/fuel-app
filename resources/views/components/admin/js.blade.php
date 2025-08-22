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

<script>
    $(document).ready(function() {
        $("table.datatable").each(function() {
            let $table = $(this);

            // Ambil atribut dari data-*
            let ajaxUrl = $table.data("ajax") || null;
            let serverSide = $table.data("server-side") || true;
            let processing = $table.data("processing") || true;
            let ordering = $table.data("ordering") || true;
            let lengthMenu = $table.data("length-menu") || [10, 25, 50, 100];
            let stateSave = $table.data("state-save") || true;

            // Ambil definisi kolom dari <th>
            let columns = [];
            $table.find("thead th").each(function() {
                columns.push({
                    data: $(this).attr("data"),
                    orderable: $(this).attr("orderable") !== "false",
                    searchable: $(this).attr("searchable") !== "false",
                    className: $(this).attr("class-name") || ""
                });
            });

            // Inisialisasi DataTable
            $table.DataTable({
                processing: processing,
                serverSide: serverSide,
                ajax: ajaxUrl,
                ordering: ordering,
                lengthMenu: eval(lengthMenu),
                stateSave: stateSave,
                columns: columns,
                dom: "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",

                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                    lengthMenu: "_MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "No entries available",
                    zeroRecords: "No matching records found",
                },
                responsive: true
            });
        });
    });
</script>
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
