<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use app\assets\Select2Asset;
use app\models\BaseModel;

/** @var yii\web\View $this */

Select2Asset::register($this);
$this->title = 'Thêm mới Page';

$this->registerJsFile('@web/js/components/admin/createPage.js', ['depends' => [AppAsset::class]]);

?>


<div class="card">
    <div class="card-header card-no-border pb-0">
        <h4>Thêm Mới Page</h4>
        <p class="mt-1 f-m-light">Thêm Table Page | Richtext Page</p>
    </div>
    <div class="card-body">
        <form action="<?= \yii\helpers\Url::to(['store']) ?>" method="post">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

            <div class="row">
                <!-- Tên Page -->
                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3">
                    <label for="pageName" class="form-label">Tên Page</label>
                    <input type="text" name="pageName" class="form-control" id="pageName">
                    <span id="pageNameError" class="text-danger"></span>
                </div>

                <!-- Chọn loại page -->
                <div class="col-12 col-md-6 col-lg-3 col-xl-2 mb-3">
                    <label for="type" class="form-label">Loại Page</label>
                    <select name="type" id="type" class="form-select" onchange="toggleTabInputs()">
                        <option value="richtext">Rich Text</option>
                        <option value="table">Table</option>
                    </select>
                </div>

                <!-- TABLE NAME -->
                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3" id="tableNameInput">
                    <label for="tableName" class="form-label">Tên Bảng</label>
                    <input type="text" name="tableName" class="form-control" id="tableName">
                    <span id="tableNameError" class="text-danger"></span>
                </div>

                <!-- Phần input cho loại page "Table" -->
                <div id="tableInputs" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Loại</th>
                                    <!-- <th>Độ Dài</th> -->
                                    <th class="text-center">Not_Null</th>
                                    <!-- <th class="text-center">A_I</th> -->
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="columnsContainer">
                                <!-- Row đầu tiên mặc định ẩn và có dữ liệu sẵn -->
                                <tr class="default-row">
                                    <td>
                                        <input type="text" name="columns[]" class="form-control" id="column-name-0"
                                            value="<?= BaseModel::HIDDEN_ID_KEY ?>" placeholder="Column Name" readonly>
                                        <div class="text-danger column-error" id="column-name-error-0">
                                        </div>
                                    </td>
                                    <td>
                                        <select name="data_types[]" class="form-select" id="data-type-0">
                                            <option value="SERIAL" selected>SERIAL</option>
                                            <option value="TEXT">TEXT</option>
                                        </select>
                                        <div class="text-danger data-type-error" id="data-type-error-0">
                                        </div>
                                    </td>
                                    <!-- <td>
                                                        <input type="number" name="data_sizes[]" class="form-control"
                                                            id="data-size-0" placeholder="Length" value="" readonly>
                                                        <div class="text-danger data-size-error" id="data-size-error-0">
                                                        </div>
                                                    </td> -->
                                    <td class="text-center">
                                        <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input"
                                            id="is-not-null-0" checked>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="is_primary[]" value="1" class="form-check-input"
                                            id="is-primary-0" checked>
                                        <div class="text-danger primary-error" id="primary-error-0">
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" name="columns[]" class="form-control" id="column-name-1"
                                            value="" placeholder="Column Name">
                                        <div class="text-danger column-error" id="column-name-error-1">
                                        </div>
                                    </td>
                                    <td>
                                        <select name="data_types[]" class="form-select" id="data-type-1">
                                            <option value="TEXT" selected>TEXT</option>
                                            <option value="INT">NUMBER</option>
                                        </select>
                                        <div class="text-danger data-type-error" id="data-type-error-1">
                                        </div>
                                    </td>
                                    <!-- <td>
                                                        <input type="number" name="data_sizes[]" class="form-control"
                                                            id="data-size-1" placeholder="Length" value="">
                                                        <div class="text-danger data-size-error" id="data-size-error-1">
                                                        </div>
                                                    </td> -->
                                    <td class="text-center">
                                        <input type="checkbox" name="is_not_null[]" value="1" class="form-check-input"
                                            id="is-not-null-1">
                                    </td>
                                    <!-- <td class="text-center">
                                                        <input type="checkbox" name="is_primary[]" value="1"
                                                            class="form-check-input" id="is-primary-1">
                                                        <div class="text-danger primary-error" id="primary-error-1">
                                                        </div>
                                                    </td> -->
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary my-3" onclick="addColumn()">+
                        Thêm cột</button>
                </div>

                <!-- Nút tạo -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-success" id="btn-store-page">Thêm</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
var check_exist_url = "<?= \yii\helpers\Url::to(['pages/check-name-existence']) ?>";
</script>