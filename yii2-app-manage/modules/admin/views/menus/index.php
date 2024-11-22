<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
$this->title = 'Danh Sách Menu';


?>
<?php include Yii::getAlias('@app/views/layouts/_icon.php'); ?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar-settings.php'); ?>


<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <!-- You can add page title or breadcrumbs here -->
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <div class="d-flex flex-column flex-md-row align-items-md-center">
                            <div class="me-auto mb-3 mb-md-0 text-center text-md-start">
                                <h4>Danh sách Menu</h4>
                                <p class="mt-1 f-m-light">Menu Nhóm | Menu Đơn</p>
                            </div>
                            <div
                                class="d-flex flex-wrap justify-content-center align-items-center me-md-2 mb-3 mb-md-0">
                                <a class="btn btn-outline-warning me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#hideModal">
                                    <i class="fas fa-eye me-1"></i> Hiện/Ẩn
                                </a>
                                <a class="btn btn-outline-primary me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#sortModal">
                                    <i class="fas fa-sort-amount-down me-1"></i> Sắp Xếp
                                </a>
                                <a class="btn btn-danger me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#trashBinModal">
                                    <i class="fas fa-trash me-1"></i> Thùng Rác
                                </a>
                                <a class="btn btn-success mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#createMenuModal">
                                    <i class="fas fa-plus me-1"></i> Thêm Menu
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-responsive custom-scrollbar border table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 2%"></th>
                                        <th colspan="2" style="width: 20%">Tên Menu</th>
                                        <th style="width: 6%" class="text-center">Icon</th>
                                        <th style="width: 20%">Page</th>
                                        <th style="width: 8%" class="text-center">Trạng Thái</th>
                                        <th style="width: 8%">Thao tác</th>
                                        <th style="width: 3%"></th>

                                    </tr>
                                </thead>
                                <?php
                                $menuParents = array_filter($menus, fn($menu) => $menu->parent_id === null);
                                $menuChildren = array_filter($menus, fn($menu) => $menu->parent_id !== null);
                                ?>
                                <tbody id="columnsContainer">
                                    <?php foreach ($menuParents as $parentMenu): ?>
                                    <?php if ($parentMenu->deleted != 1): ?>
                                    <tr class="parent-row" data-parent-id="<?= Html::encode($parentMenu->id) ?>">
                                        <td class="toggle-icon text-center">
                                            <?php
                                                    $hasChildren = array_filter($menuChildren, fn($child) => $child->parent_id == $parentMenu->id);
                                                    ?>
                                            <?php if (!empty($hasChildren)): ?>
                                            <i class="fas fa-plus-circle"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td colspan="2"><?= Html::encode($parentMenu->name) ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center"
                                                id="icon-display">
                                                <svg class="stroke-icon" width="24" height="24">
                                                    <use
                                                        href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $parentMenu->icon ?>">
                                                    </use>
                                                </svg>
                                            </div>
                                        </td>
                                        <td>
                                            <?php foreach ($pages as $page): ?>
                                            <?php if ($page->menu_id == $parentMenu->id): ?>
                                            <span class="badge badge-primary"><?= Html::encode($page->name) ?></span>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $parentMenu->status == 1 ? '<span class="badge badge-warning">Ẩn</span>' : '<span class="badge badge-success">Hiện</span>' ?>
                                        </td>
                                        <td class="text-nowrap text-center">
                                            <button class="btn btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-page-menu-id="<?= $parentMenu->id ?>"
                                                data-menu-name="<?= Html::encode($parentMenu->name) ?>"
                                                data-icon="<?= Html::encode($parentMenu->icon) ?>"
                                                data-status="<?= Html::encode($parentMenu->status) ?>"
                                                data-position="<?= Html::encode($parentMenu->position) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info me-1" id="submenu"
                                                data-menu-name="<?= Html::encode($parentMenu->name) ?>"
                                                data-menu-id="<?= $parentMenu->id ?>">
                                                <i class="fas fa-cogs"></i>
                                            </button>
                                            <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-menu-id="<?= $parentMenu->id ?>">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <?php foreach ($menuChildren as $index => $childMenu): ?>
                                    <?php if ($childMenu->parent_id == $parentMenu->id): ?>
                                    <tr class="child-row" data-parent-id="<?= Html::encode($parentMenu->id) ?>"
                                        data-sort-id="<?= Html::encode($childMenu->id) ?>" style="display: none;">
                                        <td colspan="2" rowspan=""></td>
                                        <td style="width: 18%" class="text-nowrap">
                                            <?= Html::encode($childMenu->name) ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center"
                                                id="icon-display">
                                                <svg class="stroke-icon" width="24" height="24">
                                                    <use
                                                        href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $childMenu->icon ?>">
                                                    </use>
                                                </svg>
                                            </div>
                                        </td>
                                        <td>
                                            <?php foreach ($pages as $page): ?>
                                            <?php if ($page->menu_id == $childMenu->id): ?>
                                            <span class="badge badge-primary"><?= Html::encode($page->name) ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $childMenu->status == 1 ?
                                                                '<span class="badge badge-warning">Ẩn</span>' :
                                                                '<span class="badge badge-success">Hiện</span>' ?>
                                        </td>
                                        <td class="text-nowrap text-center">
                                            <button class="btn btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-page-menu-id="<?= $childMenu->id ?>"
                                                data-menu-name="<?= Html::encode($childMenu->name) ?>"
                                                data-icon="<?= Html::encode($childMenu->icon) ?>"
                                                data-status="<?= Html::encode($childMenu->status) ?>"
                                                data-position="<?= Html::encode($childMenu->position) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info me-1" id="submenu"
                                                data-menu-name="<?= Html::encode($childMenu->name) ?>"
                                                data-menu-id="<?= $childMenu->id ?>">
                                                <i class="fas fa-cogs"></i>
                                            </button>
                                            <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-menu-id="<?= $childMenu->id ?>">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </td>
                                        <td class="sort-icon text-center" style="color: #6e6e6e;">
                                            <i class="fas fa-sort"></i>
                                        </td>
                                    </tr>

                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                    <!-- Hiển thị menu con -->


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.toggle-icon i').on('click', function(e) {
        const toggleIcon = $(this); // Lấy chính icon được nhấn
        const parentRow = toggleIcon.closest('tr'); // Tìm hàng cha
        const parentId = parentRow.data('parent-id'); // Lấy ID của hàng cha

        // Tìm các hàng con liên quan
        $(`.child-row[data-parent-id='${parentId}']`).each(function() {
            const childRow = $(this);
            if (childRow.is(':visible')) {
                childRow.hide(); // Ẩn hàng con
                toggleIcon.removeClass('fa-minus-circle').addClass(
                    'fa-plus-circle'); // Biểu tượng thu gọn
            } else {
                childRow.show(); // Hiển thị hàng con
                toggleIcon.removeClass('fa-plus-circle').addClass(
                    'fa-minus-circle'); // Biểu tượng mở rộng
            }
        });
    });

    // Sắp xếp các hàng trong bảng menu (cha và con)
    $(document).on('click', 'th.sortable', function() {
        var columnIndex = $(this).index(); // Lấy chỉ mục cột được nhấp vào
        var rows = $('#columnsContainer tr').get(); // Lấy tất cả các hàng trong bảng

        rows.sort(function(a, b) {
            var cellA = $(a).children('td').eq(columnIndex).text().trim(); // Lấy nội dung cột
            var cellB = $(b).children('td').eq(columnIndex).text().trim();

            if (cellA < cellB) return -1; // So sánh giá trị cột
            if (cellA > cellB) return 1;
            return 0;
        });

        // Đặt lại các hàng vào tbody sau khi sắp xếp
        $.each(rows, function(index, row) {
            $('#columnsContainer').append(row);
        });
    });

    // Kéo và thả các hàng trong bảng
    $('#columnsContainer').sortable({
        handle: '.sort-icon', // Biểu tượng kéo
        update: function(event, ui) {
            var sortedIDs = $('#columnsContainer').sortable('toArray', {
                attribute: 'data-sort-id' // Thuộc tính dùng để phân biệt các hàng con
            });

            // Gửi dữ liệu đã sắp xếp lên server
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/save-sort']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    sortedIDs: sortedIDs
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Sắp xếp thành công!');
                    } else {
                        showToast('Có lỗi xảy ra khi lưu dữ liệu.');
                    }
                },
                error: function() {
                    showToast('Có lỗi xảy ra khi lưu dữ liệu.');
                }
            });
        }
    });

});

$(document).ready(function() {
    $('#confirm-hide-btn').click(function() {
        let hideStatus = {};

        $('.toggle-hide-btn').each(function() {
            const menuId = $(this).data('menu-id');
            const isChecked = $(this).is(':checked');
            hideStatus[menuId] = isChecked ? 0 : 1;
        });

        if (confirm("Xác nhận thao tác?")) {

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/update-hide-status']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    hideStatus: hideStatus
                },
                success: function(response) {
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
                error: function(xhr, status, error) {
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
    $("#confirm-sort-btn").click(function() {
        var sortedData = [];
        $("#sortable-pages li").each(function(index) {
            var menuId = $(this).data("menu-id");
            sortedData.push({
                id: menuId,
                position: index + 1
            });
        });
        if (confirm("Xác nhận sắp xếp?")) {

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/update-sort-order']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    menus: sortedData
                },
                success: function(response) {
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
                error: function(xhr, status, error) {
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
    $('#toggleStatusMenus').on('change', function() {
        const showAll = $(this).is(':checked');

        $('.page-item').each(function() {
            const isStatus = $(this).data('status') == 1;
            if (isStatus) {
                $(this).toggleClass('hidden-page', !showAll);
            }
        });
    });

    $(document).on('click', '#confirm-restore-btn', function() {
        const menuId = $(this).data('menu-id');

        if (confirm("Bạn có chắc chắn muốn khôi phục menu này không?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/restore-menu']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    menuId: menuId,
                },
                success: function(response) {
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
                error: function(xhr, status, error) {
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

    $(document).on('click', '#delete-permanently-btn', function() {
        const menuId = $(this).data('menu-id');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn menu này không?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/delete-permanently-menu']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    menuId: menuId,
                },
                success: function(response) {
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
                error: function(xhr, status, error) {
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

    $('#confirm-delete-btn').on('click', function() {
        const menuId = $(this).data('menu-id');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['menus/delete-menu']) ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                menuId: menuId,
            },
            success: function(response) {
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
            error: function(xhr, status, error) {
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

    $('#confirm-delete-permanently-btn').on('click', function() {
        const menuId = $(this).data('menu-id');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn không?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/delete-permanently-menu']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    menuId: menuId,
                },
                success: function(response) {
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
                error: function(xhr, status, error) {
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
document.addEventListener("DOMContentLoaded", function() {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const confirmDeleteBtn = document.getElementById("confirm-delete-btn");
    const confirmDeletePermanentlyBtn = document.getElementById("confirm-delete-permanently-btn");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            const menuId = this.getAttribute("data-menu-id");
            confirmDeleteBtn.setAttribute("data-menu-id", menuId);
            confirmDeletePermanentlyBtn.setAttribute("data-menu-id", menuId);
        });
    });
});

$(document).ready(function() {
    $('.form-multi-select').select2({
        placeholder: 'Chọn',
        allowClear: true
    });
    $(document).on('click', '#submenu', function() {
        var menuId = $(this).data('menu-id');
        var menuName = $(this).data('menu-name');
        $('#saveSubMenuChanges').attr('data-menu-id', menuId);
        $('#submenu-pages').empty();
        $('#submenu-menus').empty();
        $('#subMenuModalLabel').text(menuName);

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['menus/get-submenu']) ?>',
            type: 'GET',
            data: {
                menu_id: menuId
            },
            success: function(response) {
                console.log("🚀 ~ response:", response);
                if (response.success) {
                    response.childTabs.forEach(page => {
                        $('#submenu-pages').append(
                            `<option value="${page.id}" selected>${page.name}</option>`
                        );
                    });

                    if (response.childMenus.length > 0) {
                        // Nếu có menu con
                        $('#submenu-menus').parent().show();
                        $('#submenu-pages').parent().hide();

                        response.childMenus.forEach(menu => {
                            $('#submenu-menus').append(
                                `<option value="${menu.id}" selected>${menu.name}</option>`
                            );
                        });

                        response.potentialMenus.forEach(menu => {
                            $('#submenu-menus').append(
                                `<option value="${menu.id}">${menu.name}</option>`
                            );
                        });
                    } else {
                        // Nếu không có menu con
                        $('#submenu-menus').parent().hide();
                        $('#submenu-pages').parent().show();

                        response.potentialTabs.forEach(page => {
                            $('#submenu-pages').append(
                                `<option value="${page.id}">${page.name}</option>`
                            );
                        });
                    }

                    $('#subMenuModal').modal('show');
                } else {
                    alert(response.message || 'Không thể tải dữ liệu.');
                }
            },
            error: function(xhr, status, error) {
                console.log('Lỗi AJAX:', error);
                alert('Có lỗi xảy ra khi tải dữ liệu.');
            }
        });
    });


    $(document).on('click', '#saveSubMenuChanges', function() {
        var menuId = $(this).attr('data-menu-id');
        var selectedTabs = $('#submenu-pages').val();
        var selectedMenus = $('#submenu-menus').val();
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['menus/save-sub-menu']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                menuId: menuId,
                selectedTabs: selectedTabs,
                selectedMenus: selectedMenus
            },
            success: function(response) {
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
            error: function(xhr, status, error) {
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

$(document).ready(function() {
    $(document).on('click', '.edit-btn', function() {
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

        $('#selected-icon').html(
            '<use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#' +
            icon + '"></use>');
        $('#selected-icon-label').text(icon);
    });

    // Lưu thay đổi menu
    $(document).on('click', '#saveTabMenuChanges', function() {
        var form = $('#editMenuForm');
        var menuId = form.data('menu-id');
        var menuName = $('#tabmenuName').val();
        var menuType = $('#tabmenuType').val();
        var icon = $('#selected-icon-label').text(); // Lấy icon đã chọn
        var status = $('#menustatus').val();
        var position = $('#tabMenuPosition').val();


        // Gửi dữ liệu tới server để cập nhật menu
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['update-menu']) ?>',
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
            success: function(response) {
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
            error: function(xhr, status, error) {
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
</script>
<!-- Modal sửa Menu  -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Sửa Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMenuForm">
                    <!-- Tên Menu -->
                    <div class="mb-3">
                        <label for="tabmenuName" class="form-label">Tên Menu</label>
                        <input type="text" class="form-control" id="tabmenuName" name="name" required>
                    </div>

                    <!-- Icon -->
                    <div class="mb-3">
                        <label for="icon-selected-value" class="form-label">Chọn icon</label>
                        <input type="hidden" id="icon-selected-value" value="">
                        <div class="row">
                            <div class="col-12">
                                <div id="icon-select-wrapper" class="d-flex align-items-center justify-content-between"
                                    style="cursor: pointer; border: 1px solid #ccc; padding: 8px; border-radius: 8px;">
                                    <span id="selected-icon-label">Chọn icon</span>
                                    <svg id="selected-icon" class="stroke-icon mx-2" width="24" height="24"></svg>
                                </div>

                                <!-- Danh sách icon -->
                                <div id="icon-list" class="d-flex flex-wrap mt-2"
                                    style="display: none; overflow-y: auto; max-height: 200px; border: 1px solid #ccc; border-radius: 8px;">
                                    <?php foreach ($iconOptions as $iconValue => $iconLabel): ?>
                                    <div class="icon-item col-2 col-md-2 col-lg-1 me-2 mb-2 text-center"
                                        data-icon="<?= Html::encode($iconValue) ?>"
                                        style="cursor: pointer; padding: 4px;">
                                        <svg class="stroke-icon" width="40" height="40">
                                            <use
                                                href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= Html::encode($iconValue) ?>">
                                            </use>
                                        </svg>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label for="menustatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="menustatus" name="status" required>
                            <option value="0">Hiển thị</option>
                            <option value="1">Ẩn</option>
                        </select>
                    </div>

                    <!-- Vị trí -->
                    <div class="mb-3">
                        <label for="tabMenuPosition" class="form-label">Vị trí</label>
                        <input type="number" class="form-control" id="tabMenuPosition" name="position" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveTabMenuChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Page Menu Con -->
<div class="modal fade" id="subMenuModal" tabindex="-1" aria-labelledby="subMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="subMenuModalLabel"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="submenu-pages" class="form-label">Page Con:</label>
                    <select id="submenu-pages" class="form-select form-multi-select" multiple>
                        <!-- Options sẽ được thêm qua AJAX -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="submenu-menus" class="form-label">Menu Con:</label>
                    <select id="submenu-menus" class="form-select form-multi-select" multiple>
                        <!-- Options sẽ được thêm qua AJAX -->
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveSubMenuChanges" data-menu-id="">Lưu thay
                    đổi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thùng Rác -->
<div class="modal fade" id="trashBinModal" tabindex="-1" aria-labelledby="trashBinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="trashBinModalLabel">Thùng Rác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Chọn page bạn muốn khôi phục hoặc xóa hoàn toàn:</p>
                <table class="table table-bordered table-hover table-ui">
                    <thead>
                        <tr>
                            <th>Tên Menu</th>
                            <th style="width: 20%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedMenus = false; ?>
                        <?php foreach ($menus as $page): ?>
                        <?php if ($page->deleted == 1): ?>
                        <?php $hasDeletedMenus = true; ?>
                        <tr>
                            <td><?= htmlspecialchars($page->name) ?></td>
                            <td class="text-nowrap">
                                <button type="button" class="btn btn-warning restore-page-btn" id="confirm-restore-btn"
                                    data-menu-id="<?= htmlspecialchars($page->id) ?>">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                                <button type="button" class="btn btn-danger delete-page-btn" id="delete-permanently-btn"
                                    data-page-name="<?= htmlspecialchars($page->name) ?>"
                                    data-menu-id="<?= htmlspecialchars($page->id) ?>">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!$hasDeletedMenus): ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                <em>There is nothing here.</em>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hide page -->
<div class="modal fade" id="hideModal" tabindex="-1" aria-labelledby="hideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="hideModalLabel">Hiện/Ẩn Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p class="pb-0 mb-0">Chọn page bạn muốn ẩn hoặc hiển thị:</p>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>Tên Menu</th>
                            <th class="text-center" style="width: 8%">Hiện</i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-index">
                        <?php foreach ($menus as $menu): ?>
                        <?php if ($menu->deleted != 1): ?>
                        <tr>
                            <td class="py-0">
                                <?= htmlspecialchars($menu->name) ?>
                            </td>
                            <td class="py-0" class="text-center">
                                <label class="switch mb-0 mt-1">
                                    <input class="form-check-input toggle-hide-btn" type="checkbox"
                                        data-menu-id="<?= htmlspecialchars($menu->id) ?>"
                                        <?php if ($menu->status == 0): ?> checked <?php endif; ?>>
                                    <span class="switch-state"></span>
                                </label>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-hide-btn">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sắp Xếp -->
<div class="modal fade" id="sortModal" tabindex="-1" aria-labelledby="sortModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="sortModalLabel">Sắp Xếp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p>Kéo và thả để sắp xếp các menu.</p>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="toggleStatusMenus" checked>
                    <label class="form-check-label" for="toggleStatusMenus">Hiển thị Menu đã ẩn</label>
                </div>
                <ul class="list-group" id="sortable-pages">
                    <?php foreach ($menuParents as $index => $menu): ?>
                    <?php if ($menu->deleted != 1): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center page-item"
                        data-menu-id="<?= $menu->id ?>" data-status="<?= $menu->status ?>">
                        <span><?= htmlspecialchars($menu->name) ?></span>
                        <span class="badge bg-secondary"><?= $index + 1 ?></span>
                    </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-sort-btn">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirm Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteModalLabel">Xác nhận xóa page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa page này không? Không thể hoàn tác hành động này.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-menu-id="<?= htmlspecialchars($menuId) ?>">Xóa</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-page-name="<?= htmlspecialchars($menu->name) ?>"
                    data-menu-id="<?= htmlspecialchars($menuId) ?>">Xóa Vĩnh Viễn</button>
            </div>
        </div>
    </div>
</div>