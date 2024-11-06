<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'List Tabs';

?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>

<div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <small id="toast-timestamp"></small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">Hello, I'm a web-designer.</div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a success message
    const successMessage = "<?= Yii::$app->session->getFlash('success') ?>";
    const errorMessage = "<?= Yii::$app->session->getFlash('error') ?>";
    if (successMessage) {
        document.getElementById('toast-body').textContent = successMessage;
        document.getElementById('toast-timestamp').textContent = new Date().toLocaleTimeString();
        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
    if (errorMessage) {
        document.getElementById('toast-body').textContent = errorMessage;
        document.getElementById('toast-timestamp').textContent = new Date().toLocaleTimeString();
        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
});
</script>

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
                        <div class="d-flex">
                            <div class="me-auto">
                                <h4>List of Tabs</h4>
                                <p class="mt-1 f-m-light">Table Tab | Richtext Tab</p>
                            </div>
                            <div class="btn-group-ellipsis me-2">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item fw-medium text-light-emphasis" href="#"
                                            data-bs-toggle="modal" data-bs-target="#hideModal">
                                            <i class="fas fa-eye me-1"></i> Show/Hidden Tab
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item fw-medium text-light-emphasis" href="#"
                                            data-bs-toggle="modal" data-bs-target="#sortModal">
                                            <i class="fas fa-sort-amount-down me-1"></i> Sort Order Tab
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item fw-medium text-light-emphasis" href="#"
                                            data-bs-toggle="modal" data-bs-target="#trashBinModal">
                                            <i class="fas fa-trash me-1"></i> Trash Bin
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-success"
                                    href="<?= \yii\helpers\Url::to(['settings/create']) ?>">Create Tab</a>
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
                                <tbody id="columnsContainer">
                                    <?php foreach ($tabs as $tab): ?>
                                    <?php if ($tab->deleted != 1): ?>
                                    <tr>
                                        <td><?= Html::encode($tab->id) ?></td>
                                        <td><?= Html::encode($tab->tab_name) ?></td>
                                        <td class="text-center">
                                            <?php if ($tab->tab_type == 'table'): ?>
                                            <span class="badge badge-light-primary">Table</span>
                                            <?php else: ?>
                                            <span class="badge badge-light-danger">Richtext</span>
                                            <?php endif; ?>
                                        </td>
                                        </td>
                                        <td class="text-center">
                                            <?= $tab->deleted == 3 ?
                                                    '<span class="badge badge-warning">Hide</span>' : '<span class="badge badge-success">Show</span>'
                                                    ?>
                                        </td>
                                        <td><?= Html::encode($tab->position) ?></td>
                                        <td><?= Html::encode(Yii::$app->formatter->asDate($tab->created_at)) ?></td>
                                        <td class="d-flex text-nowrap">
                                            <button class="btn btn-secondary btn-sm save-row-btn me-1"><i
                                                    class="fa-solid fa-pen-to-square"></i></button>
                                            <button href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-tab-id="<?= htmlspecialchars($tab->id) ?>">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endif; ?>

                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <p>Drag and drop to arrange tabs.</p>
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

<!-- Modal Confirm Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm delete tab</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this tab? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete permanently</button>
            </div>
        </div>
    </div>
</div>

<?php include Yii::getAlias('@app/views/layouts/_footer.php'); ?>

<script>
$(document).ready(function() {
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
document.addEventListener("DOMContentLoaded", function() {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const confirmDeleteBtn = document.getElementById("confirm-delete-btn");
    const confirmDeletePermanentlyBtn = document.getElementById("confirm-delete-permanently-btn");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            const tabId = this.getAttribute("data-tab-id");
            confirmDeleteBtn.setAttribute("data-tab-id", tabId);
            confirmDeletePermanentlyBtn.setAttribute("data-tab-id", tabId);
        });
    });
});
</script>