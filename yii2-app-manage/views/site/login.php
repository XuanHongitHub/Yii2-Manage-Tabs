<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use app\assets\AppAsset;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

AppAsset::register($this);

$this->title = 'Đăng Nhập';
?>
<div class="container-fluid p-0">
    <div class="row m-0">
        <div ss="col-12 p-0">
            <div class="login-card">
                <div>
                    <div>
                        <a class="logo" href="<?= \yii\helpers\Url::to(['/']) ?>">
                            <img class="img-fluid for-light" width="32px"
                                src="<?= Yii::getAlias('@web') ?>/images/logo-icon.png" alt="logo">

                        </a>
                    </div>
                    <div class="login-main">
                        <h1><?= Html::encode($this->title) ?></h1>
                        <p>Vui lòng điền vào các trường sau để đăng nhập:</p>

                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'fieldConfig' => [
                                'template' => "{label}\n{input}\n{error}",
                                'labelOptions' => ['class' => 'col-form-label'],
                                'inputOptions' => ['class' => 'form-control'],
                                'errorOptions' => ['class' => 'invalid-feedback'],
                            ],
                        ]); ?>

                        <div class="form-group">
                            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'password')->passwordInput() ?>
                        </div>

                        <div class="form-group">
                            <div>
                                <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block w-100', 'name' => 'login-button']) ?>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>


                        <p class="mt-4 mb-0 text-center">Không có tài khoản?<a class="ms-2"
                                href="<?= Yii::$app->urlManager->createUrl(['site/signup']) ?>">Tạo tài khoản</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>