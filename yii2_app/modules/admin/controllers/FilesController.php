<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\BaseAdminController;
use yii\web\Controller;

class FilesController extends BaseAdminController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}