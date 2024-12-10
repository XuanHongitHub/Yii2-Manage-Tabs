<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Quản Lý Người Dùng';

?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex">
            <div class="me-auto">
                <h4>Quản Lý Người Dùng</h4>
                <p class="mt-1 f-m-light">Danh sách tài khoản người dùng.</p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'username',
                    'label' => 'Tên',
                ],
                'email',
                [
                    'attribute' => 'status',
                    'label' => 'Trạng thái',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::beginForm(Url::to(['users/update-user', 'id' => $model->id]), 'post', ['class' => 'd-inline'])
                            . Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken)
                            . Html::dropDownList('status', $model->status, [
                                10 => 'Kích hoạt',
                                0 => 'Vô hiệu hóa'
                            ], [
                                'class' => 'form-control',
                            ])
                            . Html::endForm();
                    }
                ],
                [
                    'attribute' => 'role',
                    'label' => 'Quyền',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::beginForm(Url::to(['users/update-user', 'id' => $model->id]), 'post', ['class' => 'd-inline'])
                            . Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken)
                            . Html::dropDownList('role', $model->role, [10 => 'User', 20 => 'Admin'], [
                                'class' => 'form-control',
                            ])
                            . Html::endForm();
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width: 10%; text-align: center;'],
                    'template' => '{update}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::submitButton('Update', [
                                'class' => 'btn btn-primary',
                                'form' => 'form-update-' . $model->id,
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>