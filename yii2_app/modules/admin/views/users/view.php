<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = $model->username;
\yii\web\YiiAsset::register($this);
?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex">
            <div class="me-auto">
                <h4>Thông tin người dùng: </h4>
                <p class="mt-1 f-m-light"><?= $model->username ?></p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <p>
            <?= Html::a('Cập Nhật', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Xóa', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Bạn có chắc chắc xóa tài khoản người dùng này?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'username',
                'email:email',
                // 'auth_key',
                // 'access_token',
                // 'verification_token',
                // 'password_hash',
                [
                    'label' => 'Trạng thái',
                    'value' => $model->status == \app\models\User::STATUS_ACTIVE ? 'Kích hoạt' : 'Vô hiệu hóa',
                ],
                [
                    'label' => 'Quyền',
                    'value' => $model->role == \app\models\User::ROLE_ADMIN ? 'Admin' : 'User',
                ],
                [
                    'label' => 'Ngày tạo',
                    'value' => Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i:s'),
                ],
                [
                    'label' => 'Ngày cập nhật',
                    'value' => Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i:s'),
                ],
                // 'password_reset_token',
            ],
        ]) ?>
    </div>
</div>