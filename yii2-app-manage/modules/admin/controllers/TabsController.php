<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;
use app\models\User;
use yii\web\Response;
use app\models\Tab;
use app\models\TableTab;
use app\models\Menu;
use yii\web\NotFoundHttpException;
use yii\web\Exception;
use yii\filters\AccessControl;


class TabsController extends Controller
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
                'id' => SORT_DESC,
            ])
            ->all();

        $tabMenus = Menu::find()->all();
        return $this->render('index', [
            'tabs' => $tabs,
            'tabMenus' => $tabMenus,
        ]);
    }
    public function actionTabsCreate()
    {
        $tabMenus = Menu::find()->all();

        return $this->render('create', [
            'tabMenus' => $tabMenus,

        ]);
    }

    public function actionCreateTab()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->user->id;
            $tabType = Yii::$app->request->post('tab_type');
            $tabName = Yii::$app->request->post('tab_name');
            $tabmenuId = Yii::$app->request->post('menu_single');
            $icon = Yii::$app->request->post('icon');

            if (empty($tabName)) {
                Yii::$app->session->setFlash('error', 'Tên tab không được để trống.');
                return $this->redirect(['tabs-create']);
            }
            if (empty($tabmenuId)) {
                if (empty($icon)) {
                    Yii::$app->session->setFlash('error', 'Vui lòng chọn icon.');
                    return $this->redirect(['tabs-create']);
                }

                $tabMenu = new Menu();
                $tabMenu->name = $tabName;
                $tabMenu->menu_type = 'none';
                $tabMenu->icon = $icon;
                Yii::error("icon: " . $icon);

                if (!$tabMenu->save()) {
                    Yii::$app->session->setFlash('error', 'Không thể tạo menu mới.');
                    return $this->redirect(['tabs-create']);
                }
                $tabmenuId = $tabMenu->id;
            } else {
                $icon = '';
            }

            $tab = new Tab();
            $tab->user_id = $userId;
            $tab->tab_type = $tabType;
            $tab->tab_name = $tabName;
            $tab->menu_id = $tabmenuId; // Gán menu_id vào Tab
            $tab->deleted = 0;
            $tab->created_at = date('Y-m-d H:i:s');
            $tab->updated_at = date('Y-m-d H:i:s');

            if ($tabType === 'table') {
                $columns = Yii::$app->request->post('columns', []);
                $dataTypes = Yii::$app->request->post('data_types', []);
                $dataSizes = Yii::$app->request->post('data_sizes', []);
                $isNotNull = Yii::$app->request->post('is_not_null', []);
                $isPrimary = Yii::$app->request->post('is_primary', []);

                foreach ($isPrimary as $index => $primary) {
                    if (isset($isPrimary[$index]) && $isPrimary[$index] == '1') {
                        $isNotNull[$index] = '1';
                    }
                }

                $validationResult = $this->validateTableCreation($tabName, $columns, $dataTypes, $dataSizes, $isNotNull);
                if ($validationResult !== true) {
                    foreach ($validationResult as $error) {
                        Yii::$app->session->setFlash("error_{$error['field']}", $error['message']);
                    }
                    Yii::$app->session->setFlash('tableCreationData', compact('tabName', 'tabmenuId', 'columns', 'dataTypes', 'dataSizes', 'isNotNull', 'isPrimary'));
                    return $this->render('create', [
                        'tableTabs' => [],
                        'tabName' => $tabName,
                        'tabmenuId' => $tabmenuId,
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
                            // $isNotNullValue = isset($isNotNull[$index]) && $isNotNull[$index] == '1' ? 1 : 0;
                            $tableTab = new TableTab([
                                'tab_id' => $tab->id,
                                'table_name' => $tabName,
                                'column_name' => $column,
                                'data_type' => $dataTypes[$index],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                            if (!$tableTab->save()) {
                                throw new \Exception('Không thể lưu thông tin bảng.');
                            }
                        }

                        // Tạo câu lệnh CREATE TABLE
                        $createTableQuery = "CREATE TABLE `$tabName`";
                        $columnDefs = [];
                        foreach ($columns as $index => $column) {
                            $columnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
                            $dataType = $dataTypes[$index];
                            $dataSize = isset($dataSizes[$index]) ? "($dataSizes[$index])" : '';
                            $isNotNullColumn = isset($isNotNull[$index]) && $isNotNull[$index] == '1' ? 'NOT NULL' : 'NULL';

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

                        Yii::$app->session->setFlash('success', 'Tạo bảng thành công.');
                        $transaction->commit();

                        return $this->redirect(['tabs-create', 'id' => $tab->id]);
                    } else {
                        throw new \Exception('Không thể tạo tab.');
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
                    return $this->redirect(['tabs-create']);
                }
            } elseif ($tabType === 'richtext') {
                if (empty($tabName)) {
                    Yii::$app->session->setFlash('error', 'Tên tab không được để trống.');
                    return $this->redirect(['tabs-create']);
                }

                $existingTab = Tab::findOne(['tab_name' => $tabName, 'tab_type' => 'richtext', 'user_id' => $userId]);
                if ($existingTab) {
                    Yii::$app->session->setFlash('error', 'Tên tab đã tồn tại. Vui lòng chọn tên khác.');
                    return $this->redirect(['tabs-create']);
                }

                if ($tab->save()) {
                    $filePath = Yii::getAlias('@runtime/richtext/' . $tab->id . '.txt');
                    try {
                        file_put_contents($filePath, '');
                        Yii::$app->session->setFlash('success', 'Tạo tab thành công!');
                    } catch (\Exception $e) {
                        Yii::error('Không thể tạo file: ' . $e->getMessage());
                        Yii::$app->session->setFlash('error', 'Đã xảy ra lỗi khi lưu file.');
                    }
                    return $this->redirect(['tabs-create']);
                } else {
                    Yii::$app->session->setFlash('error', 'Đã xảy ra lỗi khi tạo tab. Vui lòng thử lại.');
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

            // $isNotNullValue = isset($isNotNull[$index]) ? $isNotNull[$index] : null;
            // if ($isNotNullValue !== null && !is_bool($isNotNullValue)) {
            //     $errors[] = ['message' => "Giá trị không hợp lệ cho 'Not Null' của cột '$column'.", 'field' => "is_not_null[$index]"];
            // }
        }

        return empty($errors) ? true : $errors;
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
                Yii::$app->session->setFlash('success', 'Xóa mềm thành công.');
                return $this->asJson(['success' => true, 'message' => 'Xóa mềm thành công.']);
            } else {
                Yii::$app->session->setFlash('error', 'Không có bản ghi nào được cập nhật.');
                return $this->asJson(['success' => false, 'message' => 'Không có bản ghi nào được cập nhật.']);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Thiếu tabId.');
            return $this->asJson(['success' => false, 'message' => 'Thiếu tabId.']);
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
                Yii::$app->session->setFlash('success', 'Khôi phục thành công.');
                return $this->asJson(['success' => true, 'message' => 'Khôi phục thành công.']);
            } else {
                Yii::$app->session->setFlash('error', 'Không có bản ghi nào được cập nhật.');
                return $this->asJson(['success' => false, 'message' => 'Không có bản ghi nào được cập nhật.']);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Thiếu tabId.');
            return $this->asJson(['success' => false, 'message' => 'Thiếu tabId.']);
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
            Yii::$app->session->setFlash('error', 'Tab không tồn tại.');
            return $this->asJson(['success' => false, 'message' => 'Tab không tồn tại.']);
        }
        if ($tab->tab_type == 'table') {
            $tableName = $postData['tableName'];

            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
                Yii::$app->session->setFlash('error', 'Tên bảng không hợp lệ.');
                return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
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

                Yii::$app->session->setFlash('success', 'Bảng và dữ liệu đã được xóa thành công.');
                return $this->asJson(['success' => true, 'message' => 'Bảng và dữ liệu đã được xóa thành công.']);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
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

                Yii::$app->session->setFlash('success', 'Dữ liệu richtext đã được xóa thành công.');
                return $this->asJson(['success' => true, 'message' => 'Dữ liệu richtext đã được xóa thành công.']);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        Yii::$app->session->setFlash('error', 'Loại tab không hợp lệ.');
        return $this->asJson(['success' => false, 'message' => 'Loại tab không hợp lệ.']);
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
                        Yii::$app->session->setFlash('error', 'Không thể lưu tab với ID: ' . $tab['id']);
                        return [
                            'success' => false,
                            'message' => 'Không thể lưu tab với ID: ' . $tab['id'],
                        ];
                    }
                }
            }
            Yii::$app->session->setFlash('success', 'Thứ tự đã được cập nhật thành công.');
            return ['success' => true];
        }

        Yii::$app->session->setFlash('error', 'Dữ liệu không hợp lệ.');
        return [
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.'
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
                $tab->status = $status;
                $tab->save();
            }
        }

        Yii::$app->session->setFlash('success', 'Trạng thái ẩn/hiện đã được cập nhật thành công.');
        return $this->asJson(['success' => true]);
    }


    /** 
     * Update Tab Menus Tab Action.
     *
     */
    public function actionUpdateTab()
    {
        $tabId = Yii::$app->request->post('tab_id');
        $menuId = Yii::$app->request->post('menu_id');
        $status = Yii::$app->request->post('status');
        $position = Yii::$app->request->post('position');

        $tab = Tab::findOne($tabId);
        if ($tab) {
            $tab->menu_id = $menuId;
            $tab->status = $status == 1 ? 1 : 0;
            $tab->position = $position;
            $tab->save();
            Yii::$app->session->setFlash('success', 'Tab đã được cập nhật thành công.');
            return json_encode(['status' => 'success']);
        }

        Yii::$app->session->setFlash('error', 'Không tìm thấy tab.');
        return json_encode(['status' => 'error']);
    }
}
