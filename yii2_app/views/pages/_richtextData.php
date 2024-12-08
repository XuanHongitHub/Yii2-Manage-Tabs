<?php

use app\assets\AppAsset;
use app\assets\RichtextAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */

RichtextAsset::register($this);

?>

<div class="page-content">
    <div class="d-flex flex-wrap justify-content-end align-items-center me-3 my-1">
        <button class="btn btn-secondary me-2" id="cancel-edit-button" style="display:none;">Hủy</button>
        <button class="btn btn-warning" id="edit-button">
            <i class="fa fa-edit me-1"></i> Sửa
        </button>
        <button class="btn btn-success" id="save-button" style="display:none;">
            <i class="fa fa-save me-1"></i> Lưu
        </button>
    </div>
    <div class="form-group my-1" id="view-content">
        <div id="content-display"><?= $content ?></div>
    </div>

    <div class="form-group my-1" id="edit-content" style="display:none;">
        <textarea id="richtext-editor"><?= Html::encode($content) ?></textarea>
    </div>
</div>

<!-- Modal xác nhận -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="confirmationModalLabel">Xác nhận lưu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn lưu các thay đổi?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmSave">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
    var pageId = <?= $page->id ?>;
    var save_richtext_url = "<?= Url::to(['pages/save-rich-text']) ?>";
</script>