<?php

use app\assets\AppAsset;
use app\assets\RichtextAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */

?>

<div class="page-content my-2">
    <div class="form-group" id="view-content">
        <div id="content-display"><?= $content ?></div>
    </div>
</div>