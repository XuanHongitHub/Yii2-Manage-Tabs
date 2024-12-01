<?php

use app\models\Menu;
/** @var yii\web\View $this */
/** @var app\models\Page[] $pages */


$menuId = $_GET['menuId'];
$menuName = Menu::findOne($menuId)->name ?? 'Menu Page';
$this->title = $menuName;

?>

<div class="card">
    <div class="card-body">
        <ul class="simple-wrapper nav nav-tabs" id="page-list">
            <?php foreach ($pages as $index => $page): ?>
            <li class="nav-item">
                <a class="nav-link <?= $index === 0 ? 'active' : '' ?>" href="#" data-id="<?= $page->id ?>"
                    onclick="loadPageData(<?= $page->id ?>, null)">
                    <?= htmlspecialchars($page->name) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="page-content">
            <div class="page-pane fade show active" id="page-data-current">
                <div class="table-responsive" id="table-data-current">
                    <!-- Data Loading -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php

$firstpageId = null;
foreach ($pages as $page) {
    if ($page->deleted == 0) {
        $firstpageId = $page->id;
        break;
    }
}

?>
<script async>
$(document).ready(function() {

    var firstpageId = <?= !empty($firstpageId) ? $pages[0]->id : 'null' ?>;
    if (firstpageId !== null) {
        loadPageData(firstpageId);
    } else {
        console.log("No pages available to load data.");
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
            var pageId = $('.nav-link.active').data('id');
            var column = $(this).data('column');
            var sortDirection = $(this).data('sort-direction');

            var search = $('input[name="search"]').val();
            var pageSize = $('#pageSize').val();

            if (search && typeof search === 'string') {
                search = search.trim();
            }

            loadData(pageId, page, search, pageSize, column, sortDirection);
        });

    $(document).off('click', '#goToPageButton').on('click', '#goToPageButton', function() {
        var page = $('#goToPageInput').val();
        var pageId = $('.nav-link.active').data('id');
        var search = $('input[name="search"]').val();
        var column = $(this).data('column');
        var sortDirection = $(this).data('sort-direction');
        var pageSize = $('#pageSize').val();

        if (search && typeof search === 'string') {
            search = search.trim();
        }

        if (page && !isNaN(page)) {
            page = parseInt(page) - 1;
            loadData(pageId, page, search, pageSize, column, sortDirection);
        } else {
            console.log('Invalid page number.');
        }
    });


    $(document).off('click', '#lastPageButton').on('click', '#lastPageButton', function(e) {
        e.preventDefault();

        var page = $(this).data('page');
        var pageId = $('.nav-link.active').data('id');
        var search = $('input[name="search"]').val();
        var pageSize = $('#pageSize').val();
        var totalCount = $('#totalCount').val();
        var column = $(this).data('column');
        var sortDirection = $(this).data('sort-direction');
        if (search && typeof search === 'string') {
            search = search.trim();
        }

        lastPage = Math.ceil(totalCount / pageSize) - 1;

        loadData(pageId, lastPage, search, pageSize, column, sortDirection);
    });


    $(document).off('change', '#pageSize').on('change', '#pageSize', function() {
        var pageSize = $(this).val();
        var column = $(this).data('column');
        var sortDirection = $(this).data('sort-direction');
        var pageId = $('.nav-link.active').data('id');
        var search = $('input[name="search"]').val();

        if (search && typeof search === 'string') {
            search = search.trim();
        }

        if (pageSize && (pageSize === 'all' || !isNaN(pageSize))) {
            loadData(pageId, 0, search, pageSize, column, sortDirection);
        } else {
            console.log('Invalid page size.');
        }
    });


});

function loadPageData(pageId, page, search, pageSize, column, sortDirection) {
    localStorage.clear();

    $.ajax({
        url: "<?= \yii\helpers\Url::to(['pages/load-page-data']) ?>",
        type: "GET",
        data: {
            pageId: pageId,
            page: page,
            search: search,
            pageSize: pageSize,
            column,
            sortDirection
        },
        success: function(data) {
            $('#table-data-current').html(data);
            // Cập nhật trạng thái của page hiện tại
            $('.nav-link').removeClass('active');
            $('.nav-item').removeClass('active');
            $(`[data-id="${pageId}"]`).addClass('active');
            $(`[data-id="${pageId}"]`).closest('.nav-item').addClass('active');

        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau.');
        }
    });
}
</script>