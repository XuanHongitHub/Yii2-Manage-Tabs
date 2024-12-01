<?php

use app\models\Menu;
use app\assets\RichtextAsset;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */


$menuId = $_GET['menuId'];
$menuName = Menu::findOne($menuId)->name ?? 'Menu Page';
$this->title = $menuName;

?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>

<div class="page-body">
    <!-- Container-fluid starts -->
    <div class="container-fluid pt-3">
        <div class="row">
            <div class="col-sm-12">

                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h4>
                            <?= $menuName?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="page-content">
                            <div class="page-pane fade show active" id="page-data-current">
                                <div class="table-responsive" id="table-data-current">
                                    <?= $content?>
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