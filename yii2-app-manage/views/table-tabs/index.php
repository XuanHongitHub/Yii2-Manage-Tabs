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
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#addTableTab">Thêm Mới</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#listTab">Danh Sách</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body pt-0">
                    <div class="tab-content">
                        <!-- Tab Thêm Mới -->
                        <div class="tab-pane fade show active" id="addTableTab">
                            <h5>Thêm Mới Table</h5>
                            <form action="<?= \yii\helpers\Url::to(['create-table-tabs']) ?>" id="addTableForm"
                                method="post">
                                <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                <div class="mb-3">
                                    <label for="tableName" class="form-label">Tên Bảng</label>
                                    <input type="text" name="tableName" class="form-control" id="tableName" required>
                                </div>
                                <div class="mb-3" id="columnsContainer">
                                    <label for="columns" class="form-label">Cột</label>
                                    <div class="input-group mb-2">
                                        <input type="text" name="columns[]" class="form-control"
                                            placeholder="Nhập tên cột" required>
                                        <select name="data_types[]" class="form-select" required
                                            onchange="showSizeInput(this)">
                                            <option value="">Chọn kiểu dữ liệu</option>
                                            <option value="VARCHAR">VARCHAR</option>
                                            <option value="INT">INT</option>
                                            <option value="TEXT">TEXT</option>
                                            <option value="DATE">DATE</option>
                                            <option value="BOOLEAN">BOOLEAN</option>
                                            <option value="FLOAT">FLOAT</option>
                                            <option value="DOUBLE">DOUBLE</option>
                                            <option value="DATETIME">DATETIME</option>
                                            <option value="DECIMAL">DECIMAL</option>
                                            <option value="TIMESTAMP">TIMESTAMP</option>
                                            <option value="CHAR">CHAR</option>
                                        </select>
                                        <input type="number" name="data_sizes[]" class="form-control"
                                            placeholder="Kích thước" style="display:none;" min="1">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="removeColumn(this)">Xóa</button>
                                    </div>
                                </div>
                                <button class="btn btn-outline-primary" type="button" onclick="addColumn()">+ Thêm
                                    Cột</button>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
                                </div>
                            </form>
                        </div>
                        <!-- Tab Danh Sách -->
                        <div class="tab-pane fade" id="listTab">
                            <div class="table-responsive">
                                <?php
                                $tables = [];
                                foreach ($tableTabs as $tableTab) {
                                    $tables[$tableTab->table_name][] = $tableTab;
                                }

                                foreach ($tables as $tableName => $columns): ?>
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Table: <?= htmlspecialchars($tableName) ?></h5>
                                        <div class="float-end">
                                            <a href="<?= \yii\helpers\Url::to(['edit-table', 'tableName' => $tableName]) ?>"
                                                class="btn btn-warning btn-sm">Chỉnh Sửa Tất Cả Cột</a>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteTab(<?= $columns[0]->tab_id ?>)">Xóa Tab</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Tên Cột</th>
                                                    <th scope="col">Kiểu Dữ Liệu</th>
                                                    <th scope="col">Hành Động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($columns as $column): ?>
                                                <tr>
                                                    <td><?= $column->id ?></td>
                                                    <td><?= htmlspecialchars($column->column_name) ?></td>
                                                    <td><?= htmlspecialchars($column->data_type) ?></td>
                                                    <td>
                                                        <a href="<?= \yii\helpers\Url::to(['edit-table-tab', 'id' => $column->id]) ?>"
                                                            class="btn btn-warning btn-sm">Sửa</a>
                                                        <button class="btn btn-danger btn-sm"
                                                            onclick="deleteColumn(<?= $column->id ?>)">Xóa</button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addColumn() {
    const columnsContainer = document.getElementById('columnsContainer');
    const inputGroup = document.createElement('div');
    inputGroup.className = 'input-group mb-2';
    inputGroup.innerHTML =
        `<input type="text" name="columns[]" class="form-control" placeholder="Nhập tên cột" required>
         <select name="data_types[]" class="form-select" required onchange="showSizeInput(this)">
            <option value="">Chọn kiểu dữ liệu</option>
            <option value="VARCHAR">VARCHAR</option>
            <option value="INT">INT</option>
            <option value="TEXT">TEXT</option>
            <option value="DATE">DATE</option>
            <option value="BOOLEAN">BOOLEAN</option>
            <option value="FLOAT">FLOAT</option>
            <option value="DOUBLE">DOUBLE</option>
            <option value="DATETIME">DATETIME</option>
            <option value="DECIMAL">DECIMAL</option>
            <option value="TIMESTAMP">TIMESTAMP</option>
            <option value="CHAR">CHAR</option>
         </select>
         <input type="number" name="data_sizes[]" class="form-control" placeholder="Kích thước" style="display:none;" min="1">
         <button class="btn btn-outline-secondary" type="button" onclick="removeColumn(this)">Xóa</button>`;
    columnsContainer.appendChild(inputGroup);
}

function removeColumn(button) {
    const inputGroup = button.parentElement;
    inputGroup.remove();
}

function showSizeInput(selectElement) {
    const sizeInput = selectElement.parentElement.querySelector('input[name="data_sizes[]"]');
    sizeInput.style.display = (selectElement.value === 'VARCHAR' || selectElement.value === 'CHAR' || selectElement
        .value === 'DECIMAL') ? 'inline' : 'none';
    sizeInput.value = ''; // Reset kích thước khi chọn kiểu khác
}

function deleteColumn(columnId) {
    if (confirm('Bạn có chắc chắn muốn xóa cột này không?')) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['delete-column']) ?>',
            type: 'POST',
            data: {
                id: columnId,
                '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Tải lại trang để cập nhật danh sách
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    }
}
</script>