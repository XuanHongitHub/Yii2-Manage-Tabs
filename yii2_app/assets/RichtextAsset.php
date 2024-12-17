<?php

namespace app\assets;

use yii\web\AssetBundle;

class RichtextAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'js/libs/richtext/richtexteditor/rte_theme_default.css',
    ];
    public $js = [
        'js/libs/richtext/richtexteditor/rte.js',
        'js/libs/richtext/richtexteditor/plugins/all_plugins.js',
        'js/libs/richtext/richtexteditor/lang/rte-lang-vi.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}