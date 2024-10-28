<?php

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
/** @var app\models\Tab[] $tabs */

$this->title = 'Manage Tabs';

?>
<!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
<div class="content-body">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex">
                <div class="ms-auto">
                    <div class="dropdown dropstart my-2">
                        <a class="btn btn-secondary" href="<?= \yii\helpers\Url::to(['tabs/settings']) ?>"
                            style="color: white; text-decoration: none;">
                            <i class="fa-solid fa-gear"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs" id="tab-list">
                        <?php if (!empty($tabs)): ?>
                        <?php foreach ($tabs as $index => $tab): ?>
                        <?php if ($tab->deleted == 0): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $index === 0 ? 'active' : '' ?>" href="#"
                                onclick="loadTabData(<?= $tab->id ?>, this, '<?= $tab->tab_type ?>')">
                                <?= htmlspecialchars($tab->tab_name) ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="align-items-center m-2">
                            <a class="btn btn-primary" href="<?= \yii\helpers\Url::to(['tabs/settings']) ?>">
                                Click here to add a new tab.
                            </a>
                        </div>
                        <?php endif; ?>
                    </ul>

                </div>
                <div class="card-body pt-0">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-data-current">
                            <div class="table-tabs" id="table-data-current">
                                <!-- Dữ liệu sẽ được tải vào đây -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hide tab -->
<div class="modal fade" id="hideModal" tabindex="-1" aria-labelledby="hideModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hideModalLabel">Ẩn tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p>Chọn tab bạn muốn ẩn hoặc hiện:</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tab name</th>
                            <th><i class="fa-solid fa-eye-slash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-tabs-list">
                        <?php foreach ($tabs as $tab): ?>
                        <?php if ($tab->deleted == 0 || $tab->deleted == 3): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($tab->tab_name) ?>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-hide-btn" type="checkbox"
                                        data-tab-id="<?= htmlspecialchars($tab->id) ?>"
                                        <?php if ($tab->deleted == 3): ?> checked <?php endif; ?>>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-hide-btn">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>




<!-- Modal Sort tab -->
<div class="modal fade" id="sortModal" tabindex="-1" aria-labelledby="sortModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sortModalLabel">Sắp xếp tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p>Kéo và thả để sắp xếp các tab.</p>
                <ul class="list-group" id="sortable-tabs">
                    <?php foreach ($tabs as $index => $tab): ?>
                    <li class="list-group-item" data-tab-id="<?= $tab->id ?>">
                        <?= htmlspecialchars($tab->tab_name) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-sort-btn">Lưu sắp xếp</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#confirm-hide-btn').click(function() {
        let hideStatus = {};

        // Lặp qua tất cả các nút switch và lưu trạng thái
        $('.toggle-hide-btn').each(function() {
            const tabId = $(this).data('tab-id');
            const isChecked = $(this).is(':checked');
            hideStatus[tabId] = isChecked ? 3 : 0; // 3 cho trạng thái ẩn, 0 cho trạng thái hiện
        });

        // Gửi dữ liệu qua AJAX
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/update-hide-status']) ?>', // Đường dẫn đến action cập nhật trạng thái
            method: 'POST',
            data: {
                hideStatus: hideStatus
            },
            success: function(response) {
                if (response.success) {
                    alert("Thay đổi đã được lưu thành công!");
                    location.reload(); // Tải lại trang để cập nhật giao diện
                } else {
                    alert(response.message || "Đã xảy ra lỗi khi lưu thay đổi.");
                }
            },
            error: function() {
                alert("Đã xảy ra lỗi khi lưu thay đổi.");
            }
        });
    });
    // Kích hoạt tính năng kéo và thả cho danh sách
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
});
</script>

<script>
$(document).ready(function() {
    var firstTabId = <?= $tabs[0]->id ?>;
    var firstTabElement = $('.nav-link.active')[0];
    var firstTabType = '<?= $tabs[0]->tab_type ?>';
    loadTabData(firstTabId, firstTabElement, firstTabType);
});

function loadTabData(tabId, element, tabType) {
    $.ajax({
        url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
        type: "GET",
        data: {
            tab_id: tabId,
            tabType: tabType,
        },
        success: function(data) {
            $('#table-data-current').html(data);
            $('.tab-pane').removeClass('show active');
            $('#tab-data-current').addClass('show active');

            $('.nav-link').removeClass('active');
            $('.nav-item').removeClass('active');
            $(element).addClass('active');
            $(element).closest('.nav-item').addClass('active');


        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.');
        }
    });
}
</script>