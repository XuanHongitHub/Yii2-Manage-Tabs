<?php

use app\assets\AppAsset;
use app\models\BaseModel;
use app\models\Page;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
$this->registerJsFile('js/components/frontend/multiTablePage.js', ['depends' => AppAsset::class]);
$this->registerJsFile('js/components/frontend/_tablePage.js', ['depends' => AppAsset::class]);


$this->title = $menu->name;
?>

<!-- Modal Nhập Excel -->
<div class="modal fade" id="importExelModal" tabindex="-1" aria-labelledby="importExelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExelModalLabel">Nhập Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6 me-auto">
                        <form id="importExcelForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="import-excel-file" class="form-label">Chọn Tệp Excel</label>
                                <input class="form-control" type="file" id="import-excel-file" name="import-excel-file"
                                    accept=".xlsx, .xls" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Nhập Excel</button>
                        </form>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <p class="my-1 f-m-light">Xuất Template (Chỉ Header):
                            </p>
                            <a class="btn btn-sm btn-outline-primary"
                                href='<?= Url::to(['pages/export-excel-header', 'pageId' => $pageId]) ?>'>Xuất
                                Template</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Model Báo lỗi Import -->
<div class="modal fade" id="importStatusModal" tabindex="-1" aria-labelledby="importStatusModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importStatusModalLabel">Báo lỗi Import</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <pre class="modal-body text-wrap" id="importStatusMessage">
            </pre>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Trạng Thái Nhập -->
<div class="modal fade" id="importStatusModal" tabindex="-1" aria-labelledby="importStatusModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importStatusModalLabel">Trạng Thái Nhập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <pre class="modal-body text-wrap" id="importStatusMessage">
            </pre>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Dữ Liệu -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Sửa Dữ Liệu</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-form">
                    <?php foreach ($columns as $index => $column): ?>
                        <?php if ($index === 0): ?>
                            <input type="hidden" name="<?= $column ?>" id="edit-<?= $column ?>">
                        <?php else: ?>
                            <div class="form-group">
                                <label for="edit-<?= $column ?>"><?= ucfirst($column) ?></label>
                                <input type="text" class="form-control" name="<?= $column ?>" id="edit-<?= $column ?>"
                                    placeholder="Nhập <?= ucfirst($column) ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="save-row-btn" class="btn btn-primary">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Dữ Liệu -->
<div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addDataModalLabel">Nhập dữ liệu</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-data-form">
                    <?php foreach ($columns as $index => $column): ?>
                        <?php if ($index === 0): ?>
                            <input type="hidden" name="<?= $column ?>" id="<?= $column ?>">
                        <?php else: ?>
                            <div class="form-group">
                                <label for="<?= $column ?>"><?= ucfirst($column) ?></label>
                                <input type="text" class="form-control" name="<?= $column ?>" id="<?= $column ?>"
                                    placeholder="Nhập <?= $column ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="add-row-btn" class="btn btn-primary">Thêm</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="page-content">
            <div class="page-pane fade show active" id="page-data-current">
                <div class="table-responsive" id="table-data-current">
                    <!-- DỮ LIỆU BẢNG -->
                    <div id="tableData">
                        <div class="d-flex flex-wrap justify-content-between mt-3">
                            <div class="d-md-flex d-sm-block">
                                <button class="btn btn-primary mb-2 me-2" id="add-data-btn" href="#"
                                    data-bs-toggle="modal" data-bs-target="#addDataModal">
                                    <i class="fa-solid fa-plus"></i> Nhập Mới
                                </button>

                                <div class="form-group">
                                    <?= Html::button('Xóa đã chọn', [
                                        'class' => 'btn btn-danger mb-2 me-2',
                                        'id' => 'delete-selected-btn',
                                    ]) ?>
                                </div>

                                <!-- Nút Nhập Excel -->
                                <button class="btn btn-info mb-2 me-2" id="import-data-btn" href="#"
                                    data-bs-toggle="modal" data-bs-target="#importExelModal">
                                    <i class="fa-solid fa-download"></i> Nhập Excel
                                </button>

                                <button class="btn btn-warning mb-2 me-auto" id="export-excel-btn">
                                    <i class="fa-solid fa-download"></i> Xuất Dữ Liệu
                                </button>

                            </div>

                            <div class="search-bar mb-3">
                                <?php
                                echo Html::beginForm(['/pages/load-page-data', 'pageId' => $pageId], 'get', [
                                    'data-pjax' => true,
                                    'class' => 'form-inline',
                                    'id' => 'search-form',
                                ]);
                                ?>

                                <div class="form-inline search-tab mb-2 me-2">
                                    <div class="form-group d-flex align-items-center mb-0">
                                        <i class="fa fa-search"></i>
                                        <?= Html::textInput('search', Yii::$app->request->get('search'), [
                                            'class' => 'form-control-plaintext', // Lớp CSS cho input
                                            'placeholder' => 'Tìm kiếm...'
                                        ]) ?>
                                    </div>
                                </div>
                                <?= Html::submitButton('Tìm', [
                                    'class' => 'btn btn-primary mb-2',
                                    'onclick' => 'loadData(); return false;'  // Gọi hàm loadData và ngừng gửi form
                                ]) ?>

                                <?= Html::endForm(); ?>
                            </div>
                        </div>

                        <?php
                        Pjax::begin([
                            'id' => "data-grid-{$pageId}",
                            'timeout' => 10000,
                            'enablePushState' => false,
                        ]);

                        // Hiển thị bảng GridView
                        echo GridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' =>
                            array_merge(
                                [
                                    [
                                        'class' => 'yii\grid\CheckboxColumn',
                                        'name' => BaseModel::HIDDEN_ID_KEY,
                                        'headerOptions' => ['style' => 'text-align:center; width: 3%;'],
                                        'contentOptions' => ['style' => 'text-align:center;'],
                                        'checkboxOptions' => function ($data, $key, $index, $column) {
                                            return ['value' => $data[BaseModel::HIDDEN_ID_KEY], 'data-hidden_id' => $data[BaseModel::HIDDEN_ID_KEY], 'class' => 'checkbox-row'];
                                        }
                                    ],
                                ],
                                array_map(function ($column, $index) {
                                    return [
                                        'attribute' => $column,
                                        'contentOptions' => [
                                            'class' => $index === 0 ? 'hidden-column' : '',
                                            'data-column' => $column,
                                        ],
                                        'headerOptions' => [
                                            'class' => $index === 0 ? 'sortable-column hidden-column' : 'sortable-column',
                                            'style' => 'cursor:pointer;',
                                            'data-column' => $column,
                                        ],
                                        'value' => function ($data, $index, $widget) use ($column) {
                                            return isset($data[$column]) && !empty($data[$column]) ? $data[$column] : '';
                                        },
                                        'enableSorting' => true,

                                    ];
                                }, $columns, array_keys($columns)),
                                [
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Thao tác',
                                        'headerOptions' => ['style' => 'width: 10%; text-align:center; white-space: nowrap;'],
                                        'contentOptions' => ['style' => 'text-align:center; white-space: nowrap;'],
                                        'template' => '{update} {delete}',
                                        'buttons' => [
                                            'update' => function ($url, $data, $key) {
                                                return Html::a('<i class="fa-solid fa-pen-to-square"></i>', '#', [
                                                    'class' => 'btn btn-secondary btn-m btn-edit',
                                                    'data-row' => json_encode($data),
                                                    'data-pjax' => 0,
                                                ]);
                                            },
                                            'delete' => function ($url, $data, $key) {
                                                return Html::a('<i class="fa-regular fa-trash-can"></i>', '#', [
                                                    'class' => 'btn btn-danger btn-m btn-delete',
                                                    'data-hidden_id' => $data[BaseModel::HIDDEN_ID_KEY],
                                                ]);
                                            },
                                        ],
                                    ],
                                ]
                            ),
                            'tableOptions' => ['class' => 'table table-bordered table-hover table-responsive'],
                            'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-3'>
                                            <div class='d-flex justify-content-start'>{summary}</div>
                                            <div class='d-flex justify-content-end'>{pager}</div>
                                        </div>",
                            'summary' => '<span class="text-muted">Hiển thị <b>{begin}-{end}</b> trên tổng số <b>{totalCount}</b> dòng.</span>',
                            'pager' => [
                                'class' => 'yii\widgets\LinkPager',
                                'options' => ['class' => 'pagination justify-content-end align-items-center'], // Đặt phân trang về bên phải
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

                        // Kết thúc Pjax
                        Pjax::end();

                        ?>

                        <div class="d-flex flex-column flex-md-row align-items-center my-3">
                            <!-- Đi đến trang -->
                            <div class="go-to-page d-flex align-items-center me-md-5 mb-2 mb-md-0">
                                <span class="me-2">Đến trang:</span>
                                <input class="form-control form-control-sm me-2" type="number" id="goPage" min="1"
                                    max="" style="width: 5rem;" />
                                <button id="goToPageButton" class="btn btn-primary btn-sm"
                                    onclick="loadData()">Đi</button>
                            </div>

                            <!-- Number of items per page -->
                            <div class="number-of-items d-flex align-items-center mb-2 mb-md-0">
                                <span class="me-2">Xem:</span>
                                <?php
                                $pageSizes = [10 => 10, 25 => 25, 50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000];
                                echo Html::beginForm(['/pages', 'pageId' => $pageId], 'get', [
                                    'data-pjax' => true,  // Dùng PJAX cho form này
                                    'class' => 'form-inline',
                                    'id' => 'pageSize-form', // Đảm bảo id cho form
                                ]);
                                echo Html::dropDownList(
                                    'pageSize',
                                    $pageSize,
                                    $pageSizes,
                                    [
                                        'class' => 'form-select form-select-sm autosubmit',
                                        'id' => 'pageSize',
                                        'style' => ['width' => '5rem'],
                                        'onchange' => 'loadData()',
                                    ]
                                );
                                echo Html::endForm();
                                ?>
                            </div>

                            <!-- Nút Tùy chỉnh cột -->
                            <div class="btn-group">
                                <button class="btn btn-primary btn-sm mx-2 dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-border-all"></i> Tùy Chỉnh
                                </button>
                                <ul class="dropdown-menu border">
                                    <table class="table table-borderless" id="columns-visibility">
                                        <?php $index = 0; ?>
                                        <?php foreach ($columns as $column): ?>
                                            <tr class="border" <?= $index === 0 ? 'style="display:none;"' : '' ?>>
                                                <td class="d-flex justify-content-between align-items-center">
                                                    <span><?= htmlspecialchars($column) ?></span>
                                                    <input class="form-check-input column-checkbox" type="checkbox"
                                                        id="checkbox-<?= htmlspecialchars($column) ?>"
                                                        data-column="<?= htmlspecialchars($column) ?>"
                                                        <?= $index === 0 ? 'disabled' : 'checked' ?>>
                                                </td>
                                            </tr>
                                            <?php $index++; ?>
                                        <?php endforeach; ?>
                                    </table>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var add_data_url = "<?= Url::to(['pages/add-data']) ?>";
    var update_data_url = "<?= Url::to(['pages/update-data']) ?>";
    var delete_data_url = "<?= Url::to(['pages/delete-data']) ?>";
    var pageId = "<?= $pageId ?>";
    var tableName = "<?= $dataProvider->query->from[0] ?>";
    var delete_all_data_url = "<?= Url::to(['pages/delete-selected-data']) ?>";
    var import_url = "<?= Url::to(['pages/import-excel']) ?>";
    var export_url = "<?= Url::to(['pages/export-excel']) ?>";
</script>