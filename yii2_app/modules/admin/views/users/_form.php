<?php

use app\models\User;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- Username -->
    <div class="form-group mb-3">
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    </div>

    <!-- Email -->
    <div class="form-group mb-3">
        <?= $form->field($model, 'email')->input('email') ?>
    </div>

    <!-- Status -->
    <div class="form-group mb-3">
        <?= $form->field($model, 'status')->dropDownList([User::STATUS_ACTIVE => 'Kích hoạt', User::STATUS_INACTIVE => 'Vô hiệu hóa']) ?>
    </div>

    <!-- Role -->
    <div class="form-group mb-3">
        <?= $form->field($model, 'role')->dropDownList([User::ROLE_USER => 'User', User::ROLE_ADMIN => 'Admin']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Lưu', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>