<?php
/** @var yii\web\View $this */
/** @var int $tabId */

$this->title = 'Thêm Nội Dung Richtext';
?>

<div class="content-body mt-5">
    <h5>Thêm Nội Dung Cho Tab ID: <?= $tabId ?></h5>
    <form action="<?= \yii\helpers\Url::to(['table-tabs/create-richtext', 'tabId' => $tabId]) ?>" method="post">
        <?= \yii\helpers\Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <div class="form-group">
            <label for="content">Nội Dung:</label>
            <textarea id="content" name="content" class="form-control" rows="10"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Lưu Nội Dung</button>
    </form>
</div>