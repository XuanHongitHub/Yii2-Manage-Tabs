<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = $model->username;

?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex">
            <div class="me-auto">
                <h4>Cập nhật thông tin người dùng:</h4>
                <p class="mt-1 f-m-light"><?= $model->username ?></p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>