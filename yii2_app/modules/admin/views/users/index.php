<?php

use yii\helpers\Html;
use app\models\User;

/** @var yii\web\View $this */

$this->title = 'Quản Lý Người Dùng';

?>

<div class="card">
    <div class="card-header card-no-border pb-0">
        <div class="d-flex">
            <div class="me-auto">
                <h4>Quản Lý Người Dùng</h4>
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
                        <th>Tên</th>
                        <th>Email</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th>Quyền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= Html::encode($user->id) ?></td>
                            <td><?= Html::encode($user->username) ?></td>
                            <td><?= Html::encode($user->email) ?></td>
                            <td style="text-align: center;">
                                <form action="<?= \yii\helpers\Url::to(['users/update-user', 'id' => $user->id]) ?>"
                                    method="post">
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                                    <label class="switch mb-0 mt-1">
                                        <input type="checkbox" name="status" <?= $user->status == 10 ? 'checked' : '' ?>>
                                        <span class="switch-state"></span>
                                    </label>
                            </td>
                            <td>
                                <select class="form-control" name="role">
                                    <option value="10" <?= $user->role == 10 ? 'selected' : '' ?>>User
                                    </option>
                                    <option value="20" <?= $user->role == 20 ? 'selected' : '' ?>>Admin
                                    </option>
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