<?php
/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */

$this->title = 'Chỉnh Sửa Tất Cả Cột';
?>

<div class="content-body mt-5">
    <h5>Chỉnh Sửa Các Cột Trong Bảng: <?= htmlspecialchars($tableTabs[0]->table_name) ?></h5>
    <form action="<?= \yii\helpers\Url::to(['tabs/edit-table', 'tableName' => $tableTabs[0]->table_name]) ?>"
        method="post">
        <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tên Cột</th>
                    <th scope="col">Kiểu Dữ Liệu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tableTabs as $tableTab): ?>
                    <tr>
                        <td><?= $tableTab->id ?></td>
                        <td>
                            <input type="hidden" name="column_id_<?= $tableTab->id ?>" value="<?= $tableTab->id ?>">
                            <input type="text" name="column_name_<?= $tableTab->id ?>" class="form-control"
                                value="<?= htmlspecialchars($tableTab->column_name) ?>" required>
                        </td>
                        <td>
                            <select name="data_type_<?= $tableTab->id ?>" class="form-select" required>
                                <option value="VARCHAR" <?= $tableTab->data_type === 'VARCHAR' ? 'selected' : '' ?>>VARCHAR
                                </option>
                                <option value="INT" <?= $tableTab->data_type === 'INT' ? 'selected' : '' ?>>INT</option>
                                <option value="TEXT" <?= $tableTab->data_type === 'TEXT' ? 'selected' : '' ?>>TEXT</option>
                                <option value="DATE" <?= $tableTab->data_type === 'DATE' ? 'selected' : '' ?>>DATE</option>
                                <option value="BOOLEAN" <?= $tableTab->data_type === 'BOOLEAN' ? 'selected' : '' ?>>BOOLEAN
                                </option>
                                <option value="FLOAT" <?= $tableTab->data_type === 'FLOAT' ? 'selected' : '' ?>>FLOAT
                                </option>
                                <option value="DOUBLE" <?= $tableTab->data_type === 'DOUBLE' ? 'selected' : '' ?>>DOUBLE
                                </option>
                                <option value="DATETIME" <?= $tableTab->data_type === 'DATETIME' ? 'selected' : '' ?>>
                                    DATETIME
                                </option>
                                <option value="DECIMAL" <?= $tableTab->data_type === 'DECIMAL' ? 'selected' : '' ?>>DECIMAL
                                </option>
                                <option value="TIMESTAMP" <?= $tableTab->data_type === 'TIMESTAMP' ? 'selected' : '' ?>>
                                    TIMESTAMP</option>
                                <option value="CHAR" <?= $tableTab->data_type === 'CHAR' ? 'selected' : '' ?>>CHAR</option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
    </form>
</div>