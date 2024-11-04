<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'Table Tabs';


?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>
<div class="toast-container position-fixed top-0 end-0 p-3 toast-index toast-rtl">
    <div class="toast fade" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <small id="toast-timestamp"></small>
            <button class="btn-close" type="button" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body">Hello, I'm a web-designer.</div>
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
                        <h4>Richtext Tabs</h4>
                        <p class="mt-1 f-m-light">Create richtext tab</p>
                    </div>
                    <div class="card-body">
                        <form action="<?= \yii\helpers\Url::to(['create-richtext-tabs']) ?>" method="post"
                            id="richtextForm">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                            <div class="mb-3">
                                <label for="tab_name" class="form-label">Tab Name</label>
                                <input type="text" name="tab_name" class="form-control" id="tab_name" required>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success" id="saveRichtextButton">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include Yii::getAlias('@app/views/layouts/_footer.php'); ?>

<script>
document.getElementById('richtextForm').addEventListener('submit', function(event) {
    const richTextArea = document.querySelector('.richtext-area');
    contentInput.value = richTextArea.innerHTML;
});
</script>