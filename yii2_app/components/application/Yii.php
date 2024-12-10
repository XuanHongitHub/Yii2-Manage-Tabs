<?php
require(__DIR__ . '/../../vendor/yiisoft/yii2/BaseYii.php');

class Yii extends \yii\BaseYii
{
    /**
     * @var app\components\application\Application|yii\web\Application|\yii\console\Application the application instance
     */
    public static $app;
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require(__DIR__ . '/../../vendor/yiisoft/yii2/classes.php');
Yii::$container = new yii\di\Container();
