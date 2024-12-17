<?php

/** @var yii\web\View $this */

use app\assets\AppAsset;
use yii\helpers\Url;

/** @var app\models\Page[] $pages */
/** @var app\models\Menu $menu */
$this->registerJsFile('js/components/frontend/multiPage.js', ['depends' => AppAsset::class]);

$this->title = $menu->name;
?>

<div class="card">
    <div class="card-body">
        <div class="d-flex">
            <ul class="simple-wrapper nav nav-tabs expand" id="page-list">
                <?php foreach ($pages as $index => $page): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $index === 0 ? 'active' : '' ?>" href="#" data-id="<?= $page->id ?>"
                        onclick="loadPageData(<?= $page->id ?>, null)">
                        <?= htmlspecialchars($page->name) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <ul class="simple-wrapper nav nav-tabs ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link bg-light border" href="#" id="btn-list-page" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-list me-0"></i>
                    </a>
                    <ul class="dropdown-menu profile-dropdown border" aria-labelledby="btn-list-page">
                        <li><a class="dropdown-item" href="#" id="expand-option"><i
                                    class="fa-solid fa-expand me-2"></i>Mở rộng</a></li>
                        <li><a class="dropdown-item border-top" href="#" data-bs-toggle="modal"
                                data-bs-target="#listPageModal">
                                <i class="fa-solid fa-book me-2"></i>Danh Sách</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="page-content">
            <div class="page-pane fade show active" id="page-data-current">
                <div id="page-data-current">
                    <!-- Data Loading -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal listPageModal -->
<div class="modal fade" id="listPageModal" tabindex="-1" aria-labelledby="listPageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="listPageModalLabel">Danh Sách Page</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Nút sắp xếp -->
                    <button class="btn btn-outline-secondary btn-sm" id="sort-toggle">
                        <i class="fa-solid fa-arrow-up-a-z"></i> Sắp xếp A-Z
                    </button>
                    <!-- Ô tìm kiếm -->
                    <input type="text" id="search-page" class="form-control form-control-sm w-50"
                        placeholder="Tìm kiếm...">
                </div>
                <ul class="list-group" id="page-list-modal">
                    <?php
                    $sortedPages = $pages;
                    usort($sortedPages, function ($a, $b) {
                        return strcmp($a->name, $b->name);
                    });
                    foreach ($sortedPages as $page):
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a class="txt-dark" href="#" onclick="loadPageData(<?= $page->id ?>, null)">
                            <?= htmlspecialchars($page->name) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="save-columns-config">Lưu</button>
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
<script>
var firstpageId = <?= !empty($firstpageId) ? $pages[0]->id : 'null' ?>;
var loadPageUrl = "<?= Url::to(['pages/load-page-data']) ?>";
var menuId = <?= $menuId = $_GET['menuId']; ?>
</script>