<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TabMenus $tabMenu */
/** @var app\models\Tab[] $childTabs */
/** @var app\models\TabMenus[] $childMenus */
/** @var app\models\Tab[] $potentialChildTabs */
/** @var app\models\TabMenus[] $potentialChildMenus */

$this->title = 'Chi tiết Menu';

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
                <div class="col-12">

                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h4>Danh sách Menu Con và Tab Con</h4>
                    </div>
                    <div class="card-body">
                        <div class="treeview">
                            <ul>
                                <!-- Lặp qua các Menu Con đã có -->
                                <?php foreach ($childMenus as $menu): ?>
                                <li>
                                    <label>
                                        <input type="checkbox" class="menu-checkbox"
                                            data-menu-id="<?= Html::encode($menu->id) ?>" checked>
                                        <?= Html::encode($menu->name) ?>
                                    </label>

                                    <!-- Hiển thị các Page Con của Menu -->
                                    <ul>
                                        <?php
                                            // Lọc các trang con (Pages) của menu hiện tại
                                            $menuPages = array_filter($childTabs, function ($tab) use ($menu) {
                                                return $tab->menu_id == $menu->id; // Điều kiện để xác định Page Con
                                            });
                                            ?>
                                        <?php foreach ($menuPages as $tab): ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" class="page-checkbox"
                                                    data-tab-id="<?= Html::encode($tab->id) ?>" checked>
                                                <?= Html::encode($tab->tab_name) ?>
                                            </label>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                                <?php endforeach; ?>

                                <!-- Lặp qua các Menu Con có thể làm con -->
                                <?php foreach ($potentialChildMenus as $menu): ?>
                                <li>
                                    <label>
                                        <input type="checkbox" class="menu-checkbox"
                                            data-menu-id="<?= Html::encode($menu->id) ?>">
                                        <?= Html::encode($menu->name) ?>
                                    </label>

                                    <!-- Hiển thị các Page Con của Menu -->
                                    <ul>
                                        <?php
                                            // Lọc các trang con (Pages) của menu hiện tại
                                            $menuPages = array_filter($potentialChildTabs, function ($tab) use ($menu) {
                                                return $tab->menu_id == $menu->id; // Điều kiện để xác định Page Con
                                            });
                                            ?>
                                        <?php foreach ($menuPages as $tab): ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" class="page-checkbox"
                                                    data-tab-id="<?= Html::encode($tab->id) ?>">
                                                <?= Html::encode($tab->tab_name) ?>
                                            </label>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Tự động kiểm tra các menu con có thể làm con nếu cần
    $('.menu-checkbox').on('change', function() {
        var menuId = $(this).data('menu-id');
        if ($(this).is(':checked')) {
            // Mã xử lý nếu checkbox menu con được chọn
        } else {
            // Mã xử lý nếu checkbox menu con bị bỏ chọn
        }
    });

    $('.page-checkbox').on('change', function() {
        var tabId = $(this).data('tab-id');
        if ($(this).is(':checked')) {
            // Mã xử lý nếu checkbox page con được chọn
        } else {
            // Mã xử lý nếu checkbox page con bị bỏ chọn
        }
    });
});
</script>

<style>
.treeview ul {
    list-style-type: none;
    padding-left: 20px;
    /* Thụt lề các mục con */
}

.treeview li {
    margin-bottom: 5px;
}

.treeview label {
    cursor: pointer;
}

.treeview input[type="checkbox"] {
    margin-right: 10px;
}
</style>