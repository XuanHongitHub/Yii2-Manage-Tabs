<?php

use app\models\User;
use yii\widgets\LinkPager;

$isAdmin = User::isUserAdmin(Yii::$app->user->identity->username);

$tabId = $_GET['tabId'];

$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;
$rowsPerPage = 10;
$globalIndexOffset = $page * $rowsPerPage;

?>
<!-- Thông Báo -->
<div class="toast-container position-fixed top-0 end-0 mt-5 p-3">
    <div id="liveToastSuccess" class="toast bg-success text-white" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Thông Báo</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Đóng"></button>
        </div>
        <div class="toast-body">
            Thành công!
        </div>
    </div>

    <div id="liveToastError" class="toast bg-danger text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <strong class="me-auto">Lỗi</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Đóng"></button>
        </div>
        <div class="toast-body">
            Có lỗi xảy ra!
        </div>
    </div>
</div>

<!-- Modal Nhập Excel -->
<div class="modal fade" id="importExelModal" tabindex="-1" aria-labelledby="importExelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExelModalLabel">Nhập Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <form id="importExcelForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="import-excel-file" class="form-label">Chọn Tệp Excel</label>
                        <input class="form-control" type="file" id="import-excel-file" name="import-excel-file"
                            accept=".xlsx, .xls" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Nhập Excel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác Nhận Nhập-->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Vấn Đề</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body" id="confirmMessage">Bạn có chắc chắn muốn tiếp tục?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmYesBtn">Tiếp tục</button>
            </div>
        </div>
    </div>
</div>

<!-- Trạng Thái Nhập -->
<div class="modal fade" id="importStatusModal" tabindex="-1" aria-labelledby="importStatusModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importStatusModalLabel">Trạng Thái Nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <pre class="modal-body text-wrap" id="importStatusMessage">
            </pre>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- DỮ LIỆU BẢNG -->
<div id="tableData">
    <div class="d-flex flex-wrap justify-content-between mt-3">
        <div class="d-md-flex d-sm-block">
            <button class="btn btn-primary mb-2 me-2" id="add-data-btn" href="#" data-bs-toggle="modal"
                data-bs-target="#addDataModal">
                <i class="fa-solid fa-plus"></i> Nhập Dữ Liệu
            </button>

            <button class="btn btn-danger mb-2 me-2" id="delete-selected-btn">
                <i class="fa-regular fa-trash-can"></i> Xóa Đã Chọn
            </button>

            <!-- Nút Nhập Excel -->
            <button class="btn btn-info mb-2 me-2" id="import-data-btn" href="#" data-bs-toggle="modal"
                data-bs-target="#importExelModal">
                <i class="fa-solid fa-download"></i> Nhập Excel
            </button>

            <!-- Nút Xuất Excel -->
            <button class="btn btn-warning mb-2 me-auto" id="exportExcelButton">
                <i class="fa-solid fa-file-export"></i> Xuất Excel
            </button>
        </div>
        <!-- Tìm Kiếm -->
        <form class="form-inline search-tab mb-2 me-3" action="<?= \yii\helpers\Url::to(['tabs/search-tab-data']) ?>"
            method="get">
            <div class="form-group d-flex align-items-center mb-0">
                <i class="fa fa-search"></i>
                <input type="hidden" name="tabId" value="<?= $tabId ?>">
                <input type="hidden" name="page" value="1">
                <input class="form-control-plaintext" type="text" name="search" placeholder="Tìm kiếm...">
            </div>
        </form>
    </div>

    <table class="display border table-bordered dataTable">
        <thead>
            <tr>
                <th class="px-2 py-0" style="width: 3%;"><input class="" type="checkbox" id="select-all"></th>
                <?php foreach ($columns as $column): ?>
                <th><?= htmlspecialchars($column->name) ?></th>
                <?php endforeach; ?>
                <th style="width: 8%;">Thao Tác</th>
            </tr>
        </thead>
        <?php if (!empty($data)): ?>
        <tbody id="tbodyData">
            <?php foreach ($data as $rowIndex => $row): ?>
            <?php
                    $globalIndex = $globalIndexOffset + $rowIndex + 1;
                    ?>
            <tr>
                <td class="px-2 py-0"><input type="checkbox" class="row-checkbox" data-row="<?= $rowIndex ?>"
                        id="<?= $rowIndex ?>" data-table-name="<?= $tableName ?>">
                </td>
                <?php foreach ($columns as $column): ?>
                <td><?= htmlspecialchars($row[$column->name]) ?></td>
                <?php endforeach; ?>
                <td class="text-nowrap">
                    <button class="btn btn-secondary btn-sm save-row-btn"
                        onclick="openEdit(<?= $rowIndex ?>, '<?= $tableName ?>')"><i
                            class="fa-solid fa-pen-to-square"></i></button>
                    <button class="btn btn-danger btn-sm" onclick="deleteRow(<?= $rowIndex ?>, '<?= $tableName ?>')"><i
                            class="fa-regular fa-trash-can"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            <script>
            function getRowData(rowIndex) {
                const table = document.querySelector('.dataTable tbody');
                const row = table.rows[rowIndex];

                if (!row) {
                    console.error("Row not found:", rowIndex);
                    return undefined;
                }

                const headerCells = document.querySelectorAll('.dataTable thead th');
                const rowData = {};

                headerCells.forEach((headerCell, idx) => {
                    if (idx < headerCells.length - 1) {
                        const columnName = headerCell.innerText.trim();
                        const cellValue = row.cells[idx].innerText.trim();

                        if (columnName) {
                            rowData[columnName] = cellValue;
                        }
                    }
                });

                return rowData;
            }
            </script>
        </tbody>
    </table>

    <div class="d-flex flex-column flex-md-row align-items-center mb-3">
        <!-- Go to Page input and button -->
        <div class="go-to-page d-flex align-items-center me-md-5 mb-2 mb-md-0">
            <span class="me-2">Go to page:</span>
            <input class="form-control form-control-sm me-2" type="number" id="goToPageInput" min="1"
                max="<?= $pagination->getPageCount() ?>" style="width: 5rem;" />
            <button id="goToPageButton" class="btn btn-primary btn-sm">Go</button>
        </div>

        <!-- Number of items per page -->
        <div class="number-of-items d-flex align-items-center mb-2 mb-md-0">
            <span class="me-2">View:</span>
            <select class="form-select form-select-sm autosubmit" name="pageSize" id="pageSize" style="width: 5rem;">
                <option value="10" <?= $pageSize == 10 ? 'selected' : '' ?>>10</option>
                <option value="25" <?= $pageSize == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?= $pageSize == 50 ? 'selected' : '' ?>>50</option>
                <option value="100" <?= $pageSize == 100 ? 'selected' : '' ?>>100</option>
                <option value="250" <?= $pageSize == 250 ? 'selected' : '' ?>>250</option>
                <option value="500" <?= $pageSize == 500 ? 'selected' : '' ?>>500</option>
                <option value="1000" <?= $pageSize == 1000 ? 'selected' : '' ?>>1000</option>
            </select>
        </div>

        <!-- Pagination Links -->
        <div class="dataTables_paginate paging_simple_numbers ms-md-auto">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination justify-content-end align-items-center'],
                'linkContainerOptions' => ['tag' => 'span'],
                'linkOptions' => [
                    'class' => 'paginate_button',
                    'data-page' => function ($page) {
                        return $page + 1;
                    },
                ],
                'activePageCssClass' => 'current',
                'disabledPageCssClass' => 'disabled',
                'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'paginate_button'],
                'prevPageLabel' => 'Previous',
                'nextPageLabel' => 'Next',
                'maxButtonCount' => 5,
            ]) ?>
        </div>

        <!-- Last Page Button -->
        <?php if ($pagination->getPageCount() > 1): ?>
        <div class="d-flex justify-content-end">
            <span class="paginate_button">
                <input type="hidden" id="totalCount" value="<?= $totalCount ?>">
                <input type="hidden" id="pageSize" value="<?= $pageSize ?>">
                <button id="lastPageButton" class="btn btn-secondary btn-sm"
                    data-page="<?= $pagination->getPageCount() - 1 ?>">
                    Last
                </button>
            </span>
        </div>
        <?php endif; ?>
    </div>


    <?php endif; ?>

    <!-- Modal Edit Data-->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm"></form> <!-- Để trống và sẽ được điền động -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Cancel">Hủy</button>
                    <button type="button" class="btn btn-primary" id="save-row-btn">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Dữ Liệu -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDataModalLabel">Insert Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php foreach ($columns as $column): ?>
                    <div class="form-group">
                        <label
                            for="<?= htmlspecialchars($column->name) ?>"><?= htmlspecialchars($column->name) ?>:</label>
                        <input type="text" class="form-control new-data-input"
                            data-column="<?= htmlspecialchars($column->name) ?>"
                            id="<?= htmlspecialchars($column->name) ?>"
                            placeholder="<?= htmlspecialchars($column->name) ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" id="add-row-btn" class="btn btn-primary">Thêm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    var tabId = <?= json_encode($tabId) ?>;
    var columns = <?= json_encode(array_map(function ($column) {
                        return htmlspecialchars($column->name);
                    }, $columns)) ?>;
    var columnsArray = Array.isArray(columns) ? columns : Object.entries(columns).map(([key]) => ({
        name: key,
    }));
    var data1 = <?= json_encode($data) ?>;

    function getRowData1(rowIndex) {
        const data = <?= json_encode($data) ?>;

        if (rowIndex < 0 || rowIndex >= data.length) {
            return undefined;
        }

        return data[rowIndex];
    }

    $(document).off('click', '#add-row-btn').on('click', '#add-row-btn', function() {
        var tableName = '<?= $tableName ?>';
        var newData = {};

        $('.new-data-input').each(function() {
            var column = $(this).data('column');
            var value = $(this).val();
            newData[column] = value;
        });

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/add-data']) ?> ',
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
                    var lastPage = response.totalPages - 1;
                    var tabId = $('.nav-link.active').data('id');

                    loadData(tabId, lastPage, null);

                    var toastElementSuccess = document.getElementById('liveToastSuccess');
                    var toastBodySuccess = toastElementSuccess.querySelector('.toast-body');
                    toastBodySuccess.innerText = "Thêm dữ liệu thành công!";

                    var toastSuccess = new bootstrap.Toast(toastElementSuccess, {
                        delay: 3000
                    });
                    toastSuccess.show();
                    $('#addDataModal').find('input').val('');
                    $('#addDataModal').modal('hide');
                } else {
                    alert('Không thể lưu dữ liệu: ' + response.message);
                }
            },
            error: function(error) {
                alert("Có lỗi xảy ra khi thêm dữ liệu.");
            }
        });
    });

    function openEdit(rowIndex, tableName) {
        let rowData = getRowData(rowIndex);

        if (!rowData) {
            console.error("Không tìm thấy dữ liệu cho chỉ mục:", rowIndex);
            return;
        }

        const form = document.getElementById('editForm');
        form.innerHTML = ''; // Xóa nội dung cũ trong form

        columnsArray.forEach(column => {
            const label = document.createElement('label');
            label.htmlFor = column.name;
            label.innerText = column.name + ":"; // Không chuyển đổi chữ cái đầu thành chữ hoa

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control';
            input.id = column.name;
            input.name = column.name;
            input.value = rowData[column.name]; // Điền giá trị từ rowData
            input.setAttribute('data-original-value', rowData[column.name]); // Thêm giá trị gốc

            const formGroup = document.createElement('div');
            formGroup.className = 'form-group';
            formGroup.appendChild(label);
            formGroup.appendChild(input);
            form.appendChild(formGroup);
        });

        const saveButton = document.getElementById('save-row-btn');
        saveButton.setAttribute('data-row-index', rowIndex);
        saveButton.setAttribute('data-table-name', tableName);

        $('#editModal').modal('show');
    }


    document.getElementById('save-row-btn').addEventListener('click', function() {
        const rowIndex = this.getAttribute('data-row-index');
        const tableName = this.getAttribute('data-table-name');
        saveRow(rowIndex, tableName);
    });

    // Save row
    function saveRow() {
        const saveButton = document.getElementById('save-row-btn');
        const rowIndex = saveButton.getAttribute('data-row-index');
        const tableName = saveButton.getAttribute('data-table-name');

        var inputs = document.querySelectorAll('#editForm input');
        var updatedData = {};
        var originalValues = {};

        inputs.forEach(function(input) {
            var column = input.name;
            updatedData[column] = input.value;
            originalValues[column] = input.getAttribute('data-original-value');
        });

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/update-data']) ?> ',
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

                    const table = document.querySelector('.dataTable tbody');
                    const row = table.rows[rowIndex];

                    Object.keys(updatedData).forEach((column, idx) => {
                        const cell = row.cells[idx +
                            1];
                        if (cell) {
                            cell.innerHTML = htmlspecialchars(updatedData[column]);
                        }
                    });

                    let rowData = getRowData(rowIndex);
                    Object.assign(rowData, updatedData);
                    var toastElementSuccess = document.getElementById('liveToastSuccess');
                    var toastBodySuccess = toastElementSuccess.querySelector('.toast-body');
                    toastBodySuccess.innerText = "Lưu dữ liệu thành công!";

                    var toastSuccess = new bootstrap.Toast(toastElementSuccess, {
                        delay: 3000
                    });
                    toastSuccess.show();
                    $('#editModal').modal('hide');

                } else {
                    alert('Không lưu được dữ liệu: ' + response.message);
                }
            },
            error: function(error) {
                alert("Đã xảy ra lỗi khi lưu dữ liệu.");
            }
        });
    }


    $(document).ready(function() {
        $('.dataTable').DataTable({
            order: [],
            columns: generateColumnsConfig($('.dataTable th').length),
            "lengthChange": false,
            "autoWidth": false,
            "responsive": true,
            "paging": false,
            "searching": false,
            "ordering": true,
            "language": {
                "info": ''
            }
        });
    });

    function generateColumnsConfig(columnCount) {
        var columns = [];
        for (var i = 0; i < columnCount; i++) {
            if (i === 0) {
                columns.push({
                    orderable: false,
                    width: '3%',
                    className: 'text-center'
                });
            } else if (i === columnCount - 1) {
                columns.push({
                    orderable: false,
                    width: '8%'
                });
            } else {
                columns.push(null);
            }
        }
        return columns;
    }

    function loadTabData(tabId, page, search, pageSize) {
        localStorage.clear();

        var loadingSpinner = $(`
             <div class="spinner-fixed">
                <i class="fa fa-spin fa-spinner me-2"></i>
            </div>
        `);
        $('body').append(loadingSpinner);

        $.ajax({
            url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
            type: "GET",
            data: {
                tabId: tabId,
                page: page,
                search: search,
                pageSize: pageSize,
            },
            success: function(data) {
                loadingSpinner.remove();

                $('#table-data-current').html(data);
                // Cập nhật trạng thái của tab hiện tại
                $('.nav-link').removeClass('active');
                $('.nav-item').removeClass('active');
                $(`[data-id="${tabId}"]`).addClass('active');
                $(`[data-id="${tabId}"]`).closest('.nav-item').addClass('active');
            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();
                console.error('Error:', error);
                Alert('Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau.');
            }
        });
    }

    function loadData(tabId, page, search, pageSize) {

        var loadingSpinner = $(`
             <div class="spinner-fixed">
                <i class="fa fa-spin fa-spinner me-2"></i>
            </div>
        `);
        $('body').append(loadingSpinner);

        $.ajax({
            url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
            type: "GET",
            data: {
                tabId: tabId,
                page: page,
                search: search,
                pageSize: pageSize,
            },
            success: function(responseData) {
                loadingSpinner.remove();

                var data = responseData.data;

                var newTbodyHtml = $(responseData).find('tbody').html();
                $('tbody').html(newTbodyHtml);

                var table = $('.dataTable').DataTable();
                table.clear();

                var rows = $(responseData).find('tbody tr').toArray().map(function(row) {
                    return $(row).prop('outerHTML');
                });

                table.rows.add($(rows.join(''))).draw();

                var paginationHtml = $(responseData).find('.dataTables_paginate').html();
                $('.dataTables_paginate').html(paginationHtml);

                var totalCount = $(responseData).find('#totalCount').val();
                var pageSize = $(responseData).find('#pageSize').val();
                var lastPage = Math.ceil(totalCount / pageSize) - 1;

                $('#totalCount').val(totalCount);
                $('#pageSize').val(pageSize);
                $('#lastPageButton').attr('data-last-page', lastPage);

            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();

                const toastLiveExample = document.getElementById('liveToast');
                toastBody.textContent = `Error: ${xhr.responseText || 'Lỗi không xác định'}`;
                const toast = new bootstrap.Toast(toastLiveExample);
                toast.show();
            }
        });
    }

    // Search Form with debounce
    function debounce(func, delay) {
        let debounceTimer;
        return function(...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    $(document).off('input', '.search-tab input[type="text"]').on('input', '.search-tab input[type="text"]', debounce(
        function() {
            const search = $(this).val().trim();
            const tabId = <?= json_encode($tabId) ?>;
            const page = 0;

            var pageSize = $('#pageSize').val();

            if (search !== "") {
                loadData(tabId, page, search, pageSize);
            } else {
                loadData(tabId, page, '', pageSize);
            }
        }, 500));

    $('.search-tab input[type="text"]').on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

    var tabId = <?= json_encode($tabId) ?>;
    $('#select-all').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

    function htmlspecialchars(str) {
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(
            /'/g,
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

    // Delete Checkbox selected
    $(document).off('click', '#delete-selected-btn').on('click', '#delete-selected-btn', function() {
        var selectedCheckboxes = $('.row-checkbox:checked');

        if (selectedCheckboxes.length === 0) {
            alert("Vui lòng chọn ít nhất một mục để xóa.");
            return;
        }

        var tableName = '<?= $tableName ?>';
        var conditions = [];

        selectedCheckboxes.each(function() {
            var rowIndex = $(this).data('row');
            var rowData = getRowData(
                rowIndex);

            if (!rowData)
                return;

            var condition = {};

            for (let key in rowData) {
                if (rowData.hasOwnProperty(key)) {
                    condition[key] = rowData[key] ||
                        null;
                }
            }

            if (Object.keys(condition).length > 0) {
                conditions.push(
                    condition);
            }
        });

        if (conditions.length === 0) {
            alert("Không có dữ liệu nào được chọn để xóa.");
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa dữ liệu đã chọn không?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['tabs/delete-data']) ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]')
                        .attr('content')
                },
                data: {
                    table: tableName,
                    conditions: conditions
                },
                success: function(response) {
                    if (response.success) {
                        var page = $('.current .paginate_button').data('id');
                        var tabId = $('.nav-link.active').data('id');
                        var search = $('input[name="search"]').val();
                        var pageSize = $('#pageSize').val();

                        if (search && typeof search === 'string') {
                            search = search.trim();
                        }

                        loadData(tabId, page, search, pageSize);
                        var toastElementSuccess = document
                            .getElementById('liveToastSuccess');
                        var toastBodySuccess = toastElementSuccess
                            .querySelector('.toast-body');
                        toastBodySuccess.innerText =
                            "Xóa dữ liệu thành công!";

                        var toastSuccess = new bootstrap.Toast(
                            toastElementSuccess, {
                                delay: 3000
                            });
                        toastSuccess.show();
                        $('#select-all').prop('checked', false);

                    } else {
                        alert(response.message ||
                            "Xóa dữ liệu không thành công.");
                    }
                },
                error: function(error) {
                    alert(
                        "Đã xảy ra lỗi khi xóa dữ liệu."
                    );
                }
            });
        }
    });


    // Delete row
    function deleteRow(rowIndex, tableName) {
        const rowData = getRowData(rowIndex);
        if (!rowData) return;

        var condition = {};

        for (let key in rowData) {
            if (rowData.hasOwnProperty(key)) {
                condition[key] = rowData[key];
            }
        }

        if (confirm("Bạn có chắc chắn muốn xóa dữ liệu không?")) {
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
                        alert(response.message || "Xóa dữ liệu không thành công.");
                    }
                },
                error: function(error) {
                    alert("Đã xảy ra lỗi khi xóa dữ liệu.");
                }
            });
        }

    }

    // Import Excel Button Click
    $(document).off('click', 'import-data-btn').on('click', '#import-data-btn', function() {
        $('#importExelModal').modal('show');
    });

    // Handle Import Excel Form Submission
    $(document).off('submit', '#importExcelForm').on('submit', '#importExcelForm', function(event) {

        event.preventDefault();
        var formData = new FormData(this);
        var tableName = '<?= $tableName ?>';
        formData.append('tableName', tableName);

        var loadingSpinner = $(` 
                <div class="loading-overlay">
                    <div class="loading-content">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span class="ml-2">Đang nhập dữ liệu, vui lòng đợi...</span>                    
                    </div>
                </div>
            `);
        $('body').append(loadingSpinner);

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/import-excel']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                loadingSpinner.remove();

                if (response.success) {

                    loadData(tabId);
                    var toastElementSuccess = document
                        .getElementById('liveToastSuccess');
                    var toastBodySuccess = toastElementSuccess
                        .querySelector('.toast-body');
                    toastBodySuccess.innerText =
                        "Tệp Excel được nhập thành công!";

                    var toastSuccess = new bootstrap.Toast(
                        toastElementSuccess, {
                            delay: 5000
                        });
                    toastSuccess.show();
                    $('#importExcelForm')[0].reset();
                    $('#importExelModal').modal('hide');
                } else if (response.duplicate) {
                    $('#confirmMessage').html(
                        `Ghi đè các mục hiện có trong cột <strong>[Khóa chính]</strong>. Bạn có muốn tiếp tục nhập không?<br><br>
                            ${response.message}`
                    );

                    $('#confirmModal').modal('show');

                    $('#confirmYesBtn').off('click').on('click', function() {
                        var newLoadingSpinner = $(` 
                                <div class="loading-overlay">
                                    <div class="loading-content">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <span class="ml-2">Đang nhập dữ liệu, vui lòng đợi...</span>                    
                                    </div>
                                </div>
                            `);
                        $('body').append(newLoadingSpinner);

                        formData.append('removeId', true);

                        $.ajax({
                            url: '<?= \yii\helpers\Url::to(['tabs/import-excel']) ?>',
                            type: 'POST',
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                newLoadingSpinner.remove();

                                if (response.success) {
                                    loadData(tabId);
                                    var toastElementSuccess = document
                                        .getElementById('liveToastSuccess');
                                    var toastBodySuccess = toastElementSuccess
                                        .querySelector('.toast-body');
                                    toastBodySuccess.innerText =
                                        "Tệp Excel được nhập và ghi đè [PK]s thành công!";

                                    var toastSuccess = new bootstrap.Toast(
                                        toastElementSuccess, {
                                            delay: 5000
                                        });
                                    toastSuccess.show();
                                    // $('#importExcelForm')[0].reset();
                                    $('#importExelModal').modal('hide');

                                } else {
                                    newLoadingSpinner.remove();
                                    showModal('Error',
                                        'Không thể nhập tệp Excel: \n' +
                                        response.message);
                                }
                            }
                        });
                        $('#importStatusModal').modal('hide');
                        $('#confirmModal').modal('hide');
                    });
                } else {
                    loadingSpinner.remove();
                    showModal('Error', 'Không thể nhập tệp Excel: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();
                showModal('Error', 'Có lỗi xảy ra khi nhập tệp Excel:');
            }
        });
    });

    // Hàm hiển thị modal với thông điệp
    function showModal(title, message) {
        $('#importStatusModalLabel').text(title);

        $('#importStatusMessage').html(message.replace(/\n/g, '<br>'));

        $('#importStatusModal').modal('show');

        $('#importExelModal').modal('hide');
    }

    // Export Excel 
    $(document).off('click', '#exportExcelButton').on('click', '#exportExcelButton', function() {

        event.preventDefault();
        var exportFormat = 'xlsx';
        var tableName = '<?= $tableName ?>';
        var loadingSpinner = $(`
             <div class="loading-overlay">
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Exporting...</span>
                    </div>
                    <span class="ml-2">Đang xuất dữ liệu, vui lòng đợi...</span>
                </div>
            </div>
        `);
        $('body').append(loadingSpinner);
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/export-excel']) ?>',
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                format: exportFormat,
                tableName: tableName,
            },
            success: function(response) {
                loadingSpinner.remove();

                if (response.success) {
                    if (response.file_url) {
                        var link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = tableName + '.' + exportFormat;
                        document.body.appendChild(
                            link);
                        link.click();
                        document.body.removeChild(link);

                        $.ajax({
                            url: '<?= \yii\helpers\Url::to(['tabs/delete-export-file']) ?>',
                            type: 'POST',
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                file_url: response.file_url,
                            },
                            success: function(deleteResponse) {
                                if (deleteResponse.success) {
                                    console.log('Đã xóa file tmp thành công.');
                                } else {
                                    console.error('Không xóa được tập tin.');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(
                                    'Đã xảy ra lỗi khi xóa file.');
                            }
                        });

                    } else {
                        alert('URL tệp bị thiếu trong phản hồi.');
                    }
                } else {
                    alert('Không xuất được Excel ' + response
                        .message);
                }

            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();

                alert('Đã xảy ra lỗi khi xuất Excel.');
            }
        });
    });
    </script>
</div>