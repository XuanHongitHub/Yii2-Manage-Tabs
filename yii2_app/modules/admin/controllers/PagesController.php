<?php

namespace app\modules\admin\controllers;

use app\models\BaseModel;
use app\models\Config;
use app\models\PageSearch;
use yii\db\Query;
use yii\web\Controller;
use Yii;
use app\models\User;
use yii\web\Response;
use app\models\Page;
use app\models\Menu;
use app\modules\admin\components\BaseAdminController;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use Exception;
use yii\filters\AccessControl;


class PagesController extends BaseAdminController
{
    public function actionIndex()
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = [
            'pageSize' => 10,
        ];

        $dataProvider->query->orderBy(['id' => SORT_ASC]);

        $trashQuery = clone $dataProvider->query;
        $trashQuery->andWhere(['deleted' => 1]);

        $trashDataProvider = new ActiveDataProvider([
            'query' => $trashQuery,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $menus = Menu::find()->all();

        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('index', [
                'dataProvider' => $dataProvider,
                'trashDataProvider' => $trashDataProvider,
                'menus' => $menus,
                'searchModel' => $searchModel,
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'trashDataProvider' => $trashDataProvider,
            'menus' => $menus,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionCreate()
    {
        return $this->render('create');
    }
    public function actionGetTablePage($pageId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $page = Page::findOne(['id' => $pageId]);

        if (!$page) {
            return $this->asJson(['success' => false, 'message' => 'Page không tồn tại.']);
        }

        $tableName = $page->table_name;
        $model = BaseModel::withTable($tableName);

        $tableSchema = Yii::$app->db->schema->getTableSchema($model::tableName());
        if (!$tableSchema) {
            return $this->asJson(['success' => false, 'message' => 'Bảng không tồn tại.']);
        }

        $allColumns = array_keys($tableSchema->columns);
        $allColumns = array_filter($allColumns, function ($column) {
            return $column !== BaseModel::HIDDEN_ID_KEY;
        });

        $configColumns = Config::find()
            ->where(['page_id' => $pageId, 'menu_id' => null])
            ->orderBy(['column_position' => SORT_ASC])
            ->all();

        $columns = [];
        $configuredColumns = [];

        foreach ($configColumns as $config) {
            $configuredColumns[$config->column_name] = [
                'column_name' => $config->column_name,
                'display_name' => $config->display_name ?? $config->column_name,
                'is_visible' => $config->is_visible,
                'column_position' => $config->column_position,
            ];
        }

        foreach ($configuredColumns as $column) {
            $columns[] = $column;
        }

        foreach ($allColumns as $column) {
            if (!isset($configuredColumns[$column])) {
                $columns[] = [
                    'column_name' => $column,
                    'display_name' => $column,
                    'is_visible' => true,
                    'column_position' => null,
                ];
            }
        }

        usort($columns, function ($a, $b) {
            return $a['column_position'] <=> $b['column_position'];
        });

        return $this->asJson([
            'success' => true,
            'columns' => $columns,
        ]);
    }

    public function actionSaveColumnsConfig()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $columnsConfig = Yii::$app->request->post('columns_config');
        $pageId = Yii::$app->request->post('pageId');

        if (!$columnsConfig || !$pageId) {
            return ['success' => false, 'message' => 'Dữ liệu không hợp lệ.'];
        }

        foreach ($columnsConfig as $index => $columnConfig) {
            $columnName = $columnConfig['column_name'];
            $isVisible = filter_var($columnConfig['is_visible'], FILTER_VALIDATE_BOOLEAN);
            $displayName = $columnConfig['display_name'];
            $columnPosition = $index;

            $config = Config::findOne([
                'column_name' => $columnName,
                'menu_id' => null,
                'page_id' => $pageId
            ]) ?? new Config();

            $config->column_name = $columnName;
            $config->menu_id = null;
            $config->page_id = $pageId;
            $config->is_visible = $isVisible;
            $config->display_name = $displayName;
            $config->column_position = $columnPosition;

            if (!$config->save()) {
                return [
                    'success' => false,
                    'message' => 'Không thể lưu tùy chỉnh.',
                    'errors' => $config->getErrors()
                ];
            }
        }

        return ['success' => true, 'message' => 'Cập nhật tùy chỉnh cột thành công.'];
    }
    public function actionGetTableName()
    {
        $allTables = Yii::$app->db->createCommand("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->queryAll();

        $excludedTables = ['manager_page', 'manager_user', 'manager_menu', 'manager_menu_page', 'manager_config', 'migration'];

        // $usedTablesInManagerPage = Yii::$app->db->createCommand("SELECT DISTINCT table_name FROM manager_page")->queryColumn();

        // $allExcludedTables = array_merge($excludedTables, $usedTablesInManagerPage);

        $validTables = array_filter($allTables, function ($table) use ($excludedTables) {
            return !in_array(strtolower($table['table_name']), $excludedTables);
        });

        $tables = array_map(function ($table) {
            return $table['table_name'];
        }, $validTables);

        return $this->asJson($tables);
    }


    public function actionStore()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->user->id;
            $pageType = Yii::$app->request->post('type');
            $pageName = Yii::$app->request->post('pageName');
            $tableName = Yii::$app->request->post('tableName');
            $content = Yii::$app->request->post('content');

            Yii::error("Dữ liệu nhận từ form: type={$pageType}, pageName={$pageName}, tableName={$tableName}", 'application');

            $existingPage = Page::find()->where(['name' => $pageName, 'user_id' => $userId])->exists();
            if ($existingPage) {
                Yii::$app->session->setFlash('error', 'Tên trang đã tồn tại. Vui lòng chọn tên khác.');
                return $this->redirect(['create']);
            }

            $tableExists = Yii::$app->db->createCommand("SELECT to_regclass(:tableName)", [':tableName' => $tableName])->queryScalar();

            if ($pageType === 'table') {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$tableExists) {
                        $columns = Yii::$app->request->post('columns', []);
                        $dataTypes = Yii::$app->request->post('data_types', []);
                        $dataSizes = Yii::$app->request->post('data_sizes', []);
                        $isNotNull = Yii::$app->request->post('is_not_null', []);
                        $isPrimary = Yii::$app->request->post('is_primary', []);

                        $createTableQuery = "CREATE TABLE \"$tableName\"";
                        $columnDefs = [];
                        foreach ($columns as $index => $column) {
                            $columnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
                            $dataType = strtoupper($dataTypes[$index]);
                            $dataSize = isset($dataSizes[$index]) ? "($dataSizes[$index])" : '';
                            $isNotNullColumn = isset($isNotNull[$index]) && $isNotNull[$index] == '1' ? 'NOT NULL' : 'NULL';

                            if ($dataType === 'VARCHAR' || $dataType === 'CHAR') {
                                $columnDef = isset($isPrimary[$index]) && $isPrimary[$index] == '1'
                                    ? "\"$columnName\" SERIAL PRIMARY KEY"
                                    : "\"$columnName\" $dataType$dataSize $isNotNullColumn";
                            } elseif (in_array($dataType, ['SERIAL', 'BIGINT', 'SMALLINT', 'FLOAT', 'DOUBLE', 'DECIMAL'])) {
                                $columnDef = isset($isPrimary[$index]) && $isPrimary[$index] == '1'
                                    ? "\"$columnName\" SERIAL PRIMARY KEY"
                                    : "\"$columnName\" $dataType $isNotNullColumn";
                            } else {
                                $columnDef = isset($isPrimary[$index]) && $isPrimary[$index] == '1'
                                    ? "\"$columnName\" SERIAL PRIMARY KEY"
                                    : "\"$columnName\" $dataType $isNotNullColumn";
                            }

                            $columnDefs[] = $columnDef;
                        }

                        $createTableQuery .= ' (' . implode(', ', $columnDefs) . ')';
                        Yii::$app->db->createCommand($createTableQuery)->execute();
                        Yii::$app->session->setFlash('success', 'Bảng mới đã được tạo thành công.');
                    } else {
                        Yii::$app->session->setFlash('info', 'Bảng đã tồn tại. Chỉ thêm thông tin page.');
                    }

                    $page = new Page();
                    $page->user_id = $userId;
                    $page->type = $pageType;
                    $page->name = $pageName;
                    $page->table_name = $tableName;
                    $page->created_at = date('Y-m-d H:i:s');
                    $page->updated_at = date('Y-m-d H:i:s');

                    if ($page->save()) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Page đã được tạo thành công!');
                        return $this->redirect(['create', 'id' => $page->id]);
                    } else {
                        throw new \Exception('Không thể lưu thông tin page.');
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
                    return $this->redirect(['create']);
                }
            } elseif ($pageType === 'richtext') {
                $page = new Page();
                $page->user_id = $userId;
                $page->type = $pageType;
                $page->name = $pageName;
                $page->table_name = $tableName;
                $page->content = $content;

                if ($page->save()) {
                    Yii::$app->session->setFlash('success', 'Tạo page thành công!');
                    return $this->redirect(['create']);
                } else {
                    Yii::$app->session->setFlash('error', 'Đã xảy ra lỗi khi tạo page. Vui lòng thử lại.');
                    return $this->redirect(['create']);
                }
            }
        }

        return $this->render('create');
    }



    public function actionCheckNameExistence()
    {
        $pageName = Yii::$app->request->post('pageName');
        $tableName = Yii::$app->request->post('tableName');

        $pageExists = Page::find()->where(['name' => $pageName])->exists();

        $tableExists = Yii::$app->db->createCommand("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = :tableName)")
            ->bindValue(':tableName', $tableName)
            ->queryScalar();

        return $this->asJson([
            'pageExists' => $pageExists,
            'tableExists' => $tableExists
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    protected function findModel($id)
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /** 
     * Delete Page Action.
     *
     */
    public function actionDeletePage()
    {
        $postData = Yii::$app->request->post();

        if (isset($postData['pageId'])) {
            $pageId = $postData['pageId'];

            $affectedRows = Page::updateAll(
                ['deleted' => 1],
                ['id' => $pageId]
            );

            if ($affectedRows > 0) {
                Yii::$app->session->setFlash('success', 'Xóa mềm thành công.');
                return $this->asJson(['success' => true, 'message' => 'Xóa mềm thành công.']);
            } else {
                Yii::$app->session->setFlash('error', 'Không có bản ghi nào được cập nhật.');
                return $this->asJson(['success' => false, 'message' => 'Không có bản ghi nào được cập nhật.']);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Thiếu pageId.');
            return $this->asJson(['success' => false, 'message' => 'Thiếu pageId.']);
        }
    }

    /** 
     * Update Restore Action.
     *
     */
    public function actionRestorePage()
    {
        $postData = Yii::$app->request->post();

        if (isset($postData['pageId'])) {
            $pageId = $postData['pageId'];

            $affectedRows = Page::updateAll(
                ['deleted' => 0],
                ['id' => $pageId]
            );

            if ($affectedRows > 0) {
                Yii::$app->session->setFlash('success', 'Khôi phục thành công.');
                return $this->asJson(['success' => true, 'message' => 'Khôi phục thành công.']);
            } else {
                Yii::$app->session->setFlash('error', 'Không có bản ghi nào được cập nhật.');
                return $this->asJson(['success' => false, 'message' => 'Không có bản ghi nào được cập nhật.']);
            }
        } else {
            Yii::$app->session->setFlash('error', 'Thiếu pageId.');
            return $this->asJson(['success' => false, 'message' => 'Thiếu pageId.']);
        }
    }

    /** 
     * Delete Permanently Page Action.
     *
     */
    public function actionDeletePermanentlyPage()
    {
        $postData = Yii::$app->request->post();

        $page = Page::find()->where(['id' => $postData['pageId']])->one();

        if (!$page) {
            Yii::$app->session->setFlash('error', 'Page không tồn tại.');
            return $this->asJson(['success' => false, 'message' => 'Page không tồn tại.']);
        }
        if ($page->type == 'table') {
            $tableName = $page->table_name;

            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
                Yii::$app->session->setFlash('error', 'Tên bảng không hợp lệ.');
                return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
            }


            $transaction = Yii::$app->db->beginTransaction();

            try {
                Yii::$app->db->createCommand()->dropTable($tableName)->execute();

                $page->delete();

                Yii::$app->session->setFlash('success', 'Bảng và dữ liệu đã được xóa thành công.');
                $transaction->commit();
                return $this->asJson(['success' => true, 'message' => 'Bảng và dữ liệu đã được xóa thành công.']);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                $transaction->rollBack();
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        } elseif ($page->type == 'richtext') {
            try {
                $page->delete();

                Yii::$app->session->setFlash('success', 'Dữ liệu richtext đã được xóa thành công.');
                return $this->asJson(['success' => true, 'message' => 'Dữ liệu richtext đã được xóa thành công.']);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        Yii::$app->session->setFlash('error', 'Loại page không hợp lệ.');
        return $this->asJson(['success' => false, 'message' => 'Loại page không hợp lệ.']);
    }

    /** 
     * Update Postion Action.
     *
     */
    public function actionUpdateSortOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $pages = Yii::$app->request->post('pages');

        if ($pages) {
            foreach ($pages as $page) {
                $model = Page::findOne($page['id']);
                if ($model) {
                    $model->position = $page['position'];
                    if (!$model->save()) {
                        Yii::$app->session->setFlash('error', 'Không thể lưu page với ID: ' . $page['id']);
                        return [
                            'success' => false,
                            'message' => 'Không thể lưu page với ID: ' . $page['id'],
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
     * Update Show/Hide Page Action.
     *
     */
    public function actionUpdateHideStatus()
    {
        $hideStatus = Yii::$app->request->post('hideStatus', []);

        foreach ($hideStatus as $pageId => $status) {
            $page = Page::findOne($pageId);
            if ($page) {
                $page->status = $status;
                $page->save();
            }
        }

        Yii::$app->session->setFlash('success', 'Trạng thái ẩn/hiện đã được cập nhật thành công.');
        return $this->asJson(['success' => true]);
    }


    /** 
     * Update Page Action.
     *
     */
    public function actionUpdatePage()
    {
        $pageId = Yii::$app->request->post('pageId');
        $pageName = Yii::$app->request->post('pageName');
        $status = Yii::$app->request->post('status');

        $page = Page::findOne($pageId);
        if ($page) {
            $page->status = $status == 1 ? 1 : 0;
            $page->name = $pageName;
            $page->save();
            Yii::$app->session->setFlash('success', 'Page đã được cập nhật thành công.');
            return json_encode(['status' => 'success']);
        }

        Yii::$app->session->setFlash('error', 'Không tìm thấy page.');
        return json_encode(['status' => 'error']);
    }
    /** 
     * Edit RichtextData Action.
     *
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $page = Page::findOne(['id' => $id]);
        if (!$page) {
            throw new NotFoundHttpException('Page không tồn tại.');
        }

        return $this->render('edit', [
            'page' => $page,
            'content' => $page->content,
        ]);
    }
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $page = Page::findOne(['id' => $id]);
        if (!$page) {
            throw new NotFoundHttpException('Page không tồn tại.');
        }

        return $this->render('view', [
            'page' => $page,
            'content' => $page->content,
        ]);
    }
    /** 
     * Update RichtextData Action.
     *
     */
    public function actionSaveRichText()
    {
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $content = Yii::$app->request->post('content');

            $page = Page::findOne(['id' => $id]);

            if (!$page) {
                throw new NotFoundHttpException('Page không tồn tại.');
            }
            $page->content = $content;

            if ($page->save()) {
                return json_encode(['status' => 'success', 'message' => 'Nội dung đã được cập nhật thành công.']);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Đã xảy ra lỗi khi cập nhật nội dung.']);
            }
        }

        return json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
    }
}