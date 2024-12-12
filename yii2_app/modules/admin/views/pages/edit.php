<?php

use app\assets\AppAsset;
use app\assets\RichtextAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Page $page */
/** @var string $content */

RichtextAsset::register($this);
$this->registerJsFile('js/components/admin/editPage.js', ['depends' => AppAsset::class]);

$this->title = $page->name;
?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex flex-column flex-md-row align-items-md-center">
            <div class="me-auto mb-md-0 text-center text-md-start">
            </div>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="page-content">
            <div class="form-group my-1" id="edit-content">
                <textarea id="richtext-editor" name="richtext-editor"><?= Html::encode($content) ?></textarea>
            </div>
        </div>

        <!-- Modal xác nhận -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="confirmationModalLabel">Xác nhận lưu</h4>
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
    </div>
</div>

<script>
var urlParams = new URLSearchParams(window.location.search);
var id = urlParams.get('id');
var save_richtext_url = "<?= Url::to(['pages/save-rich-text']) ?>";
var list_page_url = "<?= Url::to(['pages/index']) ?>";
</script>