<?php

use app\models\User;

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use app\assets\AppAsset;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Đổi mật khẩu';
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="card">
            <div class="card-header pb-0">
                <h4 class="card-title mb-0"><?= Html::encode($this->title) ?></h4>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                                'id' => 'change-password-form',
                                'method' => 'post',
                            ]); ?>

                <?= $form->field($model, 'old_password')->passwordInput()->label('Mật khẩu cũ') ?>

                <?= $form->field($model, 'new_password')->passwordInput()->label('Mật khẩu mới') ?>

                <?= $form->field($model, 'confirm_new_password')->passwordInput()->label('Nhập lại mật khẩu') ?>

                <div class="form-footer">
                    <?= Html::submitButton('Lưu', ['class' => 'btn btn-primary btn-block']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>