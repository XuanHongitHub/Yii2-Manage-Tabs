<?php

use app\assets\AppAsset;
use app\assets\RichtextAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */

RichtextAsset::register($this);

$this->registerJsFile('js/components/frontend/richtextPage.js', ['depends' => AppAsset::class]);
$this->registerJsFile('js/libs/rte.js', ['depends' => AppAsset::class]);
$this->registerJsFile('js/libs/rte_all_plugins.js', ['depends' => AppAsset::class]);
?>
<div class="form-group my-1" id="view-content">
    <!-- Hiển thị nội dung ban đầu -->
    <div id="content-display"><?= $content ?></div>
</div>

<!-- Form chỉnh sửa nội dung (ẩn khi không chỉnh sửa) -->
<div class="form-group my-1" id="edit-content" style="display:none;">
    <textarea id="richtext-editor"><?= Html::encode($content) ?></textarea>
</div>

<div class="d-flex justify-content-end my-2">
    <button type="button" class="btn btn-secondary me-2" id="cancel-edit-button" style="display: none;">
        <i class="fa fa-times me-1"></i> Hủy
    </button>
    <button type="button" class="btn btn-warning me-4" id="save-button" data-page-id="<?= $pageId ?>">
        <i class="fa fa-edit me-1"></i> Sửa
    </button>
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