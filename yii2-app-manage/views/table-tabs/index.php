<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'Table Tabs';

  // Loop through columns from session data or initialize empty
  $columns = $tableCreationData['columns'] ?? [];
  $dataTypes = $tableCreationData['data_types'] ?? [];
  $dataSizes = $tableCreationData['data_sizes'] ?? [];
  $defaultValues = $tableCreationData['default_values'] ?? [];
  $isNotNull = $tableCreationData['is_not_null'] ?? [];
  $isPrimary = $tableCreationData['is_primary'] ?? [];
?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>

<div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <small id="toast-timestamp"></small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">Hello, I'm a web-designer.</div>
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
                        <h4>Table Tabs</h4>
                        <p class="mt-1 f-m-light">Create table tab</p>
                    </div>
                    <div class="card-body">
                        <form action="<?= \yii\helpers\Url::to(['create-table-tabs']) ?>" method="post">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                            <div class="mb-3 row">
                                <div class="col-12 col-md-4">
                                    <label for="tableName" class="form-label">Table Name</label>
                                    <input type="text" name="tableName" class="form-control" id="tableName"
                                        value="<?= $tableCreationData['tableName'] ?? ''; ?>">
                                    <?php if (Yii::$app->session->hasFlash('errorFields') && Yii::$app->session->getFlash('errorFields') === 'tableName'): ?>
                                    <div class="text-danger"><?= Yii::$app->session->getFlash('error') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Length/Value</th>
                                            <th>Default</th>
                                            <th>Not Null</th>
                                            <th>A_I</th>
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
                                                <?php if (Yii::$app->session->hasFlash('errorFields') && Yii::$app->session->getFlash('errorFields') === "columns[$index]"): ?>
                                                <div class="text-danger"><?= Yii::$app->session->getFlash('error') ?>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <select name="data_types[]" class="form-select">
                                                    <?php
                                                        $dataTypeOptions = [
                                                            "INT", "BIGINT", "SMALLINT", "TINYINT", "FLOAT",
                                                            "DOUBLE", "DECIMAL", "VARCHAR", "CHAR", "TEXT",
                                                            "MEDIUMTEXT", "LONGTEXT", "DATE", "DATETIME",
                                                            "TIMESTAMP", "TIME", "BOOLEAN", "JSON", "BLOB"
                                                        ];
                                                        foreach ($dataTypeOptions as $option): ?>
                                                    <option value="<?= $option ?>"
                                                        <?= (isset($dataTypes[$index]) && $dataTypes[$index] == $option) ? 'selected' : '' ?>>
                                                        <?= $option ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="number" name="data_sizes[]" class="form-control"
                                                    value="<?= Html::encode($dataSizes[$index] ?? '') ?>"
                                                    placeholder="Length"></td>
                                            <td><input type="text" name="default_values[]" class="form-control"
                                                    value="<?= Html::encode($defaultValues[$index] ?? '') ?>"
                                                    placeholder="Default"></td>
                                            <td>
                                                <input type="checkbox" name="is_not_null[]" value="1"
                                                    class="form-check-input"
                                                    <?= (isset($isNotNull[$index]) && $isNotNull[$index] == '1') ? 'checked' : '' ?>>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="is_primary[]" value="1"
                                                    class="form-check-input" onchange="togglePrimaryKey(this)"
                                                    <?= (isset($isPrimary[$index]) && $isPrimary[$index] == '1') ? 'checked' : '' ?>>
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
                            <div class="d-flex mt-4 flex-column flex-md-row">
                                <button class="btn btn-outline-primary" type="button" onclick="addColumn()">+ Add
                                    column</button>
                                <button type="submit" class="btn btn-success ms-auto me-5">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include Yii::getAlias('@app/views/layouts/_footer.php'); ?>

<script>
function addColumn() {
    const columnsContainer = document.getElementById('columnsContainer');
    const inputGroup = document.createElement('tr');
    inputGroup.innerHTML =
        `<td><input type="text" name="columns[]" class="form-control" ></td>
         <td>
             <select name="data_types[]" class="form-select" >
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
         <td><input type="text" name="default_values[]" class="form-control" placeholder="Default"></td>
         <td>
            <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input">
        </td>
        <td>
            <input type="checkbox" name="is_primary[]" value="1" class="form-check-input" onchange="togglePrimaryKey(this)">
        </td>
         <td><button type="button" class="btn btn-danger" onclick="removeColumn(this)"><i class="fa-solid fa-minus"></i></button></td>`;
    columnsContainer.appendChild(inputGroup);
}

function removeColumn(button) {
    const row = button.closest('tr');
    row.remove();
}

function togglePrimaryKey(primaryCheckbox) {
    const notNullCheckbox = primaryCheckbox.closest('tr').querySelector('input[name="is_not_null[]"]');

    if (primaryCheckbox.checked) {
        notNullCheckbox.checked = true;
        notNullCheckbox.disabled = true;
    } else {
        notNullCheckbox.disabled = false;
    }
}
</script>