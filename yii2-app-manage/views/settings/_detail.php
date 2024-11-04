<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Tab $tabModel */
/** @var array $columns */

$this->title = 'View Table Details: ' . $tableName;

function getDataTypeValue($type)
{
    switch (strtolower($type)) {
        case 'integer':
            return 'INT';
        case 'bigint':
            return 'BIGINT';
        case 'smallint':
            return 'SMALLINT';
        case 'tinyint':
            return 'TINYINT';
        case 'float':
            return 'FLOAT';
        case 'double':
            return 'DOUBLE';
        case 'decimal':
            return 'DECIMAL';
        case 'string':
            return 'VARCHAR';
        case 'char':
            return 'CHAR';
        case 'text':
            return 'TEXT';
        case 'mediumtext':
            return 'MEDIUMTEXT';
        case 'longtext':
            return 'LONGTEXT';
        case 'date':
            return 'DATE';
        case 'datetime':
            return 'DATETIME';
        case 'timestamp':
            return 'TIMESTAMP';
        case 'time':
            return 'TIME';
        case 'boolean':
            return 'BOOLEAN';
        case 'json':
            return 'JSON';
        case 'blob':
            return 'BLOB';
        default:
            return $type;
    }
}
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
                        <h5>View Table: <?= Html::encode($tableName) ?></h5>
                        <div class="ms-auto">
                            <a href="<?= \yii\helpers\Url::to(['table-tabs/index']) ?>" class="btn btn-success">Table
                                List</a>
                        </div>
                    </div>
                    <div class="tab-pane" id="viewTableTab">

                        <div class="mb-3 row">
                            <div class="col-3">
                                <label for="tableName" class="form-label">Table Name</label>
                                <input type="text" class="form-control" id="tableName"
                                    value="<?= Html::encode($tableName) ?>" disabled>
                            </div>
                            <div class="col-3">
                                <label for="collation" class="form-label">Collation</label>
                                <input type="text" class="form-control" id="characterSet"
                                    value="<?= Html::encode($collation) ?>" disabled>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Length/Value</th>
                                        <th>Default</th>
                                        <th>Not Null</th>
                                        <th>A_I</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($columns as $columnName => $column): ?>
                                    <tr>
                                        <td><?= Html::encode($columnName) ?></td>
                                        <td><?= getDataTypeValue($column->type) ?></td>
                                        <td><?= Html::encode($column->size) ?></td>
                                        <td><?= Html::encode($column->defaultValue) ?></td>
                                        <td><?= $column->allowNull ? 'No' : 'Yes' ?></td>
                                        <td><?= $column->isPrimaryKey ? 'Yes' : 'No' ?></td>
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