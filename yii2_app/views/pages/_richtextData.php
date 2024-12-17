<?php

use app\assets\RichtextAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */

RichtextAsset::register($this);
$this->registerJsFile('@web/js/components/frontend/richtextPage.js', [
    'depends' => [\yii\web\JqueryAsset::class],
    'position' => \yii\web\View::POS_END,
]);
?>
<div class="page-content my-2" style="min-height: 76vh;">
    <div id="content-display"><?= $content ?></div>
    <div id="edit-content" style="display: none;">
        <div id="richtext-editor" name="richtext-editor"><?= Html::encode($content) ?></div>
    </div>
</div>

<div class="d-flex justify-content-end my-2">
    <button type="button" class="btn btn-warning edit-richtext me-4" id="edit-button" title="Chỉnh sửa nội dung">
        <i class="fa fa-edit me-1"></i>
    </button>
</div>

<!-- Modal xác nhận -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Xác nhận lưu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Bạn có chắc chắn muốn lưu các thay đổi?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmSave">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal xác nhận thoát -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Xác nhận thoát</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn thoát? Nội dung thay đổi sẽ không được lưu.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" id="confirmCancel" class="btn btn-danger">Xác nhận</button>
            </div>
        </div>
    </div>
</div>


<script>
var pageId = <?= $page->id ?>;
var save_richtext_url = "<?= Url::to(['pages/save-rich-text']) ?>";
</script>