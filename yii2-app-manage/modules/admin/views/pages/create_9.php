<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
$this->title = 'Create Pages';

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
                        <h4>Thêm Mới Page</h4>
                        <p class="mt-1 f-m-light">Thêm Table Page | Richtext Page</p>
                    </div>
                    <div class="card-body">
                        <form action="<?= \yii\helpers\Url::to(['store']) ?>" method="post">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                            <div class="row">
                                <!-- Tên Page -->
                                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3">
                                    <label for="pageName" class="form-label">Tên Page</label>
                                    <input type="text" name="pageName" class="form-control" id="pageName">
                                    <span id="pageNameError" class="text-danger"></span>
                                </div>

                                <!-- Chọn loại page -->
                                <div class="col-12 col-md-6 col-lg-3 col-xl-2 mb-3">
                                    <label for="type" class="form-label">Loại Page</label>
                                    <select name="type" id="type" class="form-select">
                                        <option value="richtext">Rich Text</option>
                                        <option value="table">Table</option>
                                    </select>
                                </div>

                                <!-- TABLE NAME -->
                                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3" id="tableNameInput">
                                    <label for="tableName" class="form-label">Tên Bảng</label>
                                    <input type="text" name="tableName" class="form-control" id="tableName">
                                    <span id="tableNameError" class="text-danger"></span>
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
                                                <tr>
                                                    <td>
                                                        <input type="text" name="columns[]" class="form-control"
                                                            id="column-name-0" placeholder="Column Name">
                                                        <div class="text-danger column-error" id="column-name-error-0">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select name="data_types[]" class="form-select"
                                                            id="data-type-0">
                                                            <?php
                                                            $dataTypeOptions = [
                                                                "SERIAL",
                                                                "FLOAT",
                                                                "DOUBLE",
                                                                "DECIMAL",
                                                                "VARCHAR",
                                                                "CHAR",
                                                                "TEXT",
                                                                "LONGTEXT",
                                                                "DATE",
                                                                "DATETIME",
                                                                "TIMESTAMP",
                                                                "TIME",
                                                                "BOOLEAN",
                                                            ];
                                                            foreach ($dataTypeOptions as $option): ?>
                                                                <option value="<?= $option ?>"><?= $option ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="text-danger data-type-error" id="data-type-error-0">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="data_sizes[]" class="form-control"
                                                            id="data-size-0" placeholder="Length">
                                                        <div class="text-danger data-size-error" id="data-size-error-0">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="is_not_null[]" value="1"
                                                            class="form-check-input" id="is-not-null-0" checked>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="is_primary[]" value="1"
                                                            class="form-check-input" id="is-primary-0" checked>
                                                        <div class="text-danger primary-error" id="primary-error-0">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <!-- <i class="fa-solid fa-square-minus text-danger fs-3" style="cursor: pointer;" onclick="removeColumn(this)"></i> -->
                                                    </td>
                                                </tr>
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
        // Ẩn các trường nhập liệu liên quan đến bảng ngay từ đầu
        $('#tableNameInput').hide();
        $('#tableInputs').hide();

        // Hàm kiểm tra pageName (chỉ chứa chữ có dấu, số và gạch dưới)
        function validatePageName(pageName) {
            var regex =
                /^[a-zA-Z0-9_ÀÁÂÃÄÈÉÊÌÍÒÓÔÕÖÙÚÔÜàáạãảắặằữỉỉỳýỳăặ]/; // Chỉ cho phép chữ có dấu, số và gạch dưới
            return regex.test(pageName);
        }

        // Hàm kiểm tra tableName (không có ký tự đặc biệt)
        function validateTableName(tableName) {
            var regex = /^[a-zA-Z0-9_]+$/; // Không có ký tự đặc biệt, chỉ cho phép chữ, số và gạch dưới
            return regex.test(tableName);
        }

        // Hàm kiểm tra tên cột (không trống, không ký tự đặc biệt)
        function validateColumnName(columnName) {
            var regex = /^[a-zA-Z0-9_]+$/; // Không có ký tự đặc biệt, chỉ cho phép chữ, số và gạch dưới
            return regex.test(columnName) && columnName.trim() !== '';
        }

        // Hàm kiểm tra loại dữ liệu (size chuẩn SQL)
        function validateDataSize(dataSize, dataType) {
            var isValid = true;
            if (dataType === "VARCHAR" || dataType === "CHAR") {
                if (dataSize < 1 || dataSize > 1000) {
                    isValid = false;
                }
            }
            return isValid;
        }

        // Kiểm tra pageName ngay lập tức
        $(document).on('input', '#pageName', function() {
            var pageName = $(this).val();

            // Kiểm tra nếu pageName trống
            if (pageName.trim() === '') {
                $('#pageNameError').text('Tên trang không được để trống').show();
                $('#pageName').addClass('is-invalid').removeClass('is-valid');
                $('#type').prop('disabled', true); // Vô hiệu hóa chọn loại khi pageName không hợp lệ
            } else if (!validatePageName(pageName)) {
                $('#pageNameError').text('Tên trang chỉ được phép chứa chữ có dấu, số và gạch dưới').show();
                $('#pageName').addClass('is-invalid').removeClass('is-valid');
                $('#type').prop('disabled', true); // Vô hiệu hóa chọn loại khi pageName không hợp lệ
            } else {
                // Kiểm tra tồn tại pageName
                checkPageNameExistence(pageName);
            }

            // Xử lý lại hiển thị tableNameInput khi pageName hợp lệ
            handleTableNameInputDisplay();
        });

        // Kiểm tra loại page khi chọn
        $(document).on('change', '#type', function() {
            handleTableNameInputDisplay();
        });

        // Hàm kiểm tra và hiển thị tableNameInput
        function handleTableNameInputDisplay() {
            var pageName = $('#pageName').val();
            var type = $('#type').val();

            if (pageName && !$('#pageName').hasClass('is-invalid') && type === 'table') {
                $('#tableNameInput').show(); // Hiển thị tableNameInput nếu pageName hợp lệ và type là 'table'
            } else {
                $('#tableNameInput')
                    .hide(); // Ẩn tableNameInput nếu pageName không hợp lệ hoặc type không phải 'table'
                $('#tableInputs').hide(); // Ẩn phần input của bảng
            }
        }

        // Kiểm tra pageName có tồn tại không
        function checkPageNameExistence(pageName) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['pages/check-name-existence']) ?>',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'POST',
                data: {
                    pageName: pageName
                },
                success: function(response) {
                    if (response.pageExists) {
                        $('#pageNameError').text('Tên trang đã tồn tại').show();
                        $('#pageName').addClass('is-invalid').removeClass('is-valid');
                        $('#type').prop('disabled', true);
                    } else {
                        $('#pageNameError').text('').hide();
                        $('#pageName').addClass('is-valid').removeClass('is-invalid');
                        $('#type').prop('disabled', false);
                    }
                }
            });
        }

        // Kiểm tra tableName khi người dùng nhập vào
        $(document).on('input', '#tableName', function() {
            var tableName = $(this).val();

            if (tableName.trim() === '') {
                $('#tableNameError').text('Tên bảng không được để trống').show();
                $('#tableName').addClass('is-invalid').removeClass('is-valid');
                $('#tableInputs').hide(); // Ẩn phần nhập liệu bảng khi tableName không hợp lệ
            } else if (!validateTableName(tableName)) {
                $('#tableNameError').text('Tên bảng chỉ được phép chứa chữ, số và gạch dưới').show();
                $('#tableName').addClass('is-invalid').removeClass('is-valid');
                $('#tableInputs').hide(); // Ẩn phần nhập liệu bảng khi tableName không hợp lệ
            } else {
                // Kiểm tra tồn tại tableName trên server
                checkTableNameExistence(tableName);
            }
        });

        // Kiểm tra sự tồn tại của tableName trên server
        function checkTableNameExistence(tableName) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['pages/check-name-existence']) ?>',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'POST',
                data: {
                    tableName: tableName
                },
                success: function(response) {
                    if (response.tableExists) {
                        $('#tableNameError').text('Tên bảng đã tồn tại').show();
                        $('#tableName').addClass('is-invalid').removeClass('is-valid');
                        $('#tableInputs').hide();
                    } else {
                        $('#tableNameError').text('').hide();
                        $('#tableName').addClass('is-valid').removeClass('is-invalid');
                        $('#tableInputs').show();
                    }
                }
            });
        }

        // Kiểm tra các cột (columns) khi người dùng nhập vào
        $(document).on('input', '.column-name', function() {
            var columnName = $(this).val();
            var index = $(this).data('index');

            if (columnName.trim() === '') {
                $('#column-name-error-' + index).text('Tên cột không được để trống').show();
                $('#column-name-' + index).addClass('is-invalid').removeClass('is-valid');
            } else if (!validateColumnName(columnName)) {
                $('#column-name-error-' + index).text(
                    'Tên cột không hợp lệ. Chỉ cho phép chữ, số và gạch dưới').show();
                $('#column-name-' + index).addClass('is-invalid').removeClass('is-valid');
            } else {
                $('#column-name-error-' + index).text('').hide();
                $('#column-name-' + index).addClass('is-valid').removeClass('is-invalid');
            }
        });

        // Kiểm tra dữ liệu loại cột (data type)
        $(document).on('change', '.data-type', function() {
            var dataType = $(this).val();
            var index = $(this).data('index');

            var validDataTypes = ["SERIAL", "FLOAT", "DOUBLE", "DECIMAL", "VARCHAR", "CHAR", "TEXT",
                "LONGTEXT", "DATE", "DATETIME", "TIMESTAMP", "TIME", "BOOLEAN"
            ];
            if (!validDataTypes.includes(dataType)) {
                $('#data-type-error-' + index).text('Loại dữ liệu không hợp lệ').show();
                $('#data-type-' + index).addClass('is-invalid').removeClass('is-valid');
            } else {
                $('#data-type-error-' + index).text('').hide();
                $('#data-type-' + index).addClass('is-valid').removeClass('is-invalid');
            }
        });

        // Kiểm tra dữ liệu kích thước khi có sự thay đổi (data-size)
        $(document).on('input', '.data-size', function() {
            var dataSize = $(this).val();
            var dataType = $(this).closest('tr').find('.data-type').val();
            var index = $(this).data('index');

            if (!validateDataSize(dataSize, dataType)) {
                $('#data-size-error-' + index).text('Kích thước không hợp lệ cho kiểu dữ liệu ' + dataType)
                    .show();
                $('#data-size-' + index).addClass('is-invalid').removeClass('is-valid');
            } else {
                $('#data-size-error-' + index).text('').hide();
                $('#data-size-' + index).addClass('is-valid').removeClass('is-invalid');
            }
        });

        // Kiểm tra trước khi submit
        $(document).on('submit', '#form', function(event) {

            var tableNameValid = $('#tableName').hasClass('is-valid');
            var columnValid = true;
            var typeValid = $('#type').prop('disabled') === false;

            $('.column-name').each(function() {
                var index = $(this).data('index');
                if (!$('#column-name-' + index).hasClass('is-valid')) {
                    columnValid = false;
                }
            });

            if (!pageNameValid || !tableNameValid || !columnValid || !typeValid) {
                event.preventDefault();
                alert('Vui lòng sửa các lỗi trước khi tiếp tục');
            }
        });
    });


    // Kiểm tra và chỉ chọn một checkbox 'is_primary' duy nhất
    $(document).on('change', 'input[name="is_primary"]', function() {
        $('input[name="is_primary"]').not(this).prop('checked', false);
    });



    function addColumn() {
        const rowIndex = $('#columnsContainer tr').length; // Lấy số dòng hiện tại trong tbody
        const newRow = `
            <tr>
                <td>
                    <input type="text" name="columns[]" class="form-control" id="column-name-${rowIndex}" placeholder="Column Name">
                    <div class="text-danger column-error" id="column-name-error-${rowIndex}"></div>
                </td>
                <td>
                    <select name="data_types[]" class="form-select" id="data-type-${rowIndex}">
                            <option value="TEXT" selected>TEXT<option>
                        <?php foreach ($dataTypeOptions as $option): ?>
                            <option value="<?= $option ?>"><?= $option ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-danger data-type-error" id="data-type-error-${rowIndex}"></div>
                </td>
                <td>
                    <input type="number" name="data_sizes[]" class="form-control" id="data-size-${rowIndex}" placeholder="Length">
                    <div class="text-danger data-size-error" id="data-size-error-${rowIndex}"></div>
                </td>
                <td class="text-center">
                    <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input" id="is-not-null-${rowIndex}">
                </td>
                <td class="text-center">
                    <input type="checkbox" name="is_primary[]" value="1" class="form-check-input" id="is-primary-${rowIndex}">
                    <div class="text-danger primary-error" id="primary-error-${rowIndex}"></div>
                </td>
                <td>
                    <i class="fa-solid fa-square-minus text-danger fs-3" style="cursor: pointer;" onclick="removeColumn(this)"></i>
                </td>
            </tr>
        `;
        $('#columnsContainer').append(newRow);
    }


    function removeColumn(button) {
        const row = button.closest('tr');
        row.remove();
    }
</script>