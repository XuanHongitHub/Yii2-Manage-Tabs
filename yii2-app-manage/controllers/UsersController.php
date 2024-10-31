<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index', [
            'users' => User::find()->all(),
        ]);
    }
    public function actionUpdateRole($id)
    {
        $user = User::findOne($id);

        if ($user === null) {
            throw new NotFoundHttpException("User does not exist.");
        }

        if (Yii::$app->request->isPost) {
            $user->role = Yii::$app->request->post('role');

            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Role update successful.');
                return $this->redirect('index');
            } else {
                Yii::error($user->getErrors());
                Yii::$app->session->setFlash('error', 'Role update failed. Please try again.');
            }
        }

        return $this->render('index', [
            'user' => $user,
        ]);
    }

}