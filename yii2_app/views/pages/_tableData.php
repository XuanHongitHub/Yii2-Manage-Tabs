<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */


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
                            <button class="btn btn-sm btn-outline-primary" id="exportTemplateButton">Xuất
                                Template</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác Nhận Nhập-->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Vấn Đề</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body" id="confirmMessage">Bạn có chắc chắn muốn tiếp tục?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmYesBtn">Tiếp tục</button>
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

<!-- Modal Export Excel-->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Chọn Hình Thức Xuất Dữ Liệu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nút Xuất Toàn Bộ Dữ Liệu -->
                <button class="btn btn-warning mb-2 w-100" id="exportExcelButton">
                    <i class="fa-solid fa-file-export"></i> Xuất Toàn Bộ Dữ Liệu
                </button>
                <!-- Nút Xuất View Hiện Tại -->
                <button class="btn btn-secondary mb-2 w-100" id="exportCurrentViewButton">
                    <i class="fa-solid fa-eye"></i> Xuất View Hiện Tại
                </button>

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

                                <!-- Nút Xuất Excel -->
                                <button class="btn btn-warning mb-2 me-auto" data-bs-toggle="modal"
                                    data-bs-target="#exportModal">
                                    <i class="fa-solid fa-download"></i> Xuất Dữ Liệu
                                </button>

                            </div>

                            <div class="search-bar mb-3">
                                <?php
                                // Thêm 'data-pjax' vào form để sử dụng PJAX và tránh reload trang
                                echo Html::beginForm(['/pages/load-page-data', 'pageId' => $pageId], 'get', [
                                    'data-pjax' => true,  // Dùng PJAX cho form này
                                    'class' => 'form-inline',
                                    'id' => 'search-form', // Thêm id để dễ dàng xử lý JS
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
                                        'name' => 'hidden_id',
                                        'headerOptions' => ['style' => 'text-align:center; width: 3%;'],
                                        'contentOptions' => ['style' => 'text-align:center;'],
                                        'checkboxOptions' => function ($data, $key, $index, $column) {
                                            return ['value' => $data['hidden_id'], 'data-hidden_id' => $data['hidden_id'], 'class' => 'checkbox-row'];
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
                                            return isset($data[$column]) && !empty($data[$column]) ? $data[$column] : ''; // Trả về giá trị hoặc trống
                                        },
                                        'enableSorting' => true,

                                    ];
                                }, $columns, array_keys($columns)),
                                [
                                    [
                                        'class' => 'yii\grid\ActionColumn',
                                        'header' => 'Thao tác',
                                        'headerOptions' => ['style' => 'width:15%; text-align:center;'],
                                        'contentOptions' => ['style' => 'text-align:center;'],
                                        'template' => '{update} {delete}',
                                        'buttons' => [
                                            'update' => function ($url, $data, $key) {
                                                return Html::a('<i class="fa-solid fa-pen-to-square"></i>', '#', [
                                                    'class' => 'btn btn-secondary btn-sm btn-edit',
                                                    'data-row' => json_encode($data),
                                                    'data-pjax' => 0,
                                                ]);
                                            },
                                            'delete' => function ($url, $data, $key) {
                                                return Html::a('<i class="fa-regular fa-trash-can"></i>', '#', [
                                                    'class' => 'btn btn-danger btn-sm btn-delete',
                                                    'data-hidden_id' => $data['hidden_id'], // Dùng $data['hidden_id'] để lấy id thực tế
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
    $(document).ready(function() {
        let columnVisibility = {};

        function applyColumnVisibility() {
            $('.column-checkbox').each(function() {
                const column = $(this).data('column');
                const isChecked = columnVisibility[column] !== false;

                $(this).prop('checked', isChecked);

                if (isChecked) {
                    $(`th[data-column="${column}"], td[data-column="${column}"]`).show();
                } else {
                    $(`th[data-column="${column}"], td[data-column="${column}"]`).hide();
                }
            });
        }

        $(document).off('change', '.column-checkbox').on('change', '.column-checkbox', function() {
            const column = $(this).data('column');
            const isChecked = $(this).is(':checked');

            columnVisibility[column] = isChecked;

            if (isChecked) {
                $(`th[data-column="${column}"], td[data-column="${column}"]`).show();
            } else {
                $(`th[data-column="${column}"], td[data-column="${column}"]`).hide();
            }
        });

        $(document).off('pjax:send').on('pjax:send', function() {
            console.log('Pjax sending...');
            var loadingSpinner = $(`
        <div class="spinner-fixed">
            <i class="fa fa-spin fa-spinner me-2"></i>
        </div>
    `);
            $('body').append(loadingSpinner);
        });

        $(document).off('pjax:complete').on('pjax:complete', function() {
            console.log('Pjax completed');
            $('.spinner-fixed').remove();
            console.log("🚀 ~ $ ~ window.location.pathname:", window.location.pathname);
            console.log("🚀 ~ $ ~ Load:", "<?= \yii\helpers\Url::to(['pages/load-page-data?']) ?>", window
                .location.pathname);

            applyColumnVisibility();
        });

        applyColumnVisibility();

        $(document).off('click', '#add-row-btn').on('click', '#add-row-btn', function(e) {
            e.preventDefault();

            var formData = $('#add-data-form').serialize();
            formData +=
                '&tableName=<?= $dataProvider->query->from[0] ?>';
            var pageId = '<?= $pageId ?>';

            $.ajax({
                url: "<?= \yii\helpers\Url::to(['pages/add-data']) ?>", // Đường dẫn xử lý thêm dữ liệu
                type: "POST",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF Token
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#add-data-form')[0].reset(); // Reset form
                        $('#addDataModal').modal('hide'); // Đóng modal
                        showToast('Thêm dữ liệu thành công!');
                        loadData();
                    } else {
                        alert('Có lỗi xảy ra: ' + response
                            .message); // Thông báo lỗi
                    }
                },
                error: function() {
                    alert(
                        'Không thể thêm dữ liệu. Vui lòng thử lại.'
                    ); // Thông báo lỗi nếu có sự cố
                }
            });
        });

        $(document).off('click', '.btn-edit').on('click', '.btn-edit', function() {
            var rowData = $(this).data('row');

            $.each(rowData, function(key, value) {
                var inputField = $('#edit-' + key);
                if (inputField.length) {
                    inputField.val(value);
                }
            });

            $('#editModal').modal('show');
        });

        $(document).off('click', '#save-row-btn').on('click', '#save-row-btn', function(e) {
            e.preventDefault();
            var pageId = '<?= $pageId ?>';
            var formData = $('#edit-form').serialize();
            formData += '&tableName=<?= $dataProvider->query->from[0] ?>';
            console.log("🚀 ~ $ ~ formData:", formData);
            $.ajax({
                url: "<?= \yii\helpers\Url::to(['pages/update-data']) ?>",
                type: "POST",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                        'content')
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#edit-form')[0].reset(); // Reset form
                        $('#editModal').modal('hide'); // Đóng modal
                        showToast('Cập nhật dữ liệu thành công!');
                        loadData();
                    } else {
                        alert('Có lỗi xảy ra: ' + response
                            .message);
                    }
                },
                error: function() {
                    alert(
                        'Không thể cập nhật dữ liệu. Vui lòng thử lại.'
                    );
                }
            });
        });

        $(document).off('click', '.btn-delete').on('click', '.btn-delete', function(e) {
            e.preventDefault();

            var rowId = $(this).data('hidden_id'); // Lấy ID của dòng cần xóa
            var pageId = '<?= $pageId ?>';
            if (confirm('Bạn có chắc chắn muốn xóa dòng này?')) {
                $.ajax({
                    url: "<?= \yii\helpers\Url::to(['pages/delete-data']) ?>", // Đường dẫn xử lý xóa dữ liệu
                    type: "POST",
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                            'content') // CSRF Token
                    },
                    data: {
                        hidden_id: rowId, // Truyền ID dòng cần xóa
                        tableName: '<?= $dataProvider->query->from[0] ?>',
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Xóa dữ liệu thành công!');
                            loadData();

                        } else {
                            alert('Có lỗi xảy ra: ' + response
                                .message); // Thông báo lỗi
                        }
                    },
                    error: function() {
                        alert(
                            'Không thể xóa dữ liệu. Vui lòng thử lại.'
                        ); // Thông báo lỗi nếu có sự cố
                    }
                });
            }
        });

        // Xóa nhiều bản ghi đã chọn
        $(document).off('click', '#delete-selected-btn').on('click', '#delete-selected-btn', function(e) {
            e.preventDefault();

            // Lấy tất cả các ID của các dòng được chọn
            var selectedIds = [];
            $('.checkbox-row:checked').each(function() {
                selectedIds.push($(this).data('hidden_id')); // Lấy id của dòng đã chọn
            });

            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một dòng để xóa.');
                return;
            }
            var pageId = '<?= $pageId ?>';
            // Cảnh báo xác nhận xóa
            if (confirm('Bạn có chắc chắn muốn xóa các dòng đã chọn?')) {
                $.ajax({
                    url: "<?= \yii\helpers\Url::to(['pages/delete-selected-data']) ?>", // Đường dẫn xử lý xóa nhiều dữ liệu
                    type: "POST",
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                            'content') // CSRF Token
                    },
                    data: {
                        ids: selectedIds, // Truyền danh sách ID cần xóa
                        tableName: '<?= $dataProvider->query->from[0] ?>',
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Xóa dữ liệu thành công!');
                            loadData();

                        } else {
                            alert('Có lỗi xảy ra: ' + response
                                .message); // Thông báo lỗi
                        }
                    },
                    error: function() {
                        alert(
                            'Không thể xóa dữ liệu. Vui lòng thử lại.'
                        ); // Thông báo lỗi nếu có sự cố
                    }
                });
            }
        });

        $('#search-form input[name="search"]').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loadData();
            }
        });
    });


    function loadData() {
        var search = $('#search-form input[name="search"]').val();
        var pageSize = $('#pageSize-form select[name="pageSize"]').val();
        var pageId = '<?= $pageId ?>';
        var page = $('#goPage').val();
        $.pjax({
            url: "<?= \yii\helpers\Url::to(['pages/load-page-data']) ?>",
            container: '#data-grid-' + pageId,
            type: 'GET',
            data: {
                pageId,
                page,
                search,
                pageSize,
            },
            push: false,
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 5000,
        });
    }

    // Import Excel Button Click
    $(document).off('click', 'import-data-btn').on('click', '#import-data-btn', function() {
        $('#importExelModal').modal('show');
    });

    // Handle Import Excel Form Submission
    $(document).off('submit', '#importExcelForm').on('submit', '#importExcelForm', function(event) {

        event.preventDefault();
        var formData = new FormData(this);
        var tableName = <?= json_encode($dataProvider->query->from[0]) ?>;
        formData.append('tableName', tableName);

        var loadingSpinner = $(` 
                <div class="loading-overlay">
                    <div class="loading-content">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span class="ml-2">Đang nhập dữ liệu, vui lòng đợi...</span>                    
                    </div>
                </div>
            `);
        $('body').append(loadingSpinner);

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['pages/import-excel']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                loadingSpinner.remove();

                if (response.success) {
                    var pageId = <?= json_encode($pageId) ?>;
                    loadData(pageId);
                    showToast('Nhập dữ liệu từ Excel thành công!');

                    $('#importExcelForm')[0].reset();
                    $('#importExelModal').modal('hide');
                } else if (response.duplicate) {
                    $('#confirmMessage').html(
                        `Ghi đè các mục hiện có trong cột <strong>[Khóa chính]</strong>. Bạn có muốn tiếp tục nhập không?<br><br>
                            ${response.message}`
                    );

                    $('#confirmModal').modal('show');

                    $('#confirmYesBtn').off('click').on('click', function() {
                        var newLoadingSpinner = $(` 
                                <div class="loading-overlay">
                                    <div class="loading-content">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <span class="ml-2">Đang nhập dữ liệu, vui lòng đợi...</span>                    
                                    </div>
                                </div>
                            `);
                        $('body').append(newLoadingSpinner);

                        formData.append('removeId', true);

                        $.ajax({
                            url: '<?= \yii\helpers\Url::to(['pages/import-excel']) ?>',
                            type: 'POST',
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                newLoadingSpinner.remove();

                                if (response.success) {
                                    var pageId = <?= json_encode($pageId) ?>;
                                    loadData(pageId);

                                    showToast(
                                        'Tệp Excel được nhập và ghi đè [PK]s thành công!'
                                    );

                                    // $('#importExcelForm')[0].reset();
                                    $('#importExelModal').modal('hide');

                                } else {
                                    newLoadingSpinner.remove();
                                    showModal('Error',
                                        'Không thể nhập tệp Excel: \n' +
                                        response.message);
                                }
                            }
                        });
                        $('#importStatusModal').modal('hide');
                        $('#confirmModal').modal('hide');
                    });
                } else {
                    loadingSpinner.remove();
                    showModal('Error', 'Không thể nhập tệp Excel: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();
                showModal('Error', 'Có lỗi xảy ra khi nhập tệp Excel:');
            }
        });
    });

    // Hàm hiển thị modal với thông điệp
    function showModal(title, message) {
        $('#importStatusModalLabel').text(title);

        $('#importStatusMessage').html(message.replace(/\n/g, '<br>'));

        $('#importStatusModal').modal('show');

        $('#importExelModal').modal('hide');
    }

    // Xử lý xuất view hiện tại
    $(document).off('click', '#exportCurrentViewButton').on('click', '#exportCurrentViewButton', function() {
        var tableName = <?= json_encode($dataProvider->query->from[0]) ?>;
        var visibleColumns = [];
        var tableData = [];

        // Lấy các cột hiển thị trong bảng (không bao gồm cột ẩn và cột có display: none)
        $('#data-grid thead th').each(function() {
            var columnName = $(this).data('column');
            if (!$(this).hasClass('hidden-column') && $(this).css('display') !== 'none') {
                visibleColumns.push(columnName);
            }
        });

        // Lấy dữ liệu bảng (các dòng hiển thị trong grid)
        $('#data-grid tbody tr').each(function() {
            var rowData = {};
            $(this).find('td').each(function() {
                // Lấy giá trị của cột theo data-column
                var columnName = $(this).data('column'); // Sử dụng data-column thay vì chỉ số
                if (visibleColumns.includes(columnName)) {
                    var cellValue = $(this).text().trim();
                    rowData[columnName] = cellValue;
                }
            });
            tableData.push(rowData);
        });
        console.log("🚀 ~ $ ~ visibleColumns:", visibleColumns);
        console.log("🚀 ~ $ ~ tableData:", tableData);
        // Hiển thị spinner khi đang xuất
        var loadingSpinner = $(`    
        <div class="loading-overlay">
            <div class="loading-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Exporting...</span>
                </div>
                <span class="ml-2">Đang xuất dữ liệu, vui lòng đợi...</span>
            </div>
        </div>
    `);
        $('body').append(loadingSpinner);

        // Gửi dữ liệu qua AJAX
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['pages/export-excel-current']) ?>',
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tableName: tableName,
                format: 'xlsx',
                visibleColumns: visibleColumns, // Các cột cần xuất
                tableData: tableData // Dữ liệu bảng (các dòng)
            },
            success: function(response) {
                loadingSpinner.remove();
                if (response.success) {
                    if (response.file_url) {
                        var link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = tableName + '.xlsx';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        alert('URL tệp bị thiếu trong phản hồi.');
                    }
                } else {
                    alert('Không xuất được Excel: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();
                alert('Đã xảy ra lỗi khi xuất Excel.');
            }
        });
    });


    // Xử lý xuất template (chỉ header columns)
    $(document).off('click', '#exportTemplateButton').on('click', '#exportTemplateButton', function() {
        // Lấy tên bảng từ PHP (ví dụ từ một biến PHP)
        var tableName = <?= json_encode($dataProvider->query->from[0]) ?>;

        var loadingSpinner = $(`
        <div class="loading-overlay">
            <div class="loading-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Exporting...</span>
                </div>
                <span class="ml-2">Đang xuất template (chỉ header), vui lòng đợi...</span>
            </div>
        </div>
    `);
        $('body').append(loadingSpinner);

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['pages/export-excel-header']) ?>', // Địa chỉ controller
            type: 'POST',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') // CSRF token nếu có
            },
            data: {
                tableName: tableName, // Chỉ gửi tên bảng
                format: 'xlsx' // Định dạng xuất Excel
            },
            success: function(response) {
                loadingSpinner.remove();
                if (response.success) {
                    if (response.file_url) {
                        var link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = tableName + '-template.xlsx'; // Tên file xuất
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        alert('URL tệp bị thiếu trong phản hồi.');
                    }
                } else {
                    alert('Không xuất được Excel: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();
                alert('Đã xảy ra lỗi khi xuất Excel.');
            }
        });
    });




    // Export Excel 
    $(document).off('click', '#exportExcelButton').on('click', '#exportExcelButton', function() {

        event.preventDefault();
        var exportFormat = 'xlsx';
        var tableName = <?= json_encode($dataProvider->query->from[0]) ?>;
        var loadingSpinner = $(`
             <div class="loading-overlay">
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Exporting...</span>
                    </div>
                    <span class="ml-2">Đang xuất dữ liệu, vui lòng đợi...</span>
                </div>
            </div>
        `);
        $('body').append(loadingSpinner);
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['pages/export-excel']) ?>',
            type: 'GET',
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                format: exportFormat,
                tableName: tableName,
            },
            success: function(response) {
                loadingSpinner.remove();

                if (response.success) {
                    if (response.file_url) {
                        var link = document.createElement('a');
                        link.href = response.file_url;
                        link.download = tableName + '.' + exportFormat;
                        document.body.appendChild(
                            link);
                        link.click();
                        document.body.removeChild(link);

                        $.ajax({
                            url: '<?= \yii\helpers\Url::to(['pages/delete-export-file']) ?>',
                            type: 'POST',
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                file_url: response.file_url,
                            },
                            success: function(deleteResponse) {
                                if (deleteResponse.success) {
                                    console.log('Đã xóa file tmp thành công.');
                                } else {
                                    console.error('Không xóa được tập tin.');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(
                                    'Đã xảy ra lỗi khi xóa file.');
                            }
                        });

                    } else {
                        alert('URL tệp bị thiếu trong phản hồi.');
                    }
                } else {
                    alert('Không xuất được Excel ' + response
                        .message);
                }

            },
            error: function(xhr, status, error) {
                loadingSpinner.remove();

                alert('Đã xảy ra lỗi khi xuất Excel.');
            }
        });
    });
</script>