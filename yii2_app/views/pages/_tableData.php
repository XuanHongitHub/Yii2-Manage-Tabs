<?php

use yii\widgets\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $data array */
/* @var $columns array */
/* @var $pagination yii\data\Pagination */
/* @var $sort string */
/* @var $sortDirection int */
// $pageId = $_GET['pageId'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 0;

use yii\widgets\ActiveForm;

?>

<!-- DỮ LIỆU BẢNG -->
<div id="tableData">
    <div class="d-flex flex-wrap justify-content-between mt-3">

    </div>

    <?php

Pjax::begin([
    'id' => 'data-grid',
    'timeout' => 10000,
    'enablePushState' => false,
]);

// Form tìm kiếm
echo Html::beginTag('div', ['class' => 'search-bar mb-3']);
echo Html::beginForm(['load-page-data'], 'get', ['data-pjax' => true, 'class' => 'form-inline']);
echo Html::textInput('search', Yii::$app->request->get('search', ''), [
    'class' => 'form-control mr-2',
    'placeholder' => 'Tìm kiếm...',
]);
echo Html::submitButton('Tìm', ['class' => 'btn btn-primary']);
echo Html::endForm();
echo Html::endTag('div');

// Hiển thị bảng GridView
echo GridView::widget([
    'dataProvider' => $dataProvider,  // Data provider chứa dữ liệu bảng
    'columns' => array_merge(
        // Các cột động từ $columns
        array_map(function ($column) {
            // Cấu hình cho mỗi cột
            return [
                'attribute' => $column,
                'headerOptions' => ['style' => 'text-align:center;'],
                'contentOptions' => ['style' => 'text-align:center;'],
            ];
        }, $columns),
        [
            // Cột hành động
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Thao tác',  // Tiêu đề cho cột hành động
                'headerOptions' => ['style' => 'width:15%; text-align:center;'],
                'contentOptions' => ['style' => 'text-align:center;'],
                'template' => '{update} {delete}',  // Các nút sửa và xóa
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fa-solid fa-pen-to-square"></i>', ['update', 'id' => $key], [
                            'class' => 'btn btn-secondary btn-sm',
                            'data-pjax' => 0,  // Tắt pjax khi nhấn nút sửa
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fa-regular fa-trash-can"></i>', ['delete', 'id' => $key], [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                'confirm' => 'Bạn có chắc chắn muốn xóa dòng này?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ]
    ),
    'tableOptions' => ['class' => 'table table-bordered table-hover table-responsive'],
    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center'>{pager}\n{summary}</div>",
    'pager' => [
        'class' => 'yii\widgets\LinkPager',
        'options' => ['class' => 'pagination'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
    ],
    'summary' => '<span class="text-muted">Hiển thị <b>{begin}-{end}</b> trên tổng số <b>{totalCount}</b> dòng.</span>',
]);

// Kết thúc Pjax
Pjax::end();
?>

</div>