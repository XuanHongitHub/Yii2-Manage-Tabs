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
        // 'js/libs/ckeditor/ckeditor.js',
        // 'js/libs/ckeditor/adapters.js',
        // 'js/libs/ckeditor/styles.js',
        // 'js/libs/ckeditor/ckeditor.custom.js',

    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
