$(document).ready(function () {
    $('.edit-btn').on('click', function () {
        var pageId = $(this).data('page-id');
        var pageName = $(this).data('page-name');
        var pageType = $(this).data('page-type');
        var status = $(this).data('status');

        $('#editpageName').val(pageName);
        $('#editTabType').val(pageType);
        $('#editMenu').val(menuId);
        $('#editStatus').val(status);
        $('#editTabForm').data('page-id', pageId);
    });
    $('#savePageChanges').on('click', function () {
        var form = $('#editTabForm');
        var pageId = form.data('page-id');
        var status = $('#editStatus').val();

        $.ajax({
            url: update_page_url,
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                pageId: pageId,
                menuId: menuId,
                status: status,
            },
            success: function (response) {
                $('#editModal').modal('hide');
                location.reload();
            },
            error: function () {
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            }
        });
    });
    $('.setting-btn').on('click', function () {
        var pageId = $(this).data('page-id');

        $.ajax({
            url: get_table_page_url,
            method: 'GET',
            data: { pageId: pageId },
            success: function (response) {
                var data = JSON.parse(response);
                var columns = data.columns;
                var hiddenColumns = data.hiddenColumns;

                var columnsList = $('#columns-visibility tbody');
                columnsList.empty();

                columns.forEach(function (column) {
                    var isChecked = hiddenColumns[column] === undefined || hiddenColumns[column] ? 'checked' : '';

                    var columnRow = `
                        <tr>
                            <td>${column}</td>
                            <td class="text-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input column-switch" type="checkbox" 
                                           id="switch-${column}" data-column="${column}" ${isChecked}>
                                </div>
                            </td>
                        </tr>
                    `;

                    columnsList.append(columnRow);
                });

                $('#settingModal').modal('show');
            },
            error: function () {
                alert('Lỗi khi tải dữ liệu cột');
            }
        });
    });

    $('.column-switch').on('change', function () {
        var column = $(this).data('column');
        var isChecked = $(this).prop('checked');

        // Gửi dữ liệu lên server để cập nhật
        $.ajax({
            url: '/path/to/your/actionSaveColumnVisibility', // Đường dẫn đến action cập nhật trạng thái cột
            method: 'POST',
            data: {
                column: column,
                visible: isChecked ? 1 : 0
            },
            success: function (response) {
                if (response.success) {
                    alert('Cập nhật thành công!');
                } else {
                    alert('Có lỗi xảy ra!');
                }
            },
            error: function () {
                alert('Lỗi khi gửi yêu cầu');
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
    $('#toggleStatusTabs').on('change', function () {
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

    $('#confirm-delete-btn').on('click', function () {
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

    $('#confirm-delete-permanently-btn').on('click', function () {
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