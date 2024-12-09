<?php

use mihaildev\elfinder\ElFinder;

/** @var yii\web\View $this */

$this->title = 'File Manager';
?>

<div class="card">
    <div class="card-body" style="height: calc(100vh - 110px)">
        <?= ElFinder::widget([
            'controller'       => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
            'containerOptions' => ['style' => 'height: 100%'],
        ]);
        ?>
    </div>
</div>