<?php

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
/** @var app\models\RichtextTab[] $richtextTabs */
/** @var app\models\Tab[] $tabs */

$this->title = 'Manage Tabs';
?>

<div class="content-body mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex">
                <div class="ms-auto">
                    <div class="dropdown dropstart my-2">
                        <a class="btn btn-secondary" href="<?= \yii\helpers\Url::to(['tabs/settings']) ?>"
                            style="color: white; text-decoration: none;">
                            <i class="fa-solid fa-gear"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs">
                        <?php foreach ($tabs as $index => $tab): ?>
                            <li class="nav-item">
                                <a class="nav-link cm-cl <?= $index === 0 ? 'active' : '' ?>" data-bs-toggle="tab"
                                    href="#tab<?= $tab->id ?>">
                                    <?php
                                    $tableTab = app\models\TableTab::find()->where(['tab_id' => $tab->id])->one();
                                    echo $tableTab ? htmlspecialchars($tableTab->table_name) : 'Không tìm thấy bảng';
                                    ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-body pt-0">
                    <div class="tab-content">
                        <?php foreach ($tabs as $index => $tab): ?>
                            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" id="tab<?= $tab->id ?>">
                                <?php if ($tab->tab_type == 'table'): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <button class="btn btn-light me-2" title="Menu">
                                                <i class="fas fa-bars"></i>
                                            </button>
                                            <button class="btn btn-light" title="Trash">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form class="d-flex">
                                            <input class="form-control me-2" type="search" placeholder="Tìm kiếm..."
                                                aria-label="Search">
                                            <button class="btn btn-outline-success" type="submit">Tìm</button>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr class="border-bottom-primary">
                                                    <th scope="col"><input type="checkbox" id="select-all"></th>
                                                    <?php
                                                    // Lấy bảng và cấu trúc
                                                    $tableTab = app\models\TableTab::find()->where(['tab_id' => $tab->id])->one();
                                                    $tableName = $tableTab ? $tableTab->table_name : null;

                                                    if ($tableName):
                                                        // Lấy danh sách cột của bảng
                                                        $columns = Yii::$app->db->schema->getTableSchema($tableName)->columns;
                                                        foreach ($columns as $column): ?>
                                                            <th scope="col"><?= htmlspecialchars($column->name) ?></th>
                                                        <?php endforeach; ?>
                                                        <th scope="col">Action</th> <!-- Cột Action -->
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Lấy dữ liệu từ bảng
                                                $data = Yii::$app->db->createCommand("SELECT * FROM `$tableName`")->queryAll();
                                                if (!empty($data)):
                                                    foreach ($data as $row): ?>
                                                        <tr>
                                                            <td><input type="checkbox" class="row-checkbox"></td>
                                                            <?php foreach ($columns as $column): ?>
                                                                <td>
                                                                    <input type="text" value="<?= htmlspecialchars($row[$column->name]) ?>"
                                                                        class="form-control data-input" data-id="<?= $row['id'] ?>"
                                                                        data-column="<?= $column->name ?>" onfocus="enableEdit(this)">
                                                                </td>
                                                            <?php endforeach; ?>
                                                            <td>
                                                                <button class="btn btn-danger btn-sm"
                                                                    onclick="deleteRow(<?= $row['id'] ?>)">Xóa</button>
                                                                <button class="btn btn-success btn-sm save-btn"
                                                                    onclick="saveRow(<?= $row['id'] ?>)">Lưu</button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach;
                                                else: ?>
                                                    <tr>
                                                        <td colspan="<?= count($columns) + 2 ?>" class="text-center">Không có dữ
                                                            liệu</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td> <!-- Cột checkbox cho hàng mới -->
                                                    <?php foreach ($columns as $column): ?>
                                                        <td>
                                                            <input type="text"
                                                                placeholder="Nhập <?= htmlspecialchars($column->name) ?>"
                                                                class="form-control new-data-input"
                                                                data-column="<?= htmlspecialchars($column->name) ?>"
                                                                onblur="addData(this)">
                                                        </td>
                                                    <?php endforeach; ?>
                                                    <td></td> <!-- Cột Action cho hàng mới -->
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php endif; ?>
                                <?php if ($tab->tab_type == 'richtext'): ?>
                                    <h5>Richtext Editor</h5>
                                    <div class="richtext-area" contenteditable="true"
                                        style="border: 1px solid #ced4da; padding: 10px; min-height: 150px;">
                                        <p>Bắt đầu nhập nội dung ở đây...</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function enableEdit(input) {
        const row = input.closest('tr');
        const inputs = row.querySelectorAll('.data-input');

        inputs.forEach(input => {
            input.disabled = false; // Bỏ khóa tất cả các input trong hàng
        });
    }

    function saveRow(rowId) {
        const row = document.querySelector(`tr:has(input[data-id="${rowId}"])`);
        const inputs = row.querySelectorAll('.data-input');
        const data = {};

        inputs.forEach(input => {
            const column = input.getAttribute('data-column');
            data[column] = input.value;
        });

        // Gọi AJAX để cập nhật dữ liệu
        $.post("<?= \yii\helpers\Url::to(['tabs/update-data']) ?>", {
            id: rowId,
            data: data
        }, function () {
            location.reload(); // Tải lại trang để cập nhật dữ liệu
        });
    }

    function addData(input) {
        const column = input.getAttribute('data-column');
        const value = input.value;

        // Gọi AJAX để thêm dữ liệu mới
        $.post("<?= \yii\helpers\Url::to(['tabs/create-data', 'tableName' => $tableName]) ?>", {
            column: column,
            value: value
        });
    }

    function deleteRow(id) {
        // Gọi AJAX để xóa hàng
        $.post("<?= \yii\helpers\Url::to(['tabs/delete-data']) ?>", {
            id: id
        }, function () {
            location.reload(); // Tải lại trang để cập nhật dữ liệu
        });
    }
</script>