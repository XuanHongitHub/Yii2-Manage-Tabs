<?php

use app\assets\AppAsset;
use app\assets\RichtextAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */

$this->title = "Xem: " . $page->name;
?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="ms-auto mb-md-0 text-end">
                <a class="btn btn-outline-light" title="Sửa"
                    href="<?= Url::to(['pages/edit/', 'id' => $_GET['id']]) ?>">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="page-content">
            <div class="form-group my-1">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>