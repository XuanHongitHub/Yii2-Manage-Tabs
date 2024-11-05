<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */
$tableCreationData = Yii::$app->session->getFlash('tableCreationData', []);

$this->title = 'List Tabs';

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
                        <div class="d-flex">
                            <div class="me-auto">
                                <h4>List Tabs</h4>
                                <p class="mt-1 f-m-light">Table Tab | Richtext Tab</p>
                            </div>
                            <div class="text-end">
                                <a class="btn btn-success"
                                    href="<?= \yii\helpers\Url::to(['settings/create']) ?>">Create Tab</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="display border table-bordered dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tab Name</th>
                                        <th class="text-center">Tab Type</th>
                                        <th class="text-center">Deleted</th>
                                        <th class="text-center">Status</th>
                                        <th>Position</th>
                                        <th>Created At</th>
                                        <th style="width: 8%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="columnsContainer">
                                    <?php foreach ($tabs as $tab): ?>
                                    <tr>
                                        <td><?= Html::encode($tab->id) ?></td>
                                        <td><?= Html::encode($tab->tab_name) ?></td>
                                        <td class="text-center">
                                            <?php if ($tab->tab_type == 'table'): ?>
                                            <span class="badge badge-light-primary">Table</span>
                                            <?php else: ?>
                                            <span class="badge badge-light-danger">Richtext</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $tab->deleted == 1 ? '<span class="badge badge-danger">Deleted</span>' : '<span class="badge badge-success">No</span>' ?>
                                        </td>
                                        </td>
                                        <td class="text-center">
                                            <?= $tab->deleted == 2 ?
                                                    '<span class="badge badge-warning">Hide</span>' : '<span class="badge badge-success">Show</span>'
                                                    ?>
                                        </td>
                                        <td><?= Html::encode($tab->position) ?></td>
                                        <td><?= Html::encode(Yii::$app->formatter->asDate($tab->created_at)) ?></td>
                                        <td style="white-space: nowrap">
                                            <button class="btn btn-success btn-sm save-row-btn"><i
                                                    class="fa-solid fa-pen-to-square"></i></button>
                                            <button class="btn btn-danger btn-sm"><i
                                                    class="fa-regular fa-trash-can"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include Yii::getAlias('@app/views/layouts/_footer.php'); ?>

<script>
$(document).ready(function() {
    $('.dataTable').DataTable({
        order: [],
        columnDefs: [{
            orderable: false,
            targets: -1
        }],
        "lengthChange": false,
        "autoWidth": false,
        "responsive": true,
        "paging": true,
        "searching": true,
        "ordering": true,

    });
});
</script>