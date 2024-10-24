<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\Tab;
use app\models\TableTab;
use app\models\RichtextTab;
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
                        'roles' => ['@'], // Chỉ cho phép người dùng đã đăng nhập
                    ],
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'], // Chỉ cho phép khách (không đăng nhập)
                    ],
                    [
                        'actions' => ['index', 'about', 'contact', 'logout'],
                        'allow' => true,
                        'roles' => ['@'], // Chỉ cho phép người dùng đã đăng nhập
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

    public function actionSettings()
    {
        return $this->render('settings');
    }

    public function actionManageTabs()
    {

        $tabs = Tab::find()->all();
        $tableTabs = TableTab::find()->all();
        $richtextTabs = RichtextTab::find()->all();
        return $this->render('manage-tabs', [
            'tabs' => $tabs,
            'tableTabs' => $tableTabs,
            'richtextTabs' => $richtextTabs,
        ]);
    }
    public function actionLoadTabData($tab_id)
    {
        $tableTab = TableTab::find()->where(['tab_id' => $tab_id])->one();
        $tableName = $tableTab ? $tableTab->table_name : null;

        $charsetInfo = Yii::$app->db->createCommand("SHOW TABLE STATUS LIKE '$tableName'")->queryOne();
        $collation = $charsetInfo['Collation'] ?? 'Không xác định';
        $collation = $charsetInfo['Collation'] ?? 'Không xác định';

        if ($tableName) {
            $columns = Yii::$app->db->schema->getTableSchema($tableName)->columns;
            $data = Yii::$app->db->createCommand("SELECT * FROM `$tableName`")->queryAll();

            return $this->renderPartial('_tableData', [
                'columns' => $columns,
                'data' => $data,
                'tableName' => $tableName,
                'collation' => $collation,
            ]);
        }

        return 'No data';
    }

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
        Yii::$app->response->format = Response::FORMAT_JSON;

        $tabId = Yii::$app->request->post('tab_id');
        $rowId = Yii::$app->request->post('row_id');
        $tableName = Yii::$app->request->post('table_name');

        try {
            Yii::$app->db->createCommand()->delete($tableName, ['id' => $rowId])->execute();
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
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
}