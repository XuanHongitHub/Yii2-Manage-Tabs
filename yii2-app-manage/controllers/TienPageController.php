<?php

namespace app\controllers;

use app\models\Menu;
use app\models\Page;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TienPageController extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    public function actionIndex()
    {
        $menuId = \Yii::$app->request->get('menuId');

        $menu = Menu::findOne($menuId);

        if ($menu) {
            $pages = Page::find()
                ->where(['status' => 0, 'menu_id' => $menuId])
                ->orderBy(['position' => SORT_ASC, 'id' => SORT_DESC])
                ->all();


            return $this->render('menu', [
                'pages' => $pages,
            ]);
        }

        throw new NotFoundHttpException('Không tìm thấy dữ liệu phù hợp.');
    }
}