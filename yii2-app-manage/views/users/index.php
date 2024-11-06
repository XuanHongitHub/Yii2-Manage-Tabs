<?php

use yii\helpers\Html;
use app\models\User;
/** @var yii\web\View $this */
/** @var app\models\TableTab[] $tableTabs */

$this->title = 'Manage Users';

?>
<?php include Yii::getAlias('@app/views/layouts/_sidebar.php'); ?>
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">

            </div>
        </div>
    </div>
    <!-- Container-fluid starts -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <div class="d-flex">
                            <div class="me-auto">
                                <h4>Manage Users</h4>
                                <p class="mt-1 f-m-light">Administering user accounts.</p>
                            </div>


                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="display border table-bordered dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th style="text-align: center;">Status</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= Html::encode($user->id) ?></td>
                                        <td><?= Html::encode($user->username) ?></td>
                                        <td><?= Html::encode($user->email) ?></td>
                                        <td style="text-align: center;">
                                            <span
                                                class="btn <?= $user->status == 10 ? 'badge badge-light-success' : 'badge badge-light-danger' ?>">
                                                <?= $user->status == 10 ? '<i class="fa-solid fa-circle-check"></i>' : '<i class="fa-solid fa-circle-xmark"></i>' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form
                                                action="<?= \yii\helpers\Url::to(['users/update-role', 'id' => $user->id]) ?>"
                                                method="post">
                                                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                                                <select class="form-control" name="role">
                                                    <option value="10" <?= $user->role == 10 ? 'selected' : '' ?>>
                                                        User</option>
                                                    <option value="20" <?= $user->role == 20 ? 'selected' : '' ?>>
                                                        Admin</option>
                                                </select>

                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            </form>
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

    <!-- Container-fluid Ends-->
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