<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;
use app\models\User;
use yii\web\Response;
use app\models\Tab;
use app\models\TableTab;
use app\models\TabGroups;
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

    public function actionTabsList()
    {
        $userId = Yii::$app->user->id;
        $tabs = Tab::find()
            ->where(['user_id' => $userId])
            ->orderBy([
                'position' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->all();

        $tabGroups = TabGroups::find()->all();
        return $this->render('tabs/index', [
            'tabs' => $tabs,
            'tabGroups' => $tabGroups,
        ]);
    }
    public function actionTabsCreate()
    {
        $tabGroups = TabGroups::find()->all();

        return $this->render('tabs/create', [
            'tabGroups' => $tabGroups,

        ]);
    }
    public function actionGroupCreate()
    {

        return $this->render('group/create', [

        ]);
    }
    public function actionGroupList()
    {
        $tabGroups = TabGroups::find()->all();

        return $this->render('group/index', [
            'tabGroups' => $tabGroups,

        ]);
    }
    public function actionCreateGroup()
    {
        if (Yii::$app->request->isPost) {
            $name = Yii::$app->request->post('name');
            $icon = Yii::$app->request->post('icon');
            $group_type = Yii::$app->request->post('group_type');

            $tabGroup = new TabGroups();
            $tabGroup->name = $name;
            $tabGroup->icon = $icon;
            $tabGroup->group_type = $group_type;

            if ($tabGroup->save()) {
                Yii::$app->session->setFlash('success', 'Nhóm tab đã được tạo thành công!');
            } else {
                Yii::$app->session->setFlash('error', 'Có lỗi xảy ra khi tạo nhóm tab.');
            }
        }

        return $this->redirect('group-create');
    }
    public function actionCreateTab()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->user->id;
            $tabType = Yii::$app->request->post('tab_type');
            $tabName = Yii::$app->request->post('tab_name');
            $tabGroupId = Yii::$app->request->post('tab_group');

            $tab = new Tab();
            $tab->user_id = $userId;
            $tab->tab_type = $tabType;
            $tab->tab_name = $tabName;
            $tab->group_id = $tabGroupId;
            $tab->deleted = 0;
            $tab->created_at = date('Y-m-d H:i:s');
            $tab->updated_at = date('Y-m-d H:i:s');

            if ($tabType === 'table') {
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
                                return $this->redirect(['tabs-create']);
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
                        return $this->redirect(['tabs-create', 'id' => $tab->id]);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    return $this->redirect(['tabs-create']);
                }

            } elseif ($tabType === 'richtext') {
                if (empty($tabName)) {
                    Yii::$app->session->setFlash('error', 'Tab name cannot be empty.');
                    return $this->redirect(['tabs-create']);
                }

                $existingTab = Tab::findOne(['tab_name' => $tabName, 'tab_type' => 'richtext', 'user_id' => $userId]);
                if ($existingTab) {
                    Yii::$app->session->setFlash('error', 'Tab name already exists. Please choose a different name.');
                    return $this->redirect(['tabs-create']);
                }

                if ($tab->save()) {
                    $filePath = Yii::getAlias('@runtime/richtext/' . $tab->id . '.txt');
                    try {
                        file_put_contents($filePath, '');
                        Yii::$app->session->setFlash('success', 'Created successfully!');
                    } catch (\Exception $e) {
                        Yii::error('Cannot create file: ' . $e->getMessage());
                        Yii::$app->session->setFlash('error', 'An error occurred while saving the file.');
                    }
                    return $this->redirect(['tabs-create']);
                } else {
                    Yii::$app->session->setFlash('error', 'An error occurred while creating the tab. Please try again.');
                    return $this->redirect(['tabs-create']);
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
                    if ($dataSize != null) {
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


       /** 
     * Delete Tab Action.
     *
     */
    public function actionDeleteTab()
    {
        $postData = Yii::$app->request->post();

        if (isset($postData['tabId'])) {
            $tabId = $postData['tabId'];

            $affectedRows = Tab::updateAll(
                ['deleted' => 1],
                ['id' => $tabId]
            );

            if ($affectedRows > 0) {
                return $this->asJson(['success' => true, 'message' => 'Soft delete successful.']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'No records updated.']);
            }
        } else {
            return $this->asJson(['success' => false, 'message' => 'Missing tabId.']);
        }
    }
    /** 
     * Update Restore Action.
     *
     */
    public function actionRestoreTab()
    {
        $postData = Yii::$app->request->post();

        if (isset($postData['tabId'])) {
            $tabId = $postData['tabId'];

            $affectedRows = Tab::updateAll(
                ['deleted' => 0],
                ['id' => $tabId]
            );

            if ($affectedRows > 0) {
                return $this->asJson(['success' => true, 'message' => 'Restore successful.']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'No records updated.']);
            }
        } else {
            return $this->asJson(['success' => false, 'message' => 'Missing tabId.']);
        }
    }
    /** 
     * Delete Permanently Tab Action.
     *
     */
    public function actionDeletePermanentlyTab()
    {
        $postData = Yii::$app->request->post();

        $tabId = $postData['tabId'];

        $tab = Tab::find()->where(['id' => $tabId])->one();

        if (!$tab) {
            return $this->asJson(['success' => false, 'message' => 'Tab does not exist.']);
        }
        if ($tab->tab_type == 'table') {
            $tableName = $postData['tableName'];

            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
                return $this->asJson(['success' => false, ' message' => 'Invalid table name.']);
            }
            $sql = "DROP TABLE IF EXISTS `$tableName`";

            try {
                Yii::$app->db->createCommand($sql)->execute();

                $tableTabTable = 'table_tab';
                $deleteTabSql = "DELETE FROM `$tableTabTable` WHERE `tab_id` = :tabId";
                Yii::$app->db->createCommand($deleteTabSql)->bindValue(':tabId', $tabId)->execute();

                $tabTable = 'tab';
                $deleteTabRecordSql = "DELETE FROM `$tabTable` WHERE `id` = :tabId";
                Yii::$app->db->createCommand($deleteTabRecordSql)->bindValue(':tabId', $tabId)->execute();

                return $this->asJson(['success' => true, 'message' => 'Table and data were successfully deleted.']);
            } catch (\Exception $e) {
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        } elseif ($tab->tab_type == 'richtext') {
            try {
                $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.txt');

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $tabTable = 'tab';
                $deleteTabRecordSql = "DELETE FROM `$tabTable` WHERE `id` = :tabId";
                Yii::$app->db->createCommand($deleteTabRecordSql)->bindValue(':tabId', $tabId)->execute();

                return $this->asJson(['success' => true, 'message' => 'Richtext data was successfully deleted.']);
            } catch (\Exception $e) {
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        return $this->asJson(['success' => false, 'message' => 'Invalid tab type.']);
    }
    /** 
     * Update Postion Action.
     *
     */
    public function actionUpdateSortOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tabs = Yii::$app->request->post('tabs');

        if ($tabs) {
            foreach ($tabs as $tab) {
                $model = Tab::findOne($tab['id']);
                if ($model) {
                    $model->position = $tab['position'];
                    if (!$model->save()) {
                        return [
                            'success' => false,
                            'message' => 'Unable to save tab with ID: ' . $tab['id'],
                        ];
                    }
                }
            }
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Invalid data.'
        ];
    }
    /** 
     * Update Show/Hide Tab Action.
     *
     */
    public function actionUpdateHideStatus()
    {
        $hideStatus = Yii::$app->request->post('hideStatus', []);

        foreach ($hideStatus as $tabId => $status) {
            $tab = Tab::findOne($tabId);
            if ($tab) {
                $tab->deleted = $status;
                $tab->save();
            }
        }

        return $this->asJson(['success' => true]);
    }


    public function actionUpdateTab()
{
    $tabId = Yii::$app->request->post('tab_id');
    $groupId = Yii::$app->request->post('group_id');
    $status = Yii::$app->request->post('status');
    $position = Yii::$app->request->post('position');

    $tab = Tab::findOne($tabId);
    if ($tab) {
        $tab->group_id = $groupId;
        $tab->deleted = $status == 3 ? 3 : 0; 
        $tab->save();
        return json_encode(['status' => 'success']);
    }

    return json_encode(['status' => 'error']);
}

}