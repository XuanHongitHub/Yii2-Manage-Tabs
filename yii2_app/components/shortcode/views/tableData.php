<?php

use app\models\BaseModel;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;


Pjax::begin([
    'id' => $pjaxId,
    'enablePushState' => false
]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'headerRowOptions' => ['class' => 'sortable-column'],
    'columns' => $columns,
    'tableOptions' => ['id' => 'table-data', 'class' => 'table table-bordered table-hover table-responsive'],
    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-3'>
                                                <div class='d-flex justify-content-start'>{summary}</div>
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
]);
Pjax::end();
