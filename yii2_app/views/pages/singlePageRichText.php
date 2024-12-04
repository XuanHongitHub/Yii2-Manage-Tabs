<?php

use app\assets\RichtextAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */

RichtextAsset::register($this);

$this->title = $page->name;
?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-auto mb-3 mb-md-0 text-center text-md-start">
                <h4><?= Html::encode($this->title) ?></h4>
                <p class="mt-1 f-m-light"></p>
            </div>
            <div class="d-flex flex-wrap justify-content-center align-items-center me-md-2 mb-3 mb-md-0">
                <button class="btn btn-secondary me-2" id="cancel-edit-button" style="display:none;">Hủy</button>
                <button class="btn btn-warning" id="edit-button">
                    <i class="fa fa-edit me-1"></i> Sửa
                </button>
                <button class="btn btn-success" id="save-button" style="display:none;">
                    <i class="fa fa-save me-1"></i> Lưu
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="page-content">
            <div class="form-group my-1" id="view-content">
                <!-- Hiển thị nội dung ban đầu -->
                <div id="content-display"><?= $content ?></div>
            </div>

            <!-- Form chỉnh sửa nội dung (ẩn khi không chỉnh sửa) -->
            <div class="form-group my-1" id="edit-content" style="display:none;">
                <textarea id="richtext-editor"><?= Html::encode($content) ?></textarea>
            </div>
        </div>
    </div>
</div>

<script>
    var pageId = <?= $pageId ?>;
    var save_richtext_url = "<?= Url::to(['pages/save-rich-text']) ?>";
</script>