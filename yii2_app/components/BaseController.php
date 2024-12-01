<?php

namespace app\components;

use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class BaseController extends Controller
{

    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
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