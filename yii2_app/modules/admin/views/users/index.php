<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Quản Lý Người Dùng';

?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-auto mb-3 mb-md-0 text-center text-md-start">
                <h4>Quản Lý Người Dùng</h4>
                <p class="mt-1 f-m-light">Danh sách tài khoản người dùng.</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center align-items-center me-md-2 mb-3 mb-md-0">
                <a class="btn btn-success mb-2" href="<?= Url::to(['users/create']) ?>">
                    <i class="fas fa-plus me-1"></i> Thêm người dùng
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                'username',
                'email:email',
                // 'auth_key',
                // 'access_token',
                //'verification_token',
                //'password_hash',
                [
                    'attribute' => 'status',
                    'label' => 'Trạng thái',
                    'value' => function ($model) {
                        return $model->status == User::STATUS_ACTIVE
                            ? '<span class="badge bg-success">Kích hoạt</span>'
                            : '<span class="badge bg-danger">Vô hiệu hóa</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'role',
                    'label' => 'Quyền',
                    'value' => function ($model) {
                        return $model->role == User::ROLE_ADMIN
                            ? '<span class="badge badge-light-danger">Admin</span>'
                            : '<span class="badge badge-light-primary">User</span>';
                    },
                    'format' => 'raw',
                ],
                //'created_at',
                //'updated_at',
                //'password_reset_token',
                [
                    'class' => ActionColumn::className(),
                    'header' => 'Hành động',
                    'headerOptions' => ['style' => 'width: 15%; text-align: center;'],
                    'contentOptions' => ['style' => 'width: 15%; text-align: center;'],
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-eye"></i>', $url, [
                                'class' => 'btn btn-info btn-m',
                                'title' => 'Xem',
                            ]);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-edit"></i>', $url, [
                                'class' => 'btn btn-primary btn-m',
                                'title' => 'Cập nhật',
                            ]);
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-trash-alt"></i>', $url, [
                                'class' => 'btn btn-danger btn-m',
                                'title' => 'Xóa',
                                'data' => [
                                    'confirm' => 'Bạn có chắc chắn xóa tài khoản người dùng này?',
                                    'method' => 'post',
                                ],
                            ]);
                        },
                    ],
                    'urlCreator' => function ($action, User $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    },
                ],
            ],
        ]); ?>
    </div>
</div>