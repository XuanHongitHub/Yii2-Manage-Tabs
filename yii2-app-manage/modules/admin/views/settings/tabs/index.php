<?php

use yii\helpers\Html;
use app\models\TabMenus;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'List Tabs';

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
                                <h4>Danh sách Tabs</h4>
                                <p class="mt-1 f-m-light">Table Tab | Richtext Tab</p>
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
                                <a class="btn btn-success mb-2"
                                    href="<?= \yii\helpers\Url::to(['settings/tabs-create']) ?>">
                                    <i class="fas fa-plus me-1"></i> Thêm Tab
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
                                        <th class="text-center">Group</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th>Vị trí</th>
                                        <th>Ngày tạo</th>
                                        <th style="width: 8%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="columnsContainer">
                                    <?php foreach ($tabs as $tab): ?>
                                        <?php if ($tab->deleted != 1): ?>
                                            <tr>
                                                <td><?= Html::encode($tab->id) ?></td>
                                                <td><?= Html::encode($tab->tab_name) ?></td>
                                                <td class="text-center">
                                                    <?php if ($tab->tab_type == 'table'): ?>
                                                        <span class="badge badge-light-primary">Table</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-light-danger">Richtext</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= $tab && $tab->menu_id ? TabMenus::findOne($tab->menu_id)->name : ''; ?>
                                                </td>

                                                </td>
                                                <td class="text-center">
                                                    <?= $tab->deleted == 3 ?
                                                        '<span class="badge badge-warning">Ẩn</span>' : '<span class="badge badge-success">Hiện</span>'
                                                    ?>
                                                </td>
                                                <td><?= Html::encode($tab->position) ?></td>
                                                <td><?= Html::encode(Yii::$app->formatter->asDate($tab->created_at)) ?></td>
                                                <td class="d-flex text-nowrap justify-content-center">
                                                    <button class="btn btn-primary btn-sm edit-btn me-1" data-bs-toggle="modal"
                                                        data-bs-target="#editModal"
                                                        data-tab-id="<?= htmlspecialchars($tab->id) ?>"
                                                        data-tab-name="<?= htmlspecialchars($tab->tab_name) ?>"
                                                        data-tab-type="<?= htmlspecialchars($tab->tab_type) ?>"
                                                        data-group-id="<?= htmlspecialchars($tab->menu_id) ?>"
                                                        data-status="<?= htmlspecialchars($tab->deleted) ?>"
                                                        data-position="<?= htmlspecialchars($tab->position) ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        class="btn btn-danger btn-sm delete-btn"
                                                        data-tab-id="<?= htmlspecialchars($tab->id) ?>">
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
                <h5 class="modal-title" id="editModalLabel">Sửa Tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTabForm">
                    <!-- Không cho phép sửa tên Tab -->
                    <div class="mb-3">
                        <label for="editTabName" class="form-label">Tên Tab</label>
                        <input type="text" class="form-control" id="editTabName" name="tab_name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="editTabType" class="form-label">Loại Tab</label>
                        <input class="form-control" id="editTabType" name="tab_type" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="editGroup" class="form-label">Group</label>
                        <select class="form-select" id="editGroup" name="menu_id">
                            <option class="txt-primary" value=""
                                <?= $tab->menu_id === null ? 'selected' : '' ?>>-- Không --</option>

                            <?php foreach ($tabMenus as $group): ?>
                                <option value="<?= $group->id ?>" <?= $group->id == $tab->menu_id ? 'selected' : '' ?>>
                                    <?= Html::encode($group->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="editStatus" name="status">
                            <option value="0" <?= $tab->deleted == 0 ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="3" <?= $tab->deleted == 3 ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editPosition" class="form-label">Vị trí</label>
                        <input type="number" class="form-control" id="editPosition" name="position"
                            value="<?= $tab->position ?>">
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
            var tabId = $(this).data('tab-id');
            var tabName = $(this).data('tab-name');
            var tabType = $(this).data('tab-type');
            var menuId = $(this).data('group-id');
            var status = $(this).data('status');
            var position = $(this).data('position');

            $('#editTabName').val(tabName);
            $('#editTabType').val(tabType);
            $('#editGroup').val(menuId);
            $('#editStatus').val(status);
            $('#editPosition').val(position);
            $('#editTabForm').data('tab-id', tabId);
        });

        $('#saveTabChanges').on('click', function() {
            var form = $('#editTabForm');
            var tabId = form.data('tab-id');
            var menuId = $('#editGroup').val();
            var status = $('#editStatus').val();
            var position = $('#editPosition').val();

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['settings/update-tab']) ?>',
                type: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    tab_id: tabId,
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
                <h5 class="modal-title" id="trashBinModalLabel">Thùng Rác</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Chọn tab bạn muốn khôi phục hoặc xóa hoàn toàn:</p>
                <table class="table table-bordered table-hover table-ui">
                    <thead>
                        <tr>
                            <th>Tên Tab</th>
                            <th style="width: 20%; text-align: center;">Loại</th>
                            <th style="width: 20%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedTabs = false; ?>
                        <?php foreach ($tabs as $tab): ?>
                            <?php if ($tab->deleted == 1): ?>
                                <?php $hasDeletedTabs = true; ?>
                                <tr>
                                    <td><?= htmlspecialchars($tab->tab_name) ?></td>
                                    <td class="text-center">
                                        <?php if ($tab->tab_type == 'table'): ?>
                                            <span class="badge badge-light-primary">Table</span>
                                        <?php else: ?>
                                            <span class="badge badge-light-danger">Richtext</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <button type="button" class="btn btn-warning restore-tab-btn" id="confirm-restore-btn"
                                            data-tab-id="<?= htmlspecialchars($tab->id) ?>">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger delete-tab-btn" id="delete-permanently-btn"
                                            data-tab-name="<?= htmlspecialchars($tab->tab_name) ?>"
                                            data-tab-id="<?= htmlspecialchars($tab->id) ?>">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!$hasDeletedTabs): ?>
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
                <h5 class="modal-title" id="hideModalLabel">Hiện/Ẩn Tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p class="pb-0 mb-0">Chọn tab bạn muốn ẩn hoặc hiển thị:</p>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>Tên Tab</th>
                            <th class="text-center" style="width: 45%">Loại</th>
                            <th class="text-center" style="width: 8%">Hiện</i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-tabs-list">
                        <?php foreach ($tabs as $tab): ?>
                            <?php if ($tab->deleted == 0 || $tab->deleted == 3): ?>
                                <tr>
                                    <td class="py-0">
                                        <?= htmlspecialchars($tab->tab_name) ?>
                                    </td>
                                    <td class="text-center py-0">
                                        <?php if ($tab->tab_type == 'table'): ?>
                                            <span class="badge badge-light-primary">Table</span>
                                        <?php else: ?>
                                            <span class="badge badge-light-danger">Richtext</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-0" class="text-center">
                                        <label class="switch mb-0 mt-1">
                                            <input class="form-check-input toggle-hide-btn" type="checkbox"
                                                data-tab-id="<?= htmlspecialchars($tab->id) ?>"
                                                <?php if ($tab->deleted == 0): ?> checked <?php endif; ?>>
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
                <p>Kéo và thả để sắp xếp các tab.</p>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="toggleDeletedTabs" checked>
                    <label class="form-check-label" for="toggleDeletedTabs">Hiển thị tab đã ẩn</label>
                </div>
                <ul class="list-group" id="sortable-tabs">
                    <?php foreach ($tabs as $index => $tab): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center tab-item"
                            data-tab-id="<?= $tab->id ?>" data-deleted="<?= $tab->deleted ?>">
                            <span><?= htmlspecialchars($tab->tab_name) ?></span>
                            <span class="badge bg-secondary"><?= $index + 1 ?></span>
                        </li>
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
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Xóa</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-tab-name="<?= htmlspecialchars($tab->tab_name) ?>"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Xóa Vĩnh Viễn</button>
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
                const tabId = $(this).data('tab-id');
                const isChecked = $(this).is(':checked');
                hideStatus[tabId] = isChecked ? 0 : 3;
            });

            if (confirm("Xác nhận thao tác?")) {

                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['settings/update-hide-status']) ?>',
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
        $('#toggleDeletedTabs').on('change', function() {
            const showAll = $(this).is(':checked');

            $('.tab-item').each(function() {
                const isDeleted = $(this).data('deleted') == 3;
                if (isDeleted) {
                    $(this).toggleClass('hidden-tab', !showAll);
                }
            });
        });

        $("#confirm-sort-btn").click(function() {
            var sortedData = [];
            $("#sortable-tabs li").each(function(index) {
                var tabId = $(this).data("tab-id");
                sortedData.push({
                    id: tabId,
                    position: index + 1
                });
            });
            if (confirm("Xác nhận sắp xếp?")) {

                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['settings/update-sort-order']) ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        tabs: sortedData
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
            const tabId = $(this).data('tab-id');

            if (confirm("Bạn có chắc chắn muốn khôi phục tab này không?")) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['settings/restore-tab']) ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        tabId: tabId,
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
            const tabId = $(this).data('tab-id');
            const tableName = $(this).data('tab-name');

            if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn tab này không?")) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['settings/delete-permanently-tab']) ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        tabId: tabId,
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
                        alert("Có lỗi xảy ra khi xóa tab.");
                    }
                });
            }
        });

        $('#confirm-delete-btn').on('click', function() {
            const tabId = $(this).data('tab-id');

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['settings/delete-tab']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    tabId: tabId,
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                        $('#deleteModal').modal('hide');
                    } else {
                        alert(response.message || "Xóa tab thất bại.");
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi xóa tab.");
                }
            });
        });

        $('#confirm-delete-permanently-btn').on('click', function() {
            const tabId = $(this).data('tab-id');
            const tableName = $(this).data('tab-name');

            if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn không?")) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['settings/delete-permanently-tab']) ?>',
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        tabId: tabId,
                        tableName: tableName,
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                            $('#deleteModal').modal('hide');
                        } else {
                            alert(response.message || "Xóa tab thất bại.");
                        }
                    },
                    error: function() {
                        alert("Có lỗi xảy ra khi xóa tab.");
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
                const tabId = this.getAttribute("data-tab-id");
                confirmDeleteBtn.setAttribute("data-tab-id", tabId);
                confirmDeletePermanentlyBtn.setAttribute("data-tab-id", tabId);
            });
        });
    });
</script>