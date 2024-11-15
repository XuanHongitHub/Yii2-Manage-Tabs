<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'Create Group';

?>
<?php include Yii::getAlias('@app/views/layouts/_icon.php'); ?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar-settings.php'); ?>


<div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <small id="toast-timestamp"></small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">Msg</div>
    </div>
</div>

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
                        <h4>Thêm Menu tab</h4>
                        <p class="mt-1 f-m-light">Menu tab chứa các tab con</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                                <label for="name" class="form-label">Tên Menu</label>
                                <input type="text" id="name" class="form-control" value="">
                            </div>
                            <div class="col-12 col-md-6 col-lg-2 col-xl-2 mb-3">
                                <label for="menu_type" class="form-label">Chọn Menu loại</label>
                                <select id="menu_type" class="form-select">
                                    <option value="tab_menu" selected>Menu chứa Tab con</option>
                                    <option value="menu_group">Menu chứa Menu con</option>
                                </select>
                            </div>
                        </div>
                        <!-- Icon -->
                        <div class="col-12 col-md-8 col-lg-6 col-xl-4 custom-col mb-3 mb-3">
                            <label for="icon-select" class="form-label">Chọn icon</label>
                            <div class="row">
                                <div class="col-12">
                                    <div id="icon-select-wrapper"
                                        class="d-flex align-items-center justify-content-between"
                                        style="cursor: pointer; border: 1px solid #ccc; padding: 8px; border-radius: 8px;">
                                        <span
                                            id="selected-icon-label"><?= isset($selectedIconLabel) ? Html::encode($selectedIconLabel) : 'Chọn icon' ?></span>
                                        <svg id="selected-icon" class="stroke-icon mx-2" width="24" height="24">
                                            <use
                                                href="<?= isset($selectedIcon) ? Yii::getAlias('@web') . "/images/icon-sprite.svg#{$selectedIcon}" : '' ?>">
                                            </use>
                                        </svg>
                                    </div>

                                    <!-- Danh sách icon -->
                                    <div id="icon-list" class="d-flex flex-wrap mt-2"
                                        style="display: none; overflow-y: auto; max-height: 200px; border: 1px solid #ccc; border-radius: 8px;">
                                        <?php foreach ($iconOptions as $iconValue => $iconLabel): ?>
                                        <div class="icon-item col-2 col-md-2 col-lg-1 me-2 mb-2 text-center"
                                            data-icon="<?= Html::encode($iconValue) ?>"
                                            style="cursor: pointer; padding: 4px">
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

                            <!-- Input ẩn để lưu icon đã chọn -->
                            <input type="hidden" id="icon-selected-value"
                                value="<?= Html::encode($selectedIcon ?? '') ?>">
                        </div>
                        <div class="mt-3">
                            <button type="button" id="saveTabMenuChanges" class="btn btn-success">Tạo Menu</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($('#icon-selected-value').val() === '') {
        var firstIcon = $('#icon-list .icon-item').first().data('icon');

        $('#selected-icon-label').text('Icon: ' + firstIcon);
        $('#selected-icon use').attr('href', '<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#' +
            firstIcon);
        $('#icon-selected-value').val(firstIcon);
        $('#icon-list .icon-item').first().addClass('selected');
    }

    $('.icon-item').on('click', function() {
        var selectedIcon = $(this).data('icon');
        $('#selected-icon-label').text(selectedIcon);
        $('#selected-icon use').attr('href', '<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#' +
            selectedIcon);
        $('#icon-selected-value').val(selectedIcon);
        $('#icon-list').hide();
    });

    $('#saveTabMenuChanges').on('click', function() {
        var menuName = $('#name').val();
        var menuType = $('#menu_type').val();
        var icon = $('#icon-selected-value').val();

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['settings/create-or-update-menu']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: menuName,
                menu_type: menuType,
                icon: icon,
                status: 0,
                position: 0
            },
            success: function(response) {
                if (response.success) {
                    window.location.href =
                        '<?= \yii\helpers\Url::to(['settings/menu-list']) ?>';
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');

            }
        });
    });
});
</script>