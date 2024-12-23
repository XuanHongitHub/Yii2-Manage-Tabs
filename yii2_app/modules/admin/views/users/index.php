<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

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

        <div class="d-flex">
            <div class="search-bar ms-auto">
                <?php
                $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => Yii::$app->request->url,
                    'options' => ['data-pjax' => true, 'class' => 'form-inline'],
                ]);
                ?>
                <div class="form-inline search-tab mb-2 me-2">
                    <div class="form-group d-flex align-items-center mb-0">
                        <i class="fa fa-search"></i>
                        <?= $form->field($searchModel, 'searchQuery', [
                            'template' => "{input}",
                            'inputOptions' => [
                                'class' => 'form-control-plaintext mb-0',
                                'placeholder' => 'Tìm kiếm... ',
                            ],
                            'options' => ['class' => 'mb-0'],
                        ])->label(false) ?>
                    </div>
                </div>

                <?= Html::submitButton('Tìm', ['class' => 'btn btn-primary mb-2']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                'username',
                'email:email',
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
            'tableOptions' => ['id' => 'table-data', 'class' => 'table table-bordered table-hover custom-td', 'style' => 'table-layout: fixed; min-width: 100%'],
            'layout' => "<div class='table-responsive' id='tableData' style='max-height: 65vh;'>{items}</div>\n<div class='d-flex flex-wrap justify-content-between align-items-center mt-3'>
                        <div class='d-flex flex-column flex-md-row justify-content-start mb-2 mb-md-0'>{summary}</div>
                        <div class='d-flex justify-content-end'>{pager}</div>
                    </div>",
            'summary' => '<span class="text-muted">Hiển thị <b>{begin}-{end}</b> trên tổng số <b>{totalCount}</b> dòng.</span>',
            'pager' => [
                'class' => 'yii\widgets\LinkPager',
                'options' => ['class' => 'pagination justify-content-end align-items-center'],
                'linkContainerOptions' => ['tag' => 'span'],
                'linkOptions' => [
                    'class' => 'paginate_button',
                ],
                'activePageCssClass' => 'current',
                'disabledPageCssClass' => 'disabled',
                'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'paginate_button'],
                'prevPageLabel' => 'Trước',
                'nextPageLabel' => 'Tiếp',
                'maxButtonCount' => 5,
            ],
        ]); ?>

    </div>
</div>