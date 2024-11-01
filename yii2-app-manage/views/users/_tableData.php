<?php

use app\models\User;

$isAdmin = User::isUserAdmin(Yii::$app->user->identity->username);

$tabId = $_GET['tab_id'];


?>
<div class="toast-container position-fixed top-0 end-0 mt-5 p-3">
    <div id="liveToastSuccess" class="toast bg-success text-white" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Successfully!
        </div>
    </div>

    <div id="liveToastError" class="toast bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Error!
        </div>
    </div>
</div>
<div class="d-flex flex-wrap justify-content-between mt-3">
    <div class="d-flex align-items-center me-2 mb-1">
        <div class="btn-group-ellipsis me-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-ellipsis"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item fw-medium text-light-emphasis" href="#" data-bs-toggle="modal"
                        data-bs-target="#hideModal">
                        <i class="fas fa-eye me-1"></i> Show/Hidden Tab
                    </a>
                </li>
                <li>
                    <a class="dropdown-item fw-medium text-light-emphasis" href="#" data-bs-toggle="modal"
                        data-bs-target="#sortModal">
                        <i class="fas fa-sort-amount-down me-1"></i> Sort Order Tab
                    </a>
                </li>
                <li>
                    <a class="dropdown-item fw-medium text-light-emphasis" href="#" data-bs-toggle="modal"
                        data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt me-1"></i> Delete Tab
                    </a>
                </li>
                <li>
                    <a class="dropdown-item fw-medium text-light-emphasis" href="#" data-bs-toggle="modal"
                        data-bs-target="#trashBinModal">
                        <i class="fas fa-trash me-1"></i> Trash Bin
                    </a>
                </li>
            </ul>
        </div>
        <button class="btn btn-danger" id="delete-selected-btn">
            <i class="fa-regular fa-trash-can"></i> Delete selected
        </button>
    </div>


</div>

<?php if (!empty($data)): ?>
<table class="display border dataTable dataTable">
    <thead>
        <tr>
            <th class="sorting_disabled" scope="col"><input type="checkbox" id="select-all"></th>
            <?php foreach ($columns as $column): ?>
            <th scope="col"><?= $column->name ?></th>
            <?php endforeach; ?>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $rowIndex => $row): ?>
        <tr>
            <td scope="col">
                <input type="checkbox" class="row-checkbox" data-row="<?= $rowIndex ?>"
                    data-table-name="<?= $tableName ?>">
            </td>

            <?php foreach ($columns as $column): ?>
            <td>
                <span class="data-display" data-value="<?= $row[$column->name] ?>">
                    <?= $row[$column->name] ?>
                </span>
            </td>
            <?php endforeach; ?>
            <td style="white-space: nowrap">
                <button class="btn btn-success btn-sm save-btn"
                    onclick="saveRow(<?= $rowIndex ?>, '<?= $tableName ?>')"><i
                        class="fa-regular fa-floppy-disk"></i></button>
                <button class="btn btn-danger btn-sm" onclick="deleteRow(<?= $rowIndex ?>, '<?= $tableName ?>')"><i
                        class="fa-regular fa-trash-can"></i></button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <?php foreach ($columns as $column): ?>
            <td>
                <input type="text" placeholder="<?= htmlspecialchars($column->name) ?>"
                    class="form-control new-data-input" data-column="<?= htmlspecialchars($column->name) ?>">
            </td>
            <?php endforeach; ?>
            <td>
                <button class="btn btn-primary" id="add-row-btn">Add</button>
            </td>
        </tr>
    </tfoot>
</table>
<?php else: ?>
<table class="table table-bordered table-hover dataTable">
    <thead>
        <tr>
            <th class="sorting_disabled" scope="col"><input type="checkbox" id="select-all"></th>
            <?php foreach ($columns as $column): ?>
            <th scope="col"><?= htmlspecialchars($column->name) ?></th>
            <?php endforeach; ?>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <?php foreach ($columns as $column): ?>
            <td>
                <input type="text" placeholder="<?= htmlspecialchars($column->name) ?>"
                    class="form-control new-data-input" data-column="<?= htmlspecialchars($column->name) ?>">
            </td>
            <?php endforeach; ?>
            <td>
                <button class="btn btn-primary" id="add-row-btn">Add</button>
            </td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>

<!-- Modal Confirm Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm remove tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this tab? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete</button>
                <?php if ($isAdmin): ?>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete permanently</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
var tabId = <?= json_encode($tabId) ?>;
var columns = <?= json_encode(array_map(function ($column) {
    return htmlspecialchars($column->name);
}, $columns)) ?>;
var tabtype = 'table';
// Data Table
$(document).ready(function() {
    $('.dataTable').DataTable({
        order: [],
        columns: generateColumnsConfig($('.dataTable th').length),
        "lengthChange": false,
        "autoWidth": false,
        "responsive": true,
        "paging": true,
        "searching": true,
        "ordering": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= \yii\helpers\Url::to(['tabs/get-data']) ?>",
            "type": "POST",
            "data": function(d) {
                d.tabId = tabId;
            }
        },
    });
});


function generateColumnsConfig(columnCount) {
    var columns = [];
    for (var i = 0; i < columnCount; i++) {
        if (i === 0 || i === columnCount - 1) {
            columns.push({
                orderable: false
            });
        } else {
            columns.push(null);
        }
    }
    return columns;
}

// Selected all checkbox + Add data
$(document).ready(function() {
    var tabId = <?= json_encode($tabId) ?>;
    $('#select-all').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
    });
    $('#add-row-btn').on('click', function() {
        var tableName = '<?= $tableName ?>';
        var newData = {};
        $('.new-data-input').each(function() {
            var column = $(this).data('column');
            var value = $(this).val();
            newData[column] = value;
        });
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/add-data']) ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                table: tableName,
                data: newData
            },
            success: function(response) {
                if (response.success) {
                    loadTabData(tabId);

                    var toastElementSuccess = document.getElementById('liveToastSuccess');
                    var toastBodySuccess = toastElementSuccess.querySelector('.toast-body');
                    toastBodySuccess.innerText = "Data added successfully!";

                    var toastSuccess = new bootstrap.Toast(toastElementSuccess, {
                        delay: 3000
                    });
                    toastSuccess.show();
                } else {
                    var toastElementError = document.getElementById('liveToastError');
                    var toastBodyError = toastElementError.querySelector('.toast-body');
                    toastBodyError.innerText = response.message || "Failed to add data.";

                    var toastError = new bootstrap.Toast(toastElementError, {
                        delay: 3000
                    });
                    toastError.show();
                }
            },
            error: function(error) {
                alert("An error occurred while adding data.");
            }
        });
    });
});

function htmlspecialchars(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g,
        '&#039;');
}
$('td').on('click', function() {
    var input = $(this).find('.data-input');
    var display = $(this).find('.data-display');
    if (input.is(':hidden')) {
        display.hide();
        input.show().focus();
    }
});
$('.data-input').on('blur', function() {
    var input = $(this);
    var display = input.siblings('.data-display');
    display.text(input.val()).show();
    input.hide();
});


// Save row
function saveRow(rowIndex, tableName) {
    var inputs = document.querySelectorAll('input[data-row-index="' + rowIndex + '"]');
    var updatedData = {};
    var originalValues = {};
    inputs.forEach(function(input) {
        var column = input.getAttribute('data-column');
        updatedData[column] = input.value;
        originalValues[column] = input.getAttribute('data-original-value');
    });
    $.ajax({
        url: '<?= \yii\helpers\Url::to(['tabs/update-data']) ?>',
        method: 'POST',
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            table: tableName,
            data: updatedData,
            originalValues: originalValues
        },
        success: function(response) {
            if (response.success) {
                inputs.forEach(function(input) {
                    input.setAttribute('data-original-value', input.value);
                });

                var toastElementSuccess = document.getElementById('liveToastSuccess');
                var toastBodySuccess = toastElementSuccess.querySelector('.toast-body');
                toastBodySuccess.innerText = "Data saved successfully!";

                var toastSuccess = new bootstrap.Toast(toastElementSuccess, {
                    delay: 3000
                });
                toastSuccess.show();
            } else {
                var toastElementError = document.getElementById('liveToastError');
                var toastBodyError = toastElementError.querySelector('.toast-body');
                toastBodyError.innerText = response.message || "Failed to save data.";

                var toastError = new bootstrap.Toast(toastElementError, {
                    delay: 3000
                });
                toastError.show();
            }
        },
        error: function(error) {
            alert("An error occurred while saving data.");
        }
    });
}

// Delete Checkbox selected
$('#delete-selected-btn').on('click', function() {
    var selectedCheckboxes = $('.row-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        alert("Please select at least one item to delete.");
        return;
    }

    var tableName = '<?= $tableName ?>';
    var selectedIds = $('.row-checkbox:checked').map(function() {
        return $(this).data('value');
    }).get().filter(Boolean);

    var conditions = [];

    $('.row-checkbox:checked').each(function() {
        var rowIndex = $(this).data('row');
        var inputs = $('input[data-row-index="' + rowIndex + '"]');

        if (inputs.length === 0) {
            return;
        }

        var condition = {};
        inputs.each(function() {
            let columnName = $(this).data('column');
            let columnValue = $(this).val();

            if (columnName && columnName !== 'undefined') {
                condition[columnName] = columnValue ||
                    null;
            }
        });

        if (Object.keys(condition).length > 0) {
            conditions.push(condition);
        } else {}
    });

    if (confirm("Are you sure you want to delete data?")) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/delete-data']) ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                table: tableName,
                ids: selectedIds,
                conditions: conditions
            },
            success: function(response) {
                if (response.success) {
                    loadTabData(tabId);
                } else {
                    alert(response.message || "Deleting data failed.");
                }
            },
            error: function(error) {
                alert("An error occurred while deleting data.");
            }
        });
    }
});


// Delete row
function deleteRow(rowIndex, tableName) {
    var inputs = $('input[data-row-index="' + rowIndex + '"]');
    var condition = {};

    inputs.each(function() {
        let columnName = $(this).data('column');
        let columnValue = $(this).val();
        condition[columnName] = columnValue || null;
    });

    if (confirm("Are you sure you want to delete data?")) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/delete-data']) ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                table: tableName,
                conditions: [condition]
            },
            success: function(response) {
                if (response.success) {
                    loadTabData(tabId);
                } else {
                    alert(response.message || "Deleting data failed.");
                }
            },
            error: function(error) {
                alert("An error occurred while deleting data.");
            }
        });
    }

}


$(document).ready(function() {
    $('#confirm-delete-btn').on('click', function() {
        const tabId = $(this).data('tab-id');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/delete-tab']) ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tabId: tabId,
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                    $('#deleteModal').modal('hide');
                } else {
                    alert(response.message || "Deleting table failed.");
                }
            },
            error: function(error) {
                alert("An error occurred while deleting table.");
            }
        });
    });

    $('#confirm-delete-permanently-btn').on('click', function() {
        const tabId = $(this).data('tab-id');
        var tableName = '<?= $tableName ?>';

        if (confirm("Are you sure you want to delete permanenttly?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['tabs/delete-permanently-tab']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    tabId: tabId,
                    tableName: tableName,
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                        $('#deleteModal').modal('hide');
                    } else {
                        alert(response.message || "Deleting table failed.");
                    }
                },
                error: function(error) {
                    alert("An error occurred while deleting table.");
                }
            });
        }
    });
});
</script>