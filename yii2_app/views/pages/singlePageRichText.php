<?php

use app\assets\RichtextAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */

$this->title = $page->name;
?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-auto mb-3 mb-md-0 text-center text-md-start">
                <h4><?= Html::encode($this->title) ?></h4>
                <p class="mt-1 f-m-light"></p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="page-content">

            <div class="form-group my-1" id="view-content">
                <div id="content-display"><?= $content ?></div>
            </div>
        </div>
    </div>
</div>