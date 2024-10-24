<?php
$tabId = $_GET['tab_id'];
?>

<?php if (!empty($data)): ?>
<div class="row mb-3">
    <div class="col-3">
        <label for="characterSet" class="btn btn-warning">
            Charset: <?= htmlspecialchars($collation) ?>
        </label>
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
            <td><input type="checkbox" class="row-checkbox"></td>
            <?php foreach ($columns as $column): ?>
            <td>
                <input type="text" value="<?= htmlspecialchars($row[$column->name]) ?>" class="form-control data-input"
                    data-row-index="<?= $rowIndex ?>" data-table-name="<?= $tableName ?>"
                    data-column="<?= $column->name ?>"
                    data-original-value="<?= htmlspecialchars($row[$column->name]) ?>" onfocus="enableEdit(this)">
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
                <button class="btn btn-primary" id="add-row-btn">Thêm</button>
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
                    class="form-control new-data-input" data-tab-id=""
                    data-column="<?= htmlspecialchars($column->name) ?>">
            </td>
            <?php endforeach; ?>
            <td>
                <button class="btn btn-primary" id="add-row-btn">Thêm</button>
            </td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
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
                    console.log("Thêm thành công", response);
                    loadTabData(tabId);
                } else {
                    alert(response.message || "Thêm dữ liệu thất bại.");
                }
            },
            error: function(error) {
                console.log("Thêm dữ liệu thất bại", error);
                alert("Có lỗi xảy ra khi thêm dữ liệu.");
            }
        });
    });
});

// Hàm để xử lý htmlspecialchars
function htmlspecialchars(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function enableEdit(inputElement) {
    var tableName = inputElement.getAttribute('data-table-name');
    var rowIndex = inputElement.getAttribute('data-row-index');
    var column = inputElement.getAttribute('data-column');
    var value = inputElement.value;

    console.log("Table: ", tableName, "Row Index:", rowIndex, "Column:", column, "New Value:", value);
}

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
                console.log("Lưu thành công", response);
                inputs.forEach(function(input) {
                    input.setAttribute('data-original-value', input.value);
                });
            } else {
                alert(response.message || "Lưu dữ liệu thất bại.");
            }
        },
        error: function(error) {
            console.log("Lưu dữ liệu thất bại", error);
            alert("Có lỗi xảy ra khi lưu dữ liệu.");
        }
    });
}
$(document).ready(function() {
    // Khởi tạo DataTable
    $('.dataTable').DataTable({
        columnDefs: [{
            orderable: false,
            targets: 0
        }],
        "lengthChange": false,
        "autoWidth": false,
        "responsive": true,
        "paging": true,
        "searching": true,
        "ordering": true,
        "language": {
            "info": "Hiển thị từ _START_ đến _END_ của _TOTAL_ mục",
            "infoEmpty": "Không có dữ liệu",
            "infoFiltered": "(lọc từ _MAX_ bản ghi)",
            "search": "Tìm kiếm:",
            "paginate": {
                "first": "Đầu",
                "last": "Cuối",
                "next": "Tiếp theo",
                "previous": "Trước"
            }
        },
        dom: 'Bftip',
        buttons: [
            'copy', 'excel', 'csv', 'pdf'
        ]
    });
});
</script>