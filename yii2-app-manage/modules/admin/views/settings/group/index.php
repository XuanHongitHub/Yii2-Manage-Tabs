<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'List Groups';

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
                                <h4>List of Tabs</h4>
                                <p class="mt-1 f-m-light">Table Tab | Richtext Tab</p>
                            </div>
                            <div
                                class="d-flex flex-wrap justify-content-center align-items-center me-md-2 mb-3 mb-md-0">
                                <a class="btn btn-outline-warning me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#hideModal">
                                    <i class="fas fa-eye me-1"></i> Show/Hidden
                                </a>
                                <a class="btn btn-outline-primary me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#sortModal">
                                    <i class="fas fa-sort-amount-down me-1"></i> Sort Tab
                                </a>
                                <a class="btn btn-danger me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#trashBinModal">
                                    <i class="fas fa-trash me-1"></i> Trash Bin
                                </a>
                                <a class="btn btn-success mb-2" href="<?= \yii\helpers\Url::to(['settings/create']) ?>">
                                    <i class="fas fa-plus me-1"></i> New Tab
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
                                        <th>Tên Nhóm</th>
                                        <th>Icon</th>
                                        <th>Loại Nhóm</th>
                                        <th>Vị Trí</th>
                                        <th>Trạng Thái</th>
                                        <th>Created At</th>
                                        <th style="width: 8%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="columnsContainer">
                                    <?php foreach ($tabGroups as $tabGroup): ?>
                                    <tr>
                                        <td><?= Html::encode($tabGroup->id) ?></td>
                                        <td><?= Html::encode($tabGroup->name) ?></td>
                                        <td>
                                            <div class="col-2 d-flex align-items-center ms-3" id="icon-display">
                                                <svg class="stroke-icon" width="24" height="24">
                                                    <use
                                                        href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= $tabGroup->icon ?>">
                                                    </use>
                                                </svg>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($tabGroup->group_type == 'menu_group'): ?>
                                            <span class="badge badge-light-primary">Sub Menu</span>
                                            <?php else: ?>
                                            <span class="badge badge-light-danger">Sub Tab</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= Html::encode($tabGroup->position) ?></td>
                                        <td><?= $tabGroup->deleted ? 'Yes' : 'No' ?></td>
                                        <td><?= Yii::$app->formatter->asDatetime($tabGroup->created_at) ?></td>
                                        <td class="d-flex text-nowrap justify-content-center">
                                            <button class="btn btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-tab-group-id="<?= $tabGroup->id ?>"
                                                data-group-name="<?= Html::encode($tabGroup->name) ?>"
                                                data-group-type="<?= Html::encode($tabGroup->group_type) ?>"
                                                data-icon="<?= Html::encode($tabGroup->icon) ?>"
                                                data-status="<?= Html::encode($tabGroup->deleted) ?>"
                                                data-position="<?= Html::encode($tabGroup->position) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-tab-id="<?= $tabGroup->id ?>">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </td>

                                    </tr>
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

<!-- Modal sửa Tab Group -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Sửa Tab Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTabGroupForm">
                    <!-- Tên nhóm -->
                    <div class="mb-3">
                        <label for="tabGroupName" class="form-label">Tên nhóm</label>
                        <input type="text" class="form-control" id="tabGroupName" name="name" required>
                    </div>

                    <!-- Loại nhóm -->
                    <div class="mb-3">
                        <label for="tabGroupType" class="form-label">Loại nhóm</label>
                        <select class="form-select" id="tabGroupType" name="group_type" required>
                            <option value="menu_group">Sub Menu</option>
                            <option value="sub_tab">Sub Tab</option>
                        </select>
                    </div>

                    <!-- Icon -->
                    <div class="mb-3">
                        <label for="icon-select" id="icon" class="form-label">Chọn icon</label>
                        <div class="d-flex align-items-center">
                            <div class="col-8">
                                <!-- Hiển thị khu vực chọn icon -->
                                <div id="icon-select-wrapper" class="d-flex align-items-center"
                                    style="cursor: pointer; border: 1px solid #ccc; padding: 8px; border-radius: 8px;">
                                    <span id="selected-icon-label">Chọn icon</span>
                                    <svg id="selected-icon" class="stroke-icon ms-3" width="24" height="24">
                                        <use
                                            href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= reset(array_keys($iconOptions)) ?>">
                                        </use>
                                    </svg>
                                </div>

                                <!-- Danh sách icon -->
                                <div id="icon-list" class="mt-2"
                                    style="display: none !important; overflow-y: auto; max-height: 200px; border: 1px solid #ccc; border-radius: 8px;">
                                    <?php foreach ($iconOptions as $iconValue => $iconLabel): ?>
                                    <div class="icon-item me-2 mb-2" data-icon="<?= Html::encode($iconValue) ?>"
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
                        <label for="tabGroupStatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="tabGroupStatus" name="status" required>
                            <option value="1">Hiển thị</option>
                            <option value="3">Ẩn</option>
                        </select>
                    </div>

                    <!-- Vị trí -->
                    <div class="mb-3">
                        <label for="tabGroupPosition" class="form-label">Vị trí</label>
                        <input type="number" class="form-control" id="tabGroupPosition" name="position" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveTabGroupChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Khi nhấn vào nút sửa
    $('.edit-btn').on('click', function() {
        var groupId = $(this).data('tab-group-id');
        var groupName = $(this).data('group-name');
        var groupType = $(this).data('group-type');
        var icon = $(this).data('icon');
        var status = $(this).data('status');
        var position = $(this).data('position');

        // Điền giá trị vào các trường trong modal
        $('#tabGroupName').val(groupName);
        $('#tabGroupType').val(groupType); // Chọn loại nhóm
        $('#icon-select').val(icon); // Chọn icon
        $('#tabGroupStatus').val(status);
        $('#tabGroupPosition').val(position);

        // Lưu trữ ID nhóm vào form để sử dụng khi gửi
        $('#editTabGroupForm').data('group-id', groupId);
    });

    // Khi nhấn "Lưu thay đổi"
    $('#saveTabGroupChanges').on('click', function() {
        var form = $('#editTabGroupForm');
        var groupId = form.data('group-id');
        var groupName = $('#tabGroupName').val();
        var groupType = $('#tabGroupType').val();
        var icon = $('#icon-select').val();
        var status = $('#tabGroupStatus').val();
        var position = $('#tabGroupPosition').val();

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['settings/update-group']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: groupId,
                name: groupName,
                group_type: groupType,
                icon: icon,
                status: status,
                position: position
            },
            success: function(response) {
                $('#editModal').modal('hide');
                location.reload(); // Tải lại trang để thấy thay đổi
            },
            error: function() {
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            }
        });
    });

    // Cập nhật icon khi thay đổi
    $('#icon-select').change(function() {
        const selectedIcon = $(this).val();
        const iconDisplay = $('#icon-display-edit svg use');
        iconDisplay.attr('href', `<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#${selectedIcon}`);
    });
});

$(document).ready(function() {
    // Mở/Đóng danh sách icon khi nhấn vào khối chọn
    $('#icon-select-wrapper').on('click', function() {
        $('#icon-list').toggle(); // Hiện/ẩn danh sách icon
    });

    // Khi nhấn vào một icon, cập nhật icon đã chọn và thêm viền highlight
    $('#icon-list .icon-item').on('click', function() {
        var selectedIcon = $(this).data('icon'); // Lấy icon đã chọn

        // Cập nhật tên của icon đã chọn vào ô chọn
        $('#selected-icon-label').text('Icon: ' + selectedIcon);

        // Thay đổi icon đã chọn
        $('#selected-icon use').attr('href', '<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#' +
            selectedIcon);

        // Thêm viền highlight cho icon được chọn
        $('#icon-list .icon-item').removeClass('selected');
        $(this).addClass('selected');

        // Cập nhật giá trị cho trường select (nếu bạn cần sử dụng)
        $('#icon-select').val(selectedIcon);

        // Đóng danh sách icon sau khi chọn
        $('#icon-list').hide();
    });

    // Thêm viền highlight cho icon được chọn
    $('#icon-list .icon-item').css({
        'border': '1px solid transparent',
        'border-radius': '8px',
        'padding': '4px'
    });

    $('#icon-list .icon-item.selected').css({
        'border': '2px solid #007bff' // Viền màu xanh dương cho icon được chọn
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

<!-- Modal Trash Bin -->
<div class="modal fade" id="trashBinModal" tabindex="-1" aria-labelledby="trashBinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trashBinModalLabel">Trash Bin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Select the tab you want to restore or delete completely:</p>
                <table class="table table-bordered table-hover table-ui">
                    <thead>
                        <tr>
                            <th>Tab name</th>
                            <th style="width: 20%; text-align: center;">Type</th>
                            <th style="width: 20%; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedTabs = false; ?>
                        <?php foreach ($tabGroups as $tab): ?>
                        <?php if ($tab->deleted == 1): ?>
                        <?php $hasDeletedTabs = true; ?>
                        <tr>
                            <td><?= htmlspecialchars($tab->name) ?></td>
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
                                    data-tab-name="<?= htmlspecialchars($tab->name) ?>"
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hide tab -->
<div class="modal fade" id="hideModal" tabindex="-1" aria-labelledby="hideModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hideModalLabel">Show/Hidden Tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p>Select the tab you want to hide or show:</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tab name</th>
                            <th class="text-center">Show</i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-tabs-list">
                        <?php foreach ($tabGroups as $tab): ?>
                        <?php if ($tab->deleted == 0 || $tab->deleted == 3): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($tab->name) ?>
                            </td>
                            <td class="text-center">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-hide-btn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sort tab -->
<div class="modal fade" id="sortModal" tabindex="-1" aria-labelledby="sortModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sortModalLabel">Sort Tabs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p>Drag and drop to arrange tabs.</p>
                <ul class="list-group" id="sortable-tabs">
                    <?php foreach ($tabGroups as $index => $tab): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center"
                        data-tab-id="<?= $tab->id ?>">
                        <span><?= htmlspecialchars($tab->name) ?></span>
                        <span class="badge bg-secondary"><?= $index + 1 ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-sort-btn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirm Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm delete tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this tab? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete permanently</button>
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
        <div class="toast-body" id="toast-body">Notification</div>
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

    });
});
$(document).ready(function() {
    $('#confirm-hide-btn').click(function() {
        let hideStatus = {};

        $('.toggle-hide-btn').each(function() {
            const tabId = $(this).data('tab-id');
            const isChecked = $(this).is(':checked');
            hideStatus[tabId] = isChecked ? 3 : 0;
        });

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/update-hide-status']) ?>',
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
                    alert(response.message || "An error occurred while saving changes.");
                }
            },
            error: function() {
                alert("An error occurred while saving changes.");
            }
        });
    });
    $("#sortable-tabs").sortable();

    $("#confirm-sort-btn").click(function() {
        var sortedData = [];
        $("#sortable-tabs li").each(function(index) {
            var tabId = $(this).data("tab-id");
            sortedData.push({
                id: tabId,
                position: index + 1
            });
        });

        $.ajax({
            url: '/tabs/update-sort-order',
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
                    alert(response.message || "Error.");
                }
            },
            error: function() {
                alert("Error.");
            }
        });
    });
    $(document).on('click', '#confirm-restore-btn', function() {
        const tabId = $(this).data('tab-id');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/restore-tab']) ?>',
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
                    alert(response.message || "Restore table failed.");
                }
            },
            error: function(error) {
                alert("An error occurred while Restore table.");
            }
        });
    });
    $(document).ready(function() {
        $(document).on('click', '#delete-permanently-btn', function() {
            const tabId = $(this).data('tab-id');
            const tableName = $(this).data('tab-name');

            if (confirm("Are you sure you want to permanently delete this tab?")) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['tabs/delete-permanently-tab']) ?>',
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
                            alert(response.message || "Deletion failed.");
                        }
                    },
                    error: function(error) {
                        alert("An error occurred while deleting the tab.");
                    }
                });
            }
        });
    });

});
$(document).ready(function() {
    $('#confirm-delete-btn').on('click', function() {
        const tabId = $(this).data('tab-id');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/delete-tab']) ?>',
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
                    alert(response.message || "Deleting table failed.");
                }
            },
            error: function(error) {
                alert("An error occurred while deleting table.");
            }
        });
    });

    $('#confirm-delete-permanently-btn').on('click', function() {
        const tabId = $(this).data('tab-id');
        var tableName = '<?= $tableName ?>';

        if (confirm("Are you sure you want to delete permanenttly?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['tabs/delete-permanently-tab']) ?>',
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
                        alert(response.message || "Deleting table failed.");
                    }
                },
                error: function(error) {
                    alert("An error occurred while deleting table.");
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