<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\ChangePasswordForm;
use app\models\ContactForm;
use app\models\User;
use app\models\Page;

use yii\db\Exception;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'login', 'signup'], // Thêm 'index' vào đây
                'rules' => [
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'], // Cho phép người dùng chưa đăng nhập
                    ],
                    [
                        'actions' => ['logout', 'index'], // Cả 'logout' và 'index' yêu cầu đăng nhập
                        'allow' => true,
                        'roles' => ['@'], // Chỉ cho phép người đã đăng nhập
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'], // Không cho phép người chưa đăng nhập
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
        $pages = Page::find()
            ->andWhere(['deleted' => 0])
            ->orderBy([
                'position' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->all();

        // $tableTabs = TableTab::find()->all();

        return $this->render('index', [
            'pages' => $pages,
            // 'tableTabs' => $tableTabs,
        ]);
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
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Action SignUp
     *
     * @return string
     */
    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Đăng ký thành công.');

            return $this->redirect(['login']);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }
    /**
     * Action để hiển thị trang đổi mật khẩu.
     *
     * @return string
     */
    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();

        // Kiểm tra xem người dùng đã gửi form hay chưa
        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            Yii::$app->session->setFlash('success', 'Your password has been changed.');
            return $this->goBack();
        }

        return $this->render('change-password', [
            'model' => $model,
        ]);
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
}
