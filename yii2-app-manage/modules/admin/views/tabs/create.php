<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'Create Tabs';

// Loop through columns from session data or initialize empty
$columns = $tableCreationData['columns'] ?? [];
$dataTypes = $tableCreationData['dataTypes'] ?? [];
$tabmenuId = $tableCreationData['tabmenuId'] ?? [];
$dataSizes = $tableCreationData['dataSizes'] ?? [];
$defaultValues = $tableCreationData['defaultValues'] ?? [];
$isNotNull = $tableCreationData['isNotNull'] ?? [];
$isPrimary = $tableCreationData['isPrimary'] ?? [];

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
                        <h4>Thêm Mới Tab</h4>
                        <p class="mt-1 f-m-light">Thêm Table Tab | Richtext Tab</p>
                    </div>
                    <div class="card-body">
                        <form action="<?= \yii\helpers\Url::to(['create-tab']) ?>" method="post">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3">
                                    <label for="tab_name" class="form-label">Tên Tab</label>
                                    <input type="text" name="tab_name" class="form-control" id="tab_name"
                                        value="<?= $tableCreationData['tabName'] ?? ''; ?>">
                                    <?php if (Yii::$app->session->hasFlash('error_tab_name')): ?>
                                    <div class="text-danger"><?= Yii::$app->session->getFlash('error_tab_name') ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Chọn loại tab -->
                                <div class="col-12 col-md-6 col-lg-3 col-xl-2 mb-3">
                                    <label for="tab_type" class="form-label">Loại Tab</label>
                                    <select name="tab_type" id="tab_type" class="form-select"
                                        onchange="toggleTabInputs()">
                                        <option value="table">Table</option>
                                        <option value="richtext">Rich Text</option>
                                    </select>
                                </div>
                                <!-- Menu Tab -->
                                <div class="col-12 col-md-6 col-lg-2 col-xl-2 mb-3">
                                    <label for="menu_single" class="form-label">Chọn Loại Menu</label>
                                    <select name="menu_single" id="menu_single" class="form-select">
                                        <option class="txt-primary" value="">-- Không --</option>

                                        <?php foreach ($tabMenus as $menu): ?>
                                        <?php if ($menu->menu_type !== 'none'): ?>
                                        <!-- Kiểm tra menu_type -->
                                        <option value="<?= $menu->id ?>"
                                            <?= (isset($tabmenuId) && $tabmenuId == $menu->id) ? 'selected' : '' ?>>
                                            <?= Html::encode($menu->name) ?>
                                        </option>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (Yii::$app->session->hasFlash('error_tab_menu')): ?>
                                    <div class="text-danger"><?= Yii::$app->session->getFlash('error_tab_menu') ?></div>
                                    <?php endif; ?>
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

                                    <input type="hidden" id="icon-selected-value" name="icon"
                                        value="<?= Html::encode($selectedIcon ?? '') ?>">

                                </div>

                                <!-- Phần input cho loại tab "Table" -->
                                <div id="tableInputs" style="display: none;">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Tên</th>
                                                    <th>Loại</th>
                                                    <th>Độ Dài</th>
                                                    <th class="text-center">Not_Null</th>
                                                    <th class="text-center">A_I</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="columnsContainer">
                                                <?php
                                                foreach ($columns as $index => $column): ?>
                                                <tr>
                                                    <td>
                                                        <input type="text" name="columns[]" class="form-control"
                                                            value="<?= Html::encode($column) ?>">
                                                        <?php if (Yii::$app->session->hasFlash("error_columns[$index]")): ?>
                                                        <div class="text-danger">
                                                            <?= Yii::$app->session->getFlash("error_columns[$index]") ?>
                                                        </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <select name="data_types[]" class="form-select">
                                                            <?php
                                                                $dataTypeOptions = [
                                                                    "INT",
                                                                    "BIGINT",
                                                                    "SMALLINT",
                                                                    "TINYINT",
                                                                    "FLOAT",
                                                                    "DOUBLE",
                                                                    "DECIMAL",
                                                                    "VARCHAR",
                                                                    "CHAR",
                                                                    "TEXT",
                                                                    "MEDIUMTEXT",
                                                                    "LONGTEXT",
                                                                    "DATE",
                                                                    "DATETIME",
                                                                    "TIMESTAMP",
                                                                    "TIME",
                                                                    "BOOLEAN",
                                                                    "JSON",
                                                                    "BLOB"
                                                                ];
                                                                foreach ($dataTypeOptions as $option): ?>
                                                            <option value="<?= $option ?>"
                                                                <?= (isset($dataTypes[$index]) && $dataTypes[$index] == $option) ? 'selected' : '' ?>>
                                                                <?= $option ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <?php if (Yii::$app->session->hasFlash("error_data_types[$index]")): ?>
                                                        <div class="text-danger">
                                                            <?= Yii::$app->session->getFlash("error_data_types[$index]") ?>
                                                        </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><input type="number" name="data_sizes[]" class="form-control"
                                                            value="<?= Html::encode($dataSizes[$index] ?? '') ?>"
                                                            placeholder="Length">
                                                        <?php if (Yii::$app->session->hasFlash("error_data_sizes[$index]")): ?>
                                                        <div class="text-danger">
                                                            <?= Yii::$app->session->getFlash("error_data_sizes[$index]") ?>
                                                        </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="is_not_null[<?= $index ?>]"
                                                            value="1" class="form-check-input"
                                                            <?= (isset($isNotNull[$index]) && $isNotNull[$index] == '1') ? 'checked' : '' ?>>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="is_primary[]" value="1"
                                                            class="form-check-input"
                                                            <?= (isset($isPrimary[$index]) && $isPrimary[$index] == '1') ? 'checked' : '' ?>>
                                                        <?php if (Yii::$app->session->hasFlash("error_is_primary[$index]")): ?>
                                                        <div class="text-danger">
                                                            <?= Yii::$app->session->getFlash("error_is_primary[$index]") ?>
                                                        </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <i class="fa-solid fa-square-minus text-danger fs-3"
                                                            style="cursor: pointer;" onclick="removeColumn(this)"></i>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary my-3" onclick="addColumn()">+
                                        Thêm cột</button>
                                </div>
                                <!-- Nút tạo -->
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Thêm</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
function toggleTabInputs() {
    var tabType = document.getElementById('tab_type').value;
    document.getElementById('tableInputs').style.display = tabType === 'table' ? 'block' :
        'none';
}

document.addEventListener("DOMContentLoaded", function() {
    toggleTabInputs();
});

function addColumn() {
    const columnsContainer = document.getElementById('columnsContainer');
    const inputGroup = document.createElement('tr');
    inputGroup.innerHTML =
        `<td><input type="text" name="columns[]" class="form-control"></td>
        <td>
            <select name="data_types[]" class="form-select">
                <option value="INT">INT</option>
                <option value="BIGINT">BIGINT</option>
                <option value="SMALLINT">SMALLINT</option>
                <option value="TINYINT">TINYINT</option>
                <option value="FLOAT">FLOAT</option>
                <option value="DOUBLE">DOUBLE</option>
                <option value="DECIMAL">DECIMAL</option>
                <option value="VARCHAR">VARCHAR</option>
                <option value="CHAR">CHAR</option>
                <option value="TEXT">TEXT</option>
                <option value="MEDIUMTEXT">MEDIUMTEXT</option>
                <option value="LONGTEXT">LONGTEXT</option>
                <option value="DATE">DATE</option>
                <option value="DATETIME">DATETIME</option>
                <option value="TIMESTAMP">TIMESTAMP</option>
                <option value="TIME">TIME</option>
                <option value="BOOLEAN">BOOLEAN</option>
                <option value="JSON">JSON</option>
                <option value="BLOB">BLOB</option>
            </select>
        </td>
        <td><input type="number" name="data_sizes[]" class="form-control" placeholder="Length"></td>

        <td class="text-center">
            <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input">
        </td>
        <td class="text-center">
            <input type="checkbox" name="is_primary[]" value="1" class="form-check-input">
        </td>
        <td><i class="fa-solid fa-square-minus text-danger fs-3" style="cursor: pointer;" onclick="removeColumn(this)"></i></td>`;
    columnsContainer.appendChild(inputGroup);
}


function removeColumn(button) {
    const row = button.closest('tr');
    row.remove();
}
$(document).ready(function() {
    $('.icon-item').on('click', function() {
        var selectedIcon = $(this).data('icon');
        $('#selected-icon-label').text(selectedIcon);
        $('#selected-icon use').attr('href', '<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#' +
            selectedIcon);
        $('#icon-selected-value').val(selectedIcon);
        console.log("🚀 ~ selectedIcon:", selectedIcon);
        console.log("Input value:", $('#icon-selected-value').val());
        $('#icon-list').hide();
    });

    $('#menu_single').change(function() {
        var selectedMenu = $(this).val();
        if (selectedMenu !== '') {
            $('#icon-list').hide();
            $('#selected-icon-label').text('Chọn icon');
            $('#icon-selected-value').val('');
        } else {
            $('#icon-list').show();
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const menuId = urlParams.get('menuId');
    console.log("🚀 ~ document.addEventListener ~ menuId:", menuId);

    if (menuId) {
        document.getElementById('menu_single').value = menuId;
    }
});
</script>

<style>
#icon-display {
    margin-top: 5px;
}

.stroke-icon {
    fill: currentColor;
    stroke: #363636;
}
</style>