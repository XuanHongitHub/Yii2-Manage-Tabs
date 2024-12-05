<?php

use yii\helpers\Html;
use app\models\Menu;
use app\assets\Select2Asset;

/** @var yii\web\View $this */

Select2Asset::register($this);

$this->title = 'Danh sách Page';

$this->registerCssFile('@web/css/datatables.css', ['depends' => [\yii\web\YiiAsset::class]]);

?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-auto mb-3 mb-md-0 text-center text-md-start">
                <h4>Danh sách Pages</h4>
                <p class="mt-1 f-m-light">Table Page | Richtext Page</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center align-items-center me-md-2 mb-3 mb-md-0">
                <a class="btn btn-outline-warning me-2 mb-2" href="#" data-bs-toggle="modal"
                    data-bs-target="#hideModal">
                    <i class="fas fa-eye me-1"></i> Hiện/Ẩn
                </a>
                <a class="btn btn-outline-primary me-2 mb-2" href="#" data-bs-toggle="modal"
                    data-bs-target="#sortModal">
                    <i class="fas fa-sort-amount-down me-1"></i> Sắp Xếp
                </a>
                <a class="btn btn-danger me-2 mb-2" href="#" data-bs-toggle="modal" data-bs-target="#trashBinModal">
                    <i class="fas fa-trash me-1"></i> Thùng Rác
                </a>
                <a class="btn btn-success mb-2" href="<?= \yii\helpers\Url::to(['pages/create']) ?>">
                    <i class="fas fa-plus me-1"></i> Thêm Page
                </a>
            </div>

        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">
            <table class="display border table-bordered dataTable no-footer">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th class="text-center">Loại</th>
                        <th>Menu Cha</th>
                        <th class="text-center">Trạng thái</th>
                        <th style="width: 12%">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="columnsContainer">
                    <?php foreach ($pages as $page): ?>
                        <?php if ($page->deleted != 1): ?>
                            <tr>
                                <td><?= Html::encode($page->name) ?></td>
                                <td class="text-center">
                                    <?php if ($page->type == 'table'): ?>
                                        <span class="badge badge-light-primary">Table</span>
                                    <?php else: ?>
                                        <span class="badge badge-light-danger">Richtext</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?= $page && $page->menu_id ? Menu::findOne($page->menu_id)->name : ''; ?>
                                </td>

                                <td class="text-center">
                                    <?= $page->status == 1 ?
                                        '<span class="badge badge-warning">Ẩn</span>' : '<span class="badge badge-success">Hiện</span>'
                                        ?>
                                </td>
                                <td class="text-center text-nowrap">
                                    <button class="btn btn-primary btn-sm edit-btn me-1" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-page-id="<?= htmlspecialchars($page->id) ?>"
                                        data-page-name="<?= htmlspecialchars($page->name) ?>"
                                        data-page-type="<?= htmlspecialchars($page->type) ?>"
                                        data-menu-id="<?= htmlspecialchars($page->menu_id) ?>"
                                        data-status="<?= htmlspecialchars($page->status) ?>"
                                        data-position="<?= htmlspecialchars($page->position) ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        class="btn btn-danger btn-sm delete-btn"
                                        data-page-id="<?= htmlspecialchars($page->id) ?>">
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
<!-- Modal sửa -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Sửa Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTabForm">
                    <!-- Không cho phép sửa tên Page -->
                    <div class="mb-3">
                        <label for="edittableName" class="form-label">Tên Page</label>
                        <input type="text" class="form-control" id="edittableName" name="name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="editTabType" class="form-label">Loại Page</label>
                        <input class="form-control" id="editTabType" name="type" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="editMenu" class="form-label">Menu</label>
                        <select class="form-select" id="editMenu" name="menu_id">
                            <option class="txt-primary" value="" <?= $page->menu_id === null ? 'selected' : '' ?>>--
                                Không --</option>

                            <?php foreach ($menus as $menu): ?>
                                <option value="<?= $menu->id ?>" <?= $menu->id == $page->menu_id ? 'selected' : '' ?>>
                                    <?= Html::encode($menu->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="editStatus" name="status">
                            <option value="0" <?= $page->status == 0 ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="1" <?= $page->status == 1 ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editPosition" class="form-label">Vị trí</label>
                        <input type="number" class="form-control" id="editPosition" name="position"
                            value="<?= $page->position ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveTabChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Khi nhấn vào nút sửa
        $('.edit-btn').on('click', function () {
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

        $('#saveTabChanges').on('click', function () {
            var form = $('#editTabForm');
            var pageId = form.data('page-id');
            var menuId = $('#editMenu').val();
            var status = $('#editStatus').val();
            var position = $('#editPosition').val();

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['pages/update-page']) ?>',
                type: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId: pageId,
                    menuId: menuId,
                    status: status,
                    position: position
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
    });
</script>



<script>
    $(document).ready(function () {
        $('#confirm-hide-btn').click(function () {
            let hideStatus = {};

            $('.toggle-hide-btn').each(function () {
                const pageId = $(this).data('page-id');
                const isChecked = $(this).is(':checked');
                hideStatus[pageId] = isChecked ? 0 : 3;
            });

            if (confirm("Xác nhận thao tác?")) {

                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['pages/update-hide-status']) ?>',
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
        $("#sortable-pages").sortable();

        // Lọc danh sách page khi bật/tắt switch
        $('#toggleStatusTabs').on('change', function () {
            const showAll = $(this).is(':checked');

            $('.page-item').each(function () {
                const isStatus = $(this).data('status') == 1;
                if (isStatus) {
                    $(this).toggleClass('hidden-page', !showAll);
                }
            });
        });

        $("#confirm-sort-btn").click(function () {
            var sortedData = [];
            $("#sortable-pages li").each(function (index) {
                var pageId = $(this).data("page-id");
                sortedData.push({
                    id: pageId,
                    position: index + 1
                });
            });
            if (confirm("Xác nhận sắp xếp?")) {

                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['pages/update-sort-order']) ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        pages: sortedData
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                            $('#sortModal').modal('hide');
                        } else {
                            alert(response.message || "Lỗi.");
                        }
                    },
                    error: function () {
                        alert("Lỗi.");
                    }
                });
            }
        });
        $(document).on('click', '#confirm-restore-btn', function () {
            const pageId = $(this).data('page-id');

            if (confirm("Bạn có chắc chắn muốn khôi phục page này không?")) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['pages/restore-page']) ?>',
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

        $(document).on('click', '#delete-permanently-btn', function () {
            const pageId = $(this).data('page-id');
            const pageName = $(this).data('page-name');

            if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn page này không?")) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['pages/delete-permanently-page']) ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        pageId: pageId,
                        pageName: pageName,
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
                url: '<?= \yii\helpers\Url::to(['pages/delete-page']) ?>',
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
                    url: '<?= \yii\helpers\Url::to(['pages/delete-permanently-page']) ?>',
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
</script>

<?php
$jsFiles = [
    'js/libs/jquery.dataTables.min.js',
    'js/libs/datatable.custom.js',
    'js/libs/datatable.custom1.js',
];

foreach ($jsFiles as $js) {
    $this->registerJsFile($js, ['depends' => [\yii\web\YiiAsset::class]]);
}
?>