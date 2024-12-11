$(document).ready(function () {
    $(document).off('click', '#edit-richtext').on('click', '#edit-richtext', function () {
        var pageId = $(this).data('id');
        var baseUrl = window.location.origin + '/admin/';
        window.location.href = baseUrl + 'pages/edit?id=' + pageId;
    });


    $(document).off('click', '.edit-btn').on('click', '.edit-btn', function () {
        var pageId = $(this).data('page-id');
        var pageName = $(this).data('page-name');
        var status = $(this).data('page-status');
        console.log("🚀 ~ status:", status);

        $('#editName').val(pageName);
        $('#editStatus').val(status);
        $('#editPageForm').data('page-id', pageId);
        $('#editModal').modal('show');
    });
    $(document).off('click', '#savePageChanges').on('click', '#savePageChanges', function () {
        var form = $('#editPageForm');
        var pageId = form.data('page-id');
        var pageName = $('#editName').val();
        var status = $('#editStatus').val();
        var isValid = true;

        $('#editNameError').hide();

        if (!pageName) {
            $('#editNameError').text('Tên page không được để trống.').show();
            isValid = false;
        } else if (/[^a-zA-ZÀ-ỹà-ỹ0-9_ ]/g.test(pageName)) {
            $('#editNameError').text('Tên page không chứa ký tự đặc biệt.').show();
            isValid = false;
        } else if (pageName.length > 30) {
            $('#editNameError').text('Tên page không được quá 30 ký tự.').show();
            isValid = false;
        }

        if (isValid) {
            $.ajax({
                url: update_page_url,
                type: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId,
                    status,
                    pageName,
                },
                success: function (response) {
                    $('#editModal').modal('hide');
                    location.reload();
                },
                error: function () {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            });
        }
    });

    $(document).off('click', '.setting-btn').on('click', '.setting-btn', function () {
        var pageId = $(this).data('page-id');
        var button = $(this);
        button.prop('disabled', true);

        $.ajax({
            url: get_table_page_url,
            method: 'GET',
            data: { pageId: pageId },
            success: function (response) {
                var columns = response.columns;
                var hiddenColumns = response.hiddenColumns;

                var columnsList = $('#columns-visibility tbody tr td .list-group');
                columnsList.empty();

                columns.forEach(function (column) {
                    var isChecked = hiddenColumns[column] === undefined || hiddenColumns[column] ? 'checked' : '';

                    var columnRow = `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${column}</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input column-switch"
                                    type="checkbox"
                                    id="switch-${column}"
                                    data-column="${column}" ${isChecked}>
                            </div>
                        </div>
                    `;
                    columnsList.append(columnRow);
                });

                $('#save-columns-visible').data('page-id', pageId);

                $('#settingModal').modal('show');
            },
            error: function () {
                alert('Lỗi khi tải dữ liệu cột');
            },
            complete: function () {
                button.prop('disabled', false);
            }
        });
    });

    $(document).off('click', '#save-columns-visible').on('click', '#save-columns-visible', function () {
        var pageId = $(this).data('page-id');
        if (!pageId) {
            alert('Page ID không hợp lệ.');
            return;
        }

        let columnsVisibility = [];

        $('#columns-visibility .column-switch').each(function (index) {
            const columnName = $(this).data('column');
            const isChecked = $(this).prop('checked');

            columnsVisibility.push({
                column_name: columnName,
                is_visible: isChecked
            });
        });

        $.ajax({
            url: save_column_visibility_url,
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                pageId: pageId,
                columns_visibility: columnsVisibility
            },
            success: function (response) {
                if (response.success) {
                    showToast('Cập nhật thành công');
                    $('#settingModal').modal('hide');
                } else {
                    showToast('Lỗi cập nhật: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                showToast('Lỗi AJAX: ' + error);
            }
        });
    });


    $('#confirm-hide-btn').click(function () {
        let hideStatus = {};

        $('.toggle-hide-btn').each(function () {
            const pageId = $(this).data('page-id');
            const isChecked = $(this).is(':checked');
            hideStatus[pageId] = isChecked ? 0 : 1;
        });

        if (confirm("Xác nhận thao tác?")) {

            $.ajax({
                url: update_status_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    hideStatus: hideStatus
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || "Có lỗi xảy ra khi lưu thay đổi.");
                    }
                },
                error: function () {
                    alert("Có lỗi xảy ra khi lưu thay đổi.");
                }
            });
        }
    });
    $('#toggleStatusPages').on('change', function () {
        const showAll = $(this).is(':checked');

        $('.page-item').each(function () {
            const isStatus = $(this).data('status') == 1;
            if (isStatus) {
                $(this).toggleClass('hidden-page', !showAll);
            }
        });
    });

    $(document).on('click', '.restore-page-btn', function () {
        const pageId = $(this).data('page-id');

        if (confirm("Bạn có chắc chắn muốn khôi phục page này không?")) {
            $.ajax({
                url: restore_page_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId: pageId,
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                        $('#trashBinModal').modal('hide');
                    } else {
                        alert(response.message || "Khôi phục thất bại.");
                    }
                },
                error: function () {
                    alert("Có lỗi xảy ra khi khôi phục.");
                }
            });
        }
    });

    $(document).on('click', '.delete-page-btn', function () {
        const pageId = $(this).data('page-id');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn page này không?")) {
            $.ajax({
                url: delete_permanently_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId: pageId,
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || "Xóa thất bại.");
                    }
                },
                error: function () {
                    alert("Có lỗi xảy ra khi xóa page.");
                }
            });
        }
    });

    $(document).off('click', '#confirm-delete-btn').on('click', '#confirm-delete-btn', function () {
        const pageId = $(this).data('page-id');

        $.ajax({
            url: delete_soft_url,
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                pageId: pageId,
            },
            success: function (response) {
                if (response.success) {
                    location.reload();
                    $('#deleteModal').modal('hide');
                } else {
                    alert(response.message || "Xóa page thất bại.");
                }
            },
            error: function () {
                alert("Có lỗi xảy ra khi xóa page.");
            }
        });
    });
    $(document).off('click', '#confirm-delete-permanently-btn').on('click', '#confirm-delete-permanently-btn', function () {
        const pageId = $(this).data('page-id');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn không?")) {
            $.ajax({
                url: delete_permanently_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId: pageId,
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                        $('#deleteModal').modal('hide');
                    } else {
                        alert(response.message || "Xóa page thất bại.");
                    }
                },
                error: function () {
                    alert("Có lỗi xảy ra khi xóa page.");
                }
            });
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const confirmDeleteBtn = document.getElementById("confirm-delete-btn");
    const confirmDeletePermanentlyBtn = document.getElementById("confirm-delete-permanently-btn");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function () {
            const pageId = this.getAttribute("data-page-id");
            confirmDeleteBtn.setAttribute("data-page-id", pageId);
            confirmDeletePermanentlyBtn.setAttribute("data-page-id", pageId);
        });
    });
});