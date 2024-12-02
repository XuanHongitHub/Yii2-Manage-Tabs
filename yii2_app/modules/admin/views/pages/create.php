<?php

use yii\helpers\Html;
use app\assets\Select2Asset;

Select2Asset::register($this);
/** @var yii\web\View $this */
$this->title = 'Thêm mới Page';

?>


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
                                    <!-- <th>Độ Dài</th> -->
                                    <th class="text-center">Not_Null</th>
                                    <!-- <th class="text-center">A_I</th> -->
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="columnsContainer">
                                <!-- Row đầu tiên mặc định ẩn và có dữ liệu sẵn -->
                                <tr class="default-row">
                                    <td>
                                        <input type="text" name="columns[]" class="form-control" id="column-name-0"
                                            value="hidden_id" placeholder="Column Name" readonly>
                                        <div class="text-danger column-error" id="column-name-error-0">
                                        </div>
                                    </td>
                                    <td>
                                        <select name="data_types[]" class="form-select" id="data-type-0">
                                            <option value="SERIAL" selected>SERIAL</option>
                                            <option value="TEXT">TEXT</option>
                                        </select>
                                        <div class="text-danger data-type-error" id="data-type-error-0">
                                        </div>
                                    </td>
                                    <!-- <td>
                                                        <input type="number" name="data_sizes[]" class="form-control"
                                                            id="data-size-0" placeholder="Length" value="" readonly>
                                                        <div class="text-danger data-size-error" id="data-size-error-0">
                                                        </div>
                                                    </td> -->
                                    <td class="text-center">
                                        <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input"
                                            id="is-not-null-0" checked>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="is_primary[]" value="1" class="form-check-input"
                                            id="is-primary-0" checked>
                                        <div class="text-danger primary-error" id="primary-error-0">
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" name="columns[]" class="form-control" id="column-name-1"
                                            value="" placeholder="Column Name">
                                        <div class="text-danger column-error" id="column-name-error-1">
                                        </div>
                                    </td>
                                    <td>
                                        <select name="data_types[]" class="form-select" id="data-type-1">
                                            <option value="TEXT" selected>TEXT</option>
                                            <option value="INT">NUMBER</option>
                                        </select>
                                        <div class="text-danger data-type-error" id="data-type-error-1">
                                        </div>
                                    </td>
                                    <!-- <td>
                                                        <input type="number" name="data_sizes[]" class="form-control"
                                                            id="data-size-1" placeholder="Length" value="">
                                                        <div class="text-danger data-size-error" id="data-size-error-1">
                                                        </div>
                                                    </td> -->
                                    <td class="text-center">
                                        <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input"
                                            id="is-not-null-1">
                                    </td>
                                    <!-- <td class="text-center">
                                                        <input type="checkbox" name="is_primary[]" value="1"
                                                            class="form-check-input" id="is-primary-1">
                                                        <div class="text-danger primary-error" id="primary-error-1">
                                                        </div>
                                                    </td> -->
                                    <td>
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
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        toggleTabInputs();

        $(document).on('input change', '#pageName, #tableName, .form-control', function() {
            debouncedValidateField(this);
        });

        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => func.apply(this, args), delay);
            };
        }

        const debouncedValidateField = debounce(function(inputElement) {
            validateField(inputElement);
            if ($('#type').val() === 'table') {
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
            let errors = [];

            $('#pageName, #tableName').each(function() {
                if ($(this).val() === '' && $(this).is(':visible')) {
                    validateField(this);
                    errors.push(`${$(this).attr('id')} không được để trống!`);
                }
            });

            if ($('#type').val() === 'table') {
                $('#tableName').each(function() {
                    if ($(this).val() === '' && $(this).is(':visible')) {
                        validateField(this);
                        errors.push('Tên bảng không được để trống!');
                    }
                });

                $('#columnsContainer tr').each(function(index) {
                    let columnName = $(this).find(`#column-name-${index}`).val();
                    let dataType = $(this).find(`#data-type-${index}`).val();
                    let dataSize = $(this).find(`#data_size-${index}`).val();
                    let isValidColumn = validateColumn(index, columnName, dataType, dataSize);

                    if (!isValidColumn) {
                        errors.push('Có lỗi với các cột!');
                    }
                });
            }

            if (pageExists || tableExists) {
                errors.push('Tên Page hoặc Tên Bảng đã tồn tại.');
            }

            if (errors.length > 0) {
                event.preventDefault();
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
                INT: {
                    requiresSize: false
                },
                TEXT: {
                    requiresSize: false
                },
            };

            const config = dataTypeConfig[dataType];

            if (!/^[a-zA-Z0-9_]+$/.test(columnName)) {
                $(columnNameErrorSelector).text('Tên cột chỉ có thể bao gồm chữ cái, số và dấu gạch dưới.');
                isValid = false;
            } else {
                $(columnNameErrorSelector).text('');
            }

            if (config) {
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

        function validateField(inputElement) {
            let fieldId = $(inputElement).attr('id');
            let fieldValue = $(inputElement).val();
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
                        `${fieldName} không được chứa khoảng trắng hoặc ký tự đặc biệt`);
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
            $(inputSelector).css('border', exists ? '1px solid red' : '1px solid green')
                .toggleClass('is-invalid', exists)
                .toggleClass('is-valid', !exists);

            if (type === 'table') {
                $('#tableInputs').toggle(!exists);
            }
        }

        function setFieldValidation(inputSelector, isValid, message = '') {
            const borderColor = isValid ? '1px solid green' : '1px solid red';
            $(inputSelector).css('border', borderColor)
                .toggleClass('is-valid', isValid)
                .toggleClass('is-invalid', !isValid);
            $(inputSelector + 'Error').text(message).toggle(!isValid);
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

    $(document).on('', function() {

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
                    <option value="TEXT">TEXT</option>
                    <option value="INT">NUMBER</option>
                </select>
                <div class="text-danger data-type-error" id="data-type-error-${rowIndex}"></div>
            </td>
            <td class="text-center">
                <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input" id="is-not-null-${rowIndex}">
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