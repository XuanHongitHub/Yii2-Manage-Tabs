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
            $columns = Yii::$app->request->post('columns');
            $dataTypes = Yii::$app->request->post('data_types');
            $tableName = Yii::$app->request->post('tableName');
            $dataSizes = Yii::$app->request->post('data_sizes');

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
                    foreach ($columns as $index => $column) {
                        $dataType = $dataTypes[$index];
                        $dataSize = isset($dataSizes[$index]) ? $dataSizes[$index] : null;

                        $columnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
                        if ($dataType === 'VARCHAR' && $dataSize) {
                            $createTableQuery .= "`$columnName` VARCHAR($dataSize), ";
                        } elseif ($dataType === 'CHAR' && $dataSize) {
                            $createTableQuery .= "`$columnName` CHAR($dataSize), ";
                        } else {
                            $createTableQuery .= "`$columnName` $dataType, ";
                        }
                    }
                    $createTableQuery = rtrim($createTableQuery, ', ') . ")";

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
            // Cập nhật thông tin cột trong table_tab
            $tableTab->column_name = Yii::$app->request->post('column_name');
            $tableTab->data_type = Yii::$app->request->post('data_type');

            // Cập nhật bảng SQL nếu cần thiết
            $tableName = $tableTab->table_name;
            $originalColumnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $tableTab->column_name);

            // Lưu thay đổi cho table_tab
            if ($tableTab->save()) {
                // Nếu kiểu dữ liệu đã thay đổi, cập nhật trong bảng SQL
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

            // Xóa cột khỏi bảng SQL
            $dropColumnQuery = "ALTER TABLE `$tableName` DROP `$columnName`";
            Yii::$app->db->createCommand($dropColumnQuery)->execute();

            // Xóa cột khỏi bảng table_tab
            $column->delete();

            return $this->asJson(['success' => true, 'message' => 'Xóa cột thành công!']);
        }

        return $this->asJson(['success' => false, 'message' => 'Cột không tồn tại.']);
    }

}