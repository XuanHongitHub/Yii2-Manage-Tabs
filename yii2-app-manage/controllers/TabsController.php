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

    // Action Add Table
    public function actionAddTable()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $title = $request->post('title');
            $tabType = $request->post('tab_type');
            $columns = $request->post('columns');

            $tab = new Tab();
            $tab->user_id = Yii::$app->user->id;
            $tab->title = $title;
            $tab->tab_type = $tabType;
            $tab->deleted = 0;
            $tab->created_at = date('Y-m-d H:i:s');
            $tab->updated_at = date('Y-m-d H:i:s');

            if ($tab->save()) {
                $tableTab = new TableTab();
                $tableTab->tab_id = $tab->id;
                $tableTab->table_name = $title;
                $tableTab->columns = $columns;
                $tableTab->created_at = date('Y-m-d H:i:s');
                $tableTab->updated_at = date('Y-m-d H:i:s');

                if ($tableTab->save()) {
                    return $this->asJson(['success' => true, 'message' => 'Thêm tab và bảng thành công!']);
                } else {
                    return $this->asJson(['success' => false, 'message' => 'Không thể lưu vào bảng table_tab.']);
                }
            } else {
                return $this->asJson(['success' => false, 'message' => 'Không thể lưu tab.']);
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
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