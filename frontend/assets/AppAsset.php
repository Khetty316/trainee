<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle {

    CONST general_version = '0.2.7';

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css?v=' . self::general_version,
        'css/navbar.css?v=' . self::general_version,
        'css/Chart.css'
    ];
    public $js = [
        'js/my.js?v=' . self::general_version,
        'js/navbar.js?v=' . self::general_version,
        'js/chart.js',
        'js/chartjs-plugin-datalabels.js',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'rmrevin\yii\fontawesome\NpmFreeAssetBundle'
    ];

}
