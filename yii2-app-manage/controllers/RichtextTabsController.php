<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\Tab;
use app\models\TableTab;
use yii\filters\AccessControl;



class RichtextTabsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],  // Yêu cầu người dùng đã đăng nhập
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],  // Từ chối người dùng chưa đăng nhập
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $richtext_tab = Tab::find()->where(['tab_type' => 'richtext'])
            ->where(['user_id' => $userId])
            ->all();

        return $this->render('index', [
            'richtext_tab' => $richtext_tab,
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
                $defaultValues = Yii::$app->request->post('default_values', []);
                $isPrimary = Yii::$app->request->post('is_primary', []);

                // Thực hiện xác thực dữ liệu
                $validationResult = $this->validateTableCreation($tabName, $columns, $dataTypes);
                if ($validationResult !== true) {
                    foreach ($validationResult as $error) {
                        Yii::$app->session->setFlash("error_{$error['field']}", $error['message']);
                    }
                    Yii::$app->session->setFlash('tableCreationData', compact('tabName', 'columns', 'dataTypes', 'dataSizes', 'isNotNull', 'defaultValues', 'isPrimary'));
                    return $this->redirect(['table-tabs/create']);
                }

                // Tạo transaction để đảm bảo tạo tab và bảng đi kèm
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($tab->save()) {
                        // Lưu các cột trong bảng TableTab
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
                                Yii::$app->session->setFlash('error', 'Không thể lưu vào bảng table_tab.');
                                return $this->redirect(['table-tabs/create']);
                            }
                        }

                        // Tạo câu lệnh SQL để tạo bảng
                        $createTableQuery = "CREATE TABLE `$tabName`";
                        $columnDefs = [];
                        foreach ($columns as $index => $column) {
                            $columnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
                            $dataType = $dataTypes[$index];
                            $dataSize = isset($dataSizes[$index]) ? "($dataSizes[$index])" : '';
                            $isNotNullColumn = isset($isNotNull[$index]) ? 'NOT NULL' : 'NULL';
                            $defaultValue = '';  // Có thể thêm logic xử lý `defaultValue` ở đây nếu cần
                            $columnDef = isset($isPrimary[$index]) && $isPrimary[$index] == '1'
                                ? "`$columnName` INT AUTO_INCREMENT PRIMARY KEY"
                                : "`$columnName` $dataType$dataSize $isNotNullColumn $defaultValue";
                            $columnDefs[] = $columnDef;
                        }
                        $createTableQuery .= ' (' . implode(', ', $columnDefs) . ') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
                        Yii::$app->db->createCommand($createTableQuery)->execute();

                        $transaction->commit();
                        return $this->redirect(['table-tabs/create', 'id' => $tab->id]);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                    return $this->redirect(['table-tabs/create']);
                }

            } elseif ($tabType === 'richtext') {
                // Xử lý nếu loại tab là "richtext"
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
        return $this->redirect(['table-tabs/create']);
    }



    public function actionDetail($id)
    {
        $richtextTab = Tab::find()->where(['id' => $id])->one();
        $filePath = Yii::getAlias('@runtime/richtext/' . $id . '.rtf');
        $content = file_exists($filePath) ? file_get_contents($filePath) : '';

        return $this->render('_detail', [
            'richtextTab' => $richtextTab,
            'content' => $content,
        ]);
    }

}