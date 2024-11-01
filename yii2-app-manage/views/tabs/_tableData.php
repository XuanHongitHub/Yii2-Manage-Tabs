<?php

use app\models\User;
use yii\widgets\LinkPager;

$isAdmin = User::isUserAdmin(Yii::$app->user->identity->username);

$tabId = $_GET['tabId'];

?>

<div class="d-flex flex-wrap justify-content-between mt-3">
    <div class="d-md-flex d-sm-block">
        <div class="btn-group-ellipsis me-2 mb-2">
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
        <button class="btn btn-danger mb-2 me-auto" id="delete-selected-btn">
            <i class="fa-regular fa-trash-can"></i> Delete selected
        </button>
    </div>
    <!-- Search Form -->
    <form class="form-inline search-tab mb-2" action="#" method="get">
        <div class="form-group d-flex align-items-center mb-0">
            <i class="fa fa-search"></i>
            <input class="form-control-plaintext" type="text" name="search" placeholder="Search...">
        </div>
    </form>
</div>


<table class="display border table-bordered dataTable">
    <thead>
        <tr>
            <th class="sorting_disabled"><input type="checkbox" id="select-all"></th>
            <?php foreach ($columns as $column): ?>
                <th><?= htmlspecialchars($column->name) ?></th>
            <?php endforeach; ?>
            <th>Action</th>
        </tr>
    </thead>
    <?php if (!empty($data)): ?>
        <tbody>
            <?php foreach ($data as $rowIndex => $row): ?>
                <tr>
                    <td><input type="checkbox" class="row-checkbox" data-row="<?= $rowIndex ?>"
                            data-table-name="<?= $tableName ?>"></td>
                    <?php foreach ($columns as $column): ?>
                        <td><?= htmlspecialchars($row[$column->name]) ?></td>
                    <?php endforeach; ?>
                    <td>
                        <button class="btn btn-success btn-sm save-btn"
                            onclick="saveRow(<?= $rowIndex ?>, '<?= $tableName ?>')"><i
                                class="fa-regular fa-floppy-disk"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="deleteRow(<?= $rowIndex ?>, '<?= $tableName ?>')"><i
                                class="fa-regular fa-trash-can"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="dataTables_paginate paging_simple_numbers">
        <?= LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination justify-content-start'],
            'linkContainerOptions' => ['tag' => 'span'], // Thay đổi tag container cho các link
            'linkOptions' => ['class' => 'paginate_button'], // Cung cấp class cho các link
            'activePageCssClass' => 'current', // Class cho trang hiện tại
            'disabledPageCssClass' => 'disabled', // Class cho các link bị vô hiệu hóa
            'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'paginate_button'], // Class cho thẻ span vô hiệu hóa
            'prevPageLabel' => 'Previous', // Văn bản cho nút "Previous"
            'nextPageLabel' => 'Next',
        ]) ?>
    </div>
<?php else: ?>
    <p>No data found.</p>
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

    <script>
        var tabId = <?= json_encode($tabId) ?>;
        var columns = <?= json_encode(array_map(function ($column) {
            return htmlspecialchars($column->name);
        }, $columns)) ?>;

        $(document).ready(function () {
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

        function loadTabData(tabId, element, page) { // Thêm tham số page
            console.log("🚀 ~ loadData ~ tabId:", tabId);
            $.ajax({
                url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
                type: "GET",
                data: {
                    tabId: tabId,
                    page: page // Gửi tham số page
                },
                success: function (data) {
                    $('#table-data-current').html(data);
                    $('.tab-pane').removeClass('show active');
                    $('#tab-data-current').addClass('show active');

                    $('.nav-link').removeClass('active');
                    $('.nav-item').removeClass('active');
                    $(element).addClass('active');
                    $(element).closest('.nav-item').addClass('active');
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred while loading data. Please try again later.');
                }
            });
        }

        //Search Form
        $('.search-tab input[type="text"]').on('input', function () {
            const searchTerm = $(this).val();
            loadData(searchTerm);
        });

        function loadData(searchTerm) {
            $.ajax({
                url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
                type: "GET",
                data: {
                    tabId: tabId,
                    search: searchTerm
                },
                success: function (data) {
                    const newTbody = $(data).find('tbody'); // Find tbody in returned data
                    $('table.display tbody').html(newTbody.html());


                },
                error: function (xhr, status, error) {
                    const toastLiveExample = document.getElementById('liveToast');
                    toastBody.textContent = `Error: ${xhr.responseText || 'Unknown error'}`;
                    const toast = new bootstrap.Toast(toastLiveExample);
                    toast.show();

                }
            });
        }



        // Selected all checkbox + Add data
        $(document).ready(function () {
            var tabId = <?= json_encode($tabId) ?>;
            $('#select-all').on('change', function () {
                const isChecked = $(this).is(':checked');
                $('.row-checkbox').prop('checked', isChecked);
            });
            $('#add-row-btn').on('click', function () {
                var tableName = '<?= $tableName ?>';
                var newData = {};
                $('.new-data-input').each(function () {
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
                    success: function (response) {
                        if (response.success) {
                            loadTabData(tabId);

                            var toastElementSuccess = document.getElementById(
                                'liveToastSuccess');
                            var toastBodySuccess = toastElementSuccess.querySelector(
                                '.toast-body');
                            toastBodySuccess.innerText = "Data added successfully!";

                            var toastSuccess = new bootstrap.Toast(toastElementSuccess, {
                                delay: 3000
                            });
                            toastSuccess.show();
                        } else {
                            var toastElementError = document.getElementById('liveToastError');
                            var toastBodyError = toastElementError.querySelector('.toast-body');
                            toastBodyError.innerText = response.message ||
                                "Failed to add data.";

                            var toastError = new bootstrap.Toast(toastElementError, {
                                delay: 3000
                            });
                            toastError.show();
                        }
                    },
                    error: function (error) {
                        alert("An error occurred while adding data.");
                    }
                });
            });
        });

        function htmlspecialchars(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(
                /'/g,
                '&#039;');
        }
        $('td').on('click', function () {
            var input = $(this).find('.data-input');
            var display = $(this).find('.data-display');
            if (input.is(':hidden')) {
                display.hide();
                input.show().focus();
            }
        });
        $('.data-input').on('blur', function () {
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
            inputs.forEach(function (input) {
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
                success: function (response) {
                    if (response.success) {
                        inputs.forEach(function (input) {
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
                error: function (error) {
                    alert("An error occurred while saving data.");
                }
            });
        }

        // Delete Checkbox selected
        $('#delete-selected-btn').on('click', function () {
            var selectedCheckboxes = $('.row-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                alert("Please select at least one item to delete.");
                return;
            }

            var tableName = '<?= $tableName ?>';
            var selectedIds = $('.row-checkbox:checked').map(function () {
                return $(this).data('value');
            }).get().filter(Boolean);

            var conditions = [];

            $('.row-checkbox:checked').each(function () {
                var rowIndex = $(this).data('row');
                var inputs = $('input[data-row-index="' + rowIndex + '"]');

                if (inputs.length === 0) {
                    return;
                }

                var condition = {};
                inputs.each(function () {
                    let columnName = $(this).data('column');
                    let columnValue = $(this).val();

                    if (columnName && columnName !== 'undefined') {
                        condition[columnName] = columnValue ||
                            null;
                    }
                });

                if (Object.keys(condition).length > 0) {
                    conditions.push(condition);
                } else { }
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
                    success: function (response) {
                        if (response.success) {
                            loadTabData(tabId);
                        } else {
                            alert(response.message || "Deleting data failed.");
                        }
                    },
                    error: function (error) {
                        alert("An error occurred while deleting data.");
                    }
                });
            }
        });


        // Delete row
        function deleteRow(rowIndex, tableName) {
            var inputs = $('input[data-row-index="' + rowIndex + '"]');
            var condition = {};

            inputs.each(function () {
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
                    success: function (response) {
                        if (response.success) {
                            loadTabData(tabId);
                        } else {
                            alert(response.message || "Deleting data failed.");
                        }
                    },
                    error: function (error) {
                        alert("An error occurred while deleting data.");
                    }
                });
            }

        }


        $(document).ready(function () {
            $('#confirm-delete-btn').on('click', function () {
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
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                            $('#deleteModal').modal('hide');
                        } else {
                            alert(response.message || "Deleting table failed.");
                        }
                    },
                    error: function (error) {
                        alert("An error occurred while deleting table.");
                    }
                });
            });

            $('#confirm-delete-permanently-btn').on('click', function () {
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
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                                $('#deleteModal').modal('hide');
                            } else {
                                alert(response.message || "Deleting table failed.");
                            }
                        },
                        error: function (error) {
                            alert("An error occurred while deleting table.");
                        }
                    });
                }
            });
        });
    </script>