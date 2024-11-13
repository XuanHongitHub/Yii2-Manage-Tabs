<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'List Menu';

?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>




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
                        <div class="d-flex flex-column flex-md-row align-items-md-center">
                            <div class="me-auto mb-3 mb-md-0 text-center text-md-start">
                                <h4>List of Tabs</h4>
                                <p class="mt-1 f-m-light">Table Tab | Richtext Tab</p>
                            </div>
                            <div
                                class="d-flex flex-wrap justify-content-center align-items-center me-md-2 mb-3 mb-md-0">
                                <a class="btn btn-outline-warning me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#hideModal">
                                    <i class="fas fa-eye me-1"></i> Show/Hidden
                                </a>
                                <a class="btn btn-outline-primary me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#sortModal">
                                    <i class="fas fa-sort-amount-down me-1"></i> Sort Tab
                                </a>
                                <a class="btn btn-danger me-2 mb-2" href="#" data-bs-toggle="modal"
                                    data-bs-target="#trashBinModal">
                                    <i class="fas fa-trash me-1"></i> Trash Bin
                                </a>
                                <a class="btn btn-success mb-2" href="<?= \yii\helpers\Url::to(['settings/create']) ?>">
                                    <i class="fas fa-plus me-1"></i> New Tab
                                </a>
                            </div>

                        </div>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="display border table-bordered dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Status</th>
                                        <th>Position</th>
                                        <th>Created</th>
                                        <th style="width: 8%">Actions</th>
                                    </tr>
                                </thead>


                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    $(document).ready(function () {
        $('.dataTable').DataTable({
            order: [],
            columnDefs: [{
                orderable: false,
                targets: -1
            }],
            "lengthChange": false,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "ordering": true,

        });
    });
    $(document).ready(function () {
        $('#confirm-hide-btn').click(function () {
            let hideStatus = {};

            $('.toggle-hide-btn').each(function () {
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
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || "An error occurred while saving changes.");
                    }
                },
                error: function () {
                    alert("An error occurred while saving changes.");
                }
            });
        });
        $("#sortable-tabs").sortable();

        $("#confirm-sort-btn").click(function () {
            var sortedData = [];
            $("#sortable-tabs li").each(function (index) {
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
                success: function (response) {
                    if (response.success) {
                        location.reload();
                        $('#sortModal').modal('hide');
                    } else {
                        alert(response.message || "Error.");
                    }
                },
                error: function () {
                    alert("Error.");
                }
            });
        });
        $(document).on('click', '#confirm-restore-btn', function () {
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
                success: function (response) {
                    if (response.success) {
                        location.reload();
                        $('#trashBinModal').modal('hide');
                    } else {
                        alert(response.message || "Restore table failed.");
                    }
                },
                error: function (error) {
                    alert("An error occurred while Restore table.");
                }
            });
        });
        $(document).ready(function () {
            $(document).on('click', '#delete-permanently-btn', function () {
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
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.message || "Deletion failed.");
                            }
                        },
                        error: function (error) {
                            alert("An error occurred while deleting the tab.");
                        }
                    });
                }
            });
        });

    });
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
    document.addEventListener("DOMContentLoaded", function () {
        const deleteButtons = document.querySelectorAll(".delete-btn");
        const confirmDeleteBtn = document.getElementById("confirm-delete-btn");
        const confirmDeletePermanentlyBtn = document.getElementById("confirm-delete-permanently-btn");

        deleteButtons.forEach(button => {
            button.addEventListener("click", function () {
                const tabId = this.getAttribute("data-tab-id");
                confirmDeleteBtn.setAttribute("data-tab-id", tabId);
                confirmDeletePermanentlyBtn.setAttribute("data-tab-id", tabId);
            });
        });
    });
</script>