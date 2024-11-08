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

        'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap',
        'https://fonts.googleapis.com/css?family=Righteous&display=swap',
        'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css',
        // 'css/font-awesome.css',
        // 'css/jquery.dataTables.min.css',
        'css/rte_theme_default.css',
        'css/datatables.css',
        'css/bootstrap.css',
        'css/style.css',
        'css/responsive.css',
    ];
    public $js = [
        'js/jquery.min.js',
        'js/bootstrap.bundle.min.js',
        'js/simplebar.js',
        'js/custom.js',
        'js/sidebar-menu.js',
        'js/bootstrap-notify.min.js',
        'js/jquery.dataTables.min.js',
        'js/datatable.custom.js',
        'js/datatable.custom1.js',
        'js/rte.js',
        'https://richtexteditor.com/richtexteditor/plugins/all_plugins.js',
        'js/custom-notify.js',
        'js/script.js',
        'js/jquery-ui.js',
    ];
    public $depends = [
        // 'yii\web\YiiAsset',
        // 'yii\bootstrap5\BootstrapAsset'
    ];
}