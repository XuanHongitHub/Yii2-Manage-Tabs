<?php

use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
/** @var app\models\Tab[] $tabs */

$this->title = 'Tabs Data';
?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>

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
                    <div class="ms-auto my-2">
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
                                No Tabs Available. Please create a new tab in the settings.
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
foreach ($tabs as $tab) {
    if ($tab->deleted == 0) {
        $firstTabId = $tab->id;
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

    function loadTabData(tabId, page, search, pageSize) {
        localStorage.clear();

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
            var pageSize = $('#pageSize').val();

            if (search && typeof search === 'string') {
                search = search.trim();
            }

            loadData(tabId, page, search, pageSize);
        });

    $(document).off('click', '#goToPageButton').on('click', '#goToPageButton', function() {
        var page = $('#goToPageInput').val();
        var tabId = $('.nav-link.active').data('id');
        var search = $('input[name="search"]').val();
        var pageSize = $('#pageSize').val();

        if (search && typeof search === 'string') {
            search = search.trim();
        }

        if (page && !isNaN(page)) {
            page = parseInt(page) - 1;
            loadData(tabId, page, search, pageSize);
        } else {
            console.log('Invalid page number.');
        }
    });


    $(document).off('click', '#lastPageButton').on('click', '#lastPageButton', function(e) {
        e.preventDefault();

        var page = $(this).data('page');
        var tabId = $('.nav-link.active').data('id');
        var search = $('input[name="search"]').val();
        var pageSize = $('#pageSize').val();
        var totalCount = $('#totalCount').val();

        if (search && typeof search === 'string') {
            search = search.trim();
        }

        lastPage = Math.ceil(totalCount / pageSize) - 1;

        loadData(tabId, lastPage, search, pageSize);
    });


    $(document).off('change', '#pageSize').on('change', '#pageSize', function() {
        var pageSize = $(this).val();

        var tabId = $('.nav-link.active').data('id');
        var search = $('input[name="search"]').val();

        if (search && typeof search === 'string') {
            search = search.trim();
        }

        if (pageSize && (pageSize === 'all' || !isNaN(pageSize))) {
            loadData(tabId, 0, search, pageSize);
        } else {
            console.log('Invalid page size.');
        }
    });


});
</script>