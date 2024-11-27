<?php

namespace app\assets;

use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/select2.min.css',
    ];
    public $js = [
        'js/jquery.min.js',
        'js/select2.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}