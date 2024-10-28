<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    public function actionUpdateRole($id)
    {
        $user = User::findOne($id);

        if ($user === null) {
            throw new NotFoundHttpException("Người dùng không tồn tại.");
        }

        if (Yii::$app->request->isPost) {
            $user->role = Yii::$app->request->post('role');
            if ($user->save()) {
                return $this->redirect(['tabs/settings', 'activeTab' => 'userManagementTab']);

            } else {
                Yii::error($user->getErrors());
            }
        }

        return $this->redirect(['tabs/settings', 'activeTab' => 'userManagementTab']);
    }


}