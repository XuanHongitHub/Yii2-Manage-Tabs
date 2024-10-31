<?php

use yii\helpers\Html;
use app\models\User;
/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */

$this->title = 'Table Tabs';

?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">

            </div>
        </div>
    </div>
    <!-- Container-fluid starts -->
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
                                    <input type="text" name="tableName" class="form-control" id="tableName" required>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="characterSet" class="form-label">Character Set</label>
                                    <select name="character_set" id="characterSet" class="form-select" required>
                                        <option value="utf8mb4" data-collation="utf8mb4_unicode_ci">utf8mb4</option>
                                        <option value="utf8" data-collation="utf8_unicode_ci">utf8</option>
                                        <option value="latin1" data-collation="latin1_swedish_ci">latin1</option>
                                        <option value="latin2" data-collation="latin2_general_ci">latin2</option>
                                        <option value="ascii" data-collation="ascii_general_ci">ascii</option>
                                        <option value="utf16" data-collation="utf16_unicode_ci">utf16</option>
                                        <option value="utf32" data-collation="utf32_unicode_ci">utf32</option>
                                        <option value="cp1251" data-collation="cp1251_general_ci">cp1251</option>
                                        <option value="cp1252" data-collation="cp1252_general_ci">cp1252</option>
                                        <option value="macroman" data-collation="macroman_general_ci">macroman
                                        </option>
                                    </select>
                                    <?= Html::hiddenInput('collation', '', ['id' => 'collationField']) ?>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
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
                                        <tr>
                                            <td><input type="text" name="columns[]" class="form-control" required>
                                            </td>
                                            <td>
                                                <select name="data_types[]" class="form-select" required>
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
                                            <td><input type="number" name="data_sizes[]" class="form-control"
                                                    placeholder="Length"></td>
                                            <td><input type="text" name="default_values[]" class="form-control"
                                                    placeholder="Default"></td>
                                            <td>
                                                <input type="checkbox" name="is_not_null[]" value="1"
                                                    class="form-check-input">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="is_primary[]" value="1"
                                                    class="form-check-input" onchange="togglePrimaryKey(this)">
                                            </td>
                                            <td>
                                                <i class="fa-solid fa-square-minus text-danger fs-3"
                                                    style="cursor: pointer;" onclick="removeColumn(this)"></i>
                                            </td>

                                        </tr>
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
    <!-- Container-fluid Ends-->
</div>

<?php include Yii::getAlias('@app/views/layouts/_footer.php'); ?>

<script>
    document.getElementById('richtextForm').addEventListener('submit', function (event) {
        const richTextArea = document.querySelector('.richtext-area');
        const contentInput = document.getElementById('content');
        contentInput.value = richTextArea.innerHTML;
    });


    document.getElementById('characterSet').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const collation = selectedOption.getAttribute('data-collation');
        document.getElementById('collationField').value = collation;
    });

    function addColumn() {
        const columnsContainer = document.getElementById('columnsContainer');
        const inputGroup = document.createElement('tr');
        inputGroup.innerHTML =
            `<td><input type="text" name="columns[]" class="form-control" required></td>
         <td>
             <select name="data_types[]" class="form-select" required>
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
            <input type="checkbox" name="is_not_null[]" value="1"
                class="form-check-input">
        </td>
        <td>
            <input type="checkbox" name="is_primary[]" value="1"
                class="form-check-input" onchange="togglePrimaryKey(this)">
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
            notNullCheckbox.disabled = true
        } else {
            notNullCheckbox.disabled = false;
        }
    }
</script>