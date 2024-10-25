<?php
$tabId = $_GET['tab_id'];
?>

<?php if (!empty($data)): ?>
<div class="row mb-3">
    <div class="col-3">
        <button class="btn btn-danger" id="delete-selected-btn">Xóa đã chọn</button>
    </div>
    <div class="col-3 ms-auto">
        <span class="btn btn-warning">
            Charset: <?= htmlspecialchars($collation) ?>
        </span>
    </div>
</div>
<table class="table table-bordered table-hover dataTable">
    <thead class="table-light">
        <tr>
            <th class="sorting_disabled" scope="col"><input type="checkbox" id="select-all"></th>
            <?php foreach ($columns as $column): ?>
            <th scope="col"><?= htmlspecialchars($column->name) ?></th>
            <?php endforeach; ?>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $rowIndex => $row): ?>
        <tr>
            <td>
                <input type="checkbox" class="row-checkbox" data-row="<?= $rowIndex ?>"
                    data-table-name="<?= $tableName ?>">
            </td>

            <?php foreach ($columns as $column): ?>
            <td>
                <span class="data-display" data-value="<?= htmlspecialchars($row[$column->name]) ?>">
                    <?= htmlspecialchars($row[$column->name]) ?>
                </span>
                <input type="text" value="<?= htmlspecialchars($row[$column->name]) ?>" class="form-control data-input"
                    style="display:none;" data-row-index="<?= $rowIndex ?>" data-table-name="<?= $tableName ?>"
                    data-column="<?= $column->name ?>"
                    data-original-value="<?= htmlspecialchars($row[$column->name]) ?>">
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
    <thead class="table-light">
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
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
var tabId = <?= json_encode($tabId) ?>;
var columns = <?= json_encode(array_map(function ($column) {
        return htmlspecialchars($column->name);
    }, $columns)) ?>;

$(document).ready(function() {
    $('.dataTable').DataTable({

        columns: generateColumnsConfig($('.dataTable th').length),
        "lengthChange": false,
        "autoWidth": false,
        "responsive": true,
        "paging": true,
        "searching": true,
        "ordering": true,
        "language": {
            "infoEmpty": "Không có dữ liệu",
            "search": "Tìm kiếm:",
            "paginate": true
        },
        dom: 'Bftip',
        buttons: ['copy', 'excel', 'csv', 'pdf']
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
                } else {
                    alert(response.message || "Add dữ liệu thất bại.");
                }
            },
            error: function(error) {
                alert("Có lỗi xảy ra khi Add dữ liệu.");
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
            } else {
                alert(response.message || "Lưu dữ liệu thất bại.");
            }
        },
        error: function(error) {
            alert("Có lỗi xảy ra khi lưu dữ liệu.");
        }
    });
}
$('#delete-selected-btn').on('click', function() {
    var tableName = '<?= $tableName ?>'; // Tên bảng
    var selectedIds = $('.row-checkbox:checked').map(function() {
        return $(this).data('value'); // Lấy giá trị ID từ checkbox
    }).get().filter(Boolean); // Lọc ra giá trị không rỗng

    console.log("Selected IDs:", selectedIds); // Log danh sách ID đã chọn

    var conditions = []; // Mảng để chứa các điều kiện cho câu lệnh SQL

    $('.row-checkbox:checked').each(function() {
        var rowIndex = $(this).data('row'); // Lấy chỉ số hàng
        console.log("Row Index:", rowIndex); // Log chỉ số hàng

        var inputs = $('input[data-row-index="' + rowIndex + '"]');

        if (inputs.length === 0) {
            console.log("No inputs found for row index:", rowIndex);
            return;
        }

        var condition = {}; // Đối tượng để chứa điều kiện của hàng hiện tại
        inputs.each(function() {
            let columnName = $(this).data('column'); // Lấy tên cột
            let columnValue = $(this).val(); // Lấy giá trị của input

            console.log("Column Name:", columnName, "Value:", columnValue);

            // Chỉ thêm vào điều kiện nếu columnName không phải là undefined và không phải là giá trị trống
            if (columnName && columnName !== 'undefined') {
                // Nếu columnValue trống, bạn có thể muốn xử lý khác đi
                condition[columnName] = columnValue ||
                    null; // Lưu giá trị vào điều kiện, nếu rỗng thì lưu là null
            }
        });

        // Chỉ thêm điều kiện vào mảng nếu có ít nhất một cặp key-value hợp lệ
        if (Object.keys(condition).length > 0) {
            conditions.push(condition);
        } else {
            console.log("No valid conditions found for row index:", rowIndex);
        }
    });

    console.log("Conditions for SQL:", conditions); // Log điều kiện

    $.ajax({
        url: '<?= \yii\helpers\Url::to(['tabs/delete-data']) ?>',
        method: 'POST',
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            table: tableName,
            ids: selectedIds,
            conditions: conditions // Gửi điều kiện đã được tạo
        },
        success: function(response) {
            if (response.success) {
                loadTabData(tabId); // Tải lại dữ liệu
            } else {
                alert(response.message || "Xóa dữ liệu thất bại.");
            }
        },
        error: function(error) {
            alert("Có lỗi xảy ra khi xóa dữ liệu.");
        }
    });
});
</script>