<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;
use app\models\User;
use yii\web\Response;
use app\models\Page;
use app\models\Menu;
use yii\web\NotFoundHttpException;
use yii\web\Exception;
use yii\filters\AccessControl;


class PagesController extends Controller
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
        $pages = Page::find()
            ->orderBy([
                'position' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->all();

        $menus = Menu::find()->all();
        return $this->render('index', [
            'pages' => $pages,
            'menus' => $menus,
        ]);
    }
    public function actionCreate()
    {
        $pages = Page::find()
            ->select(['name', 'table_name'])
            ->asArray()
            ->all();

        return $this->render('create', ['pages' => $pages]);
    }

    public function actionStore()
    {
        if (Yii::$app->request->isPost) {
            $userId = Yii::$app->user->id;
            $pageType = Yii::$app->request->post('type');
            $pageName = Yii::$app->request->post('pageName');
            $tableName = Yii::$app->request->post('tableName');
            Yii::error("Dữ liệu nhận từ form: type={$pageType}, pageName={$pageName}, tableName={$tableName}", 'application');

            $page = new Page();
            $page->user_id = $userId;
            $page->type = $pageType;
            $page->name = $pageName;
            $page->table_name = $tableName;
            $page->created_at = date('Y-m-d H:i:s');
            $page->updated_at = date('Y-m-d H:i:s');

            if ($pageType === 'table') {
                $columns = Yii::$app->request->post('columns', []);
                $dataTypes = Yii::$app->request->post('data_types', []);
                $dataSizes = Yii::$app->request->post('data_sizes', []);
                $isNotNull = Yii::$app->request->post('is_not_null', []);
                $isPrimary = Yii::$app->request->post('is_primary', []);

                Yii::info("Dữ liệu table: columns=" . implode(', ', $columns) . ", dataTypes=" . implode(', ', $dataTypes), 'application');
                foreach ($isPrimary as $index => $primary) {
                    if (isset($isPrimary[$index]) && $isPrimary[$index] == '1') {
                        $isNotNull[$index] = '1';
                    }
                }

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($page->save()) {
                        // Tạo câu lệnh CREATE TABLE cho PostgreSQL
                        $createTableQuery = "CREATE TABLE \"$tableName\"";
                        $columnDefs = [];
                        foreach ($columns as $index => $column) {
                            $columnName = preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
                            $dataType = strtoupper($dataTypes[$index]);
                            $dataSize = isset($dataSizes[$index]) ? "($dataSizes[$index])" : '';
                            $isNotNullColumn = isset($isNotNull[$index]) && $isNotNull[$index] == '1' ? 'NOT NULL' : 'NULL';

                            // Xử lý kiểu dữ liệu và khóa chính
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

                        Yii::$app->session->setFlash('success', 'Tạo bảng thành công.');
                        $transaction->commit();

                        return $this->redirect(['create', 'id' => $page->id]);
                    } else {
                        throw new \Exception('Không thể tạo page.');
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
                    return $this->redirect(['create']);
                }
            } elseif ($pageType === 'richtext') {

                $existingTab = Page::findOne(['name' => $tableName, 'type' => 'richtext', 'user_id' => $userId]);
                if ($existingTab) {
                    Yii::$app->session->setFlash('error', 'Tên page đã tồn tại. Vui lòng chọn tên khác.');
                    return $this->redirect(['create']);
                }

                if ($page->save()) {
                    $filePath = Yii::getAlias('@runtime/richtext/' . $page->id . '.txt');
                    try {
                        file_put_contents($filePath, '');
                        Yii::$app->session->setFlash('success', 'Tạo page thành công!');
                    } catch (\Exception $e) {
                        Yii::error('Không thể tạo file: ' . $e->getMessage());
                        Yii::$app->session->setFlash('error', 'Đã xảy ra lỗi khi lưu file.');
                    }
                    return $this->redirect(['create']);
                } else {
                    Yii::$app->session->setFlash('error', 'Đã xảy ra lỗi khi tạo page. Vui lòng thử lại.');
                    return $this->redirect(['create']);
                }
            }
        }
        return $this->render('create', [
            'tableTabs' => [],
        ]);
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
    public function actionDeleteTab()
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
    public function actionRestoreTab()
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
    public function actionDeletePermanentlyTab()
    {
        $postData = Yii::$app->request->post();

        $pageId = $postData['pageId'];

        $page = Page::find()->where(['id' => $pageId])->one();

        if (!$page) {
            Yii::$app->session->setFlash('error', 'Page không tồn tại.');
            return $this->asJson(['success' => false, 'message' => 'Page không tồn tại.']);
        }
        if ($page->type == 'table') {
            $tableName = $postData['tableName'];

            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tableName)) {
                Yii::$app->session->setFlash('error', 'Tên bảng không hợp lệ.');
                return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
            }
            $sql = "DROP TABLE IF EXISTS `$tableName`";

            try {
                Yii::$app->db->createCommand($sql)->execute();

                $tableTabTable = 'table_tab';
                $deleteTabSql = "DELETE FROM `$tableTabTable` WHERE `pageId` = :pageId";
                Yii::$app->db->createCommand($deleteTabSql)->bindValue(':pageId', $pageId)->execute();

                $tabTable = 'page';
                $deleteTabRecordSql = "DELETE FROM `$tabTable` WHERE `id` = :pageId";
                Yii::$app->db->createCommand($deleteTabRecordSql)->bindValue(':pageId', $pageId)->execute();

                Yii::$app->session->setFlash('success', 'Bảng và dữ liệu đã được xóa thành công.');
                return $this->asJson(['success' => true, 'message' => 'Bảng và dữ liệu đã được xóa thành công.']);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
            }
        } elseif ($page->type == 'richtext') {
            try {
                $filePath = Yii::getAlias('@runtime/richtext/' . $pageId . '.txt');

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $tabTable = 'page';
                $deleteTabRecordSql = "DELETE FROM `$tabTable` WHERE `id` = :pageId";
                Yii::$app->db->createCommand($deleteTabRecordSql)->bindValue(':pageId', $pageId)->execute();

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
        $menuId = Yii::$app->request->post('menuId');
        $status = Yii::$app->request->post('status');
        $position = Yii::$app->request->post('position');

        $page = Page::findOne($pageId);
        if ($page) {
            $page->menu_id = $menuId;
            $page->status = $status == 1 ? 1 : 0;
            $page->position = $position;
            $page->save();
            Yii::$app->session->setFlash('success', 'Page đã được cập nhật thành công.');
            return json_encode(['status' => 'success']);
        }

        Yii::$app->session->setFlash('error', 'Không tìm thấy page.');
        return json_encode(['status' => 'error']);
    }
}