$(document).ready(function () {
    toggleTabInputs();
    editor1 = new RichTextEditor("#richtext-editor");

    let pageExists = false,
        tableExists = false,
        isTableFromAutocomplete = false;
    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    };

    const debouncedValidateField = debounce(function (inputElement) {
        validateField(inputElement);
    }, 500);

    $(document).on('input change', '#pageName, #tableName, .form-control', function () {
        if ($(this).attr('id') === 'tableName' && !isTableFromAutocomplete) {
            debouncedValidateField(this);
        } else if ($(this).attr('id') !== 'tableName') {
            const tableName = $('#tableName').val();
            debouncedValidateField(this);
        }
    });

    $(document).on('input change', '#columnsContaine .form-control', function () {
        if ($('#type').val() === 'table' && !isTableFromAutocomplete && tableExists == false) {
            $('#columnsContainer tr.new-row').each(function (index) {
                const columnName = $(this).find(`#column-name-${index}`).val();
                validateColumn(index, columnName);
            });
        }
    });

    function validateField(inputElement) {
        const fieldId = $(inputElement).attr('id');
        const fieldValue = $(inputElement).val();
        const fieldName = fieldId === 'pageName' ? 'Page' : 'Table';

        if (fieldId === 'pageName') {
            const pageNamePattern = /^[a-zA-ZÀ-ỹà-ỹ0-9_ ]{1,30}$/;
            if (!pageNamePattern.test(fieldValue)) {
                setFieldValidation(`#${fieldId}`, false, `${fieldName} không nhập ký tự đặc biệt, tối đa 30 ký tự.`);
                return;
            }
        } else {
            if (!isTableFromAutocomplete) {
                const otherFieldPattern = /^[a-zA-Z0-9_]+$/;
                const restrictedTableName = ['table_name', 'manager_page', 'manager_user', 'manager_menu', 'manager_menu_page', 'manager_config', 'migration'];

                if (!otherFieldPattern.test(fieldValue) || restrictedTableName.includes(fieldValue)) {
                    setFieldValidation(`#${fieldId}`, false, `${fieldName} không được chứa khoảng trắng, ký tự đặc biệt hoặc tên bảng bị cấm`);
                    return;
                }
            }
        }
        checkIfNameExists(fieldValue, fieldId, fieldName);
    }
    function checkIfNameExists(name, fieldId, fieldName) {
        if (name === '') {
            setFieldValidation(`#${fieldId}`, false, `${fieldName} không được để trống!`);
            return;
        }

        const url = fieldId === 'pageName' ? check_exist_url : check_exist_url;
        const data = fieldId === 'pageName' ? { pageName: name } : { tableName: name };

        $.ajax({
            url: url,
            method: 'POST',
            headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            data: data,
            success(response) {
                if (fieldId === 'pageName') {
                    pageExists = response.pageExists;
                    handleExistenceResponse(pageExists, 'page', '#pageName', '#pageNameError');
                } else {
                    if (!isTableFromAutocomplete) {
                        tableExists = response.tableExists;
                        handleExistenceResponse(tableExists, 'table', '#tableName', '#tableNameError');
                    } else {
                        handleExistenceResponse(false, 'table', '#tableName', '#tableNameError');
                    }
                }
            },
            error() {
                alert(`Có lỗi xảy ra khi kiểm tra sự tồn tại tên ${fieldName}.`);
            }
        });
    }

    function handleExistenceResponse(exists, type, inputSelector, errorSelector) {
        if (type === 'table') {
            if (exists) {
                $('#tableInputs').hide();
                $(inputSelector).css('border', '1px solid green')
                    .addClass('is-valid')
                    .removeClass('is-invalid');
                $('#tableInputs').hide();
                tableExists = true;
            } else {
                if (!isTableFromAutocomplete) {
                    $(inputSelector).css('border', '1px solid green')
                        .addClass('is-valid')
                        .removeClass('is-invalid');
                    $('#tableInputs').show();
                    $(errorSelector).text('').hide();
                    tableExists = false;
                }
            }
        } else if (type === 'page') {
            if (exists) {
                $(errorSelector).text('Tên Page đã tồn tại')
                    .css('color', 'red')
                    .show();
                $(inputSelector).css('border', '1px solid red')
                    .addClass('is-invalid')
                    .removeClass('is-valid');
                $('#tableInputs').hide();
                pageExists = true;
            } else {
                $(errorSelector).text('').hide();
                $(inputSelector).css('border', '1px solid green')
                    .addClass('is-valid')
                    .removeClass('is-invalid');
                pageExists = false;
            }
        }
    }

    function setFieldValidation(inputSelector, isValid, message = '') {
        const borderColor = isValid ? '1px solid green' : '1px solid red';
        $(inputSelector).css('border', borderColor)
            .toggleClass('is-valid', isValid)
            .toggleClass('is-invalid', !isValid);
        $(inputSelector + 'Error').text(message).toggle(!isValid);
    }

    function validateColumn(index, columnName, dataType, dataSize) {
        let isValid = true;
        const columnNameErrorSelector = `#column-name-error-${index}`;
        const restrictedColumns = ['id'];

        if (!columnName || columnName.trim() === '') {
            $(columnNameErrorSelector).text('Tên cột không được để trống.').show();
            isValid = false;
        } else if (restrictedColumns.includes(columnName.toLowerCase()) || !/^[a-zA-Z0-9_]+$/.test(columnName)) {
            let errorMessage = '';
            if (restrictedColumns.includes(columnName.toLowerCase())) {
                errorMessage = `Tên cột '${columnName}' không được phép sử dụng.`;
            } else {
                errorMessage = 'Tên cột chỉ có thể bao gồm chữ cái, số và dấu gạch dưới.';
            }
            $(columnNameErrorSelector).text(errorMessage).show();
            isValid = false;
        } else {
            const columnNames = [];
            $('#columnsContainer tr').each(function (rowIndex) {
                if (rowIndex !== index) {
                    const otherColumnName = $(this).find(`#column-name-${rowIndex}`).val();
                    columnNames.push(otherColumnName);
                }
            });
            if (columnNames.includes(columnName)) {
                $(columnNameErrorSelector).text(`Tên cột '${columnName}' đã bị trùng.`).show();
                isValid = false;
            } else {
                $(columnNameErrorSelector).text('').hide();
            }
        }

        return isValid;
    }


    $('#tableName').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: get_table_url,
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function (data) {
                    response($.map(data, function (value, key) {
                        return {
                            label: value,
                            value: value
                        };
                    }));
                }
            });
        },
        minLength: 1,
        select: function (event, ui) {
            console.log("Bạn đã chọn bảng: " + ui.item.value);
            $('#tableInputs').hide();
            $('#columnsContainer .new-row').empty();
            $('#tableNameSuccess').text('Sử dụng bảng sẵn có').css('color', 'green').show();

            $('#tableName').val(ui.item.value);
            isTableFromAutocomplete = true;
            tableExists = true;
        },
        close: function () {
            if (!tableExists) {
                isTableFromAutocomplete = false;
                $('#tableInputs').show();
                $('#tableNameSuccess').text('').hide();
            }
        }
    });


    $(document).on('input', '#tableName', function () {
        isTableFromAutocomplete = false;
        $('#tableInputs').show();
        $('#tableNameSuccess').text('').hide();
        tableExists = false;
    });
    $(document).on('click', '#btn-store-page', function (event) {
        let errors = [];

        $('#pageName, #tableName').each(function () {
            if ($(this).val() === '' && $(this).is(':visible')) {
                validateField(this);
                errors.push(`${$(this).attr('id')} không được để trống!`);
            }
        });

        if ($('#type').val() === 'table') {
            if (!isTableFromAutocomplete) {
                $('#tableName').each(function () {
                    if ($(this).val() === '' && $(this).is(':visible')) {
                        validateField(this);
                        errors.push('Tên bảng không được để trống!');
                    }
                });

                $('#columnsContainer tr.new-row').each(function () {
                    const columnName = $(this).find("input[name='columns[]']").val();
                    const dataType = $(this).find("select[name='data_types[]']").val();
                    const isValidColumn = validateColumn($(this).index(), columnName, dataType);
                    if (!isValidColumn) {
                        errors.push(`Lỗi ở cột ${columnName}`);
                    }
                });

            }
        }

        if (pageExists || (tableExists && !isTableFromAutocomplete)) {
            errors.push('Tên Page hoặc Tên Bảng đã tồn tại.');
        }

        if (errors.length > 0) {
            event.preventDefault();
            showToast(`Vui lòng kiểm tra lại các trường bắt buộc!`);
        }
    });
});

function toggleTabInputs() {
    var type = $('#type').val();
    var richTextInputs = $('#richTextInputs');
    var tableInputs = $('#tableInputs');
    var tableNameInput = $('#tableNameInput');
    $('#richTextInputs input, #richTextInputs textarea').val('');
    $('#tableNameInput input').val('');

    if (type === 'richtext') {
        richTextInputs.show();
        tableInputs.hide();
        tableNameInput.hide();
    } else if (type === 'table') {
        richTextInputs.hide();
        tableNameInput.show();
    }
}

function addColumn() {
    const rowIndex = $('#columnsContainer tr').length;

    const newRow = `
        <tr class="new-row">
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