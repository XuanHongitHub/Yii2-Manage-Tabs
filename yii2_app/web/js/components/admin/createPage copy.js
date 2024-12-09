$(document).ready(function () {
    toggleTabInputs();
    editor1 = new RichTextEditor("#richtext-editor");

    $(document).off('input change', '#pageName, #tableName, .form-control').on('input change', '#pageName, #tableName, .form-control', function () {
        debouncedValidateField(this);
    });

    function debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    const debouncedValidateField = debounce(function (inputElement) {
        validateField(inputElement);
        if ($('#type').val() === 'table') {
            $('#columnsContainer tr').each(function (index) {
                let columnName = $(this).find(`#column-name-${index}`).val();
                let dataType = $(this).find(`#data-type-${index}`).val();
                let dataSize = $(this).find(`#data_size-${index}`).val();
                validateColumn(index, columnName, dataType, dataSize);
            });
        }
    }, 500);

    let pageExists = false;
    let tableExists = false;

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

        let url = fieldId === 'pageName' ? check_exist_url :
            check_exist_url;
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
            success: function (response) {
                if (fieldId === 'pageName') {
                    pageExists = response.pageExists;
                    handleExistenceResponse(pageExists, 'page', '#pageName', '#pageNameError');
                } else {
                    tableExists = response.tableExists;
                    handleExistenceResponse(tableExists, 'table', '#tableName', '#tableNameError');
                }
            },
            error: function () {
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

    $(document).off('click', '#btn-store-page').on('click', '#btn-store-page', function (event) {
        let errors = [];

        $('#pageName, #tableName').each(function () {
            if ($(this).val() === '' && $(this).is(':visible')) {
                validateField(this);
                errors.push(`${$(this).attr('id')} không được để trống!`);
            }
        });

        if ($('#type').val() === 'table') {
            $('#tableName').each(function () {
                if ($(this).val() === '' && $(this).is(':visible')) {
                    validateField(this);
                    errors.push('Tên bảng không được để trống!');
                }
            });

            $('#columnsContainer tr').each(function (index) {
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
});

function toggleTabInputs() {
    var type = $('#type').val();
    var richTextInputs = $('#richTextInputs');
    var tableInputs = $('#tableInputs');
    var tableNameInput = $('#tableNameInput');

    if (type === 'richtext') {
        richTextInputs.show();
        tableInputs.hide();
        tableNameInput.hide();
    } else {
        // tableInputs.show();
        richTextInputs.hide();
        tableNameInput.show();

    }
}


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