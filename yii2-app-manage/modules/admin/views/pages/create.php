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
                                    <select name="type" id="type" class="form-select" onchange="toggleTabInputs()">
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
                                                            id="column-name" placeholder="Column Name">
                                                        <div class="text-danger column-error" id="column-name-error">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select name="data_types[]" class="form-select" id="data-type">
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
                                                            <option value="<?= $option ?>"><?= $option ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="text-danger data-type-error" id="data-type-error">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="data_sizes[]" class="form-control"
                                                            id="data-size" placeholder="Length">
                                                        <div class="text-danger data-size-error" id="data-size-error">
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="is_not_null[]" value="1"
                                                            class="form-check-input" id="is-not-null">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="is_primary[]" value="1"
                                                            class="form-check-input" id="is-primary">
                                                        <div class="text-danger primary-error" id="primary-error"></div>
                                                    </td>
                                                    <td>
                                                        <i class="fa-solid fa-square-minus text-danger fs-3"
                                                            style="cursor: pointer;" onclick="removeColumn(this)"></i>
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
    toggleTabInputs();

    $(document).on('input', '#pageName, #tableName', function() {
        validateField(this);
    });

    $(document).on('change', '#type', function() {
        toggleTabInputs();
    });

    let pageExists = false;
    let tableExists = false;

    $(document).on('submit', 'form', function(event) {
        let isValid = true;

        // Kiểm tra các trường bắt buộc
        $('#pageName, #tableName').each(function() {
            if ($(this).val() === '' && $(this).is(':visible')) {
                validateField(this);
                isValid = false;
            }
        });

        // Kiểm tra tableName nếu loại là 'table' hoặc 'richtext'
        if ($('#type').val() === 'table') {
            $('#tableName').each(function() {
                if ($(this).val() === '' && $(this).is(':visible')) {
                    validateField(this);
                    isValid = false;
                }
            });

            // Kiểm tra các cột
            $('#columnsContainer tr').each(function() {
                let columnName = $(this).find('input[name="columns[]"]').val();
                let dataType = $(this).find('select[name="data_types[]"]').val();
                let dataSize = $(this).find('input[name="data_sizes[]"]').val();
                let isValidColumn = validateColumn(columnName, dataType, dataSize);

                if (!isValidColumn) {
                    isValid = false;
                    $(this).find('input[name="columns[]"]').css('border', '1px solid red')
                        .addClass('is-invalid');
                    $(this).find('select[name="data_types[]"]').css('border', '1px solid red')
                        .addClass('is-invalid');
                    $(this).find('input[name="data_sizes[]"]').css('border', '1px solid red')
                        .addClass('is-invalid');
                }
            });
        } else if ($('#type').val() === 'richtext') {
            $('#tableNameInput').hide();
            $('#tableInputs').hide();
        }

        // Kiểm tra kết quả tổng thể
        if (!isValid) {
            event.preventDefault();
            showToast('Vui lòng kiểm tra lại các trường bắt buộc!');
        }
    });

    function validateColumn(columnName, dataType, dataSize) {
        let isValid = true;

        // Kiểm tra tên cột hợp lệ
        if (!/^[a-zA-Z0-9_]+$/.test(columnName)) {
            $('#column-name-error').text('Tên cột chỉ có thể bao gồm chữ cái, số và dấu gạch dưới.');
            isValid = false;
        } else {
            $('#column-name-error').text('');
        }

        // Kiểm tra kiểu dữ liệu hợp lệ
        const validDataTypes = ['INT', 'BIGINT', 'SMALLINT', 'TINYINT', 'FLOAT', 'DOUBLE', 'DECIMAL', 'VARCHAR',
            'CHAR', 'TEXT',
            'MEDIUMTEXT', 'LONGTEXT', 'DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'BOOLEAN', 'JSON', 'BLOB'
        ];
        if (!validDataTypes.includes(dataType)) {
            $('#data-type-error').text('Kiểu dữ liệu không hợp lệ.');
            isValid = false;
        } else {
            $('#data-type-error').text('');
        }

        // Kiểm tra độ dài cho các kiểu dữ liệu có độ dài (VARCHAR, CHAR, DECIMAL, FLOAT, ...)
        if (['VARCHAR', 'CHAR'].includes(dataType)) {
            if (!dataSize || isNaN(dataSize) || dataSize <= 0 || dataSize > 1000) {
                $('#data-size-error').text('Độ dài phải là một số lớn hơn 0 và nhỏ hơn 1000.');
                isValid = false;
            } else {
                $('#data-size-error').text('');
            }
        }

        if (['DECIMAL', 'FLOAT', 'DOUBLE'].includes(dataType)) {
            if (!dataSize || isNaN(dataSize) || dataSize < 0 || dataSize > 38) {
                $('#data-size-error').text('Kích thước phải là một số từ 0 đến 38.');
                isValid = false;
            } else {
                $('#data-size-error').text('');
            }
        }

        // Kiểm tra các kiểu dữ liệu không cần kích thước (TEXT, BLOB, ...)
        const invalidDataSizeTypes = ['TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'DATE', 'DATETIME', 'TIMESTAMP', 'TIME',
            'BOOLEAN', 'JSON', 'BLOB'
        ];
        if (invalidDataSizeTypes.includes(dataType) && dataSize) {
            $('#data-size-error').text('Các kiểu dữ liệu này không yêu cầu kích thước.');
            isValid = false;
        }

        return isValid;
    }

    // Hàm kiểm tra và hiển thị lỗi cho từng trường input
    function validateField(inputElement) {
        let fieldId = $(inputElement).attr('id');
        let fieldValue = $(inputElement).val();
        let errorMessage = $('#' + fieldId + 'Error');
        let fieldName = fieldId === 'pageName' ? 'Page' : 'Table';

        // Kiểm tra trường trống và kiểm tra sự tồn tại
        checkIfNameExists(fieldValue, fieldId, fieldName);
    }

    function checkIfNameExists(name, fieldId, fieldName) {
        // Kiểm tra nếu trường bị trống
        if (name === '') {
            let errorMessageText = `${fieldName} không được để trống!`;
            $('#' + fieldId + 'Error').text(errorMessageText).show();
            $('#' + fieldId).css('border', '1px solid red').addClass('is-invalid').removeClass('is-valid');
            return; // Dừng lại nếu trường bị trống
        }

        // Kiểm tra tên có chứa ký tự đặc biệt không hợp lệ
        const invalidCharsPattern = /[^a-zA-Z0-9_]/; // Chỉ cho phép chữ cái, số và dấu gạch dưới
        if (invalidCharsPattern.test(name)) {
            let errorMessageText = `${fieldName} chỉ được phép chứa chữ cái, số và dấu gạch dưới (_).`;
            $('#' + fieldId + 'Error').text(errorMessageText).show();
            $('#' + fieldId).css('border', '1px solid red').addClass('is-invalid').removeClass('is-valid');
            return; // Dừng lại nếu tên chứa ký tự đặc biệt
        }

        // Nếu trường không trống và không có ký tự đặc biệt, tiếp tục kiểm tra sự tồn tại
        let url = fieldId === 'pageName' ? '<?= \yii\helpers\Url::to(['pages/check-name-existence']) ?>' :
            '<?= \yii\helpers\Url::to(['pages/check-name-existence']) ?>';
        let data = fieldId === 'pageName' ? {
            pageName: name
        } : {
            tableName: name
        };

        $.ajax({
            url: url,
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: function(response) {
                if (fieldId === 'pageName') {
                    pageExists = response.pageExists;
                    handleExistenceResponse(pageExists, 'page', '#pageName', '#pageNameError');
                } else {
                    tableExists = response.tableExists;
                    handleExistenceResponse(tableExists, 'table', '#tableName', '#tableNameError');
                }
            },
            error: function() {
                alert(`Có lỗi xảy ra khi kiểm tra sự tồn tại tên ${fieldName}.`);
            }
        });
    }


    function handleExistenceResponse(exists, type, inputSelector, errorSelector) {
        if (exists) {
            $(errorSelector).text(`${type === 'page' ? 'Tên Page' : 'Tên Bảng'} đã tồn tại.`).show();
            $(inputSelector).css('border', '1px solid red').addClass('is-invalid').removeClass('is-valid');
            $('#tableInputs').hide(); // Ẩn form nhập columns nếu tồn tại
        } else {
            $(errorSelector).text('').hide();
            $(inputSelector).css('border', '1px solid green').addClass('is-valid').removeClass('is-invalid');
            $('#tableInputs').show(); // Hiển thị lại form nhập columns nếu không tồn tại
        }
    }

});

// Hàm kiểm tra và hiển thị input khi thay đổi loại
function toggleTabInputs() {
    var type = $('#type').val();
    var tableInputs = $('#tableInputs');
    var tableNameInput = $('#tableNameInput');

    if (type === 'richtext') {
        tableInputs.hide();
        tableNameInput.hide();
    } else {
        tableInputs.show();
        tableNameInput.show();
    }
}

function addColumn() {
    const columnsContainer = document.getElementById('columnsContainer');
    const inputGroup = document.createElement('tr');
    inputGroup.innerHTML =
        `<td><input type="text" name="columns[]" class="form-control" placeholder="Length"></td>
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