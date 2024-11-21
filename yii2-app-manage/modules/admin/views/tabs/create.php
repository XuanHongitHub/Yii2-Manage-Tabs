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
</script>