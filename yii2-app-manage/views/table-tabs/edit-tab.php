<?php
/** @var yii\web\View $this */
/** @var app\models\TableTab $tableTab */

$this->title = 'Chỉnh Sửa Cột';
?>

<div class="content-body mt-5">
    <h5>Chỉnh Sửa Cột: <?= htmlspecialchars($tableTab->column_name) ?></h5>
    <form action="<?= \yii\helpers\Url::to(['tabs/edit-table-tab', 'id' => $tableTab->id]) ?>" method="post">
        <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <div class="mb-3">
            <label for="column_name" class="form-label">Tên Cột</label>
            <input type="text" name="column_name" class="form-control" id="column_name"
                value="<?= htmlspecialchars($tableTab->column_name) ?>" required>
        </div>
        <div class="mb-3">
            <label for="data_type" class="form-label">Kiểu Dữ Liệu</label>
            <select name="data_type" class="form-select" required>
                <option value="">Chọn kiểu dữ liệu</option>
                <option value="VARCHAR" <?= $tableTab->data_type === 'VARCHAR' ? 'selected' : '' ?>>VARCHAR</option>
                <option value="INT" <?= $tableTab->data_type === 'INT' ? 'selected' : '' ?>>INT</option>
                <option value="TEXT" <?= $tableTab->data_type === 'TEXT' ? 'selected' : '' ?>>TEXT</option>
                <option value="DATE" <?= $tableTab->data_type === 'DATE' ? 'selected' : '' ?>>DATE</option>
                <option value="BOOLEAN" <?= $tableTab->data_type === 'BOOLEAN' ? 'selected' : '' ?>>BOOLEAN</option>
                <option value="FLOAT" <?= $tableTab->data_type === 'FLOAT' ? 'selected' : '' ?>>FLOAT</option>
                <option value="DOUBLE" <?= $tableTab->data_type === 'DOUBLE' ? 'selected' : '' ?>>DOUBLE</option>
                <option value="DATETIME" <?= $tableTab->data_type === 'DATETIME' ? 'selected' : '' ?>>DATETIME</option>
                <option value="DECIMAL" <?= $tableTab->data_type === 'DECIMAL' ? 'selected' : '' ?>>DECIMAL</option>
                <option value="TIMESTAMP" <?= $tableTab->data_type === 'TIMESTAMP' ? 'selected' : '' ?>>TIMESTAMP
                </option>
                <option value="CHAR" <?= $tableTab->data_type === 'CHAR' ? 'selected' : '' ?>>CHAR</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
    </form>
</div>