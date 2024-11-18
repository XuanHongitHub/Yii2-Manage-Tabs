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
                                <p class="mt-1 f-m-light">Sub Menu | Sub Tab</p>
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
                            <table class="display border table-bordered dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Menu</th>
                                        <th>Icon</th>
                                        <th>Loại Menu</th>
                                        <th class="text-center">Trạng Thái</th>

                                        <th>Vị Trí</th>
                                        <th>Created At</th>
                                        <th style="width: 8%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="columnsContainer">
                                    <?php foreach ($tabMenus as $tabMenu): ?>
                                    <?php if ($tabMenu->deleted != 1): ?>

                                    <tr>
                                        <td><?= Html::encode($tabMenu->id) ?></td>
                                        <td><?= Html::encode($tabMenu->name) ?></td>
                                        <td>
                                            <div class="col-2 d-flex align-items-center ms-3" id="icon-display">
                                                <svg class="stroke-icon" width="24" height="24">
                                                    <use
                                                        href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $tabMenu->icon ?>">
                                                    </use>
                                                </svg>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($tabMenu->menu_type == 'menu_group'): ?>
                                            <span class="badge badge-light-primary">Menu Con</span>
                                            <?php elseif ($tabMenu->menu_type == 'menu_single'): ?>
                                            <span class="badge badge-light-danger">Tab Con</span>
                                            <?php else: ?>
                                            <span class="badge badge-light-dark">Không</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $tabMenu->status == 1 ?
                                                        '<span class="badge badge-warning">Ẩn</span>' : '<span class="badge badge-success">Hiện</span>'
                                                    ?>
                                        </td>
                                        <td><?= Html::encode($tabMenu->position) ?></td>

                                        <td><?= Yii::$app->formatter->asDatetime($tabMenu->created_at) ?></td>
                                        <td class="d-flex text-nowrap justify-content-center">
                                            <button class="btn btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-tab-menu-id="<?= $tabMenu->id ?>"
                                                data-menu-name="<?= Html::encode($tabMenu->name) ?>"
                                                data-menu-type="<?= Html::encode($tabMenu->menu_type) ?>"
                                                data-icon="<?= Html::encode($tabMenu->icon) ?>"
                                                data-status="<?= Html::encode($tabMenu->status) ?>"
                                                data-position="<?= Html::encode($tabMenu->position) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info me-1" data-bs-toggle="modal"
                                                data-bs-target="#subTabModal" data-menu-id="<?= $tabMenu->id ?>">
                                                <i class="fas fa-cogs"></i> Tab Con
                                            </button>
                                            <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-menu-id="<?= $tabMenu->id ?>">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
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

                    <!-- Loại Menu -->
                    <div class="mb-3">
                        <label for="tabmenuType" class="form-label">Loại Menu</label>
                        <select class="form-select" id="tabmenuType" name="menu_type" required>
                            <option value="none">-- Không --</option>
                            <option value="menu_group">Sub Menu</option>
                            <option value="menu_single">Sub Tab</option>
                        </select>
                    </div>

                    <!-- Icon -->
                    <div class="mb-3">
                        <label for="icon-select" class="form-label">Chọn icon</label>
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
                        <label for="tabMenustatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="tabMenustatus" name="status" required>
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
<div class="modal fade" id="subTabModal" tabindex="-1" aria-labelledby="subTabModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subTabModalLabel">Tab Menu Con</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="subTabForm">
                    <div class="mb-3">
                        <label for="selectedSubItems" class="form-label">Các Tab/Menu Con Đã Chọn</label>
                        <ul id="selectedSubItems" class="list-group" name="selected_subitems[]">
                            <li class="list-group-item text-center">
                                <button type="button" class="btn btn-link" id="addNewItem">+ Thêm Tab/Menu</button>
                            </li>
                            <!-- Các mục đã chọn sẽ được hiển thị ở đây -->
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveSubTabChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function() {
    $('.btn-info').on('click', function() {
        var menuId = $(this).data('menu-id');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['menus/get-child']) ?>',
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: menuId
            },
            success: function(response) {
                var parsedResponse = JSON.parse(response);
                console.log("🚀 ~ parsedResponse:", parsedResponse);


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
    $('.edit-btn').on('click', function() {
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
        $('#tabMenustatus').val(status);
        $('#tabMenuPosition').val(position);
        $('#editMenuForm').data('menu-id', menuId);

        // Hiển thị icon đã chọn
        $('#selected-icon').html('<use href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#' +
            icon + '"></use>');
        $('#selected-icon-label').text(icon);
    });

    // Lưu thay đổi menu
    $('#saveTabMenuChanges').on('click', function() {
        var form = $('#editMenuForm');
        var menuId = form.data('menu-id');
        var menuName = $('#tabmenuName').val();
        var menuType = $('#tabmenuType').val();
        var icon = $('#selected-icon-label').text(); // Lấy icon đã chọn
        var status = $('#tabMenustatus').val();
        var position = $('#tabMenuPosition').val();
        var selectedSubMenus = $('#selectedSubMenus').val(); // Lấy các menu con đã chọn

        // Gửi dữ liệu tới server để cập nhật menu
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['create-or-update-menu']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: menuId,
                name: menuName,
                menu_type: menuType,
                icon: icon,
                status: status,
                position: position,
                selected_submenus: selectedSubMenus // Gửi danh sách menu con đã chọn
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
                            <th style="width: 20%; text-align: center;">Loại</th>
                            <th style="width: 20%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedMenus = false; ?>
                        <?php foreach ($tabMenus as $tab): ?>
                        <?php if ($tab->deleted == 1): ?>
                        <?php $hasDeletedMenus = true; ?>
                        <tr>
                            <td><?= htmlspecialchars($tab->name) ?></td>
                            <td class="text-center">
                                <?php if ($tabMenu->menu_type == 'menu_group'): ?>
                                <span class="badge badge-light-primary">Menu Con</span>
                                <?php elseif ($tabMenu->menu_type == 'menu_single'): ?>
                                <span class="badge badge-light-danger">Tab Con</span>
                                <?php else: ?>
                                <span class="badge badge-light-dark">Không</span>
                                <?php endif; ?>
                            </td>
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
                            <th class="text-center" style="width: 45%">Loại</th>
                            <th class="text-center" style="width: 8%">Hiện</i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-tabs-list">
                        <?php foreach ($tabMenus as $menu): ?>
                        <?php if ($menu->deleted != 1): ?>
                        <tr>
                            <td class="py-0">
                                <?= htmlspecialchars($menu->name) ?>
                            </td>
                            <td class="text-center py-0">
                                <?php if ($menu->menu_type == 'menu_group'): ?>
                                <span class="badge badge-light-primary">Menu Con</span>
                                <?php elseif ($menu->menu_type == 'menu_single'): ?>
                                <span class="badge badge-light-danger">Tab Con</span>
                                <?php else: ?>
                                <span class="badge badge-light-dark">Không</span>
                                <?php endif; ?>
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
                <h5 class="modal-title" id="sortModalLabel">Sắp Xếp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p>Kéo và thả để sắp xếp các menu.</p>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="toggleStatusMenus" checked>
                    <label class="form-check-label" for="toggleStatusMenus">Hiển thị Menu đã ẩn</label>
                </div>
                <ul class="list-group" id="sortable-tabs">
                    <?php foreach ($tabMenus as $index => $menu): ?>
                    <?php if ($menu->deleted != 1): ?>
                    <li class="list-menu-item d-flex justify-content-between align-items-center tab-item"
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



<script>
$(document).ready(function() {
    $('.dataTable').DataTable({
        order: [],
        columnDefs: [{
            orderable: false,
            targets: -1
        }],
        "lengthChange": false,
        "autoWidth": false,
        "responsive": true,
        "paging": true,
        "searching": true,
        "ordering": true,
        "language": {
            "sEmptyTable": "Không có dữ liệu",
            "sInfo": "Đang hiển thị _START_ đến _END_ trong tổng số _TOTAL_ mục",
            "sInfoEmpty": "Đang hiển thị 0 đến 0 trong tổng số 0 mục",
            "sInfoFiltered": "(Được lọc từ _MAX_ mục)",
            "sInfoPostFix": "",
            "sLengthMenu": "Hiển thị _MENU_ mục",
            "sLoadingRecords": "Đang tải...",
            "sProcessing": "Đang xử lý...",
            "sSearch": "Tìm kiếm:",
            "sZeroRecords": "Không tìm thấy kết quả nào",
            "oPaginate": {
                "sFirst": "Đầu tiên",
                "sLast": "Cuối cùng",
                "sNext": "Tiếp theo",
                "sPrevious": "Trước"
            },
            "oAria": {
                "sSortAscending": ": Sắp xếp cột tăng dần",
                "sSortDescending": ": Sắp xếp cột giảm dần"
            }
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
                        alert(response.message || "Có lỗi xảy ra khi lưu thay đổi.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi lưu thay đổi.");
                }
            });
        }
    });
    $("#sortable-tabs").sortable();

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

    $("#confirm-sort-btn").click(function() {
        var sortedData = [];
        $("#sortable-tabs li").each(function(index) {
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
</script>