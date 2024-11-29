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

<!-- DỮ LIỆU BẢNG -->
<div id="tableData">
    <div class="d-flex flex-wrap justify-content-between mt-3">
        <div class="d-md-flex d-sm-block">
            <button class="btn btn-primary mb-2 me-2" id="add-data-btn" href="#" data-bs-toggle="modal"
                    data-bs-target="#addDataModal">
                <i class="fa-solid fa-plus"></i> Nhập Mới
            </button>

            <button class="btn btn-danger mb-2 me-2" id="delete-selected-btn">
                <i class="fa-regular fa-trash-can"></i> Xóa Đã Chọn
            </button>
            <!-- Nút Nhập Excel -->
            <button class="btn btn-info mb-2 me-2" id="import-data-btn" href="#" data-bs-toggle="modal"
                    data-bs-target="#importExelModal">
                <i class="fa-solid fa-download"></i> Nhập Excel
            </button>

            <!-- Nút Xuất Excel -->
            <button class="btn btn-warning mb-2 me-auto" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fa-solid fa-download"></i> Xuất Dữ Liệu
            </button>

        </div>
    </div>


    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns,
    ]);
    ?>
    <?php Pjax::end(); ?>

    <div class="d-flex flex-column flex-md-row align-items-center my-3">
        <!-- Number of items per page -->
        <div class="number-of-items d-flex align-items-center mb-2 mb-md-0">
            <span class="me-2">Xem:</span>
            <?php
                $pageSizes = [10 => 10, 25 => 25, 50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000];
                Html::dropDownList('pageSize',$pageSize, $pageSizes, [
                    'class' => 'form-select form-select-sm autosubmit',
                    'id' => 'pageSize',
                    'style' => ['width' => '5rem']]
                );
            ?>
        </div>

        <!-- Nút Tùy chỉnh cột -->
        <div class="btn-group">
            <button class="btn btn-primary btn-sm mx-2 dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    data-popper-placement="top-start" aria-expanded="false"><i class="fa-solid fa-border-all"></i> Tùy
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



    <!-- Modal Sửa dữ liệu-->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Sửa dữ liệu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm"></form> <!-- Để trống và sẽ được điền động -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            aria-label="Cancel">Hủy</button>
                    <button type="button" class="btn btn-primary" id="save-row-btn">Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Dữ Liệu -->
    <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addDataModalLabel">Nhập dữ liệu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php foreach ($columns as $column): ?>
                        <?php if (isset($columns[$column->name]) && !$columns[$column->name]->isPrimaryKey): ?>
                            <div class="form-group">
                                <label
                                    for="<?= htmlspecialchars($column->name) ?>"><?= htmlspecialchars($column->name) ?>:</label>
                                <input type="text" class="form-control new-data-input"
                                       data-column="<?= htmlspecialchars($column->name) ?>"
                                       id="<?= htmlspecialchars($column->name) ?>"
                                       placeholder="<?= htmlspecialchars($column->name) ?>">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" id="add-row-btn" class="btn btn-primary">Thêm</button>
                </div>
            </div>
        </div>
    </div>
</div>