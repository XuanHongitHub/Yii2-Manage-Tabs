<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\Tab;
use app\models\TableTab;
use yii\web\NotFoundHttpException;
use yii\web\Exception;
use yii\filters\AccessControl;


class SettingsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $tabs = Tab::find()
            ->where(['user_id' => $userId])
            ->orderBy([
                'position' => SORT_ASC,
                'id' => SORT_ASC,
            ])
            ->all();

        return $this->render('index', [
            'tabs' => $tabs,
        ]);
    }
    public function actionCreate()
    {

        return $this->render('create', [

        ]);
    }
    public function actionCreateTab()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->user->id;
            $tabType = Yii::$app->request->post('tab_type');
            $tabName = Yii::$app->request->post('tab_name');

            // Khởi tạo đối tượng Tab
            $tab = new Tab();
            $tab->user_id = $userId;
            $tab->tab_type = $tabType;
            $tab->tab_name = $tabName;
            $tab->deleted = 0;
            $tab->created_at = date('Y-m-d H:i:s');
            $tab->updated_at = date('Y-m-d H:i:s');

            if ($tabType === 'table') {
                // Xử lý nếu loại tab là "table"
                $columns = Yii::$app->request->post('columns', []);
                $dataTypes = Yii::$app->request->post('data_types', []);
                $dataSizes = Yii::$app->request->post('data_sizes', []);
                $isNotNull = Yii::$app->request->post('is_not_null', []);
                $isPrimary = Yii::$app->request->post('is_primary', []);

                $validationResult = $this->validateTableCreation($tabName, $columns, $dataTypes, $dataSizes, $isNotNull);
                if ($validationResult !== true) {
                    foreach ($validationResult as $error) {
                        Yii::$app->session->setFlash("error_{$error['field']}", $error['message']);
                    }
                    Yii::$app->session->setFlash('tableCreationData', compact('tabName', 'columns', 'dataTypes', 'dataSizes', 'isNotNull', 'isPrimary'));
                    return $this->render('create', [
                        'tableTabs' => [],
                        'tabName' => $tabName,
                        'columns' => $columns,
                        'dataTypes' => $dataTypes,
                        'dataSizes' => $dataSizes,
                        'isNotNull' => $isNotNull,
                        'isPrimary' => $isPrimary,
                    ]);
                }

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($tab->save()) {
                        foreach ($columns as $index => $column) {
                            $tableTab = new TableTab([
                                'tab_id' => $tab->id,
                                'table_name' => $tabName,
                                'column_name' => $column,
                                'data_type' => $dataTypes[$index],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                            if (!$tableTab->save()) {
                                Yii::$app->session->setFlash('error', 'Cannot save');
                                return $this->redirect(['table-tabs/create']);
                            }
                        }

                        $createTableQuery = "CREATE TABLE `$tabName`";
                        $columnDefs = [];
                        foreach ($columns as $index => $column) {
                            $columnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
                            $dataType = $dataTypes[$index];
                            $dataSize = isset($dataSizes[$index]) ? "($dataSizes[$index])" : '';
                            $isNotNullColumn = isset($isNotNull[$index]) ? 'NOT NULL' : 'NULL';

                            if (in_array($dataType, ['VARCHAR', 'CHAR'])) {
                                $columnDef = isset($isPrimary[$index]) && $isPrimary[$index] == '1'
                                    ? "`$columnName` INT AUTO_INCREMENT PRIMARY KEY"
                                    : "`$columnName` $dataType$dataSize $isNotNullColumn";
                            } elseif (in_array($dataType, ['INT', 'BIGINT', 'SMALLINT', 'TINYINT', 'FLOAT', 'DOUBLE', 'DECIMAL'])) {
                                $columnDef = isset($isPrimary[$index]) && $isPrimary[$index] == '1'
                                    ? "`$columnName` INT AUTO_INCREMENT PRIMARY KEY"
                                    : "`$columnName` $dataType $isNotNullColumn";
                            } else {
                                $columnDef = isset($isPrimary[$index]) && $isPrimary[$index] == '1'
                                    ? "`$columnName` INT AUTO_INCREMENT PRIMARY KEY"
                                    : "`$columnName` $dataType $isNotNullColumn";
                            }

                            $columnDefs[] = $columnDef;
                        }

                        $createTableQuery .= ' (' . implode(', ', $columnDefs) . ') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
                        Yii::$app->db->createCommand($createTableQuery)->execute();

                        Yii::$app->session->setFlash('success', 'Create Successful.');

                        $transaction->commit();
                        return $this->redirect(['table-tabs/create', 'id' => $tab->id]);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    return $this->redirect(['table-tabs/create']);
                }

            } elseif ($tabType === 'richtext') {
                if (empty($tabName)) {
                    Yii::$app->session->setFlash('error', 'Tab name cannot be empty.');
                    return $this->redirect(['table-tabs/create']);
                }

                $existingTab = Tab::findOne(['tab_name' => $tabName, 'tab_type' => 'richtext', 'user_id' => $userId]);
                if ($existingTab) {
                    Yii::$app->session->setFlash('error', 'Tab name already exists. Please choose a different name.');
                    return $this->redirect(['table-tabs/create']);
                }

                if ($tab->save()) {
                    $filePath = Yii::getAlias('@runtime/richtext/' . $tab->id . '.rtf');
                    try {
                        file_put_contents($filePath, '');
                        Yii::$app->session->setFlash('success', 'Created successfully!');
                    } catch (\Exception $e) {
                        Yii::error('Cannot create file: ' . $e->getMessage());
                        Yii::$app->session->setFlash('error', 'An error occurred while saving the file.');
                    }
                    return $this->redirect(['table-tabs/create']);
                } else {
                    Yii::$app->session->setFlash('error', 'An error occurred while creating the tab. Please try again.');
                    return $this->redirect(['table-tabs/create']);
                }
            }
        }
        return $this->render('create', [
            'tableTabs' => [],
        ]);
    }



    /**
     * Validate the table creation inputs.
     *
     * @param string $tableName
     * @param array $columns
     * @param array $dataTypes
     * @return array|string
     */
    protected function validateTableCreation($tableName, $columns, $dataTypes, $dataSizes, $isNotNull)
    {
        $errors = [];

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            $errors[] = ['message' => 'Table name can only include letters, numbers, and underscores.', 'field' => 'tab_name'];
        }

        if (empty($columns) || empty($dataTypes)) {
            $errors[] = ['message' => 'Columns and data types must not be empty.', 'field' => 'columns'];
        }

        if (count($columns) !== count($dataTypes)) {
            $errors[] = ['message' => 'Each column must have a matching data type.', 'field' => 'data_types'];
        }

        foreach ($columns as $index => $column) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                $errors[] = ['message' => 'Column names can only include letters, numbers, and underscores.', 'field' => "columns[$index]"];
            }
            $dataType = $dataTypes[$index] ?? null;
            $dataSize = $dataSizes[$index] ?? null;

            if (
                !in_array($dataType, [
                    'INT',
                    'BIGINT',
                    'SMALLINT',
                    'TINYINT',
                    'FLOAT',
                    'DOUBLE',
                    'DECIMAL',
                    'VARCHAR',
                    'CHAR',
                    'TEXT',
                    'MEDIUMTEXT',
                    'LONGTEXT',
                    'DATE',
                    'DATETIME',
                    'TIMESTAMP',
                    'TIME',
                    'BOOLEAN',
                    'JSON',
                    'BLOB'
                ])
            ) {
                $errors[] = ['message' => "Invalid data type for column '$column'.", 'field' => "data_types[$index]"];
            } else {
                if (in_array($dataType, ['VARCHAR', 'CHAR'])) {
                    if ($dataSize === null || !is_numeric($dataSize) || $dataSize <= 0 || $dataSize > 1000) {
                        $errors[] = ['message' => "Length for column '$column' must be a positive number not greater than 1000 for data type '$dataType'.", 'field' => "data_sizes[$index]"];
                    }
                }
                if (in_array($dataType, ['DECIMAL', 'FLOAT', 'DOUBLE'])) {
                    if ($dataSize === null || !is_numeric($dataSize) || $dataSize < 0 || $dataSize > 38) {
                        $errors[] = ['message' => "Invalid size for column '$column' with data type '$dataType'. Maximum size is 38.", 'field' => "data_sizes[$index]"];
                    }
                }
                if (in_array($dataType, ['TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'BOOLEAN', 'JSON', 'BLOB'])) {
                    if ($dataSize !== null) {
                        $errors[] = ['message' => "Column '$column' with data type '$dataType' should not have a size.", 'field' => "data_sizes[$index]"];
                    }
                }
            }

            $isNotNullValue = $isNotNull[$index] ?? null;
            if ($isNotNullValue !== null && !is_bool($isNotNullValue)) {
                $errors[] = ['message' => "Invalid value for 'Not Null' on column '$column'.", 'field' => "is_not_null[$index]"];
            }
        }

        return empty($errors) ? true : $errors;
    }




    public function actionDetail($id)
    {
        $table = Tab::find()->where(['id' => $id])->one();
        $tableName = $table->tab_name;

        $charsetInfo = Yii::$app->db->createCommand("SHOW TABLE STATUS LIKE '$tableName'")->queryOne();
        $collation = $charsetInfo['Collation'] ?? 'Không xác định';
        $columns = Yii::$app->db->schema->getTableSchema($tableName)->columns;

        return $this->render('_detail', [
            'table' => $table,
            'columns' => $columns,
            'tableName' => $tableName,
            'collation' => $collation,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    protected function findModel($id)
    {
        if (($model = Tab::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}