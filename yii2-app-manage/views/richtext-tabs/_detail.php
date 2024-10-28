<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Tab $tabModel */
/** @var array $columns */

$this->title = 'View Richtext Details: ' . $richtextTab->tab_name;

$tabId = $_GET['id'];

?>
<div class="content-body mt-2">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex">
                <div class="ms-auto">
                    <div class="dropdown dropstart my-2">
                        <a class="btn btn-secondary" href="<?= \yii\helpers\Url::to(['tabs/settings']) ?>"
                            style="color: white; text-decoration: none;">
                            <i class="fa-solid fa-gear"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex mb-3">
                        <div class="m-0">
                            <h5><?= Html::encode($richtextTab->tab_name) ?></h5>
                        </div>
                        <div class="ms-auto">
                            <a href="<?= \yii\helpers\Url::to(['richtext-tabs/index']) ?>"
                                class="btn btn-success">Richtext
                                List</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <textarea id="editor" name="content" class="form-control" rows="10"><?= $content ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <a href="<?= \yii\helpers\Url::to(['tabs/download', 'tab_id' => $tabId]) ?>"
                            class="btn btn-primary ms-2" target="_blank">
                            Download .txt
                        </a>
                        <button type="button" class="btn btn-success" id="save-button">Lưu</button>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.getElementById('save-button').addEventListener('click', function() {
    var content = document.getElementById('editor').value;

    $.ajax({
        url: "<?= \yii\helpers\Url::to(['tabs/save-richtext']) ?>",
        type: "POST",
        data: {
            tab_id: <?= $tabId ?>,
            content: content
        },
        success: function(response) {
            alert('Nội dung đã được lưu thành công!');
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi lưu nội dung. Vui lòng thử lại sau.');
        }
    });
});
</script>