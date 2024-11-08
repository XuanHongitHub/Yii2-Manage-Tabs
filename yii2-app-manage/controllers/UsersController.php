<?php

namespace app\controllers;

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
            throw new NotFoundHttpException("User does not exist.");
        }

        if (Yii::$app->request->isPost) {
            $status = Yii::$app->request->post('status') ? 10 : 9;
            $user->status = $status;

            $user->role = Yii::$app->request->post('role');

            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'User updated successfully.');
            } else {
                Yii::error($user->getErrors());
                Yii::$app->session->setFlash('error', 'Update failed. Please try again.');
            }
        }

        return $this->redirect(['index']);
    }
}