<?php

use app\assets\AppAsset;
use app\assets\RichtextAsset;
use yii\helpers\Html;
use app\assets\Select2Asset;
use app\models\BaseModel;

/** @var yii\web\View $this */

Select2Asset::register($this);
RichtextAsset::register($this);

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
                    <input type="text" name="pageName" class="form-control" id="pageName"
                        value="<?= Html::encode($pageName ?? '') ?>" autocomplete="off">
                    <span id="pageNameError" class="text-danger"></span>
                </div>

                <!-- Chọn loại page -->
                <div class="col-12 col-md-6 col-lg-3 col-xl-2 mb-3">
                    <label for="type" class="form-label">Loại Page</label>
                    <select name="type" id="type" class="form-select" onchange="toggleTabInputs()">
                        <option value="richtext" selected>Rich Text</option>
                        <option value="table_new">Table Mới</option>
                        <option value="table_selected">Table Sẵn Có</option>
                    </select>
                </div>

                <!-- Tên Bảng Có Sẵn (Chỉ hiển thị khi chọn "Chọn Table Sẵn Có") -->
                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3" id="tableSelectedInput" style="display: none;">
                    <label for="tableName" class="form-label">Tên Bảng</label>

                    <?php if (!empty($validTableNames)): ?>
                        <select name="tableName" class="form-select">
                            <?php foreach ($validTableNames as $tableName): ?>
                                <option value="<?= $tableName ?>"><?= $tableName ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <div class="form-control" style="height: calc(2.25rem + 2px); text-align: center;">
                            Không có bảng hợp lệ.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tên Bảng Mới(Chỉ hiển thị khi chọn "Chọn Table Mới") -->
                <div class="col-12 col-md-6 col-lg-3 col-xl-3 mb-3" id="tableNameInput" style="display: none;">
                    <label for="tableName" class="form-label">Tên Bảng</label>
                    <input type="text" name="tableName" class="form-control" id="tableName"
                        value="<?= Html::encode($tableName ?? '') ?>" autocomplete="off">
                    <span id="tableNameError" class="text-danger"></span>
                </div>

                <!-- Input cho Rich Text -->
                <div id="richTextInputs">
                    <div class="form-group my-1" id="edit-content">
                        <textarea name="content" id="richtext-editor"></textarea>
                    </div>
                </div>

                <!-- Input cho Table -->
                <div id="tableInputs" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Loại</th>
                                    <th class="text-center">Not_Null</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="columnsContainer">
                                <!-- Các cột sẽ được thêm vào đây khi người dùng chọn tạo bảng mới -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary my-3" onclick="addColumn()">+ Thêm cột</button>
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