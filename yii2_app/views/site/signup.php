<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\SignupForm $model */

$this->title = 'Đăng Ký';

?>
<?php $this->beginPage() ?>
<?php
$this->head();
$cssFile = [
    'css/style.css',
    'css/bootstrap.css',
];

foreach ($cssFile as $css) {
    $this->registerCssFile($css, ['depends' => [\yii\web\YiiAsset::class]]);
}
?>
<title><?= Html::encode($this->title) ?></title>
<?php $this->beginBody(); ?>
<div class="login-card login-dark">
    <div>
        <div>
            <a class="logo" href="<?= Yii::$app->homeUrl ?>">
                <img class="img-fluid for-light" width="32px" src="<?= Yii::getAlias('@web') ?>/images/logo-icon.png"
                    alt="login page">
            </a>
        </div>
        <div class="login-main">
            <h1><?= Html::encode($this->title) ?></h1>
            <p>Vui lòng điền vào các trường sau để đăng ký:</p>

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

            <p class="mt-4 mb-0">Bạn đã có tài khoản? <a class="ms-2"
                    href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>">Đăng nhập</a></p>
        </div>
    </div>
</div>
<?php $this->endBody() ?>

<?php $this->endPage() ?>