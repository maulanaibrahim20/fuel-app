$(document).ready(function () {
    $("table.datatable").each(function () {
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
        $table.find("thead th").each(function () {
            columns.push({
                data: $(this).attr("data"),
                orderable: $(this).attr("orderable") !== "false",
                searchable: $(this).attr("searchable") !== "false",
                className: $(this).attr("class-name") || "",
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
            dom:
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6'f>>" +
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
            responsive: true,
        });
    });
    $(document).on("submit", "#form-ajax", function (e) {
        e.preventDefault();

        let $form = $(this);
        let action = $form.attr("action");
        let method = $form.attr("method") || "POST";
        let formData = new FormData(this);
        let isModalContext = $form.closest("#globalModal").length > 0; // Cek apakah form dalam modal

        // cari tombol submit di dalam form
        let $btn = $form.find("#btn-ajax");
        let btnText = $btn.html(); // simpan text asli (pakai html biar support icon juga)

        // ubah state jadi loading
        $btn.prop("disabled", true).html(
            `<span class="spinner-border spinner-border-sm"></span> Loading...`
        );

        $.ajax({
            url: action,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (isModalContext) {
                    // Handle success untuk form dalam modal
                    if (res.redirect) {
                        $("#globalModal").modal("hide");
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = res.redirect;
                        });
                        return;
                    }

                    if (res.code === 200 || res.code === 201) {
                        $("#globalModal").modal("hide");
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.reload();
                        });
                        return;
                    }

                    // Jika ada HTML baru (biasanya untuk validation error)
                    if (res.html) {
                        $("#globalModalBody").html(res.html);
                        return;
                    }
                } else {
                    // Handle success untuk form biasa (di luar modal)
                    if (res.redirect) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false,
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
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: res.message || "Proses gagal",
                        });
                    }
                }
            },
            error: function (xhr) {
                let res = xhr.responseJSON;
                let msg = "Terjadi kesalahan.";

                if (res) {
                    if (res.code === 422 && res.data) {
                        if (isModalContext && res.html) {
                            // Update modal content dengan error validation
                            $("#globalModalBody").html(res.html);
                            return; // Jangan tampilkan SweetAlert
                        } else {
                            let errorMessages = [];
                            Object.keys(res.data).forEach(function (key) {
                                errorMessages.push(res.data[key][0]);
                            });
                            msg = res.message + "\n" + errorMessages.join("\n");
                        }
                    } else if (res.message) {
                        msg = res.message;
                    }
                }

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: msg,
                });
            },
            complete: function () {
                $btn.prop("disabled", false).html(btnText);
            },
        });
    });

    // Logout handler (tetap sama)
    $(document).on("click", "#btn-logout", function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Yakin ingin logout?",
            text: "Anda akan keluar dari aplikasi.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, logout",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: "success",
                    title: "Logout berhasil",
                    text: "Anda akan diarahkan ke halaman login....",
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => {
                    $("#form-logout").submit();
                });
            }
        });
    });
    // Buat modal global di body jika belum ada
    if (!$("#globalModal").length) {
        $("body").append(`
            <div class="modal fade" id="globalModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="globalModalTitle">Modal Title</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="globalModalBody">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    // Function untuk membuka modal dengan konten dari route
    window.openGlobalModal = function (options) {
        const defaults = {
            url: null,
            title: "Modal",
            size: "", // '', 'modal-sm', 'modal-lg', 'modal-xl'
            footerButtons: [
                {
                    text: "Close",
                    class: "btn btn-label-secondary",
                    dismiss: true,
                },
            ],
            onSuccess: null,
            onError: null,
            onComplete: null,
        };

        const config = $.extend({}, defaults, options);

        if (!config.url) {
            console.error("URL is required for global modal");
            return;
        }

        // Set modal size
        const modalDialog = $("#globalModal .modal-dialog");
        modalDialog.removeClass("modal-sm modal-lg modal-xl");
        if (config.size) {
            modalDialog.addClass(config.size);
        }

        // Set title
        $("#globalModalTitle").text(config.title);

        // Show loading state
        $("#globalModalBody").html(`
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        // Set footer buttons
        let footerHtml = "";
        config.footerButtons.forEach((button) => {
            const dismissAttr = button.dismiss ? 'data-bs-dismiss="modal"' : "";
            const onClickAttr = button.onclick
                ? `onclick="${button.onclick}"`
                : "";
            footerHtml += `<button type="button" class="${button.class}" ${dismissAttr} ${onClickAttr}>${button.text}</button>`;
        });
        $("#globalModalFooter").html(footerHtml);

        // Show modal
        $("#globalModal").modal("show");

        // Load content
        $.ajax({
            url: config.url,
            type: "GET",
            success: function (response) {
                if (typeof response === "string") {
                    $("#globalModalBody").html(response);
                } else if (response.html) {
                    $("#globalModalBody").html(response.html);
                } else if (response.content) {
                    $("#globalModalBody").html(response.content);
                } else {
                    $("#globalModalBody").html(response);
                }

                // Update title jika ada di response
                if (response.title) {
                    $("#globalModalTitle").text(response.title);
                }

                // Update footer jika ada di response
                if (response.footer) {
                    $("#globalModalFooter").html(response.footer);
                }

                // Callback success
                if (
                    config.onSuccess &&
                    typeof config.onSuccess === "function"
                ) {
                    config.onSuccess(response);
                }
            },
            error: function (xhr) {
                let errorMsg = "Terjadi kesalahan saat memuat konten modal.";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                $("#globalModalBody").html(`
                    <div class="alert alert-danger" role="alert">
                        <i class="bx bx-error-circle me-2"></i>
                        ${errorMsg}
                    </div>
                `);

                // Callback error
                if (config.onError && typeof config.onError === "function") {
                    config.onError(xhr);
                }
            },
            complete: function () {
                // Callback complete
                if (
                    config.onComplete &&
                    typeof config.onComplete === "function"
                ) {
                    config.onComplete();
                }
            },
        });
    };

    // Event listener untuk button dengan data attribute
    $(document).on("click", "[data-modal-url]", function (e) {
        e.preventDefault();

        const $btn = $(this);
        const url = $btn.data("modal-url");
        const title = $btn.data("modal-title") || "Modal";
        const size = $btn.data("modal-size") || "";

        openGlobalModal({
            url: url,
            title: title,
            size: size,
        });
    });

    // Extend Form AJAX Global untuk mendukung modal context
    const originalFormAjaxHandler = $(document).find("#form-ajax").length > 0;

    // Override success handler untuk form dalam modal
    $(document).on(
        "ajaxSuccess",
        "#globalModal #form-ajax",
        function (event, xhr, settings) {
            const res = xhr.responseJSON;

            if (res) {
                if (res.redirect) {
                    $("#globalModal").modal("hide");
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                    return;
                }

                if (res.code === 200 || res.code === 201) {
                    $("#globalModal").modal("hide");
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.reload();
                    });
                    return;
                }

                // Update modal content jika ada HTML dari server (untuk validation error)
                if (res.html) {
                    $("#globalModalBody").html(res.html);
                    return;
                }
            }
        }
    );

    // Override error handler untuk form dalam modal
    $(document).on(
        "ajaxError",
        "#globalModal #form-ajax",
        function (event, xhr, settings) {
            const res = xhr.responseJSON;

            if (res && res.code === 422 && res.html) {
                // Update modal content dengan error validation
                $("#globalModalBody").html(res.html);
                event.preventDefault(); // Prevent default error handling
                return false;
            }
        }
    );

    // Alternative: Custom form handler khusus untuk modal (jika method di atas tidak work)
    $(document).on("submit", "#globalModal #form-ajax", function (e) {
        const $form = $(this);

        // Set flag untuk menandai ini adalah form dalam modal
        $form.attr("data-modal-context", "true");

        // Biarkan form AJAX global handle sisanya, tapi dengan context modal
        // Form akan dihandle oleh script global Anda yang sudah ada
    });

    // Hook ke dalam AJAX global success handler untuk modal context
    const originalAjaxStart = $.fn.ajaxStart;
    const originalAjaxStop = $.fn.ajaxStop;

    // Monitor AJAX untuk form dalam modal
    $("#globalModal").on("ajaxStart", function () {
        // Optional: bisa ditambahkan loading state khusus untuk modal
    });

    $("#globalModal").on("ajaxStop", function () {
        // Optional: handle setelah AJAX selesai dalam modal
    });

    // Reset modal saat ditutup
    $("#globalModal").on("hidden.bs.modal", function () {
        $(this).find(".modal-dialog").removeClass("modal-sm modal-lg modal-xl");
        $("#globalModalTitle").text("Modal");
        $("#globalModalBody").empty();
        $("#globalModalFooter").html(
            '<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>'
        );
    });
});

// Helper functions
window.closeGlobalModal = function () {
    $("#globalModal").modal("hide");
};

window.updateModalContent = function (content) {
    $("#globalModalBody").html(content);
};

window.updateModalTitle = function (title) {
    $("#globalModalTitle").text(title);
};

window.updateModalFooter = function (footer) {
    $("#globalModalFooter").html(footer);
};
