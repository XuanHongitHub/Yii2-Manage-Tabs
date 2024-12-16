<?php

use app\assets\AppAsset;
use app\models\BaseModel;
use app\models\Page;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
$this->registerJsFile('js/components/frontend/multiTablePage.js', ['depends' => AppAsset::class]);
$this->registerJsFile('js/components/frontend/_tablePage.js', ['depends' => AppAsset::class]);
$this->title = $menu->name;
// var_dump($configColumns);

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
    <div class="modal-dialog modal-lg">
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
                            <div class="form-group mb-2">
                                <label for="edit-<?= $column ?>"><?= ucfirst($column) ?></label>
                                <input type="text" class="form-control" name="<?= $column ?>" id="edit-<?= $column ?>">
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
    <div class="modal-dialog modal-lg">
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
                            <div class="form-group mb-2">
                                <label for="<?= $column ?>"><?= ucfirst($column) ?></label>
                                <input type="text" class="form-control" name="<?= $column ?>" id="<?= $column ?>">
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

<!-- Modal Config -->
<div class="modal fade" id="columnsModal" tabindex="-1" aria-labelledby="columnsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnsModalLabel">Tùy Chỉnh Cột</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless" id="columns-config">
                    <thead>
                        <tr>
                            <th style="width: 4%; text-align: center;"><i class="fa-solid fa-arrow-down-wide-short"></i>
                            </th>
                            <th style="width: 30%;">Tên cột</th>
                            <th style="width: 30%;">Tên hiển thị</th>
                            <th style="width: 6%; text-align: center;">Ẩn/Hiện</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-config">
                        <?php
                        $columnPositions = [];
                        foreach ($configColumns as $config) {
                            $columnPositions[$config['column_name']] = $config;
                        }

                        $columns = array_merge(
                            array_column($configColumns, 'column_name'),
                            array_diff($columns, array_column($configColumns, 'column_name'))
                        );
                        ?>

                        <?php foreach ($columns as $column): ?>
                            <?php
                            if ($column === BaseModel::HIDDEN_ID_KEY) continue;

                            $config = $columnPositions[$column] ?? null;
                            $displayName = $config['display_name'] ?? $column;
                            $isVisible = isset($config['is_visible']) ? $config['is_visible'] : true;
                            ?>
                            <tr data-column="<?= htmlspecialchars($column) ?>">
                                <td class="text-nowrap" style="width: 4%; text-align: center; vertical-align: middle;">
                                    <span class="drag-handle" style="cursor: grab; font-size: 20px;">&#9776;</span>
                                </td>
                                <td class="column-name text-nowrap" style="width: 30%; vertical-align: middle;">
                                    <?= htmlspecialchars($column) ?>
                                </td>
                                <td class="text-nowrap" style="width: 30%; vertical-align: middle;">
                                    <?= htmlspecialchars($displayName) ?>
                                </td>
                                <td class="text-nowrap" style="width: 4%; text-align: center; vertical-align: middle;">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input column-switch text-center" type="checkbox"
                                            id="switch-<?= htmlspecialchars($column) ?>"
                                            data-column="<?= htmlspecialchars($column) ?>"
                                            <?= $isVisible ? 'checked' : '' ?>>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="save-columns-config">Lưu</button>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center my-2">
    <div class="d-flex flex-wrap justify-content-start">
        <button class="btn btn-primary me-2 mb-2" id="add-data-btn" href="#" data-bs-toggle="modal"
            data-bs-target="#addDataModal">
            <i class="fa-solid fa-plus"></i> Nhập Mới
        </button>

        <div class="form-group me-2 mb-2">
            <?= Html::button('Xóa đã chọn', [
                'class' => 'btn btn-danger',
                'id' => 'delete-selected-btn',
            ]) ?>
        </div>

        <button class="btn btn-info me-2 mb-2" id="import-data-btn" href="#" data-bs-toggle="modal"
            data-bs-target="#importExelModal">
            <i class="fa-solid fa-download"></i> Nhập Excel
        </button>

        <button class="btn btn-warning me-auto mb-2" id="export-excel-btn">
            <i class="fa-solid fa-download"></i> Xuất Dữ Liệu
        </button>
    </div>

    <!-- Nút Tùy chỉnh -->
    <div class="btn-group ms-auto me-2 mb-2">
        <button class="btn btn-primary" type="button" id="btn-config">
            <i class="fa-solid fa-border-all"></i> Tùy Chỉnh
        </button>
    </div>

    <div class="search-bar mb-2">
        <?php
        echo Html::beginForm(null, 'get', [
            'data-pjax' => true,
            'class' => 'form-inline',
            'id' => 'search-form',
        ]);
        ?>
        <div class="form-inline search-tab me-2">
            <div class="form-group d-flex align-items-center mb-0">
                <i class="fa fa-search"></i>
                <?= Html::textInput('search', Yii::$app->request->get('search'), [
                    'class' => 'form-control-plaintext',
                    'placeholder' => 'Tìm kiếm...'
                ]) ?>
            </div>
        </div>
        <?= Html::submitButton('Tìm', [
            'class' => 'btn btn-primary',
            'onclick' => 'loadData(); return false;'
        ]) ?>
        <?= Html::endForm(); ?>
    </div>

</div>

<!-- DỮ LIỆU BẢNG -->
<?php

Pjax::begin([
    'id' => "data-grid-{$pageId}",
    'timeout' => 10000,
    'enablePushState' => false,
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
    'headerRowOptions' => ['class' => 'sortable-column'],
    'columns' => array_merge(
        [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => BaseModel::HIDDEN_ID_KEY,
                'headerOptions' => ['style' => 'text-align:center; width: 40px;'],
                'contentOptions' => ['style' => 'text-align:center; width: 40px;'],
                'checkboxOptions' => function ($data, $key, $index, $column) {
                    return ['value' => $data[BaseModel::HIDDEN_ID_KEY], 'data-hidden_id' => $data[BaseModel::HIDDEN_ID_KEY], 'class' => 'checkbox-row'];
                }
            ],
        ],
        array_map(function ($column) use ($configColumns) {
            $config = null;
            foreach ($configColumns as $columnConfig) {
                if ($columnConfig['column_name'] === $column) {
                    $config = $columnConfig;
                    break;
                }
            }

            $displayName = $config['display_name'] ?? $column;
            $isVisible = isset($config['is_visible']) ? $config['is_visible'] : true;
            $columnWidth = $config['column_width'] ?? null;

            return [
                'attribute' => $column,
                'label' => $displayName,
                'enableSorting' => true,
                'visible' => (
                    ($column !== BaseModel::HIDDEN_ID_KEY) &&
                    $isVisible
                ),
                'contentOptions' => [],
                'headerOptions' => [
                    'class' => 'rs-col text-nowrap text-center',
                    'style' => $columnWidth ? 'width:' . $columnWidth . 'px' : 'min-width: fit-content;',
                    'data-column-name' => $column,
                ]
            ];
        }, $columns),
        [
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Thao tác',
                'headerOptions' => ['style' => 'width: 120px; text-align:center; white-space: nowrap;'],
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
    'tableOptions' => ['id' => 'table-data', 'class' => 'table table-bordered table-hover', 'style' => 'table-layout: fixed; min-width: 100%'],
    'layout' => "<div class='table-responsive' id='tableData' style='max-height: 65vh;'>{items}</div>\n<div class='d-flex flex-wrap justify-content-between align-items-center mt-3'>
                <div class='d-flex flex-column flex-md-row justify-content-start mb-2 mb-md-0'>{summary}</div>
                <div class='d-flex flex-column flex-md-row justify-content-start mb-2 mb-md-0'>{gopage}</div>
                <div class='d-flex flex-column flex-md-row justify-content-start mb-2 mb-md-0'>{perpage}</div>
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

?>

<script>
    var pageId = "<?= $pageId ?>";
    var add_data_url = "<?= Url::to(['pages/add-data']) ?>";
    var update_data_url = "<?= Url::to(['pages/update-data']) ?>";
    var delete_data_url = "<?= Url::to(['pages/delete-data']) ?>";
    var tableName = "<?= $dataProvider->query->from[0] ?>";
    var delete_all_data_url = "<?= Url::to(['pages/delete-selected-data']) ?>";
    var import_url = "<?= Url::to(['pages/import-excel']) ?>";
    var export_url = "<?= Url::to(['pages/export-excel']) ?>";
    var save_column_config_url = "<?= Url::to(['pages/save-columns-config']) ?>";
    var save_column_width_url = "<?= Url::to(['pages/save-columns-width']) ?>";
</script>