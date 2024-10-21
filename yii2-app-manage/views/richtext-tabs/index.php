<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\RichtextTab[] $richtextTabs */
/** @var int $tabId */

$this->title = 'Quản Lý Nội Dung Richtext';
?>

<div class="richtext-tab-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Thêm Nội Dung', ['create-richtext-tab', 'tabId' => $tabId], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nội Dung</th>
                    <th scope="col">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($richtextTabs)): ?>
                <?php foreach ($richtextTabs as $richtextTab): ?>
                <tr>
                    <td><?= $richtextTab->id ?></td>
                    <td><?= htmlspecialchars($richtextTab->content) ?></td>
                    <td>
                        <?= Html::a('Sửa', ['edit-richtext', 'id' => $richtextTab->id], ['class' => 'btn btn-warning btn-sm']) ?>
                        <button class="btn btn-danger btn-sm"
                            onclick="deleteRichtextTab(<?= $richtextTab->id ?>)">Xóa</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Chưa có nội dung nào.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteRichtextTab(id) {
    if (confirm('Bạn có chắc chắn muốn xóa nội dung này không?')) {
        $.ajax({
            url: '<?= Url::to(['table-tabs/delete-richtext']) ?>/' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    }
}
</script>