<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\Tab;
use app\models\TableTab;
use app\models\RichtextTab;
use app\models\SignupForm;
use app\models\ContactForm;

class TableTabsController extends Controller
{
    public function actionIndex()
    {
        $tableTabs = TableTab::find()->all();

        return $this->render('index', [
            'tableTabs' => $tableTabs,
        ]);
    }
    public function actionCreateTableTabs()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->user->id;
            $tableName = Yii::$app->request->post('tableName');
            $columns = Yii::$app->request->post('columns');
            $dataTypes = Yii::$app->request->post('data_types');
            $dataSizes = Yii::$app->request->post('data_sizes');
            $isPrimary = Yii::$app->request->post('is_primary', []);
            $isNotNull = Yii::$app->request->post('is_not_null', []);
            $defaultValues = Yii::$app->request->post('default_values', []);
            $characterSet = Yii::$app->request->post('character_set', 'utf8mb4');

            Yii::error($columns, 'columns');
            Yii::error($dataTypes, 'data_types');
            Yii::error($tableName, 'table_name');

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $tab = new Tab();
                $tab->user_id = $userId;
                $tab->tab_type = 'table';
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
                            return $this->redirect(['index']);
                        }
                    }

                    $createTableQuery = "CREATE TABLE `$tableName` (";
                    $createTableQuery .= "`id` INT AUTO_INCREMENT PRIMARY KEY, "; // Thêm cột ID tự động tăng

                    foreach ($columns as $index => $column) {
                        if (strtolower(trim($column)) === 'id') {
                            continue; // Bỏ qua cột này nếu nó đã là 'id'
                        }

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
                        $columnDef = "`$columnName` $dataType";

                        if ($dataType === 'VARCHAR' && $dataSize) {
                            $columnDef .= "($dataSize)";
                        }

                        $columnDef .= " $isNotNullColumn $defaultValue";

                        $createTableQuery .= $columnDef . ", ";
                    }

                    // Thêm kiểu mã hóa cho bảng
                    $createTableQuery = rtrim($createTableQuery, ', ') . ") CHARACTER SET $characterSet COLLATE ${characterSet}_unicode_ci";

                    Yii::$app->db->createCommand($createTableQuery)->execute();

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Thêm tab và bảng thành công!');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Không thể lưu tab.');
                    return $this->redirect(['index']);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['index']);
            }
        }

        return $this->redirect(['index']);
    }


    public function actionEditTableTab($id)
    {
        $tableTab = TableTab::findOne($id);
        if (!$tableTab) {
            return $this->asJson(['success' => false, 'message' => 'Cột không tồn tại.']);
        }

        if (Yii::$app->request->isPost) {
            $tableTab->column_name = Yii::$app->request->post('column_name');
            $tableTab->data_type = Yii::$app->request->post('data_type');

            $tableName = $tableTab->table_name;
            $originalColumnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $tableTab->column_name);

            if ($tableTab->save()) {
                $newColumnName = preg_replace('/[^a-zA-Z0-9_]/', '_', Yii::$app->request->post('column_name'));
                if ($originalColumnName !== $newColumnName || $tableTab->data_type !== Yii::$app->request->post('data_type')) {
                    $alterTableQuery = "ALTER TABLE `$tableName` CHANGE `$originalColumnName` `$newColumnName` " . Yii::$app->request->post('data_type');
                    Yii::$app->db->createCommand($alterTableQuery)->execute();
                }

                return $this->asJson(['success' => true, 'message' => 'Chỉnh sửa cột thành công!']);
            } else {
                return $this->asJson(['success' => false, 'message' => 'Không thể lưu thay đổi.']);
            }
        }

        return $this->render('edit-tab', [
            'tableTab' => $tableTab,
        ]);
    }
    public function actionEditTable($tableName)
    {
        $tableTabs = TableTab::find()->where(['table_name' => $tableName])->all();

        if (Yii::$app->request->isPost) {
            foreach ($tableTabs as $tableTab) {
                $columnId = Yii::$app->request->post('column_id_' . $tableTab->id);
                $columnName = Yii::$app->request->post('column_name_' . $tableTab->id);
                $dataType = Yii::$app->request->post('data_type_' . $tableTab->id);

                $tableTab->column_name = $columnName;
                $tableTab->data_type = $dataType;
                $tableTab->save();

                $originalColumnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $tableTab->column_name);
                $alterTableQuery = "ALTER TABLE `$tableName` CHANGE `$originalColumnName` `$columnName` $dataType";
                Yii::$app->db->createCommand($alterTableQuery)->execute();
            }

            return $this->redirect(['tableTabs']);
        }

        return $this->render('edit-table', [
            'tableTabs' => $tableTabs,
        ]);
    }
    public function actionDeleteColumn($id)
    {
        $column = TableTab::findOne($id);
        if ($column) {
            $tableName = $column->table_name;
            $columnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $column->column_name);

            $dropColumnQuery = "ALTER TABLE `$tableName` DROP `$columnName`";
            Yii::$app->db->createCommand($dropColumnQuery)->execute();

            $column->delete();

            return $this->asJson(['success' => true, 'message' => 'Xóa cột thành công!']);
        }

        return $this->asJson(['success' => false, 'message' => 'Cột không tồn tại.']);
    }

}