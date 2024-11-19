<?php

use app\assets\Select2Asset;
use yii\helpers\Html;

/** @var yii\web\View $this */

$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'Thêm Menu';

?>
<?php include Yii::getAlias('@app/views/layouts/_icon.php'); ?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar-settings.php'); ?>

<!-- Toast thông báo -->
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

                        </div>

                        <!-- Chọn loại menu và chọn tab cùng hàng -->
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                                <label for="menu_type" class="form-label">Chọn Menu loại</label>
                                <select id="menu_type" class="form-select">
                                    <option value="menu_single" selected>Menu chứa Tab con</option>
                                    <option value="menu_group">Menu chứa Menu con</option>
                                    <option value="none">Không có Menu con</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                                <label for="tabs" class="form-label">Chọn Tab</label>
                                <select id="tabs" class="form-select input-air-primary digits form-multi-select"
                                    multiple="">
                                    <?php foreach ($tabs as $tab): ?>
                                    <option value="<?= $tab->id ?>"><?= $tab->tab_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                                <label for="menus" class="form-label">Chọn Menu</label>
                                <select id="menus" class="form-select input-air-primary digits form-multi-select"
                                    multiple="">
                                    <?php foreach ($menus as $menu): ?>
                                    <option value="<?= $menu->id ?>"><?= $menu->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
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
    // Khởi tạo select2 cho các select có class .form-multi-select
    $('.form-multi-select').select2({
        placeholder: 'Chọn Tab',
        allowClear: true
    });

    $('#menu_type').on('change', function() {
        var menuType = $(this).val();
        if (menuType === 'none') {
            $('#tabs').prop('multiple', false);
        } else {
            $('#tabs').prop('multiple', true);
        }
    });

    $('#saveTabMenuChanges').on('click', function() {
        var menuName = $('#name').val();
        var menuType = $('#menu_type').val();
        var icon = $('#icon-selected-value').val();
        var selectedTabs = $('#tabs').val();
        var selectedMenus = $('#menus').val();

        console.log("🚀 ~ $ ~ selectedTabs:", selectedTabs);
        console.log("🚀 ~ $ ~ selectedMenus:", selectedMenus);

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['create-or-update-menu']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: menuName,
                menu_type: menuType,
                icon: icon,
                status: 0,
                position: 0,
                selectedTabs: selectedTabs,
                selectedMenus: selectedMenus

            },
            success: function(response) {
                if (response.success) {
                    window.location.href =
                        '<?= \yii\helpers\Url::to(['index']) ?>';
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Lỗi khi lưu menu.');
            }
        });
    });
});
</script>