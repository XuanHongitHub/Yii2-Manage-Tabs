<?php
/** @var yii\web\View $this */
/** @var app\models\RichtextTab $richtextTab */

$this->title = 'Chỉnh Sửa Nội Dung Richtext';
?>

<div class="content-body mt-5">
    <h5>Chỉnh Sửa Nội Dung</h5>
    <form action="<?= \yii\helpers\Url::to(['table-tabs/edit-richtext', 'id' => $richtextTab->id]) ?>" method="post">
        <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <div class="form-group">
            <label for="content">Nội Dung:</label>
            <textarea id="content" name="content" class="form-control"
                rows="10"><?= htmlspecialchars($richtextTab->content) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Lưu Thay Đổi</button>
    </form>
</div>