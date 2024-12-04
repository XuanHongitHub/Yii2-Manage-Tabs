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
        'js/components/frontend/richtextPage.js',
        'js/libs/rte.js',
        'js/libs/rte_all_plugins.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
