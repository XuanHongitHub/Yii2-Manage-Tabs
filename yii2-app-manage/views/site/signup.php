<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Signup';
?>
<div class="container-fluid p-0">
    <div class="row m-0">
        <div class="col-12 p-0">
            <div class="login-card login-dark">
                <div>
                    <div>
                        <a class="logo" href="<?= Yii::$app->homeUrl ?>">
                            <img class="img-fluid for-light" src="<?= Yii::getAlias('@web') ?>/images/logo/logo-1.png"
                                alt="login page">
                            <img class="img-fluid for-dark" src="<?= Yii::getAlias('@web') ?>/images/logo/logo.png"
                                alt="login page">
                        </a>
                    </div>
                    <div class="login-main">
                        <h1><?= Html::encode($this->title) ?></h1>
                        <p>Please fill out the following fields to signup:</p>

                        <?php $form = ActiveForm::begin([
                            'id' => 'form-signup',
                            'fieldConfig' => [
                                'template' => "{label}\n{input}\n{error}",
                                'labelOptions' => ['class' => 'col-form-label pt-0'],
                                'inputOptions' => ['class' => 'form-control'],
                                'errorOptions' => ['class' => 'invalid-feedback'],
                            ],
                        ]); ?>

                        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
                        <?= $form->field($model, 'email')->textInput() ?>
                        <?= $form->field($model, 'password')->passwordInput() ?>

                        <div class="form-group">
                            <?= Html::submitButton('Signup', ['class' => 'btn btn-primary btn-block w-100', 'name' => 'signup-button']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                        <p class="mt-4 mb-0">Already have an account? <a class="ms-2"
                                href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>