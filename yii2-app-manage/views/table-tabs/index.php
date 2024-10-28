<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Table Tab';
?>
<div class="content-body mt-3">
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
                <div class="card-body">
                    <div class="d-flex my-3">
                        <h5>List Table: <?= Html::encode($tableName) ?></h5>
                    </div>
                    <div class="tab-pane" id="viewTableTab">

                        <div class="table-responsive">
                            <?php if (!empty($tableTabs)): ?>
                            <table class="table table-bordered dataTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Table Name</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tableTabs as $tab): ?>
                                    <tr>
                                        <td><?= Html::encode($tab->id) ?></td>
                                        <td><?= Html::encode($tab->tab_name) ?></td>
                                        <td><?= Html::encode($tab->created_at) ?></td>
                                        <td>
                                            <?= Html::a('Detail', ['detail', 'id' => $tab->id], ['class' => 'btn btn-primary']) ?>
                                            <?= Html::a('Delete', ['delete', 'id' => $tab->id], [
                                                        'class' => 'btn btn-danger',
                                                        'data' => [
                                                            'confirm' => 'Are you sure you want to delete this item?',
                                                            'method' => 'post',
                                                        ],
                                                    ]) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <p>No data.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>