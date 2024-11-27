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
        toggleTabInputs();

        $(document).on('input, change', '#pageName, #tableName, .form-control', function() {
            debouncedValidateField(this);
        });

        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Sử dụng debounce cho validateField
        const debouncedValidateField = debounce(function(inputElement) {
            validateField(inputElement);
            if ($('#type').val() === 'table') {
                // Kiểm tra các cột
                $('#columnsContainer tr').each(function(index) {
                    let columnName = $(this).find(`#column-name-${index}`).val();
                    let dataType = $(this).find(`#data-type-${index}`).val();
                    let dataSize = $(this).find(`#data_size-${index}`).val();
                    validateColumn(index, columnName, dataType, dataSize);
                });
            }
        }, 500);

        let pageExists = false;
        let tableExists = false;

        $(document).on('submit', 'form', function(event) {
            let isValid = true;
            let errors = [];

            // Kiểm tra các trường bắt buộc
            $('#pageName, #tableName').each(function() {
                if ($(this).val() === '' && $(this).is(':visible')) {
                    validateField(this);
                    isValid = false;
                    errors.push(`${$(this).attr('id')} không được để trống!`);
                }
            });

            // Kiểm tra tableName nếu loại là 'table' hoặc 'richtext'
            if ($('#type').val() === 'table') {
                $('#tableName').each(function() {
                    if ($(this).val() === '' && $(this).is(':visible')) {
                        validateField(this);
                        isValid = false;
                        errors.push('Tên bảng không được để trống!');
                    }
                });

                // Kiểm tra các cột
                $('#columnsContainer tr').each(function(index) {
                    let columnName = $(this).find(`#column-name-${index}`).val();
                    let dataType = $(this).find(`#data-type-${index}`).val();
                    let dataSize = $(this).find(`#data_size-${index}`).val();
                    let isValidColumn = validateColumn(index, columnName, dataType, dataSize);

                    if (!isValidColumn) {
                        isValid = false;
                        errors.push('Có lỗi với các cột!');
                    }
                });
            }

            // Kiểm tra kết quả tổng thể
            if (!isValid) {
                event.preventDefault();
                showErrors(errors);
                showToast('Vui lòng kiểm tra lại các trường bắt buộc!');
            }
        });

        function validateColumn(index, columnName, dataType, dataSize) {
            let isValid = true;
            let columnNameErrorSelector = `#column-name-error-${index}`;
            let dataTypeErrorSelector = `#data-type-error-${index}`;
            let dataSizeErrorSelector = `#data-size-error-${index}`;

            const dataTypeConfig = {
                SERIAL: {
                    requiresSize: false
                },
                FLOAT: {
                    requiresSize: true,
                    min: 0,
                    max: 38
                },
                DOUBLE: {
                    requiresSize: true,
                    min: 0,
                    max: 38
                },
                DECIMAL: {
                    requiresSize: true,
                    min: 0,
                    max: 38
                },
                VARCHAR: {
                    requiresSize: true,
                    min: 1,
                    max: 1000
                },
                CHAR: {
                    requiresSize: true,
                    min: 1,
                    max: 255
                },
                TEXT: {
                    requiresSize: false
                },
                BOOLEAN: {
                    requiresSize: false
                },
            };

            const config = dataTypeConfig[dataType];

            // Kiểm tra tên cột hợp lệ
            if (!/^[a-zA-Z0-9_]+$/.test(columnName)) {
                $(columnNameErrorSelector).text('Tên cột chỉ có thể bao gồm chữ cái, số và dấu gạch dưới.');
                isValid = false;
            } else {
                $(columnNameErrorSelector).text('');
            }

            if (config) {
                // Kiểm tra kiểu dữ liệu và kích thước
                if (config.requiresSize) {
                    if (!dataSize || isNaN(dataSize) || dataSize < config.min || dataSize > config.max) {
                        $(dataSizeErrorSelector).text(
                            `Kích thước phải trong khoảng từ ${config.min} đến ${config.max}.`);
                        isValid = false;
                    } else {
                        $(dataSizeErrorSelector).text('');
                    }
                } else if (dataSize) {
                    $(dataSizeErrorSelector).text('Kiểu dữ liệu này không yêu cầu kích thước.');
                    isValid = false;
                }
            } else {
                $(dataTypeErrorSelector).text('Kiểu dữ liệu không hợp lệ.');
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

            checkIfNameExists(fieldValue, fieldId, fieldName);
        }

        function checkIfNameExists(name, fieldId, fieldName) {
            if (name === '') {
                setFieldValidation('#' + fieldId, false, `${fieldName} không được để trống!`);
                return;
            }

            const pageNamePattern = /^[a-zA-ZÀ-ỹà-ỹ0-9_ ]{1,20}$/;
            const otherFieldPattern = /^[a-zA-Z0-9_]+$/;

            if (fieldName === 'Page') {
                if (!pageNamePattern.test(name)) {
                    setFieldValidation('#' + fieldId, false,
                        `${fieldName} Không nhập ký tự đặc biệt, tối đa 20 ký tự.`);
                    return;
                }
            } else {
                if (!otherFieldPattern.test(name)) {
                    setFieldValidation('#' + fieldId, false,
                        `${fieldName} không được chứa khoảng trắng hoặc ký tự đặc biệt`
                    );
                    return;
                }
            }
            setFieldValidation('#' + fieldId, true);

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
            $(errorSelector).text(exists ? `${type === 'page' ? 'Tên Page' : 'Tên Bảng'} đã tồn tại.` : '').toggle(
                exists);
            $(inputSelector)
                .css('border', exists ? '1px solid red' : '1px solid green')
                .toggleClass('is-invalid', exists)
                .toggleClass('is-valid', !exists);

            if (type === 'table') {
                $('#tableInputs').toggle(!exists);
            }
        }

        function setFieldValidation(inputSelector, isValid, message = '') {
            const borderColor = isValid ? '1px solid green' : '1px solid red';
            $(inputSelector)
                .css('border', borderColor)
                .toggleClass('is-valid', isValid)
                .toggleClass('is-invalid', !isValid);
            $(inputSelector + 'Error').text(message).toggle(!isValid);
        }

        function showErrors(errors) {
            const errorList = errors.map(error => `<li>${error}</li>`).join('');
            $('#errorSummary').html(`<ul>${errorList}</ul>`).show();
        }
    });

    function toggleTabInputs() {
        var type = $('#type').val();
        var tableInputs = $('#tableInputs');
        var tableNameInput = $('#tableNameInput');

        if (type === 'richtext') {
            tableInputs.hide();
            tableNameInput.hide();
        } else {
            // tableInputs.show();
            tableNameInput.show();
        }
    }

    $(document).on('change', 'input[name="is_primary[]"]', function() {
        $('input[name="is_primary[]"]').prop('checked', false); // Hủy chọn tất cả
        $(this).prop('checked', true); // Chỉ chọn checkbox hiện tại
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