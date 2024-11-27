<?php

namespace app\assets;

use yii\web\AssetBundle;

class RichtextAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/rte_theme_default.css',
    ];
    public $js = [
        'js/rte.js',
        'https://richtexteditor.com/richtexteditor/plugins/all_plugins.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}