<?php

use app\assets\AppAsset;
use app\assets\Select2Asset;
use yii\helpers\Html;

/** @var yii\web\View $this */
Select2Asset::register($this);

$this->title = 'Thêm Menu';
$this->registerJsFile('js/components/admin/createMenu.js', ['depends' => AppAsset::class]);

?>
<?php include Yii::getAlias('@app/views/layouts/_icon.php'); ?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <h4>Thêm Menu page</h4>
        <p class="mt-1 f-m-light">Menu page chứa các page con</p>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Tên Menu -->
            <div class="col-12 col-md-4 mb-3">
                <label for="name" class="form-label">Tên Menu</label>
                <input type="text" id="name" class="form-control" value="">
                <div id="name-error" class="text-danger mt-1" style="display: none;">Tên menu không được để trống.</div>
            </div>

            <!-- Icon -->
            <div class="col-12 col-md-5 mb-3">
                <label for="icon-select" class="form-label">Chọn icon</label>
                <div id="icon-select-wrapper" class="d-flex align-items-center justify-content-between"
                    style="cursor: pointer; border: 1px solid #ccc; padding: 8px; border-radius: 8px;">
                    <span id="selected-icon-label">stroke-board</span>
                    <svg id="selected-icon" class="stroke-icon mx-2" width="24" height="24">
                        <use href="<?= Yii::getAlias('@web') . "/images/icon-sprite.svg#stroke-board" ?>">
                        </use>
                    </svg>
                </div>

                <!-- Danh sách icon -->
                <div id="icon-list" class="d-flex flex-wrap mt-2"
                    style="display: none; overflow-y: auto; max-height: 200px; border: 1px solid #ccc; border-radius: 8px;">
                    <?php foreach ($iconOptions as $iconValue => $iconLabel): ?>
                    <div class="icon-item col-2 col-md-2 col-lg-1 me-2 mb-2 text-center"
                        data-icon="<?= Html::encode($iconValue) ?>" style="cursor: pointer; padding: 4px">
                        <svg class="stroke-icon" width="40" height="40">
                            <use
                                href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= Html::encode($iconValue) ?>">
                            </use>
                        </svg>
                    </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" id="icon-selected-value"
                    value="<?= Html::encode($selectedIcon ?? 'stroke-board') ?>">
            </div>
        </div>

        <div class="row">
            <!-- Menu cha -->
            <div class="col-12 col-md-4 mb-3">
                <label for="parentId" class="form-label">Chọn Menu cha</label>
                <select type="text" id="parentId" class="form-control">
                    <option value="">-- Không --</option>
                    <?php
                    foreach ($potentialMenus as $menu) {
                        echo Html::tag('option', $menu->name, ['value' => $menu->id]);
                    } ?>
                </select>
            </div>

            <!-- Page con -->
            <div class="col-12 col-md-4 mb-3">
                <label for="pages" class="form-label">Chọn Page con</label>
                <select id="pages" class="form-select input-air-primary digits form-multi-select" multiple="">
                    <?php foreach ($potentialPages as $page): ?>
                    <option value="<?= $page->id ?>"><?= $page->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <!-- Nút lưu -->
        <div class="mt-3">
            <button id="saveTabMenuChanges" class="btn btn-success">Tạo Menu</button>
        </div>
    </div>

</div>

<script>
var store_menu_url = "<?= \yii\helpers\Url::to(['menus/store']) ?>";
var list_menu_url = "<?= \yii\helpers\Url::to(['menus/index']) ?>";
</script>