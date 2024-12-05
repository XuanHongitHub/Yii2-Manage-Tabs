$(document).ready(function() {
    $(document).ready(function() {
        // Khi nhấn vào nút sửa
        $('.edit-btn').on('click', function() {
            var pageId = $(this).data('page-id');
            var tableName = $(this).data('page-name');
            var pageType = $(this).data('page-type');
            var menuId = $(this).data('menu-id');
            var status = $(this).data('status');
            var position = $(this).data('position');
    
            $('#edittableName').val(tableName);
            $('#editTabType').val(pageType);
            $('#editMenu').val(menuId);
            $('#editStatus').val(status);
            $('#editPosition').val(position);
            $('#editTabForm').data('page-id', pageId);
        });
    
        $('#saveTabChanges').on('click', function() {
            var form = $('#editTabForm');
            var pageId = form.data('page-id');
            var menuId = $('#editMenu').val();
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
                success: function(response) {
                    $('#editModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            });
        });
    });
    $('#confirm-hide-btn').click(function() {
        let hideStatus = {};

        $('.toggle-hide-btn').each(function() {
            const pageId = $(this).data('page-id');
            const isChecked = $(this).is(':checked');
            hideStatus[pageId] = isChecked ? 0 : 3;
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
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || "Có lỗi xảy ra khi lưu thay đổi.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi lưu thay đổi.");
                }
            });
        }
    });
    $("#sortable-pages").sortable();

    // Lọc danh sách page khi bật/tắt switch
    $('#toggleStatusTabs').on('change', function() {
        const showAll = $(this).is(':checked');

        $('.page-item').each(function() {
            const isStatus = $(this).data('status') == 1;
            if (isStatus) {
                $(this).toggleClass('hidden-page', !showAll);
            }
        });
    });

    $("#confirm-sort-btn").click(function() {
        var sortedData = [];
        $("#sortable-pages li").each(function(index) {
            var pageId = $(this).data("page-id");
            sortedData.push({
                id: pageId,
                position: index + 1
            });
        });
        if (confirm("Xác nhận sắp xếp?")) {

            $.ajax({
                url: update_sortOrder_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pages: sortedData
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                        $('#sortModal').modal('hide');
                    } else {
                        alert(response.message || "Lỗi.");
                    }
                },
                error: function() {
                    alert("Lỗi.");
                }
            });
        }
    });
    $(document).on('click', '#confirm-restore-btn', function() {
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
                success: function(response) {
                    if (response.success) {
                        location.reload();
                        $('#trashBinModal').modal('hide');
                    } else {
                        alert(response.message || "Khôi phục thất bại.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi khôi phục.");
                }
            });
        }
    });

    $(document).on('click', '#delete-permanently-btn', function() {
        const pageId = $(this).data('page-id');
        const pageName = $(this).data('page-name');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn page này không?")) {
            $.ajax({
                url: delete_permanently_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId: pageId,
                    pageName: pageName,
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || "Xóa thất bại.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi xóa page.");
                }
            });
        }
    });

    $('#confirm-delete-btn').on('click', function() {
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
            success: function(response) {
                if (response.success) {
                    location.reload();
                    $('#deleteModal').modal('hide');
                } else {
                    alert(response.message || "Xóa page thất bại.");
                }
            },
            error: function() {
                alert("Có lỗi xảy ra khi xóa page.");
            }
        });
    });

    $('#confirm-delete-permanently-btn').on('click', function() {
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
                success: function(response) {
                    if (response.success) {
                        location.reload();
                        $('#deleteModal').modal('hide');
                    } else {
                        alert(response.message || "Xóa page thất bại.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi xóa page.");
                }
            });
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const confirmDeleteBtn = document.getElementById("confirm-delete-btn");
    const confirmDeletePermanentlyBtn = document.getElementById("confirm-delete-permanently-btn");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            const pageId = this.getAttribute("data-page-id");
            confirmDeleteBtn.setAttribute("data-page-id", pageId);
            confirmDeletePermanentlyBtn.setAttribute("data-page-id", pageId);
        });
    });
});