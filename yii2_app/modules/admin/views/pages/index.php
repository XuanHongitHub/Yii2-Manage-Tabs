<?php

use app\assets\AppAsset;
use app\models\BaseModel;
use app\models\Config;
use yii\bootstrap5\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\assets\Select2Asset;

/** @var yii\web\View $this */

Select2Asset::register($this);
$this->registerJsFile('js/components/admin/indexPage.js', ['depends' => AppAsset::class]);

$this->title = 'Danh sách Page';

?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-auto mb-3 mb-md-0 text-center text-md-start">
                <h4>Danh sách Pages</h4>
                <p class="mt-1 f-m-light">Table Page | Richtext Page</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center align-items-center me-md-2 mb-3 mb-md-0">
                <a class="btn btn-outline-warning me-2 mb-2" href="#" data-bs-toggle="modal"
                    data-bs-target="#hideModal">
                    <i class="fas fa-eye me-1"></i> Hiện/Ẩn
                </a>
                <a class="btn btn-danger me-2 mb-2" href="#" data-bs-toggle="modal" data-bs-target="#trashBinModal">
                    <i class="fas fa-trash me-1"></i> Thùng Rác
                </a>
                <a class="btn btn-success mb-2" href="<?= Url::to(['pages/create']) ?>">
                    <i class="fas fa-plus me-1"></i> Thêm Page
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php Pjax::begin([
            'id' => 'page-gridview-pjax',
            'enablePushState' => false,  // Tắt pushState để không thay đổi URL
            'enableReplaceState' => false // Tắt replaceState để không thay đổi URL
        ]); ?>

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
                        <?= $form->field($searchModel, 'name', [
                            'template' => "{input}",
                            'inputOptions' => [
                                'class' => 'form-control-plaintext mb-0',
                                'placeholder' => 'Tìm kiếm...',
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
            'dataProvider' => new ActiveDataProvider([
                'query' => $dataProvider->query->andWhere(['deleted' => 0]),
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'name',
                    'label' => 'Tên Page',
                ],
                [
                    'attribute' => 'type',
                    'label' => 'Loại',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return $data->type == 'richtext' ?
                            '<span class="badge badge-light-danger">Richtext</span>' :
                            '<span class="badge badge-light-primary">Table</span>';
                    },
                ],
                [
                    'attribute' => 'status',
                    'label' => 'Hiện',
                    'format' => 'raw',
                    'value' => function ($data) {
                        return $data->status == 1 ?
                            '<span class="badge badge-warning">Ẩn</span>' :
                            '<span class="badge badge-success">Hiện</span>';
                    },
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Hành động',
                    'template' => '{edit} {setting} {delete} ',
                    'buttons' => [
                        'edit' => function ($url, $model, $key) {
                            return Html::button('<i class="fa-solid fa-pen-to-square"></i>', [
                                'class' => 'btn btn-primary btn-m edit-btn',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#editModal',
                                'data-page-id' => $model->id,
                                'data-page-name' => $model->name,
                                'data-page-type' => $model->type,
                                'data-page-status' => $model->status,
                                'title' => 'Chỉnh sửa trang',
                            ]);
                        },
                        'setting' => function ($url, $model, $key) {
                            if ($model->type === 'table') {
                                return Html::button('<i class="fa-solid fa-border-all"></i>', [
                                    'class' => 'btn btn-outline-primary btn-m setting-btn',
                                    'data-page-id' => $model->id,
                                    'title' => 'Tùy chỉnh cột',
                                ]);
                            } else {
                                $url = Url::to([
                                    'pages/edit',
                                    'id' => $model->id,
                                    'returnUrl' => Url::current() // Lưu URL hiện tại làm returnUrl
                                ]);                                
                                return Html::a('<i class="fa-solid fa-file-pen"></i>', $url, [
                                    'class' => 'btn btn-outline-primary btn-m',
                                    'title' => 'Sửa nội dung Richtext',
                                ]);
                            }
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::button('<i class="fa-regular fa-trash-can"></i>', [
                                'class' => 'btn btn-danger btn-m delete-btn',
                                'data-bs-toggle' => 'modal',
                                'data-bs-target' => '#deleteModal',
                                'data-page-id' => $model->id,
                                'title' => 'Xóa trang',
                            ]);
                        },
                    ],
                    'headerOptions' => ['style' => 'width: 10%; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align:center; white-space: nowrap;'],
                ],
            ],
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
            'summary' => '<span class="text-muted pt-3">Hiển thị <b>{begin}-{end}</b> trên tổng số <b>{totalCount}</b> dòng.</span>',
            'layout' => "{items}\n{summary}\n{pager}",
        ]) ?>

        <?php Pjax::end(); ?>

    </div>


</div>

<!-- Modal sửa -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Sửa Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTabForm">
                    <div class="mb-3">
                        <label for="editpageName" class="form-label">Tên Page</label>
                        <input type="text" class="form-control" id="editpageName" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Trạng thái</label>
                        <select class="form-select" id="editStatus" name="status">
                            <option value="0" <?= $page->status == 0 ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="1" <?= $page->status == 1 ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveTabChanges">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal setting -->
<div class="modal fade" id="settingModal" tabindex="-1" aria-labelledby="settingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTableModalLabel">Tùy chỉnh cột</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless" id="columns-visibility">
                    <thead>
                        <tr>
                            <th>Cột</th>
                            <th class="text-end">Ẩn/Hiện</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" id="saveColumnChanges" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Thùng Rác -->
<div class="modal fade" id="trashBinModal" tabindex="-1" aria-labelledby="trashBinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="trashBinModalLabel">Thùng Rác</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Chọn page bạn muốn khôi phục hoặc xóa hoàn toàn:</p>
                <table class="table table-bordered table-hover table-ui">
                    <thead>
                        <tr>
                            <th>Tên Page</th>
                            <th style="width: 20%; text-align: center;">Loại</th>
                            <th style="width: 20%; text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="trash-bin-list">
                        <?php
                        if ($trashDataProvider->getCount() > 0):
                            foreach ($trashDataProvider->models as $page): ?>
                        <tr>
                            <td><?= htmlspecialchars($page->name) ?></td>
                            <td class="text-center">
                                <?php if ($page->type == 'richtext'): ?>
                                <span class="badge badge-light-danger">Richtext</span>
                                <?php else: ?>
                                <span class="badge badge-light-primary">Table</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap">
                                <button type="button" class="btn btn-warning restore-page-btn"
                                    data-page-id="<?= htmlspecialchars($page->id) ?>">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                                <button type="button" class="btn btn-danger delete-page-btn"
                                    data-page-id="<?= htmlspecialchars($page->id) ?>">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                <em>Không có gì ở đây.</em>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Hide page -->
<div class="modal fade" id="hideModal" tabindex="-1" aria-labelledby="hideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="hideModalLabel">Hiện/Ẩn Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
            </div>
            <div class="modal-body">
                <p class="pb-0 mb-0">Chọn page bạn muốn ẩn hoặc hiển thị:</p>
                <table class="table dataTable">
                    <thead>
                        <tr>
                            <th>Tên Page</th>
                            <th class="text-center" style="width: 45%">Loại</th>
                            <th class="text-center" style="width: 8%">Hiện</i></th>
                        </tr>
                    </thead>
                    <tbody id="hide-index">
                        <?php foreach ($dataProvider->models as $page): ?>
                        <?php if ($page->deleted == 0): ?>
                        <tr>
                            <td class="py-0"><?= htmlspecialchars($page->name) ?></td>
                            <td class="text-center py-0">
                                <?php if ($page->type == 'richtext'): ?>
                                <span class="badge badge-light-danger">Richtext</span>
                                <?php else: ?>
                                <span class="badge badge-light-primary">Table</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-0 text-center">
                                <label class="switch mb-0 mt-1">
                                    <input class="form-check-input toggle-hide-btn" type="checkbox"
                                        data-page-id="<?= htmlspecialchars($page->id) ?>"
                                        <?php if ($page->status == 0): ?> checked <?php endif; ?>>
                                    <span class="switch-state"></span>
                                </label>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirm-hide-btn">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirm Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteModalLabel">Xác nhận xóa page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa page này không? Không thể hoàn tác hành động này.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                    data-page-id="<?= htmlspecialchars($pageId) ?>">Xóa</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                    data-page-name="<?= htmlspecialchars($page->name) ?>"
                    data-page-id="<?= htmlspecialchars($pageId) ?>">Xóa Vĩnh Viễn</button>
            </div>
        </div>
    </div>
</div>

<script>
var save_sort_url = "<?= Url::to(['pages/save-sort']) ?>";
var update_status_url = "<?= Url::to(['pages/update-hide-status']) ?>";
var update_sortOrder_url = "<?= Url::to(['pages/update-sort-order']) ?>";
var restore_page_url = "<?= Url::to(['pages/restore-page']) ?>";
var delete_permanently_url = "<?= Url::to(['pages/delete-permanently-page']) ?>";
var delete_soft_url = "<?= Url::to(['pages/delete-page']) ?>";
var save_sub_page_url = "<?= Url::to(['pages/save-sub-page']) ?>";
var yiiWebAlias = "<?= Yii::getAlias('@web') ?>";
var update_page_url = "<?= Url::to(['pages/update-page']) ?>";
var get_table_page_url = "<?= Url::to(['pages/get-table-page']) ?>";
</script>