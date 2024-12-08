$(document).ready(function () {
    $(document).off('click', '.toggle-all').on('click', '.toggle-all', function () {
        const toggleIcon = $(this).find('i');
        const isExpanded = toggleIcon.hasClass('fa-circle-minus');

        if (isExpanded) {
            // Đóng tất cả
            $('.child-row').hide();
            $('.toggle-icon i').removeClass('fa-caret-down').addClass(
                'fa-caret-right');
            toggleIcon.removeClass('fa-circle-minus').addClass('fa-circle-plus');
        } else {
            // Mở tất cả
            $('.child-row').show();
            $('.toggle-icon i').removeClass('fa-caret-right').addClass(
                'fa-caret-down');
            toggleIcon.removeClass('fa-circle-plus').addClass('fa-circle-minus');
        }
    });

    $(document).off('click', '.toggle-icon').on('click', '.toggle-icon', function () {
        const toggleIcon = $(this).find('i');
        const parentRow = $(this).closest('tr');
        const parentId = parentRow.data('parent-id');

        $(`.child-row[data-parent-id='${parentId}']`).each(function () {
            const childRow = $(this);
            if (childRow.is(':visible')) {
                childRow.hide();
                toggleIcon.removeClass('fa-caret-down').addClass('fa-caret-right');
            } else {
                childRow.show();
                toggleIcon.removeClass('fa-caret-right').addClass('fa-caret-down');
            }
        });

        const allExpanded = $('.toggle-icon i').toArray().every(icon => $(icon).hasClass(
            'fa-caret-down'));
        const allCollapsed = $('.toggle-icon i').toArray().every(icon => $(icon).hasClass(
            'fa-caret-right'));

        const toggleAllIcon = $('.toggle-all i');
        if (allExpanded) {
            toggleAllIcon.removeClass('fa-circle-plus').addClass('fa-circle-minus');
        } else if (allCollapsed) {
            toggleAllIcon.removeClass('fa-circle-minus').addClass('fa-circle-plus');
        }
    });

});

$(document).ready(function () {
    $('#confirm-hide-btn').click(function () {
        let hideStatus = {};

        $('.toggle-hide-btn').each(function () {
            const menuId = $(this).data('menu-id');
            const isChecked = $(this).is(':checked');
            hideStatus[menuId] = isChecked ? 0 : 1;
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
                        swal({
                            title: "Thành công!",
                            text: response.message || "Dữ liệu đã được cập nhật.",
                            icon: "success",
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Thất bại!",
                            text: response.message ||
                                "Có lỗi xảy ra, vui lòng thử lại.",
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Lỗi AJAX: ', error);
                    swal({
                        title: "Thất bại!",
                        text: response.message ||
                            "Có lỗi xảy ra, vui lòng thử lại.",
                        icon: "error",
                    });
                }
            });
        }
    });
    $("#sortable-pages").sortable();
    $("#confirm-sort-btn").click(function () {
        var sortedData = [];
        $("#sortable-pages li").each(function (index) {
            var menuId = $(this).data("menu-id");
            sortedData.push({
                id: menuId,
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
                    menus: sortedData
                },
                success: function (response) {
                    if (response.success) {
                        swal({
                            title: "Thành công!",
                            text: response.message || "Dữ liệu đã được cập nhật.",
                            icon: "success",
                        }).then(() => {
                            $('#sortModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Thất bại!",
                            text: response.message ||
                                "Có lỗi xảy ra, vui lòng thử lại.",
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Lỗi AJAX: ', error);
                    swal({
                        title: "Thất bại!",
                        text: response.message ||
                            "Có lỗi xảy ra, vui lòng thử lại.",
                        icon: "error",
                    });
                }
            });
        }
    });
    // Lọc danh sách page khi bật/tắt switch
    $('#toggleStatusMenus').on('change', function () {
        const showAll = $(this).is(':checked');

        $('.page-item').each(function () {
            const isStatus = $(this).data('status') == 1;
            if (isStatus) {
                $(this).toggleClass('hidden-page', !showAll);
            }
        });
    });

    $(document).on('click', '#confirm-restore-btn', function () {
        const menuId = $(this).data('menu-id');

        if (confirm("Bạn có chắc chắn muốn khôi phục menu này không?")) {
            $.ajax({
                url: restore_menu_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    menuId: menuId,
                },
                success: function (response) {
                    if (response.success) {
                        swal({
                            title: "Thành công!",
                            text: response.message || "Dữ liệu đã được cập nhật.",
                            icon: "success",
                        }).then(() => {
                            $('#trashBinModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Thất bại!",
                            text: response.message ||
                                "Có lỗi xảy ra, vui lòng thử lại.",
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Lỗi AJAX: ', error);
                    swal({
                        title: "Thất bại!",
                        text: response.message ||
                            "Có lỗi xảy ra, vui lòng thử lại.",
                        icon: "error",
                    });
                }
            });
        }
    });

    $(document).on('click', '#delete-permanently-btn', function () {
        const menuId = $(this).data('menu-id');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn menu này không?")) {
            $.ajax({
                url: delete_permanently_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    menuId: menuId,
                },
                success: function (response) {
                    if (response.success) {
                        swal({
                            title: "Thành công!",
                            text: response.message || "Xóa thành công.",
                            icon: "success",
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Thất bại!",
                            text: response.message ||
                                "Có lỗi xảy ra, vui lòng thử lại.",
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Lỗi AJAX: ', error);
                    swal({
                        title: "Thất bại!",
                        text: response.message ||
                            "Có lỗi xảy ra, vui lòng thử lại.",
                        icon: "error",
                    });
                }
            });
        }
    });

    $('#confirm-delete-btn').on('click', function () {
        const menuId = $(this).data('menu-id');

        $.ajax({
            url: delete_soft_url,
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                menuId: menuId,
            },
            success: function (response) {
                if (response.success) {
                    swal({
                        title: "Thành công!",
                        text: response.message || "Dữ liệu đã được cập nhật.",
                        icon: "success",
                    }).then(() => {
                        $('#deleteModal').modal('hide');
                        location.reload();
                    });
                } else {
                    swal({
                        title: "Thất bại!",
                        text: response.message ||
                            "Có lỗi xảy ra, vui lòng thử lại.",
                        icon: "error",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log('Lỗi AJAX: ', error);
                swal({
                    title: "Thất bại!",
                    text: response.message ||
                        "Có lỗi xảy ra, vui lòng thử lại.",
                    icon: "error",
                });
            }
        });
    });

    $('#confirm-delete-permanently-btn').on('click', function () {
        const menuId = $(this).data('menu-id');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn không?")) {
            $.ajax({
                url: delete_permanently_url,
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    menuId: menuId,
                },
                success: function (response) {
                    if (response.success) {
                        swal({
                            title: "Thành công!",
                            text: response.message || "Dữ liệu đã được cập nhật.",
                            icon: "success",
                        }).then(() => {
                            $('#deleteModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Thất bại!",
                            text: response.message ||
                                "Có lỗi xảy ra, vui lòng thử lại.",
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Lỗi AJAX: ', error);
                    swal({
                        title: "Thất bại!",
                        text: response.message ||
                            "Có lỗi xảy ra, vui lòng thử lại.",
                        icon: "error",
                    });
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
            const menuId = this.getAttribute("data-menu-id");
            confirmDeleteBtn.setAttribute("data-menu-id", menuId);
            confirmDeletePermanentlyBtn.setAttribute("data-menu-id", menuId);
        });
    });
});

$(document).ready(function () {
    $('.form-multi-select').select2({
        placeholder: 'Chọn',
        allowClear: true
    });
    $(document).off('click', '#submenu').on('click', '#submenu', function () {
        var button = $(this);
        var menuId = button.data('menu-id');
        var menuName = button.data('menu-name');

        button.prop('disabled', true);

        $('#saveSubMenuChanges').attr('data-menu-id', menuId);
        $('#sub-menus').empty();
        $('#sortable-submenus').empty();
        $('#subMenuModalLabel').text('Menu cho ' + menuName);

        $.ajax({
            url: get_sub_menu_url,
            type: 'GET',
            data: { menu_id: menuId },
            success: function (response) {
                if (response.success) {
                    response.childMenus.forEach(menu => {
                        $('#sub-menus').append(
                            `<option value="${menu.id}" selected>${menu.name}</option>`
                        );
                    });

                    response.potentialMenus.forEach(menu => {
                        $('#sub-menus').append(
                            `<option value="${menu.id}">${menu.name}</option>`
                        );
                    });

                    if (response.childMenus.length > 0) {
                        response.childMenus.forEach(menu => {
                            $('#sortable-submenus').append(`
                                <li class="list-group-item" data-id="${menu.id}">
                                    ${menu.name}
                                </li>
                            `);
                        });
                    }

                    $('#sortable-submenus').sortable();
                    $('#subMenuModal').modal('show');
                } else {
                    alert(response.message || 'Không thể tải dữ liệu.');
                }
            },
            error: function (xhr, status, error) {
                console.log('Lỗi AJAX:', error);
                alert('Có lỗi xảy ra khi tải dữ liệu.');
            },
            complete: function () {
                button.prop('disabled', false);
            }
        });
    });

    $(document).off('change', '#sub-menus').on('change', '#sub-menus', function () {
        var selectedValues = $(this).val();
        var currentList = $('#sortable-submenus li').map(function () {
            return $(this).data('id');
        }).get();

        currentList.forEach(function (id) {
            if (!selectedValues.includes(id.toString())) {
                $(`#sortable-submenus li[data-id="${id}"]`).remove();
            }
        });

        selectedValues.forEach(function (id) {
            if (!$(`#sortable-submenus li[data-id="${id}"]`).length) {
                var option = $(`#sub-menus option[value="${id}"]`);
                $('#sortable-submenus').append(`
                    <li class="list-group-item" data-id="${id}">
                        ${option.text()}
                    </li>
                `);
            }
        });
    });

    $('#sortable-submenus').sortable({
        update: function () {
            var sortedIds = $('#sortable-submenus li').map(function () {
                return $(this).data('id');
            }).get();

            $('#sub-menus').val(sortedIds).trigger('change');
        }
    });

    $(document).off('click', '#saveSubMenuChanges').on('click', '#saveSubMenuChanges', function () {
        var menuId = $(this).attr('data-menu-id');
        var selectedMenus = $('#sub-menus').val();
        var sortedData = [];

        $('#sortable-submenus li').each(function (index) {
            sortedData.push({
                id: $(this).data('id'),
                position: index + 1
            });
        });
        $.ajax({
            url: save_sub_menu_url,
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                menuId: menuId,
                selectedMenus: selectedMenus,
                sortedData
            },
            success: function (response) {
                if (response.success) {
                    swal({
                        title: "Thành công!",
                        text: response.message || "Dữ liệu đã được cập nhật.",
                        icon: "success",
                    }).then(() => {
                        $('#subTabModal').modal('hide');
                        location.reload();
                    });
                } else {
                    swal({
                        title: "Thất bại!",
                        text: response.message ||
                            "Có lỗi xảy ra, vui lòng thử lại.",
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
            }
        });
    });
});


$(document).off('click', '.edit-subpage-btn').on('click', '.edit-subpage-btn', function () {
    var button = $(this);
    var menuId = button.data('menu-id');
    var menuName = button.data('menu-name');

    button.prop('disabled', true);

    $('#editSubPageModalLabel').text('Page cho ' + menuName);
    $('#sub-pages').empty();
    $('#sortable-subpages').empty();
    $('#saveSubPageChanges').attr('data-menu-id', menuId);

    $.ajax({
        url: get_sub_page_url,
        type: 'GET',
        data: { menu_id: menuId },
        success: function (response) {
            $('#sortable-subpages').empty();
            if (response.success) {
                response.childPages.forEach(page => {
                    $('#sub-pages').append(
                        `<option value="${page.id}" selected>${page.name}</option>`
                    );
                });

                response.potentialPages.forEach(page => {
                    $('#sub-pages').append(
                        `<option value="${page.id}">${page.name}</option>`
                    );
                });

                if (response.childPages.length > 0) {
                    response.childPages.forEach(page => {
                        $('#sortable-subpages').append(`
                            <li class="list-group-item" data-id="${page.id}">
                                ${page.name}
                            </li>
                        `);
                    });
                }

                $('#sortable-subpages').sortable();
                $('#editSubPageModal').modal('show');
            } else {
                alert(response.message || 'Không thể tải dữ liệu.');
            }
        },
        error: function (xhr, status, error) {
            console.log('Lỗi AJAX:', error);
            alert('Có lỗi xảy ra khi tải dữ liệu.');
        },
        complete: function () {
            button.prop('disabled', false);
        }
    });
});
$(document).off('change', '#sub-pages').on('change', '#sub-pages', function () {
    var selectedValues = $(this).val();
    var currentList = $('#sortable-subpages li').map(function () {
        return $(this).data('id');
    }).get();

    currentList.forEach(function (id) {
        if (!selectedValues.includes(id.toString())) {
            $(`#sortable-subpages li[data-id="${id}"]`).remove();
        }
    });

    selectedValues.forEach(function (id) {
        if (!$(`#sortable-subpages li[data-id="${id}"]`).length) {
            var option = $(`#sub-pages option[value="${id}"]`);
            $('#sortable-subpages').append(`
                <li class="list-group-item" data-id="${id}">
                    ${option.text()}
                </li>
            `);
        }
    });
});

$('#sortable-subpages').sortable({
    update: function () {
        var sortedIds = $('#sortable-subpages li').map(function () {
            return $(this).data('id');
        }).get();

        $('#sub-pages').val(sortedIds).trigger('change');
    }
});

$(document).off('click', '#saveSubPageChanges').on('click', '#saveSubPageChanges', function () {
    var menuId = $(this).attr('data-menu-id');
    var sortedData = [];

    var selectedPages = $('#sub-pages').val();
    $('#sortable-subpages li').each(function (index) {
        sortedData.push({
            id: $(this).data('id'),
            position: index + 1
        });
    });

    $.ajax({
        url: save_sub_page_url,
        type: 'POST',
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            menuId,
            selectedPages,
            sortedData
        },
        success: function (response) {
            if (response.success) {
                swal({
                    title: "Thành công!",
                    text: response.message || "Dữ liệu đã được cập nhật.",
                    icon: "success",
                }).then(() => {
                    $('#editSubPageModal').modal('hide');
                    location.reload();
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
            console.error('Lỗi AJAX:', error);
            swal({
                title: "Lỗi hệ thống!",
                text: "Không thể thực hiện yêu cầu, vui lòng thử lại.",
                icon: "error",
            });
        }
    });
});

$(document).ready(function () {
    $(document).off('click', '.edit-btn').on('click', '.edit-btn', function () {
        var menuId = $(this).data('page-menu-id');
        var menuName = $(this).data('menu-name');
        var menuType = $(this).data('menu-type');
        var icon = $(this).data('icon');
        var status = $(this).data('status');
        var position = $(this).data('position');

        $('#tabmenuName').val(menuName);
        $('#tabmenuType').val(menuType);
        $('#menustatus').val(status);
        $('#tabMenuPosition').val(position);
        $('#editMenuForm').data('menu-id', menuId);
        $('#selected-icon').html('<use href="' + yiiWebAlias + '/images/icon-sprite.svg#' + icon + '"></use>');
        $('#selected-icon-label').text(icon);
    });

    $(document).off('click', '#saveTabMenuChanges').on('click', '#saveTabMenuChanges', function () {
        var form = $('#editMenuForm');
        var menuId = form.data('menu-id');
        var menuName = $('#tabmenuName').val();
        var menuType = $('#tabmenuType').val();
        var icon = $('#selected-icon-label').text(); // Lấy icon đã chọn
        var status = $('#menustatus').val();
        var position = $('#tabMenuPosition').val();


        // Gửi dữ liệu tới server để cập nhật menu
        $.ajax({
            url: update_menu_url,
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: menuId,
                name: menuName,
                icon: icon,
                status: status,
                position: position,
            },
            success: function (response) {
                if (response.success) {
                    swal({
                        title: "Thành công!",
                        text: response.message || "Dữ liệu đã được cập nhật.",
                        icon: "success",
                    }).then(() => {
                        $('#subTabModal').modal('hide'); // Ẩn modal
                        location.reload(); // Tải lại trang nếu cần
                    });
                } else {
                    swal({
                        title: "Thất bại!",
                        text: response.message ||
                            "Có lỗi xảy ra, vui lòng thử lại.",
                        icon: "error",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log('Lỗi AJAX: ', error);
                swal({
                    title: "Thất bại!",
                    text: response.message ||
                        "Có lỗi xảy ra, vui lòng thử lại.",
                    icon: "error",
                });
            }
        });
    });
});
