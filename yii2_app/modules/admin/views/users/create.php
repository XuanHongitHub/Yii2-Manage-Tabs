<?php

use app\models\User;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Thêm người dùng';

?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex">
            <div class="me-auto">
                <h4>Thêm Người Dùng</h4>
                <p class="mt-1 f-m-light">Nhập thông tin thêm người dùng.</p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <!-- Username -->
        <div class="form-group mb-3">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>

        <!-- Email -->
        <div class="form-group mb-3">
            <?= $form->field($model, 'email')->input('email') ?>
        </div>

        <!-- Password -->
        <div class="form-group mb-3">
            <?= $form->field($model, 'password')->passwordInput() ?>
        </div>
        <div class="form-group mb-3">
            <?= $form->field($model, 'repeat_password')->passwordInput()->label('Nhập lại mật khẩu') ?>
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
</div>