<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/font.css',
        'css/font-awesome.min.css',
        'css/font-awesome.css',
        'css/scrollbar.css',
        'css/bootstrap.css',
        'css/style.css',
        'css/responsive.css',
    ];
    public $js = [
        'js/libs/bootstrap.bundle.min.js',
        'js/libs/simplebar.js',
        'js/libs/custom.js',
        'js/libs/sidebar-menu.js',
        'js/libs/bootstrap-notify.min.js',
        'js/libs/custom-notify.js',
        'js/libs/script.js',
        'js/libs/jquery-ui.js',
        'js/libs/sweet-alert.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
