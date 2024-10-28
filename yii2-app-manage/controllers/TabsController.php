<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\Tab;
use app\models\TableTab;
use app\models\SignupForm;
use app\models\ContactForm;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\db\Exception;
use yii\data\Pagination;

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
                'only' => ['index', 'manage-tabs', 'manage-users', 'about', 'contact', 'signup', 'login', 'logout'],
                'rules' => [
                    [
                        'actions' => ['index', 'about', 'manage-tabs', 'manage-users'],
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    // public function actionSettings()
    // {
    //     $users = User::find()->all(); // Lấy tất cả người dùng
    //     return $this->render('settings', [
    //         'users' => $users,
    //     ]);
    // }
    public function actionSettings($view = 'index')
    {
        // Lấy giá trị view từ tham số, mặc định là 'index'
        $contentView = $view;

        return $this->render('settings', [
            'contentView' => $contentView, // Truyền tên view
            'users' => User::find()->all(), // Các tham số khác cho view
        ]);
    }

    public function actionManageTabs()
    {
        $userId = Yii::$app->user->id;

        $tabs = Tab::find()
            ->where(['user_id' => $userId])
            ->andWhere(['in', 'deleted', [0, 3]])
            ->orderBy(['position' => SORT_ASC])
            ->all();

        $tableTabs = TableTab::find()->all();

        return $this->render('manage-tabs', [
            'tabs' => $tabs,
            'tableTabs' => $tableTabs,
        ]);
    }


    public function actionLoadTabData($tab_id, $tabType)
    {
        if ($tabType === 'table') {
            // Nếu loại tab là bảng
            $tableTab = TableTab::find()->where(['tab_id' => $tab_id])->one();
            $tableName = $tableTab ? $tableTab->table_name : null;

            if ($tableName) {
                $charsetInfo = Yii::$app->db->createCommand("SHOW TABLE STATUS LIKE '$tableName'")->queryOne();
                $collation = $charsetInfo['Collation'] ?? 'Không xác định';
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
            $richtextTab = Tab::find()->where(['id' => $tab_id])->one();
            $filePath = Yii::getAlias('@runtime/richtext/' . $tab_id . '.txt');
            $content = file_exists($filePath) ? file_get_contents($filePath) : '';

            return $this->renderPartial('_richtextData', [
                'richtextTab' => $richtextTab,
                'content' => $content,
                'filePath' => $filePath,
            ]);
        }

        return 'No data'; // Trả về thông báo nếu không tìm thấy dữ liệu
    }
    public function actionSaveRichtext()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $tabId = Yii::$app->request->post('tab_id');
            $content = Yii::$app->request->post('content');

            $filePath = Yii::getAlias('@runtime/richtext/' . $tabId . '.txt');
            try {
                file_put_contents($filePath, $content); // Cập nhật nội dung vào file
                return json_encode(['status' => 'success', 'message' => 'Nội dung đã được cập nhật thành công.']);
            } catch (\Exception $e) {
                Yii::error('Không thể cập nhật file: ' . $e->getMessage());
                return json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra khi cập nhật nội dung.']);
            }
        }
        return json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
    }
    public function actionDownload($tab_id)
    {
        $filePath = Yii::getAlias('@runtime/richtext/' . $tab_id . '.txt');

        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            throw new \yii\web\NotFoundHttpException('File not found.');
        }
    }

    public function actionUpdateData()
    {
        $tableName = Yii::$app->request->post('table');
        $data = Yii::$app->request->post('data');
        $originalValues = Yii::$app->request->post('originalValues');
        Yii::error("zzzz Table name: " . $tableName);

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

        // Yii::error($sql, __METHOD__);

        try {
            $command->execute();
            return $this->asJson(['success' => true]);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
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
            return $this->asJson(['success' => true, 'redirect' => '/tabs/manage-tabs']);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
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
                Yii::error("Checking condition: Column - $column, Value - " . json_encode($value), __METHOD__);

                // Cập nhật regex cho tên cột để cho phép bắt đầu bằng số
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

        Yii::error('Where conditions before SQL generation: ' . json_encode($whereConditions), __METHOD__);

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

        Yii::error('Generated SQL: ' . $sql, __METHOD__);

        try {
            Yii::$app->db->createCommand($sql)->execute();
            return $this->asJson(['success' => true, 'message' => 'Dữ liệu đã được xóa thành công.']);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function actionDeleteTable()
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


    public function actionDeletePermanentlyTable()
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
                Yii::error("Checking condition: Column - $column, Value - " . json_encode($value), __METHOD__);

                // Cập nhật regex cho tên cột để cho phép bắt đầu bằng số
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

        Yii::error('Where conditions before SQL generation: ' . json_encode($whereConditions), __METHOD__);

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

        Yii::error('Generated SQL: ' . $sql, __METHOD__);

        try {
            Yii::$app->db->createCommand($sql)->execute();
            return $this->asJson(['success' => true, 'message' => 'Dữ liệu đã được xóa thành công.']);
        } catch (\Exception $e) {
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function actionUpdateSortOrder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; // Định dạng phản hồi là JSON
        $tabs = Yii::$app->request->post('tabs');

        if ($tabs) {
            foreach ($tabs as $tab) {
                $model = Tab::findOne($tab['id']);
                if ($model) {
                    $model->position = $tab['position'];
                    if (!$model->save()) {
                        return [
                            'success' => false,
                            'message' => 'Không thể lưu tab với ID: ' . $tab['id'],
                        ];
                    }
                }
            }
            return ['success' => true];
        }

        return [
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.'
        ];
    }
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

    /**
     * Login action.
     *
     * @return Response|string
     */

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->loginAdmin()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    // public function actionSaveRichtext()
    // {
    //     $tabId = Yii::$app->request->post('tab_id');
    //     $content = Yii::$app->request->post('content');

    //     $richtextTab = Tab::find()->where(['id' => $tabId])->one();

    //     if ($richtextTab) {
    //         $richtextTab->content = $content;
    //         if ($richtextTab->save()) {
    //             return json_encode(['status' => 'success', 'message' => 'Nội dung đã được lưu thành công!']);
    //         } else {
    //             return json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra khi lưu nội dung.']);
    //         }
    //     }

    //     return json_encode(['status' => 'error', 'message' => 'Tab không tồn tại.']);
    // }
}