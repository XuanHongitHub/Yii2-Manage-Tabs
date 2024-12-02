<?php

use yii\widgets\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */

/* @var $this yii\web\View */
/* @var $data array */
/* @var $columns array */
/* @var $pagination yii\data\Pagination */
/* @var $sort string */
/* @var $sortDirection int */
// $pageId = $_GET['pageId'];

var_dump($dataProvider->query->from[0]);
$menuId = $_GET['menuId'];

var_dump($menuId);

$this->title = $dataProvider->query->from[0];
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
                                echo Html::beginForm(['/pages', 'menuId' => $menuId], 'get', [
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
                                <?= Html::submitButton('Tìm', ['class' => 'btn btn-primary mb-2']) ?>


                                <?= Html::endForm(); ?>
                            </div>
                        </div>

                        <?php
                        Pjax::begin([
                            'id' => 'data-grid',
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
                                        'name' => 'id',
                                        'headerOptions' => ['style' => 'text-align:center; width: 3%;'],
                                        'contentOptions' => ['style' => 'text-align:center;'],
                                        'checkboxOptions' => function ($data, $key, $index, $column) {
                                            return ['value' => $data['id'], 'data-id' => $data['id'], 'class' => 'checkbox-row'];
                                        }
                                    ],
                                ],
                                array_map(function ($column, $index) {
                                    return [
                                        'attribute' => $column,
                                        'contentOptions' => [
                                            'class' => $index === 0 ? 'hidden-column' : ''  // Ẩn cột đầu tiên
                                        ],
                                        'headerOptions' => [
                                            'class' => $index === 0 ? 'sortable-column hidden-column' : 'sortable-column',
                                            'style' => 'cursor:pointer;',
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
                                                    'data-id' => $data['id'], // Dùng $data['id'] để lấy id thực tế
                                                ]);
                                            },
                                        ],
                                    ],
                                ]
                            ),
                            'tableOptions' => ['class' => 'table table-bordered table-hover table-responsive'],
                            'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-3'>{pager}\n{summary}</div>",
                            'pager' => [
                                'class' => 'yii\widgets\LinkPager', // Đặt class cho LinkPager
                                'options' => ['class' => 'pagination justify-content-end align-items-center'], // Lớp CSS cho phân trang
                                'linkContainerOptions' => ['tag' => 'span'],
                                'linkOptions' => [
                                    'class' => 'paginate_button',
                                ],
                                'activePageCssClass' => 'current',
                                'disabledPageCssClass' => 'disabled',
                                'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'paginate_button'],
                                'prevPageLabel' => 'Tiếp', // Nhãn cho nút Previous
                                'nextPageLabel' => 'Trước', // Nhãn cho nút Next
                                'maxButtonCount' => 5, // Số lượng nút phân trang tối đa hiển thị
                            ],
                            'summary' => '<span class="text-muted">Hiển thị <b>{begin}-{end}</b> trên tổng số <b>{totalCount}</b> dòng.</span>',
                        ]);

                        // Kết thúc Pjax
                        Pjax::end();

                        ?>

                        <div class="d-flex flex-column flex-md-row align-items-center my-3">
                            <!-- Number of items per page -->
                            <div class="number-of-items d-flex align-items-center mb-2 mb-md-0">
                                <span class="me-2">Xem:</span>
                                <?php
                                $pageSizes = [10 => 10, 25 => 25, 50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000];
                                Html::dropDownList(
                                    'pageSize',
                                    $pageSize,
                                    $pageSizes,
                                    [
                                        'class' => 'form-select form-select-sm autosubmit',
                                        'id' => 'pageSize',
                                        'style' => ['width' => '5rem']
                                    ]
                                );
                                ?>
                            </div>

                            <!-- Nút Tùy chỉnh cột -->
                            <div class="btn-group">
                                <button class="btn btn-primary btn-sm mx-2 dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" data-popper-placement="top-start" aria-expanded="false"><i
                                        class="fa-solid fa-border-all"></i> Tùy
                                    Chỉnh</button>
                                <ul class="dropdown-menu border dropdown-block">
                                    <table class="table table-borderless" id="columns-visibility">
                                        <?php foreach ($columns as $column): ?>
                                            <?php if (isset($columns[$column->name]) && $columns[$column->name]->isPrimaryKey): ?>
                                                <!-- Nếu cột là khóa chính, ẩn checkbox -->
                                                <tr class="border" style="display:none;">
                                                    <td class="d-flex justify-content-between align-items-center">
                                                        <span data-checkbox-column="<?= htmlspecialchars($column->name) ?>">
                                                            <?= htmlspecialchars($column->name) ?>
                                                        </span>
                                                        <input class="form-check-input column-checkbox" type="checkbox" checked
                                                            id="checkbox-<?= htmlspecialchars($column->name) ?>"
                                                            data-column="<?= htmlspecialchars($column->name) ?>" disabled>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <tr class="border">
                                                    <td class="d-flex justify-content-between align-items-center">
                                                        <span data-checkbox-column="<?= htmlspecialchars($column->name) ?>">
                                                            <?= htmlspecialchars($column->name) ?>
                                                        </span>
                                                        <input class="form-check-input column-checkbox" type="checkbox" checked
                                                            id="checkbox-<?= htmlspecialchars($column->name) ?>"
                                                            data-column="<?= htmlspecialchars($column->name) ?>">
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
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
        console.log('Script loaded'); // Kiểm tra xem script có được nạp không

        // Ngăn submit form khi nhấn nút Tìm kiếm

        console.log('Pjax success');
        $(document).on('submit', '#search-form', function(e) {
            e.preventDefault(); // Ngăn chặn submit mặc định của form
            console.log('Pjax Search');

            var form = $(this);

            // Tạo loading spinner
            var loadingSpinner = $(`
                <div class="spinner-fixed">
                    <i class="fa fa-spin fa-spinner me-2"></i>
                </div>
            `);

            // Gắn spinner vào body
            $('body').append(loadingSpinner);

            // Gửi dữ liệu của form qua PJAX
            $.pjax({
                url: form.attr('action'), // URL của form
                container: '#data-grid', // Phần tử sẽ được cập nhật
                type: 'GET', // Phương thức GET
                data: form.serialize(), // Dữ liệu form
                push: false, // Không thay đổi URL của trang
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') // CSRF Token
                },
                timeout: 5000, // Timeout cho PJAX
            });
        });


        $(document).on('click', '#add-row-btn', function(e) {
            e.preventDefault();

            var formData = $('#add-data-form').serialize();
            formData +=
                '&tableName=<?= $dataProvider->query->from[0] ?>'; // Gửi tên bảng để xử lý thêm dữ liệu

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
                        $.pjax.reload({ // Cập nhật lại dữ liệu bảng
                            container: '#data-grid', // Đảm bảo #data-grid là container cần tải lại
                            timeout: 5000 // Timeout cho PJAX để xử lý nhanh hơn
                        });
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

        // Mở modal sửa và điền dữ liệu vào các trường
        $(document).on('click', '.btn-edit', function() {
            // Lấy dữ liệu dòng từ thuộc tính data-row (đã được mã hóa JSON)
            var rowData = $(this).data('row');

            // Duyệt qua các cột và cập nhật giá trị cho các trường trong modal
            $.each(rowData, function(key, value) {
                // Kiểm tra nếu có trường input trong modal tương ứng với tên cột
                var inputField = $('#edit-' + key);
                if (inputField.length) {
                    inputField.val(value); // Gán giá trị cột vào trường input
                }
            });

            // Hiển thị modal
            $('#editModal').modal('show');
        });

        // Lưu thay đổi dữ liệu
        $(document).on('click', '#save-row-btn', function(e) {
            e.preventDefault();

            var formData = $('#edit-form').serialize();
            formData += '&tableName=<?= $dataProvider->query->from[0] ?>'; // Gửi tên bảng
            console.log("🚀 ~ $ ~ formData:", formData);
            $.ajax({
                url: "<?= \yii\helpers\Url::to(['pages/update-data']) ?>", // Đường dẫn xử lý sửa dữ liệu
                type: "POST",
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                        'content') // CSRF Token
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#edit-form')[0].reset(); // Reset form
                        $('#editModal').modal('hide'); // Đóng modal
                        showToast('Cập nhật dữ liệu thành công!');
                        $.pjax.reload({ // Cập nhật lại dữ liệu bảng
                            container: '#data-grid',
                            timeout: 5000
                        });
                    } else {
                        alert('Có lỗi xảy ra: ' + response
                            .message); // Thông báo lỗi
                    }
                },
                error: function() {
                    alert(
                        'Không thể cập nhật dữ liệu. Vui lòng thử lại.'
                    ); // Thông báo lỗi
                }
            });
        });

        // Xóa một bản ghi khi nhấn nút xóa
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();

            var rowId = $(this).data('id'); // Lấy ID của dòng cần xóa

            if (confirm('Bạn có chắc chắn muốn xóa dòng này?')) {
                $.ajax({
                    url: "<?= \yii\helpers\Url::to(['pages/delete-data']) ?>", // Đường dẫn xử lý xóa dữ liệu
                    type: "POST",
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr(
                            'content') // CSRF Token
                    },
                    data: {
                        id: rowId, // Truyền ID dòng cần xóa
                        tableName: '<?= $dataProvider->query->from[0] ?>',
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Xóa dữ liệu thành công!');
                            $.pjax.reload({ // Cập nhật lại dữ liệu bảng
                                container: '#data-grid',
                                timeout: 5000 // Timeout cho PJAX để xử lý nhanh hơn
                            });
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
        $(document).on('click', '#delete-selected-btn', function(e) {
            e.preventDefault();

            // Lấy tất cả các ID của các dòng được chọn
            var selectedIds = [];
            $('.checkbox-row:checked').each(function() {
                selectedIds.push($(this).data('id')); // Lấy id của dòng đã chọn
            });

            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một dòng để xóa.');
                return;
            }

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
                            $.pjax.reload({ // Cập nhật lại dữ liệu bảng
                                container: '#data-grid',
                                timeout: 5000 // Timeout cho PJAX để xử lý nhanh hơn
                            });
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
    });
</script>