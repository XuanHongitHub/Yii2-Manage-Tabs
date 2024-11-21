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
                                        <th style="width: 30%">Tên Menu</th>
                                        <th style="width: 6%" class="text-center">Icon</th>
                                        <th style="width: 8%">Page</th>
                                        <th style="width: 8%" class="text-center">Trạng Thái</th>
                                        <th style="width: 8%">Thao tác</th>
                                        <th style="width: 2%"></th>

                                    </tr>
                                </thead>
                                <?php
                                $menuParents = array_filter($menus, fn($menu) => $menu->parent_id === null);
                                $menuChildren = array_filter($menus, fn($menu) => $menu->parent_id !== null);
                                ?>
                                <tbody id="columnsContainer">
                                    <?php foreach ($menuParents as $parentMenu): ?>
                                    <?php if ($parentMenu->deleted != 1): ?>
                                    <tr class="parent-row" data-parent-id="<?= Html::encode($parentMenu->id) ?>"
                                        data-sort-id="<?= Html::encode($parentMenu->id) ?>">
                                        <td class="toggle-icon text-center">
                                            <?php
                                                    $hasChildren = array_filter($menuChildren, fn($child) => $child->parent_id == $parentMenu->id);
                                                    ?>
                                            <?php if (!empty($hasChildren)): ?>
                                            <i class="fas fa-plus-circle"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= Html::encode($parentMenu->name) ?></td>
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
                                            <?php foreach ($tabs as $tab): ?>
                                            <?php if ($tab->menu_id == $parentMenu->id): ?>
                                            <span class="badge badge-primary"><?= Html::encode($tab->tab_name) ?></span>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $parentMenu->status == 1 ? '<span class="badge badge-warning">Ẩn</span>' : '<span class="badge badge-success">Hiện</span>' ?>
                                        </td>
                                        <td class="text-nowrap text-center">
                                            <button class="btn btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-tab-menu-id="<?= $parentMenu->id ?>"
                                                data-menu-name="<?= Html::encode($parentMenu->name) ?>"
                                                data-icon="<?= Html::encode($parentMenu->icon) ?>"
                                                data-status="<?= Html::encode($parentMenu->status) ?>"
                                                data-position="<?= Html::encode($parentMenu->position) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if (!empty($hasChildren)): ?>
                                            <button class="btn btn-sm btn-secondary me-1" id="submenu"
                                                data-menu-id="<?= $parentMenu->id ?>">
                                                <i class="fas fa-cogs"></i>
                                            </button>
                                            <?php else: ?>
                                            <button class="btn btn-sm btn-info me-1" id="submenu"
                                                data-menu-id="<?= $parentMenu->id ?>">
                                                <i class="fas fa-cogs"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-menu-id="<?= $parentMenu->id ?>">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </td>
                                        <td class="sort-icon text-center" style="color: #6e6e6e;">
                                            <i class="fas fa-sort"></i>
                                        </td>
                                    </tr>
                                    <!-- Hiển thị menu con -->
                                    <?php foreach ($menuChildren as $childMenu): ?>
                                    <?php if ($childMenu->parent_id == $parentMenu->id): ?>
                                    <tr class="child-row" data-parent-id="<?= Html::encode($parentMenu->id) ?>"
                                        data-sort-id="<?= Html::encode($childMenu->id) ?>" style="display: none;">
                                        <td data-order="">
                                            <!-- Không hiển thị gì -->
                                        </td>
                                        <td class="text-nowrap" data-order="<?= Html::encode($childMenu->name) ?>">
                                            -- <?= Html::encode($childMenu->name) ?></td>
                                        <td data-order="<?= Html::encode($childMenu->icon) ?>">
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
                                            <?php foreach ($tabs as $tab): ?>
                                            <?php if ($tab->menu_id == $childMenu->id): ?>
                                            <span class="badge badge-primary"><?= Html::encode($tab->tab_name) ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="text-center" data-order="<?= $childMenu->status ?>">
                                            <?= $childMenu->status == 1 ?
                                                                '<span class="badge badge-warning">Ẩn</span>' :
                                                                '<span class="badge badge-success">Hiện</span>' ?>
                                        </td>
                                        <td class="text-nowrap text-center" data-order="">
                                            <button class="btn btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-tab-menu-id="<?= $childMenu->id ?>"
                                                data-menu-name="<?= Html::encode($childMenu->name) ?>"
                                                data-icon="<?= Html::encode($childMenu->icon) ?>"
                                                data-status="<?= Html::encode($childMenu->status) ?>"
                                                data-position="<?= Html::encode($childMenu->position) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info me-1"
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
    function showToast(message) {
        document.getElementById('toast-body').textContent = message;

        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }

    // Xử lý sự kiện click cho hàng cha
    $('.parent-row').on('click', function() {
        const parentRow = $(this);
        const parentId = parentRow.data('parent-id');
        const toggleIcon = parentRow.find('.toggle-icon i');

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
                        location.reload();
                    } else {
                        alert(response.message ||
                            "Có lỗi xảy ra khi lưu thay đổi.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi lưu thay đổi.");
                }
            });
        }
    });
    // Lọc danh sách tab khi bật/tắt switch
    $('#toggleStatusMenus').on('change', function() {
        const showAll = $(this).is(':checked');

        $('.tab-item').each(function() {
            const isStatus = $(this).data('status') == 1;
            if (isStatus) {
                $(this).toggleClass('hidden-tab', !showAll);
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
                        location.reload();
                    } else {
                        alert(response.message || "Xóa thất bại.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi xóa tab.");
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
                    location.reload();
                    $('#deleteModal').modal('hide');
                } else {
                    alert(response.message || "Xóa menu thất bại.");
                }
            },
            error: function() {
                alert("Có lỗi xảy ra khi xóa menu.");
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
                        location.reload();
                        $('#deleteModal').modal('hide');
                    } else {
                        alert(response.message || "Xóa menu thất bại.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi xóa menu.");
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
        $('#saveSubMenuChanges').attr('data-menu-id', menuId);
        $('#submenu-tabs').empty();
        $('#submenu-menus').empty();

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['menus/get-submenu']) ?>',
            type: 'GET',
            data: {
                menu_id: menuId
            },
            success: function(response) {
                console.log("🚀 ~ response:", response);
                if (response.success) {
                    // Nạp dữ liệu tab con đã liên kết
                    response.childTabs.forEach(tab => {
                        $('#submenu-tabs').append(
                            `<option value="${tab.id}" selected>${tab.tab_name}</option>`
                        );
                    });

                    // Kiểm tra nếu menu có menu con
                    if (response.childMenus.length > 0) {
                        // Nếu có menu con
                        $('#submenu-menus').parent().show(); // Hiện phần chọn Menu Con
                        $('#submenu-tabs').parent().hide(); // Ẩn phần chọn Tab Con

                        // Nạp dữ liệu menu con đã liên kết
                        response.childMenus.forEach(menu => {
                            $('#submenu-menus').append(
                                `<option value="${menu.id}" selected>${menu.name}</option>`
                            );
                        });

                        // Xử lý các mục tiềm năng chưa được liên kết (potentialMenus)
                        response.potentialMenus.forEach(menu => {
                            $('#submenu-menus').append(
                                `<option value="${menu.id}">${menu.name}</option>`
                            );
                        });
                    } else {
                        // Nếu không có menu con
                        $('#submenu-menus').parent().hide(); // Ẩn phần chọn Menu Con
                        $('#submenu-tabs').parent().show(); // Hiện phần chọn Tab Con

                        // Xử lý các mục tiềm năng chưa được liên kết (potentialTabs)
                        response.potentialTabs.forEach(tab => {
                            $('#submenu-tabs').append(
                                `<option value="${tab.id}">${tab.tab_name}</option>`
                            );
                        });
                    }

                    // Hiển thị modal
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


    // Lưu thay đổi khi nhấn nút "Lưu thay đổi"
    $(document).on('click', '#saveSubMenuChanges', function() {
        var menuId = $(this).attr('data-menu-id'); // Sử dụng attr thay vì data
        // alert(menuId);
        var selectedTabs = $('#submenu-tabs').val();
        console.log("🚀 ~ $ ~ selectedTabs:", selectedTabs);
        var selectedMenus = $('#submenu-menus').val();
        console.log("🚀 ~ $ ~ selectedMenus:", selectedMenus);
        // Gửi dữ liệu tới server để lưu thay đổi
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
                    alert('Cập nhật thành công!');
                    $('#subTabModal').modal('hide');
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Lỗi AJAX: ', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            }
        });
    });
});


$(document).ready(function() {
    // Khi nhấn nút "sửa"
    $(document).on('click', '.edit-btn', function() {
        // Lấy thông tin từ các data-* attributes của button
        var menuId = $(this).data('tab-menu-id');
        var menuName = $(this).data('menu-name');
        var menuType = $(this).data('menu-type');
        var icon = $(this).data('icon');
        var status = $(this).data('status');
        var position = $(this).data('position');

        // Điền các giá trị vào form trong modal
        $('#tabmenuName').val(menuName);
        $('#tabmenuType').val(menuType);
        $('#menustatus').val(status);
        $('#tabMenuPosition').val(position);
        $('#editMenuForm').data('menu-id', menuId);

        // Hiển thị icon đã chọn
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
                $('#editModal').modal('hide');
                location.reload(); // Tải lại trang sau khi lưu
            },
            error: function(xhr, status, error) {
                console.log('Lỗi AJAX: ', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
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
                <h5 class="modal-title" id="editModalLabel">Sửa Menu</h5>
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

<!-- Modal Tab Menu Con -->
<div class="modal fade" id="subMenuModal" tabindex="-1" aria-labelledby="subMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subMenuModalLabel">Chỉnh sửa Menu con</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="submenu-tabs" class="form-label">Chọn Tab Con</label>
                    <select id="submenu-tabs" class="form-select form-multi-select" multiple>
                        <!-- Options sẽ được thêm qua AJAX -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="submenu-menus" class="form-label">Chọn Menu Con</label>
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
                <h5 class="modal-title" id="trashBinModalLabel">Thùng Rác</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Chọn tab bạn muốn khôi phục hoặc xóa hoàn toàn:</p>
                <table class="table table-bordered table-hover table-ui">
                    <thead>
                        <tr>
                            <th>Tên Menu</th>
                            <th style="width: 20%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedMenus = false; ?>
                        <?php foreach ($menus as $tab): ?>
                        <?php if ($tab->deleted == 1): ?>
                        <?php $hasDeletedMenus = true; ?>
                        <tr>
                            <td><?= htmlspecialchars($tab->name) ?></td>
                            <td class="text-nowrap">
                                <button type="button" class="btn btn-warning restore-tab-btn" id="confirm-restore-btn"
                                    data-menu-id="<?= htmlspecialchars($tab->id) ?>">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                                <button type="button" class="btn btn-danger delete-tab-btn" id="delete-permanently-btn"
                                    data-tab-name="<?= htmlspecialchars($tab->name) ?>"
                                    data-menu-id="<?= htmlspecialchars($tab->id) ?>">
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

<!-- Modal Hide tab -->
<div class="modal fade" id="hideModal" tabindex="-1" aria-labelledby="hideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hideModalLabel">Hiện/Ẩn Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p class="pb-0 mb-0">Chọn tab bạn muốn ẩn hoặc hiển thị:</p>
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

<!-- Modal Confirm Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa tab này không? Không thể hoàn tác hành động này.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-menu-id="<?= htmlspecialchars($menuId) ?>">Xóa</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-tab-name="<?= htmlspecialchars($menu->name) ?>"
                    data-menu-id="<?= htmlspecialchars($menuId) ?>">Xóa Vĩnh Viễn</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <small id="toast-timestamp"></small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">Thông Báo</div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a success message
    const successMessage = "<?= Yii::$app->session->getFlash('success') ?>";
    const errorMessage = "<?= Yii::$app->session->getFlash('error') ?>";
    if (successMessage) {
        document.getElementById('toast-body').textContent = successMessage;
        document.getElementById('toast-timestamp').textContent = new Date().toLocaleTimeString();
        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
    if (errorMessage) {
        document.getElementById('toast-body').textContent = errorMessage;
        document.getElementById('toast-timestamp').textContent = new Date().toLocaleTimeString();
        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
});
</script>