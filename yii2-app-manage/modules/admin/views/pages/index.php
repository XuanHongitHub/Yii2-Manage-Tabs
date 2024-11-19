<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
$this->title = 'Danh Sách Page';


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
                                <h4>Danh sách Page</h4>
                                <p class="mt-1 f-m-light">Sub Page | Sub Tab</p>
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
                                    data-bs-target="#createPageModal">
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
                                        <th>Tên Page</th>

                                        <th class="text-center">Trạng Thái</th>

                                        <th>Vị Trí</th>
                                        <th>Created At</th>
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
                                            <?= $page->status == 1 ?
                                                        '<span class="badge badge-warning">Ẩn</span>' : '<span class="badge badge-success">Hiện</span>'
                                                    ?>
                                        </td>
                                        <td><?= Html::encode($page->position) ?></td>

                                        <td><?= Yii::$app->formatter->asDatetime($page->created_at) ?></td>
                                        <td class="d-flex text-nowrap justify-content-center">
                                            <button class="btn btn-sm btn-primary me-1 edit-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-tab-page-id="<?= $page->id ?>"
                                                data-page-name="<?= Html::encode($page->name) ?>"
                                                data-status="<?= Html::encode($page->status) ?>"
                                                data-position="<?= Html::encode($page->position) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-page-id="<?= $page->id ?>">
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

<!-- Modal sửa Page  -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Sửa Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPageForm">
                    <!-- Tên Page -->
                    <div class="mb-3">
                        <label for="tabmenuName" class="form-label">Tên Page</label>
                        <input type="text" class="form-control" id="tabmenuName" name="name" required>
                    </div>

                    <!-- Trạng thái -->
                    <div class="mb-3">
                        <label for="tabPagestatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="tabPagestatus" name="status" required>
                            <option value="0">Hiển thị</option>
                            <option value="1">Ẩn</option>
                        </select>
                    </div>

                    <!-- Vị trí -->
                    <div class="mb-3">
                        <label for="tabPagePosition" class="form-label">Vị trí</label>
                        <input type="number" class="form-control" id="tabPagePosition" name="position" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveTabPageChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    // Khi nhấn nút "sửa"
    $('.edit-btn').on('click', function() {
        // Lấy thông tin từ các data-* attributes của button
        var menuId = $(this).data('tab-page-id');
        var menuName = $(this).data('page-name');
        var status = $(this).data('status');
        var position = $(this).data('position');

        // Điền các giá trị vào form trong modal
        $('#tabmenuName').val(menuName);
        $('#tabPagestatus').val(status);
        $('#tabPagePosition').val(position);
        $('#editPageForm').data('page-id', menuId);

    });

    // Lưu thay đổi page
    $('#saveTabPageChanges').on('click', function() {
        var form = $('#editPageForm');
        var menuId = form.data('page-id');
        var menuName = $('#tabmenuName').val();
        var status = $('#tabPagestatus').val();
        var position = $('#tabPagePosition').val();
        var selectedSubPages = $('#selectedSubPages').val(); // Lấy các page con đã chọn

        // Gửi dữ liệu tới server để cập nhật page
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['create-or-update-page']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: menuId,
                name: menuName,
                status: status,
                position: position,
                selected_submenus: selectedSubPages // Gửi danh sách page con đã chọn
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
                            <th>Tên Page</th>
                            <th style="width: 20%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedPages = false; ?>
                        <?php foreach ($pages as $tab): ?>
                        <?php if ($tab->deleted == 1): ?>
                        <?php $hasDeletedPages = true; ?>
                        <tr>
                            <td><?= htmlspecialchars($tab->name) ?></td>

                            <td class="text-nowrap">
                                <button type="button" class="btn btn-warning restore-tab-btn" id="confirm-restore-btn"
                                    data-page-id="<?= htmlspecialchars($tab->id) ?>">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                                <button type="button" class="btn btn-danger delete-tab-btn" id="delete-permanently-btn"
                                    data-tab-name="<?= htmlspecialchars($tab->name) ?>"
                                    data-page-id="<?= htmlspecialchars($tab->id) ?>">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!$hasDeletedPages): ?>
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
                <h5 class="modal-title" id="hideModalLabel">Hiện/Ẩn Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p class="pb-0 mb-0">Chọn tab bạn muốn ẩn hoặc hiển thị:</p>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>Tên Page</th>
                            <th class="text-center" style="width: 12%">Hiện</i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-index">
                        <?php foreach ($pages as $page): ?>
                        <?php if ($page->deleted != 1): ?>
                        <tr>
                            <td class="py-0">
                                <?= htmlspecialchars($page->name) ?>
                            </td>

                            <td class="py-0" class="text-center">
                                <label class="switch mb-0 mt-1">
                                    <input class="form-check-input toggle-hide-btn" type="checkbox"
                                        data-page-id="<?= htmlspecialchars($page->id) ?>"
                                        <?php if ($page->status == 0): ?> checked <?php endif; ?>>
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
                <p>Kéo và thả để sắp xếp các page.</p>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="toggleStatusPages" checked>
                    <label class="form-check-label" for="toggleStatusPages">Hiển thị Page đã ẩn</label>
                </div>
                <ul class="list-group" id="sortable-tabs">
                    <?php foreach ($pages as $index => $page): ?>
                    <?php if ($page->deleted != 1): ?>
                    <li class="list-page-item d-flex justify-content-between align-items-center tab-item"
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
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa tab này không? Không thể hoàn tác hành động này.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-page-id="<?= htmlspecialchars($menuId) ?>">Xóa</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-tab-name="<?= htmlspecialchars($page->name) ?>"
                    data-page-id="<?= htmlspecialchars($menuId) ?>">Xóa Vĩnh Viễn</button>
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
            "sLengthPage": "Hiển thị _MENU_ mục",
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
            const menuId = $(this).data('page-id');
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
    $('#toggleStatusPages').on('change', function() {
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
            var menuId = $(this).data("page-id");
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
        const menuId = $(this).data('page-id');

        if (confirm("Bạn có chắc chắn muốn khôi phục page này không?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/restore-page']) ?>',
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
        const menuId = $(this).data('page-id');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn page này không?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/delete-permanently-page']) ?>',
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
        const menuId = $(this).data('page-id');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['menus/delete-page']) ?>',
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
                    alert(response.message || "Xóa page thất bại.");
                }
            },
            error: function() {
                alert("Có lỗi xảy ra khi xóa page.");
            }
        });
    });

    $('#confirm-delete-permanently-btn').on('click', function() {
        const menuId = $(this).data('page-id');

        if (confirm("Bạn có chắc chắn muốn xóa hoàn toàn không?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['menus/delete-permanently-page']) ?>',
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
            const menuId = this.getAttribute("data-page-id");
            confirmDeleteBtn.setAttribute("data-page-id", menuId);
            confirmDeletePermanentlyBtn.setAttribute("data-page-id", menuId);
        });
    });
});
</script>