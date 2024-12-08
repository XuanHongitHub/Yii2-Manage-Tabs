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
<script>
    var firstpageId = <?= !empty($firstpageId) ? $pages[0]->id : 'null' ?>;
    var loadPageUrl = "<?= Url::to(['pages/load-page-data']) ?>";
    var menuId = <?= $menuId = $_GET['menuId']; ?>
</script>