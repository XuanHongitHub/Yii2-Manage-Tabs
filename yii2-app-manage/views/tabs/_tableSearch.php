<?php

use app\models\User;
use app\models\Tab;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
/** @var app\models\Tab[] $tabs */

$this->title = 'Tabs Data';

use yii\widgets\LinkPager;

$isAdmin = User::isUserAdmin(Yii::$app->user->identity->username);

$tabId = $_GET['tabId'];

$userId = Yii::$app->user->id;
$tabs = Tab::find()
    ->where(['user_id' => $userId])
    ->orderBy([
        'position' => SORT_ASC,
        'id' => SORT_ASC,
    ])
    ->all();


?>
<?php include Yii::getAlias('@app/views/layouts/_nav.php'); ?>

<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">

            </div>
        </div>
    </div>
    <!-- Container-fluid starts -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="d-flex settings ">
                    <div class="btn-group-ellipsis ms-auto m-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown"
                            aria-expanded="false">
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
                                    data-bs-target="#trashBinModal">
                                    <i class="fas fa-trash me-1"></i> Trash Bin
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="my-2">
                        <a class="btn btn-secondary" href="<?= \yii\helpers\Url::to(['settings/index']) ?>">
                            <i class="fa-solid fa-gear"></i>
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h4>Tabs Data</h4>
                        <p class="mt-1 f-m-light"><code>Table Tabs</code> | <code>Richtext Tabs </code></p>
                    </div>
                    <div class="card-body">
                        <ul class="simple-wrapper nav nav-tabs" id="tab-list">
                            <?php if (!empty($tabs)): ?>
                            <?php $hasValidTabs = false; ?>
                            <?php foreach ($tabs as $index => $tab): ?>
                            <?php if ($tab->deleted == 0): ?>
                            <?php $hasValidTabs = true; ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $tab->id == $tabId ? 'active' : '' ?>"
                                    href="<?= \yii\helpers\Url::to(['tabs/load-tab-data', 'tabId' => $tab->id, 'page' => '1']) ?>"
                                    data-id="<?= $tab->id ?>">
                                    <?= htmlspecialchars($tab->tab_name) ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if (!$hasValidTabs): ?>
                            <div class="align-items-center m-2">
                                No Tabs Available. Please create a new tab in the settings.
                            </div>
                            <?php endif; ?>
                            <?php else: ?>
                            <div class="align-items-center m-2">
                                No Data
                            </div>
                            <?php endif; ?>
                        </ul>

                        <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const tabs = document.querySelectorAll('#tab-list .nav-link');

                            tabs.forEach(tab => {
                                tab.addEventListener('click', function() {
                                    // Xóa lớp 'active' khỏi tất cả các tab
                                    tabs.forEach(t => t.classList.remove('active'));

                                    // Thêm lớp 'active' vào tab được nhấn
                                    tab.classList.add('active');
                                });
                            });
                        });
                        </script>


                        <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const tabs = document.querySelectorAll('#tab-list .nav-link');

                            tabs.forEach(tab => {
                                tab.addEventListener('click', function() {
                                    // Xóa lớp 'active' khỏi tất cả các tab
                                    tabs.forEach(t => t.classList.remove('active'));

                                    // Thêm lớp 'active' vào tab được nhấn
                                    tab.classList.add('active');
                                });
                            });
                        });
                        </script>
                        <div class="tab-content">

                            <div class="table-responsive" id="table-data-current">


                                <div class="d-flex flex-wrap justify-content-between mt-3">
                                    <div class="d-md-flex d-sm-block">
                                        <button class="btn btn-primary mb-2 me-2" id="add-data-btn" href="#"
                                            data-bs-toggle="modal" data-bs-target="#addDataModal">
                                            <i class="fa-solid fa-plus"></i> Insert data
                                        </button>

                                        <button class="btn btn-danger mb-2 me-2" id="delete-selected-btn">
                                            <i class="fa-regular fa-trash-can"></i> Delete selected
                                        </button>

                                        <!-- Import Excel Button -->
                                        <button class="btn btn-info mb-2 me-2" id="import-data-btn" href="#"
                                            data-bs-toggle="modal" data-bs-target="#importExelModal">
                                            <i class="fa-solid fa-download"></i> Import Excel
                                        </button>

                                        <!-- Export Excel Button -->
                                        <button class="btn btn-warning mb-2 me-auto" id="import-excel-btn" href="#"
                                            data-bs-toggle="modal" data-bs-target="#exportExcelModal">
                                            <i class="fa-solid fa-file-export"></i></i> Export Excel
                                        </button>
                                    </div>
                                    <!-- Search Form -->
                                    <form class="form-inline search-tab mb-2 me-3"
                                        action="<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>" method="get">
                                        <div class="form-group d-flex align-items-center mb-0">
                                            <i class="fa fa-search"></i>
                                            <!-- Thêm input ẩn để gửi tabId cùng với từ khóa tìm kiếm -->
                                            <input type="hidden" name="tabId" value="<?= $tabId ?>">
                                            <!-- TabId ở đây -->
                                            <input class="form-control-plaintext" type="text" name="search"
                                                placeholder="Search...">
                                        </div>
                                    </form>
                                </div>
                                <table class="display border table-bordered dataTable">
                                    <thead>
                                        <tr>
                                            <th class="p-0" style="width: 3%;"><input type="checkbox" id="select-all">
                                            </th>
                                            <?php foreach ($columns as $column): ?>
                                            <th><?= htmlspecialchars($column->name) ?></th>
                                            <?php endforeach; ?>
                                            <th style="width: 8%;">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbodyData">
                                        <?php foreach ($data as $rowIndex => $row): ?>
                                        <tr>
                                            <td class="p-0"><input type="checkbox" class="row-checkbox p-0"
                                                    data-row="<?= $rowIndex ?>" data-table-name="<?= $tableName ?>">
                                            </td>
                                            <?php foreach ($columns as $column): ?>
                                            <td><?= htmlspecialchars($row[$column->name]) ?></td>
                                            <?php endforeach; ?>
                                            <td class="text-nowrap">
                                                <button class="btn btn-secondary btn-sm save-row-btn"
                                                    onclick="openEdit(<?= $rowIndex ?>, '<?= $tableName ?>')"><i
                                                        class="fa-solid fa-pen-to-square"></i></button>
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="deleteRow(<?= $rowIndex ?>, '<?= $tableName ?>')"><i
                                                        class="fa-regular fa-trash-can"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <script>
                                        function getRowData(rowIndex) {
                                            const data = <?= json_encode($data) ?>;

                                            if (rowIndex < 0 || rowIndex >= data.length) {
                                                return undefined;
                                            }

                                            return data[rowIndex];
                                        }
                                        </script>
                                    </tbody>
                                </table>

                                <!-- Pagination Links -->
                                <div class="dataTables_paginate paging_simple_numbers">
                                    <?= LinkPager::widget([
                                                            'pagination' => $pagination,
                                                            'options' => ['class' => 'pagination justify-content-start'],
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
                                                        ]) ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
</div>
<!-- Modal Trash Bin -->
<div class="modal fade" id="trashBinModal" tabindex="-1" aria-labelledby="trashBinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trashBinModalLabel">Trash Bin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Select the tab you want to restore or delete completely:</p>
                <table class="table table-bordered table-hover table-ui">
                    <thead>
                        <tr>
                            <th>Tab name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php $hasDeletedTabs = false; ?>
                        <?php foreach ($tabs as $tab): ?>
                        <?php if ($tab->deleted == 1): ?>
                        <?php $hasDeletedTabs = true; ?>
                        <tr>
                            <td><?= htmlspecialchars($tab->tab_name) ?></td>
                            <td>
                                <button type="button" class="btn btn-warning restore-tab-btn" id="confirm-restore-btn"
                                    data-tab-id="<?= htmlspecialchars($tab->id) ?>">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                                <button type="button" class="btn btn-danger delete-tab-btn" id="delete-permanently-btn"
                                    data-tab-name="<?= htmlspecialchars($tab->tab_name) ?>"
                                    data-tab-id="<?= htmlspecialchars($tab->id) ?>">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (!$hasDeletedTabs): ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                <em>There is nothing here.</em>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hide tab -->
<div class="modal fade" id="hideModal" tabindex="-1" aria-labelledby="hideModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hideModalLabel">Show/Hidden Tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p>Select the tab you want to hide or show:</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tab name</th>
                            <th><i class="fa-solid fa-eye-slash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-tabs-list">
                        <?php foreach ($tabs as $tab): ?>
                        <?php if ($tab->deleted == 0 || $tab->deleted == 3): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($tab->tab_name) ?>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-hide-btn" type="checkbox"
                                        data-tab-id="<?= htmlspecialchars($tab->id) ?>"
                                        <?php if ($tab->deleted == 3): ?> checked <?php endif; ?>>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-hide-btn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sort tab -->
<div class="modal fade" id="sortModal" tabindex="-1" aria-labelledby="sortModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sortModalLabel">Sort Tabs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p>Kéo và thả để sắp xếp các tab.</p>
                <ul class="list-group" id="sortable-tabs">
                    <?php foreach ($tabs as $index => $tab): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center"
                        data-tab-id="<?= $tab->id ?>">
                        <span><?= htmlspecialchars($tab->tab_name) ?></span>
                        <span class="badge bg-secondary"><?= $index + 1 ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-sort-btn">Save</button>
            </div>
        </div>
    </div>
</div>
<?php include Yii::getAlias('@app/views/layouts/_footer.php'); ?>

<!-- Modal Edit Data-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <form id="editForm"></form>
                <!-- Để trống và sẽ được điền động -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    aria-label="Cancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-row-btn">Save</button>
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
                    <label for="<?= htmlspecialchars($column->name) ?>"><?= htmlspecialchars($column->name) ?>:</label>
                    <input type="text" class="form-control new-data-input"
                        data-column="<?= htmlspecialchars($column->name) ?>" id="<?= htmlspecialchars($column->name) ?>"
                        placeholder="<?= htmlspecialchars($column->name) ?>">
                </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="add-row-btn" class="btn btn-primary">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Import Excel -->
<div class="modal fade" id="importExelModal" tabindex="-1" aria-labelledby="importExelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExelModalLabel">Import Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="importExcelForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="import-excel-file" class="form-label">Select
                            Excel File</label>
                        <input class="form-control" type="file" id="import-excel-file" name="import-excel-file"
                            accept=".xlsx, .xls" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import
                        Excel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Export Excel -->
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportExcelModalLabel">Export Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportExcelForm">
                    <div class="mb-3">
                        <label for="export-format" class="form-label">Choose
                            Export Format</label>
                        <select class="form-control" id="export-format" name="export-format">
                            <option value="xlsx">Excel (.xlsx)</option>
                            <option value="xls">Excel 97-2003 (.xls)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Export
                        Excel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <small class="text-muted">just now</small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Hello, this is a toast message!
        </div>
    </div>
</div>
<script>
var tabId = <?= json_encode($tabId) ?>;
var columns = <?= json_encode(array_map(function ($column) {
        return htmlspecialchars($column->name);
    }, $columns)) ?>;
var columnsArray = Array.isArray(columns) ? columns : Object.entries(columns)
    .map(([key]) => ({
        name: key,
    }));
var data = <?= json_encode($data) ?>;

console.log("🚀 ~ columns ~ columns:", columns);

$(document).off('click', '#add-row-btn').on('click', '#add-row-btn',
    function() {
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
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                    'content')
            },
            data: {
                table: tableName,
                data: newData
            },
            success: function(response) {
                if (response.success) {
                    var lastPage = response.totalPages;
                    var tabId = $('.nav-link.active').data(
                        'id');

                    var url =
                        "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>" +
                        "?tabId=" + tabId + "&page=" + lastPage;

                    window.location.href = url;

                    $('#addDataModal').find('input').val('');
                    $('#addDataModal').modal('hide');
                } else {
                    alert('Failed to save data: ' + response
                        .message);
                }
            },
            error: function(error) {
                alert("An error occurred while adding data.");
            }
        });
    });

function openEdit(rowIndex, tableName) {
    let rowData = getRowData(rowIndex);
    console.log(columns); // Kiểm tra cấu trúc của columns
    console.log(rowData);

    if (!rowData) {
        console.error("No data found for index:", rowIndex);
        return;
    }

    const form = document.getElementById('editForm');
    form.innerHTML = ''; // Xóa nội dung cũ trong form

    columnsArray.forEach(column => {
        const label = document.createElement('label');
        label.htmlFor = column.name;
        label.innerText = column.name +
            ":"; // Không chuyển đổi chữ cái đầu thành chữ hoa

        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control';
        input.id = column.name;
        input.name = column.name;
        input.value = rowData[column.name]; // Điền giá trị từ rowData
        input.setAttribute('data-original-value', rowData[column
            .name]); // Thêm giá trị gốc

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
        originalValues[column] = input.getAttribute(
            'data-original-value');
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
                var currentPage = new URLSearchParams(window.location.search).get('page') || 1;

                var url =
                    "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>" +
                    "?tabId=" + tabId + "&page=" + currentPage;

                window.location.href = url;

            } else {
                // Hiển thị thông báo lỗi
                alert('Failed to save data: ' + response.message);
            }
        },
        error: function(error) {
            alert("An error occurred while saving data.");
        }
    });
}


$(document).ready(function() {
    $('.dataTable').DataTable({
        order: [],
        columns: generateColumnsConfig($('.dataTable th')
            .length),
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

function fetchData(tabId, page) {
    console.log("🚀 ~ zzzzz loadTabData ~ tabId:", tabId, "Page: ", page);

    $.ajax({
        url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
        type: "GET",
        data: {
            tabId: tabId,
            page: page
        },
        success: function(data) {
            $('#tableData').empty();
            $('#tableData').html(data);

            const newPagination = $(data).find(
                    '.dataTables_paginate')
                .html(); // Lấy nội dung phân trang
            $('.dataTables_paginate').html(newPagination);

            currentPage = page; // Cập nhật biến currentPage
            updatePagination(currentPage);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert(
                'An error occurred while loading data. Please try again later.'
            );
        }
    });
}

function updatePagination(currentPage) {
    console.log("🚀 ~ zzzzz page ~ : ", currentPage);

    $('.dataTables_paginate .current').removeClass('current');
    $(`.dataTables_paginate .paginate_button[data-page="${currentPage}"]`)
        .parent().addClass('current');
}

// Selected all checkbox + Add data
$(document).ready(function() {
    var tabId = <?= json_encode($tabId) ?>;
    $('#select-all').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
    });

});

function htmlspecialchars(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g,
        '&gt;').replace(/"/g, '&quot;').replace(
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
$('#delete-selected-btn').on('click', function() {
    var selectedCheckboxes = $('.row-checkbox:checked');

    if (selectedCheckboxes.length === 0) {
        alert("Please select at least one item to delete.");
        return;
    }

    var tableName = '<?= $tableName ?>';
    var conditions = [];

    selectedCheckboxes.each(function() {
        var rowIndex = $(this).data('row');
        var rowData = getRowData(
            rowIndex); // Get the data for the selected row

        if (!rowData)
            return; // If rowData doesn't exist, skip this row

        // Create a dynamic condition object
        var condition = {};

        // Populate the condition with rowData properties dynamically
        for (let key in rowData) {
            if (rowData.hasOwnProperty(key)) {
                condition[key] = rowData[key] ||
                    null; // If value is empty, set to null
            }
        }

        if (Object.keys(condition).length > 0) {
            conditions.push(
                condition); // Add the condition for this row
        }
    });

    if (conditions.length === 0) {
        alert("No data selected for deletion.");
        return;
    }

    if (confirm("Are you sure you want to delete the selected data?")) {
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

                    var currentPage = new URLSearchParams(window.location.search).get('page') || 1;

                    var url =
                        "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>" +
                        "?tabId=" + tabId + "&page=" + currentPage;

                    window.location.href = url;
                } else {
                    alert(response.message ||
                        "Deleting data failed.");
                }
            },
            error: function(error) {
                alert(
                    "An error occurred while deleting data."
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

    if (confirm("Are you sure you want to delete data?")) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/delete-data']) ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                    'content')
            },
            data: {
                table: tableName,
                conditions: [condition]
            },
            success: function(response) {
                if (response.success) {
                    var currentPage = new URLSearchParams(window.location.search).get('page') || 1;

                    var url =
                        "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>" +
                        "?tabId=" + tabId + "&page=" + currentPage;

                    window.location.href = url;
                } else {
                    alert(response.message ||
                        "Deleting data failed.");
                }
            },
            error: function(error) {
                alert("An error occurred while deleting data.");
            }
        });
    }

}

// Import Excel Button Click
$(document).off('click', 'import-data-btn').on('click', '#import-data-btn',
    function() {
        $('#importExelModal').modal('show');
    });

// Handle Import Excel Form Submission
$(document).off('submit', '#importExcelForm').on('submit', '#importExcelForm',
    function(event) {

        event.preventDefault();
        var formData = new FormData(this);
        var tableName = '<?= $tableName ?>';
        formData.append('tableName', tableName);

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
                if (response.success) {
                    alert('Excel file imported successfully!');
                    $('#importExelModal').modal('hide');
                    loadTabData(tabId);
                } else if (response.duplicate) {
                    // Hiển thị thông báo với 2 lựa chọn
                    if (confirm(response.message +
                            "\nWant to remove the 'id' column and continue importing?"
                        )) {
                        // Loại bỏ cột 'id' và gửi lại form
                        formData.append('removeId',
                            true
                        ); // Thêm cờ để server biết cần loại bỏ cột id
                        $.ajax({
                            url: '<?= \yii\helpers\Url::to(['tabs/import-excel']) ?>',
                            type: 'POST',
                            headers: {
                                'X-CSRF-Token': $(
                                    'meta[name="csrf-token"]'
                                ).attr(
                                    'content')
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(
                                response) {
                                if (response
                                    .success) {
                                    alert(
                                        'Excel file imported successfully without IDs!'
                                    );
                                    $('#importExelModal')
                                        .modal(
                                            'hide');
                                    loadTabData(
                                        tabId);
                                } else {
                                    alert('Failed to import Excel file: ' +
                                        response
                                        .message
                                    );
                                }
                            }
                        });
                    }
                } else {
                    alert('Failed to import Excel file: ' +
                        response.message);
                }
            },
            error: function(xhr, status, error) {
                alert(
                    'An error occurred while importing Excel.'
                );
            }
        });
    });



// Export Excel Button Click
$(document).off('click', '#import-excel-btn').on('click', '#import-excel-btn',
    function() {
        $('#exportExcelModal').modal('show');
    });

$(document).off('submit', '#exportExcelForm').on('submit', '#exportExcelForm',
    function(event) {
        event.preventDefault();
        var exportFormat = $('#export-format').val();
        var tableName = '<?= $tableName ?>';

        var loadingSpinner = $(
            '<a class="loading-spinner badge badge-light-primary" href=""><span class="p-1">Downloading</span></a>'
        );
        $('body').append(loadingSpinner);
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/export-excel']) ?>',
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                    'content')
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
                        link.download = tableName + '.' +
                            exportFormat;
                        document.body.appendChild(
                            link);
                        link.click();
                        document.body.removeChild(link);

                        $.ajax({
                            url: '<?= \yii\helpers\Url::to(['tabs/delete-export-file']) ?>',
                            type: 'POST',
                            headers: {
                                'X-CSRF-Token': $(
                                    'meta[name="csrf-token"]'
                                ).attr(
                                    'content')
                            },
                            data: {
                                file_url: response
                                    .file_url,
                            },
                            success: function(
                                deleteResponse) {
                                if (deleteResponse
                                    .success) {
                                    console.log(
                                        'File deleted successfully.'
                                    );
                                } else {
                                    console.error(
                                        'Failed to delete file.'
                                    );
                                }
                            },
                            error: function(xhr, status,
                                error) {
                                console.error(
                                    'An error occurred while deleting the file.'
                                );
                            }
                        });

                    } else {
                        alert(
                            'File URL is missing in the response.'
                        );
                    }
                } else {
                    alert('Failed to export Excel: ' + response
                        .message);
                }

            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();

                alert(
                    'An error occurred while exporting Excel.'
                );
            }
        });
    });
</script>

<?php

$firstTabId = null;
foreach ($tabs as $tab) {
    if ($tab->deleted == 0) {
        $firstTabId = $tab->id;
        break;
    }
}

?>
<script>
$(document).ready(function() {

    function loadTabData(tabId, page) {
        console.log("🚀 ~ loadTabData ~ tabId:", tabId);

        $.ajax({
            url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
            type: "GET",
            data: {
                tabId: tabId,
                page: page
            },
            success: function(data) {
                $('body').html(data);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while loading data. Please try again later.');
            }
        });
    }

});
</script>

<script>
$(document).ready(function() {
    $('#confirm-hide-btn').click(function() {
        let hideStatus = {};

        $('.toggle-hide-btn').each(function() {
            const tabId = $(this).data('tab-id');
            const isChecked = $(this).is(':checked');
            hideStatus[tabId] = isChecked ? 3 : 0;
        });

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/update-hide-status']) ?>',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                hideStatus: hideStatus
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || "An error occurred while saving changes.");
                }
            },
            error: function() {
                alert("An error occurred while saving changes.");
            }
        });
    });
    $("#sortable-tabs").sortable();

    $("#confirm-sort-btn").click(function() {
        var sortedData = [];
        $("#sortable-tabs li").each(function(index) {
            var tabId = $(this).data("tab-id");
            sortedData.push({
                id: tabId,
                position: index + 1
            });
        });

        $.ajax({
            url: '/tabs/update-sort-order',
            method: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tabs: sortedData
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                    $('#sortModal').modal('hide');
                } else {
                    alert(response.message || "Error.");
                }
            },
            error: function() {
                alert("Error.");
            }
        });
    });
    $(document).on('click', '#confirm-restore-btn', function() {
        const tabId = $(this).data('tab-id');

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['tabs/restore-tab']) ?>',
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
                    $('#trashBinModal').modal('hide');
                } else {
                    alert(response.message || "Restore table failed.");
                }
            },
            error: function(error) {
                alert("An error occurred while Restore table.");
            }
        });
    });
    $(document).ready(function() {
        $(document).on('click', '#delete-permanently-btn', function() {
            const tabId = $(this).data('tab-id');
            const tableName = $(this).data('tab-name');

            if (confirm("Are you sure you want to permanently delete this tab?")) {
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
                        } else {
                            alert(response.message || "Deletion failed.");
                        }
                    },
                    error: function(error) {
                        alert("An error occurred while deleting the tab.");
                    }
                });
            }
        });
    });

});
</script>