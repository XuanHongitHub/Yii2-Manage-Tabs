<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'Create Group';

?>
<?php include Yii::getAlias('@app/views/layouts/_icon.php'); ?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar-settings.php'); ?>


<div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <small id="toast-timestamp"></small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">Msg</div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a success message
    const successMessage = "<?= Yii::$app->session->getFlash('success') ?>";
    const errorMessage = "<?= Yii::$app->session->getFlash('error') ?>";

    if (successMessage) {
        document.getElementById('toast-body').textContent = successMessage;
        document.getElementById('toast-timestamp').textContent = new Date().toLocaleTimeString();
        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }

    if (errorMessage) {
        document.getElementById('toast-body').textContent = errorMessage;
        document.getElementById('toast-timestamp').textContent = new Date().toLocaleTimeString();
        const toastElement = document.getElementById('liveToast');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
});
</script>

<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <!-- You can add page title or breadcrumbs here -->
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <h4>Thêm nhóm tab</h4>
                        <p class="mt-1 f-m-light">Nhóm tab chứa các tab con</p>
                    </div>
                    <div class="card-body">
                        <form action="<?= \yii\helpers\Url::to(['settings/create-group']) ?>" method="post">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                                    <label for="name" class="form-label">Tên nhóm</label>
                                    <input type="text" name="name" class="form-control" id="name" value="">
                                </div>
                                <div class="col-12 col-md-6 col-lg-2 col-xl-2 mb-3">
                                    <label for="group_type" class="form-label">Chọn nhóm loại</label>
                                    <select id="group_type" name="group_type" class="form-select">
                                        <option value="tab_group" selected>Menu chứa nhiều Tab</option>
                                        <option value="menu_group">Menu chứa Menu con</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                                    <label for="icon-select" id="icon" class="form-label">Chọn icon</label>
                                    <select id="icon-select" name="icon" class="form-select">
                                        <?php foreach ($iconOptions as $iconValue => $iconLabel): ?>
                                        <option value="<?= Html::encode($iconValue) ?>"><?= Html::encode($iconLabel) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-2 d-flex align-items-center ms-3" id="icon-display">
                                    <svg class="stroke-icon" width="24" height="24">
                                        <use
                                            href="<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#<?= reset(array_keys($iconOptions)) ?>">
                                        </use>
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script>
document.getElementById('icon-select').addEventListener('change', function() {
    const selectedIcon = this.value;
    const iconDisplay = document.getElementById('icon-display').querySelector('use');
    iconDisplay.setAttribute('href', `<?= Yii::getAlias('@web') ?>/images/icon-sprite.svg#${selectedIcon}`);
});
</script>