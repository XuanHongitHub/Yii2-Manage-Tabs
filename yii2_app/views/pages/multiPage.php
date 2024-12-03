<?php

/** @var yii\web\View $this */
/** @var app\models\Page[] $pages */
/** @var app\models\Menu $menu */

$this->title = $menu->name;
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

});

function loadPageData(pageId) {

    $.ajax({
        url: "<?= \yii\helpers\Url::to(['pages/load-page-data']) ?>",
        type: "GET",
        data: {
            pageId: pageId,
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