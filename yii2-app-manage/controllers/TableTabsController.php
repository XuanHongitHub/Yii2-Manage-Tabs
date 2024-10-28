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
            $characterSet = Yii::$app->request->post('character_set', 'utf8mb4');
            $collation = Yii::$app->request->post('collation');
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
                            return $this->redirect(['tabs/settings']);
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

                    if ($characterSet) {
                        $createTableQuery .= " CHARACTER SET $characterSet";
                    }
                    if ($collation) {
                        $createTableQuery .= " COLLATE $collation";
                    }

                    Yii::$app->db->createCommand($createTableQuery)->execute();

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Tạo bảng thành công!');
                    return $this->redirect(['tabs/settings']);
                } else {
                    Yii::$app->session->setFlash('error', 'Có lỗi xảy ra khi lưu tab.');
                    return $this->redirect(['tabs/settings']);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['tabs/settings']);
            }
        }

        return $this->redirect(['tabs/settings']);
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