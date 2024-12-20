<?php

namespace app\modules\admin\components;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class BaseAdminController  extends Controller
{

    public $layout = 'admin';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['delete-permanently-page'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest &&
                                Yii::$app->user->identity->role === User::ROLE_ADMIN;
                        },
                    ],
                    [
                        'allow' => false,
                        'actions' => ['delete-permanently-page'],
                        'roles' => ['@'],
                    ],
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
}