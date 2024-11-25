<?php

use yii\helpers\Html;
use app\models\Menu;

/** @var yii\web\View $this */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'List Pages';

?>
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
                                <h4>Danh sách Pages</h4>
                                <p class="mt-1 f-m-light">Table Page | Richtext Page</p>
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
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th class="text-center">Loại</th>
                                        <th class="text-center">Menu</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th>Vị trí</th>
                                        <th>Ngày tạo</th>
                                        <th style="width: 8%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="columnsContainer">
                                    <?php foreach ($pages as $page): ?>
                                        <?php if ($page->deleted != 1): ?>
                                            <tr>
                                                <td><?= Html::encode($page->id) ?></td>
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

                                                </td>
                                                <td class="text-center">
                                                    <?= $page->status == 1 ?
                                                        '<span class="badge badge-warning">Ẩn</span>' : '<span class="badge badge-success">Hiện</span>'
                                                    ?>
                                                </td>
                                                <td><?= Html::encode($page->position) ?></td>
                                                <td><?= Html::encode(Yii::$app->formatter->asDate($page->created_at)) ?></td>
                                                <td class="d-flex text-nowrap justify-content-center">
                                                    <button class="btn btn-primary btn-sm edit-btn me-1" data-bs-toggle="modal"
                                                        data-bs-target="#editModal"
                                                        data-page-id="<?= htmlspecialchars($page->id) ?>"
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
            </div>
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
            var position = $('#editPosition').val();

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['pages/update-page']) ?>',
                type: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    pageId: pageId,
                    menu_id: menuId,
                    status: status,
                    position: position
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
</script>

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
                            <th>Tên Page</th>
                            <th style="width: 20%; text-align: center;">Loại</th>
                            <th style="width: 20%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedTabs = false; ?>
                        <?php foreach ($pages as $page): ?>
                            <?php if ($page->deleted == 1): ?>
                                <?php $hasDeletedTabs = true; ?>
                                <tr>
                                    <td><?= htmlspecialchars($page->name) ?></td>
                                    <td class="text-center">
                                        <?php if ($page->type == 'table'): ?>
                                            <span class="badge badge-light-primary">Table</span>
                                        <?php else: ?>
                                            <span class="badge badge-light-danger">Richtext</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <button type="button" class="btn btn-warning restore-page-btn" id="confirm-restore-btn"
                                            data-page-id="<?= htmlspecialchars($page->id) ?>">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger delete-page-btn" id="delete-permanently-btn"
                                            data-page-name="<?= htmlspecialchars($page->name) ?>"
                                            data-page-id="<?= htmlspecialchars($page->id) ?>">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!$hasDeletedTabs): ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted">
                                    <em>Không có gì ở đây.</em>
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
                <h4 class="modal-title" id="hideModalLabel">Hiện/Ẩn Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p class="pb-0 mb-0">Chọn page bạn muốn ẩn hoặc hiển thị:</p>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>Tên Page</th>
                            <th class="text-center" style="width: 45%">Loại</th>
                            <th class="text-center" style="width: 8%">Hiện</i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-index">
                        <?php foreach ($pages as $page): ?>
                            <?php if ($page->deleted == 0): ?>
                                <tr>
                                    <td class="py-0">
                                        <?= htmlspecialchars($page->name) ?>
                                    </td>
                                    <td class="text-center py-0">
                                        <?php if ($page->type == 'table'): ?>
                                            <span class="badge badge-light-primary">Table</span>
                                        <?php else: ?>
                                            <span class="badge badge-light-danger">Richtext</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-0" class="text-center">
                                        <label class="switch mb-0 mt-1">
                                            <input class="form-check-input toggle-hide-btn" type="checkbox"
                                                data-page-id="<?= htmlspecialchars($page->id) ?>"
                                                <?php if ($page->deleted == 0): ?> checked <?php endif; ?>>
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
                <p>Kéo và thả để sắp xếp các page.</p>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="toggleStatusTabs" checked>
                    <label class="form-check-label" for="toggleStatusTabs">Hiển thị page đã ẩn</label>
                </div>
                <ul class="list-group" id="sortable-pages">
                    <?php foreach ($pages as $index => $page): ?>
                        <?php if ($page->deleted != 1): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center page-item"
                                data-page-id="<?= $page->id ?>" data-status="<?= $page->status ?>">
                                <span><?= htmlspecialchars($page->name) ?></span>
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
                    data-page-id="<?= htmlspecialchars($pageId) ?>">Xóa</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-page-name="<?= htmlspecialchars($page->name) ?>"
                    data-page-id="<?= htmlspecialchars($pageId) ?>">Xóa Vĩnh Viễn</button>
            </div>
        </div>
    </div>
</div>

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
                    url: '<?= \yii\helpers\Url::to(['pages/update-sort-order']) ?>',
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
                    url: '<?= \yii\helpers\Url::to(['pages/restore-page']) ?>',
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
            const tableName = $(this).data('page-name');

            if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn page này không?")) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['pages/delete-permanently-page']) ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        pageId: pageId,
                        tableName: tableName,
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
                url: '<?= \yii\helpers\Url::to(['pages/delete-page']) ?>',
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
            const tableName = $(this).data('page-name');

            if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn không?")) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['pages/delete-permanently-page']) ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        pageId: pageId,
                        tableName: tableName,
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
</script>