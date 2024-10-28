<?php

use app\models\User;

$isAdmin = User::isUserAdmin(Yii::$app->user->identity->username);

$tabId = $_GET['tab_id'];

?>

<div class="d-flex mb-3">
    <div class="me-auto">
        <!-- Three-dot button with dropdown -->
        <div class="btn-group-ellipsis">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-ellipsis"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete
                        Tab</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#hideModal">Show/Hidden
                        Tab</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#sortModal">Sort Order
                        Tab</a></li>
            </ul>
        </div>

        <!-- Modal Confirm Delete -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm remove tab</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this tab? This action cannot be undone.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirm-delete-btn"
                            data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete</button>
                        <?php if ($isAdmin): ?>
                        <button type="button" class="btn btn-danger" id="confirm-delete-permanently-btn"
                            data-tab-id="<?= htmlspecialchars($tabId) ?>">Delete permanently</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="m-0">
        <div class="btn btn-outline-secondary">
            <span class="fw-medium"><?= $richtextTab->tab_name ?></span> | <span class="fw-bold">Richtext</span>
        </div>
    </div>
</div>

<div class="form-group">
    <textarea id="editor" name="content" class="form-control" rows="10"><?= $content ?></textarea>
</div>
<div class="d-flex justify-content-between mb-3">
    <a href="<?= \yii\helpers\Url::to(['tabs/download', 'tab_id' => $tabId]) ?>" class="btn btn-primary"
        target="_blank">
        Download .txt
    </a>
    <button type="button" class="btn btn-success" id="save-button">Save</button>
</div>



<script>
document.getElementById('save-button').addEventListener('click', function() {
    var content = document.getElementById('editor').value;

    $.ajax({
        url: "<?= \yii\helpers\Url::to(['tabs/save-richtext']) ?>", // Thay 'your-controller' bằng tên controller của bạn
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