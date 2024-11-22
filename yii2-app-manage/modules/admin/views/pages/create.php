<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
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
                        <form action="<?= \yii\helpers\Url::to(['store']) ?>" method="post">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                            <div class="row">
                                <!-- Tên Page -->
                                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3">
                                    <label for="pageName" class="form-label">Tên Page</label>
                                    <input type="text" name="pageName" class="form-control" id="pageName"
                                        value="<?= $tableCreationData['pageName'] ?? ''; ?>">
                                    <!-- Thông báo lỗi sẽ hiển thị ở đây -->
                                    <?php if (Yii::$app->session->hasFlash('error_page_Name')): ?>
                                        <div class="text-danger"><?= Yii::$app->session->getFlash('error_page_Name') ?>
                                        </div>
                                    <?php else: ?>
                                        <span id="pageNameError" class="text-danger"></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Chọn loại page -->
                                <div class="col-12 col-md-6 col-lg-3 col-xl-2 mb-3">
                                    <label for="type" class="form-label">Loại Page</label>
                                    <select name="type" id="type" class="form-select" onchange="toggleTabInputs()">
                                        <option value="table">Table</option>
                                        <option value="richtext">Rich Text</option>
                                    </select>
                                </div>

                                <!-- TABLE NAME -->
                                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3" id="tableNameInput">
                                    <label for="table_name" class="form-label">Tên Bảng</label>
                                    <input type="text" name="table_name" class="form-control" id="table_name"
                                        value="<?= $tableCreationData['tableName'] ?? ''; ?>">
                                    <?php if (Yii::$app->session->hasFlash('error_tableName')): ?>
                                        <div class="text-danger"><?= Yii::$app->session->getFlash('error_tableName') ?>
                                        </div>
                                    <?php else: ?>
                                        <span id="table_nameError" class="text-danger"></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Phần input cho loại page "Table" -->
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
</div>

<script>
    $(document).ready(function() {
        // Kiểm tra trạng thái ban đầu của form khi trang được tải
        toggleTabInputs();

        // Lắng nghe sự kiện input trên các trường pageName và tableName
        $('#pageName, #table_name').on('input', function() {
            validateField(this);
        });

        // Lắng nghe sự thay đổi trên loại page để toggle các input tương ứng
        $('#type').on('change', function() {
            toggleTabInputs();
        });

        // Hàm kiểm tra trạng thái hiển thị của các inputs dựa trên loại page
        function toggleTabInputs() {
            var type = $('#type').val();
            var tableInputs = $('#tableInputs');
            var tableNameInput = $('#tableNameInput');

            if (type === 'table') {
                tableInputs.show();
                tableNameInput.show();
            } else {
                tableInputs.hide();
                tableNameInput.hide();
            }
        }

        $('form').on('submit', function(event) {
            var isValid = true;

            // Kiểm tra tất cả các trường input và select
            $('#pageName, #table_name').each(function() {
                if ($(this).val() === '') {
                    validateField(this); // Gọi hàm validate cho trường hợp rỗng
                    isValid = false;
                }
            });

            // Kiểm tra bảng các cột
            $('#columnsContainer tr').each(function() {
                var columnName = $(this).find('input[name="columns[]"]').val();
                if (columnName === '') {
                    isValid = false;
                    $(this).find('input[name="columns[]"]').css('border', '1px solid red').addClass(
                        'is-invalid');
                }
            });

            // Nếu form không hợp lệ, ngừng submit
            if (!isValid) {
                event.preventDefault(); // Ngừng submit
                showToast('Vui lòng kiểm tra lại các trường bắt buộc!');

            }
        });

        // Hàm kiểm tra và hiển thị lỗi cho từng trường input
        function validateField(inputElement) {
            var fieldId = $(inputElement).attr('id');
            var fieldValue = $(inputElement).val();
            var errorMessage = $('#' + fieldId + 'Error');
            var fieldName = fieldId === 'pageName' ? 'Page' : 'Table';
            var errorMessageText = '';

            if (fieldValue === '') {
                errorMessageText = `${fieldName} không được để trống!`;
                errorMessage.text(errorMessageText).show();
                $(inputElement).css('border', '1px solid red').addClass('is-invalid').removeClass('is-valid');
            } else {
                errorMessage.text('').hide();
                $(inputElement).css('border', '1px solid green').addClass('is-valid').removeClass('is-invalid');
            }
        }

        function checkIfPageNameExists(pageName) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['pages/check-name-existence']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') // Lấy CSRF token
                },
                data: {
                    pageName: pageName
                },
                success: function(response) {
                    if (response.pageExists) {
                        $('#pageNameError')
                            .text('Tên Page đã tồn tại.')
                            .show();
                        $('#pageName')
                            .css('border', '1px solid red')
                            .addClass('is-invalid')
                            .removeClass('is-valid')
                    } else {
                        $('#pageNameError')
                            .text('')
                            .hide();
                        $('#pageName')
                            .css('border', '1px solid green')
                            .addClass('is-valid')
                            .removeClass('is-invalid');
                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi kiểm tra sự tồn tại tên Page.");
                }
            });
        }

        // Hàm gửi AJAX kiểm tra sự tồn tại của tableName
        function checkIfTableNameExists(tableName) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['pages/check-name-existence']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') // Lấy CSRF token
                },
                data: {
                    tableName: tableName
                },
                success: function(response) {
                    if (response.tableExists) {
                        $('#table_nameError')
                            .text('Tên Bảng đã tồn tại.')
                            .show();
                        $('#tableNameInput input')
                            .css('border', '1px solid red')
                            .addClass('is-invalid')
                            .removeClass('is-valid')

                    } else {
                        $('#table_nameError')
                            .text('')
                            .hide();
                        $('#tableNameInput input')
                            .css('border', '1px solid green')
                            .addClass('is-valid')
                            .removeClass('is-invalid')

                    }
                },
                error: function() {
                    alert("Có lỗi xảy ra khi kiểm tra sự tồn tại tên Bảng.");
                }
            });
        }

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