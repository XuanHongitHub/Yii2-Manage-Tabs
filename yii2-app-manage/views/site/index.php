<?php

use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
/** @var app\models\Tab[] $tabs */

$this->title = 'Tabs Data';
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
                                <a class="nav-link <?= $index === 0 ? 'active' : '' ?>" href="#"
                                    data-id="<?= $tab->id ?>" onclick="loadTabData(<?= $tab->id ?>, null)">
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
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tab-data-current">
                                <div class="table-responsive" id="table-data-current">
                                    <!-- Data Loading -->
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
<?php

$firstTabId = null;
foreach($tabs as $tab) {
    if ($tab -> deleted == 0) {
        $firstTabId = $tab -> id; 
        break;
    }
}

?>
<script async>
$(document).ready(function() {

    var firstTabId = <?= !empty($firstTabId) ? $tabs[0]->id : 'null' ?>;
    if (firstTabId !== null) {
        loadTabData(firstTabId);
    } else {
        console.log("No tabs available to load data.");
    }

    function loadTabData(tabId, page, search) {
        console.log("🚀 ~ loadTabData ~ tabId:", tabId);
        localStorage.clear(); 

        $.ajax({
            url: "<?= \yii\helpers\Url::to(['tabs/load-tab-data']) ?>",
            type: "GET",
            data: {
                tabId: tabId,
                page: page,
                search: search,
            },
            success: function(data) {
                $('#table-data-current').html(data);
                // Cập nhật trạng thái của tab hiện tại
                $('.nav-link').removeClass('active');
                $('.nav-item').removeClass('active');
                $(`[data-id="${tabId}"]`).addClass('active');
                $(`[data-id="${tabId}"]`).closest('.nav-item').addClass('active');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while loading data. Please try again later.');
            }
        });
    }


    $(document).off('keydown', '#goToPageInput').on('keydown', '#goToPageInput',
        function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#goToPageButton').click();
            }
        });
    $(document).off('click', '.pagination .paginate_button').on('click', '.pagination .paginate_button',
        function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            var tabId = $('.nav-link.active').data('id');
            var search = $('input[name="search"]').val();

            if (search && typeof search === 'string') {
                search = search.trim();
            } else {
                console.log('Search term is empty or undefined.');
            }

            console.log("🚀 ~ $ ~ tabId | PAGE:", page);
            console.log("🚀 ~ $ ~ tabId | SEARCH:", search);

            // Pass searchTerm instead of the undefined search
            loadData(tabId, page, search);
        });
    $(document).off('click', '#goToPageButton').on('click', '#goToPageButton', function() {
        var page = $('#goToPageInput').val();
        var tabId = $('.nav-link.active').data('id');
        var search = $('input[name="search"]').val();

        if (search && typeof search === 'string') {
            search = search.trim();
        }

        if (page && !isNaN(page)) {
            page = parseInt(page) - 1; // Chuyển thành chỉ số page (0-based)
            loadData(tabId, page, search);
        } else {
            console.log('Invalid page number.');
        }
    });

    $(document).off('click', '#lastPageButton').on('click', '#lastPageButton', function(e) {
        e.preventDefault(); // Ngăn chặn hành vi mặc định của link
        var tabId = $('.nav-link.active').data('id');
        var search = $('input[name="search"]').val();

        if (search && typeof search === 'string') {
            search = search.trim();
        }

        var lastPage = $(this).data('page');
        loadData(tabId, lastPage, search);
    });


});
</script>



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