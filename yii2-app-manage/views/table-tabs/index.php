<?php

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */

$this->title = 'Table Tab';
?>

<div class="content-body mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>New Table</h4>
                </div>
                <div class="card-body pt-0">
                    <form action="<?= \yii\helpers\Url::to(['create-table-tabs']) ?>" method="post">
                        <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                        <div class="mb-3 row">
                            <div class="col-3">
                                <label for="tableName" class="form-label">Table Name</label>
                                <input type="text" name="tableName" class="form-control" id="tableName" required>
                            </div>
                            <div class="col-3">
                                <label for="characterSet" class="form-label">Character Set</label>
                                <select name="character_set" id="characterSet" class="form-select" required>
                                    <option value="utf8mb4">utf8mb4</option>
                                    <option value="utf8">utf8</option>
                                    <option value="latin1">latin1</option>
                                    <option value="latin2">latin2</option>
                                    <option value="ascii">ascii</option>
                                    <option value="utf16">utf16</option>
                                    <option value="utf32">utf32</option>
                                    <option value="cp1251">cp1251</option>
                                    <option value="cp1252">cp1252</option>
                                    <option value="macroman">macroman</option>
                                </select>
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
                                        <td><input type="text" name="columns[]" class="form-control" required></td>
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
                                        <td><button type="button" class="btn btn-danger"
                                                onclick="removeColumn(this)">-</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button class="btn btn-outline-primary" type="button" onclick="addColumn()">+ Add
                            column</button>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
             <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input"> 
         </td>
         <td>
             <input type="checkbox" name="is_primary[]" value="1"  class="form-check-input" onchange="togglePrimaryKey(this)"> 
         </td>
         <td><button type="button" class="btn btn-danger" onclick="removeColumn(this)">-</button></td>`;
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