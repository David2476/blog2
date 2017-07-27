<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CommentAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/comment.css',
        'css/style.css',
    ];
    public $js = [
        'js/jquery-1.12.0.min.js',  //先加载jquery，再加载其它js,不然会报错
        'js/jquery.flexText.js',
//        'js/test.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
      //  'yii\web\JqueryAsset',
    ];
    public $jsOptions = [ //我靠，加上这段整个页面(post/detail)的代码部分都不显示了,不加等于默认把js文件加载在底部了，也不行
        'position' => \yii\web\View::POS_BEGIN
    ];
}
