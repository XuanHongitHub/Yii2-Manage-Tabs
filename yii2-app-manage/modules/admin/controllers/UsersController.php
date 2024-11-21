<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class UsersController extends Controller
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
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserAdmin(Yii::$app->user->identity->username);
                        }
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
        return $this->render('index', [
            'users' => User::find()->all(),
        ]);
    }
    public function actionUpdateUser($id)
    {
        $user = User::findOne($id);

        if ($user === null) {
            throw new NotFoundHttpException("Người dùng không tồn tại.");
        }

        if (Yii::$app->request->isPost) {
            $status = Yii::$app->request->post('status') ? 10 : 9;
            $user->status = $status;

            $user->role = Yii::$app->request->post('role');

            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Cập nhật thông tin Người dùng thành công!');
            } else {
                Yii::error($user->getErrors());
                Yii::$app->session->setFlash('error', 'Cập nhật thất bại. Vui lòng thử lại.');
            }
        }

        return $this->redirect(['index']);
    }
}