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


class TableTabsController extends Controller
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $tableTabs = Tab::find()->where(['tab_type' => 'table'])
            ->where(['user_id' => $userId])
            ->all();

        return $this->render('index', [
            'tableTabs' => $tableTabs,
        ]);
    }
    public function actionCreateTableTabs()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->user->id;
            $tableName = Yii::$app->request->post('tableName');
            $columns = Yii::$app->request->post('columns', []);
            $dataTypes = Yii::$app->request->post('data_types', []);
            $dataSizes = Yii::$app->request->post('data_sizes', []);
            $isNotNull = Yii::$app->request->post('is_not_null', []);
            $defaultValues = Yii::$app->request->post('default_values', []);
            $isPrimary = Yii::$app->request->post('is_primary', []);

            // Perform validations
            $validationResult = $this->validateTableCreation($tableName, $columns, $dataTypes);

            if ($validationResult !== true) {
                $errorMessages = [];

                foreach ($validationResult as $error) {
                    $errorMessages[] = $error['message'];
                }

                // Join all messages into a single string with line breaks
                $formattedMessages = implode('<br>', $errorMessages);
                Yii::$app->session->setFlash('error', $formattedMessages);

                // Optionally set which field had the error
                // This can be adjusted based on your needs
                Yii::$app->session->setFlash('errorFields', $error['field']);
                Yii::$app->session->setFlash('tableCreationData', [
                    'tableName' => $tableName,
                    'columns' => $columns,
                    'data_types' => $dataTypes,
                    'data_sizes' => $dataSizes,
                    'is_not_null' => $isNotNull,
                    'default_values' => $defaultValues,
                    'is_primary' => $isPrimary,
                ]);

                return $this->redirect(['table-tabs/index']);
            }
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $tab = new Tab();
                $tab->user_id = $userId;
                $tab->tab_type = 'table';
                $tab->tab_name = $tableName;
                $tab->deleted = 0;
                $tab->created_at = date('Y-m-d H:i:s');
                $tab->updated_at = date('Y-m-d H:i:s');

                if ($tab->save()) {
                    foreach ($columns as $index => $column) {
                        $dataType = $dataTypes[$index];
                        $tableTab = new TableTab();
                        $tableTab->tab_id = $tab->id;
                        $tableTab->table_name = $tableName;
                        $tableTab->column_name = $column;
                        $tableTab->data_type = $dataType;
                        $tableTab->created_at = date('Y-m-d H:i:s');
                        $tableTab->updated_at = date('Y-m-d H:i:s');

                        if (!$tableTab->save()) {
                            Yii::$app->session->setFlash('error', 'Không thể lưu vào bảng table_tab.');
                            return $this->redirect(['table-tabs/index']);
                        }
                    }

                    $createTableQuery = "CREATE TABLE `$tableName`";
                    $columnDefs = [];

                    if (!empty($columns)) {
                        $createTableQuery .= " (";
                        foreach ($columns as $index => $column) {
                            $dataType = $dataTypes[$index];
                            $dataSize = isset($dataSizes[$index]) ? $dataSizes[$index] : null;
                            $isNotNullColumn = isset($isNotNull[$index]) ? 'NOT NULL' : 'NULL';

                            if (in_array($dataType, ['TEXT', 'BLOB', 'JSON', 'GEOMETRY'])) {
                                $defaultValue = '';
                            } else {
                                $defaultValue = isset($defaultValues[$index]) && $defaultValues[$index] !== ''
                                    ? "DEFAULT '" . addslashes($defaultValues[$index]) . "'" : '';
                            }

                            $columnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);

                            if (isset($isPrimary[$index]) && $isPrimary[$index] == '1') {
                                $columnDef = "`$columnName` INT AUTO_INCREMENT PRIMARY KEY";
                            } else {
                                $columnDef = "`$columnName` $dataType";

                                if ($dataType === 'VARCHAR' && $dataSize) {
                                    $columnDef .= "($dataSize)";
                                }

                                $columnDef .= " $isNotNullColumn $defaultValue";
                            }

                            $columnDefs[] = $columnDef;
                        }

                        $createTableQuery .= implode(', ', $columnDefs) . ")";
                    }

                    $createTableQuery .= " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

                    Yii::$app->db->createCommand($createTableQuery)->execute();

                    $transaction->commit();

                    // Redirect to detail page after successful creation
                    return $this->redirect(['table-tabs/_detail', 'id' => $tab->id]);
                } else {
                    Yii::$app->session->setFlash('error', 'Có lỗi xảy ra khi lưu tab.');
                    return $this->redirect(['table-tabs/index']);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['table-tabs/index']);
            }
        }

        return $this->redirect(['table-tabs/index']);
    }

    /**
     * Validate the table creation inputs.
     *
     * @param string $tableName
     * @param array $columns
     * @param array $dataTypes
     * @return array|string
     */
    protected function validateTableCreation($tableName, $columns, $dataTypes)
    {
        $errors = [];

        // Validate table name
        if (empty($tableName) || !preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            $errors[] = ['message' => 'Table names can only contain letters, numbers, and underscores.', 'field' => 'tableName'];
        }

        // Validate columns and data types
        if (empty($columns) || empty($dataTypes)) {
            $errors[] = ['message' => 'Columns and data types cannot be empty.', 'field' => 'columns'];
        }

        if (count($columns) !== count($dataTypes)) {
            $errors[] = ['message' => 'Each column must have a corresponding data type.', 'field' => 'data_types'];
        }

        foreach ($columns as $index => $column) {
            if (empty($column) || !preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                $errors[] = ['message' => 'Column names can only contain letters, numbers, and underscores, and cannot be empty.', 'field' => "columns[$index]"];
            }
        }

        return empty($errors) ? true : $errors; // Return true if no errors, otherwise return the errors
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