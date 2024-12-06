$(document).ready(function () {
    $('.form-multi-select').select2({
        placeholder: '-- Không --',
        allowClear: true
    });

    $('#saveTabMenuChanges').on('click', function () {
        let parentId = $('#parentId').val();
        let menuName = $('#name').val().trim();
        let icon = $('#icon-selected-value').val();
        let selectedPages = $('#pages').val();

        if (!menuName) {
            $('#name-error').show().text('Tên menu không được để trống.');
            return;
        } else {
            $('#name-error').hide();
        }

        let specialCharRegex = /[^a-zA-ZÀ-ỹà-ỹ0-9_ ]/;
        if (specialCharRegex.test(menuName)) {
            $('#name-error').show().text('Tên menu không được chứa ký tự đặc biệt.');
            return;
        }

        if (menuName.length < 3 || menuName.length > 30) {
            $('#name-error').show().text('Tên menu phải có từ 3 đến 50 ký tự.');
            return;
        }

        if (!icon) {
            swal({
                title: "Không hợp lệ!",
                text: "Vui lòng chọn một icon.",
                icon: "error",
            });
            return;
        }

        $.ajax({
            url: store_menu_url,
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                name: menuName,
                icon,
                parentId,
                selectedPages,
            },
            success: function (response) {
                if (response.success) {
                    swal({
                        title: "Thành công!",
                        text: response.message || "Dữ liệu đã được cập nhật.",
                        icon: "success",
                        buttons: {
                            confirm: {
                                text: "Xem danh sách",
                                value: true,
                                visible: true,
                                className: "swal-button--defeat"
                            },
                            cancel: {
                                text: "Tiếp tục thêm",
                                value: null,
                                visible: true,
                                className: "swal-button--cancel",
                                closeModal: false
                            },
                        }
                    }).then((willViewList) => {
                        if (willViewList) {
                            window.location.href = list_menu_url;
                        } else {
                            location.reload();
                        }
                    });

                } else {
                    swal({
                        title: "Thất bại!",
                        text: response.message || "Có lỗi xảy ra, vui lòng thử lại.",
                        icon: "error",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('Lỗi AJAX: ', error);
                swal({
                    title: "Lỗi hệ thống!",
                    text: "Không thể thực hiện yêu cầu, vui lòng thử lại.",
                    icon: "error",
                });
            },
        });
    });

});
