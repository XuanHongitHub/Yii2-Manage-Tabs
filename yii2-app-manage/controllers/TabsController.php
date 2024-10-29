<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\Tab;
use app\models\TableTab;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Exception;

class TabsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'manage-users', 'about', 'contact', 'signup', 'login', 'logout'],
                'rules' => [
                    [
                        'actions' => ['index', 'about', 'manage-users'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'about', 'contact', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['admin'],
                        'allow' => true,
                        'roles' => ['@'], // Chỉ cho phép admin đã đăng nhập
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserAdmin(Yii::$app->user->identity->username); // Kiểm tra admin
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    /**
     * Displays Manage Tabs.
     *
     * @return string
     */
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

        $tableTabs = TableTab::find()->all();

        return $this->render('index', [
            'tabs' => $tabs,
            'tableTabs' => $tableTabs,
        ]);
    }

    /**
     * Displays Settings Page.
     *
     */
    public function actionSettings()
    {
        return $this->render('settings', [
            'users' => User::find()->all(),
        ]);
    }
    /**
     * Load Tab Data Action.
     *
     */
    public function actionLoadTabData($tab_id)
    {
        $tab = Tab::find()->where(['id' => $tab_id])->one();

        if ($tab === null) {
            return 'No data';
        }

        $tabType = $tab->tab_type;

        if ($tabType === 'table') {
            // Table Tab
            $tableTab = TableTab::find()->where(['tab_id' => $tab_id])->one();
            $tableName = $tableTab ? $tableTab->table_name : null;

            if ($tableName) {
                $charsetInfo = Yii::$app->db->createCommand("SHOW TABLE STATUS LIKE '$tableName'")->queryOne();
                $collation = $charsetInfo['Collation'] ?? 'Unknown';
                $columns = Yii::$app->db->schema->getTableSchema($tableName)->columns;
                $data = Yii::$app->db->createCommand("SELECT * FROM `$tableName`")->queryAll();

                return $this->renderPartial('_tableData', [
                    'columns' => $columns,
                    'data' => $data,
                    'tableName' => $tableName,
                    'collation' => $collation,
                ]);
            }
        } elseif ($tabType === 'richtext') {
            // Richtext Tab
            $filePath = Yii::getAlias('@runtime/richtext/' . $tab_id . '.txt');
            $content = file_exists($filePath) ? file_get_contents($filePath) : '';

            return $this->renderPartial('_richtextData', [
                'richtextTab' => $tab,
                'content' => $content,
                'filePath' => $filePath,
            ]);
        }

        return 'No data';
    }
    /** 
     * Update RichtextData Action.
     *
     */
    public function actionSaveRichtext()
    {
        if (Yii::$app->request->isPost) {
            $tabId = Yii::$app->request->post('tabId');
            $content = Yii::$app->request->post('content');

            $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.txt');
            try {
                file_put_contents($filePath, $content);
                return json_encode(['status' => 'success', 'message' => 'Content has been updated successfully.']);
            } catch (\Exception $e) {
                return json_encode(['status' => 'error', 'message' => 'An error occurred while updating the content.']);
            }
        }
        return json_encode(['status' => 'error', 'message ' => 'Invalid request.']);
    }
    /** 
     * Download RichtextData Action.
     *
     */
    public function actionDownload($tab_id)
    {
        $filePath = Yii::getAlias('@runtime/richtext/' . $tab_id . '.txt');

        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('File not found.');
        }
    }
    /** 
     * Update TableData Action.
     *
     */
    public function actionUpdateData()
    {
        $tableName = Yii::$app->request->post('table');
        $data = Yii::$app->request->post('data');
        $originalValues = Yii::$app->request->post('originalValues');

        if (isset($originalValues['id'])) {
            $whereCondition = "`id` = :original_id";
        } else {
            $whereCondition = '';
        }

        $setClause = [];
        foreach ($data as $column => $value) {
            $setClause[] = "`$column` = :$column";
        }
        $setCondition = implode(", ", $setClause);

        $sql = "UPDATE `$tableName` SET $setCondition" . ($whereCondition ? " WHERE $whereCondition" : "");
        $command = Yii::$app->db->createCommand($sql);

        foreach ($data as $column => $value) {
            $command->bindValue(":$column", $value === '' ? null : $value);
        }
        if (isset($originalValues['id'])) {
            $command->bindValue(":original_id", $originalValues['id']);
        }

        try {
            $command->execute();
            return $this->asJson(['success' => true]);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    /** 
     * Create TableData Action.
     *
     */
    public function actionAddData()
    {
        $tableName = Yii::$app->request->post('table');
        $data = Yii::$app->request->post('data');

        $validData = [];
        foreach ($data as $column => $value) {
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column) || is_numeric($column)) {
                $validData[$column] = $value === '' ? null : $value;
            }
        }

        if (empty($validData)) {
            return $this->asJson(['success' => false, 'message' => 'Không có cột hợp lệ để thêm dữ liệu.']);
        }

        $sql = "INSERT INTO `$tableName` (`" . implode("`, `", array_keys($validData)) . "`) VALUES (:" . implode(", :", array_keys($validData)) . ")";
        $command = Yii::$app->db->createCommand($sql);

        foreach ($validData as $column => $value) {
            $command->bindValue(":$column", $value);
        }

        try {
            $command->execute();
            return $this->asJson(['success' => true, 'redirect' => '/tabs']);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    /** 
     * Delete TableData Action.
     *
     */
    public function actionDeleteData()
    {
        $postData = Yii::$app->request->post();

        $table = $postData['table'];
        $conditions = isset($postData['conditions']) ? $postData['conditions'] : [];

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            return $this->asJson(['success' => false, 'message' => 'Tên bảng không hợp lệ.']);
        }

        $whereConditions = [];

        foreach ($conditions as $condition) {
            $tempConditions = [];

            foreach ($condition as $column => $value) {

                if (preg_match('/^[a-zA-Z_0-9][a-zA-Z0-9_]*$/', $column)) {
                    if ($value === '') {
                        $tempConditions[] = "`$column` IS NULL";
                    } else {
                        $tempConditions[] = "`$column` = '" . addslashes($value) . "'";
                    }
                } else {
                    Yii::error("Invalid column name: $column", __METHOD__);
                }
            }

            if (!empty($tempConditions)) {
                $whereConditions[] = '(' . implode(' AND ', $tempConditions) . ')';
            } else {
                Yii::error("No valid conditions for this set: " . json_encode($condition), __METHOD__);
            }
        }


        if (empty($whereConditions)) {
            $sql = "DELETE FROM `$table` WHERE ";
            $columns = array_keys($conditions[0]);
            $nullConditions = [];

            foreach ($columns as $column) {
                $nullConditions[] = "`$column` IS NULL";
            }

            $sql .= implode(' AND ', $nullConditions);
        } else {
            $sql = "DELETE FROM `$table` WHERE " . implode(' OR ', $whereConditions);
        }

        try {
            Yii::$app->db->createCommand($sql)->execute();
            return $this->asJson(['success' => true, 'message' => 'Successfully!']);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
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
}