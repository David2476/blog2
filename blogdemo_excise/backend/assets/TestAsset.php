<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/07/05
 * Time: 21:22
 */

namespace backend\assets;


use yii\web\AssetBundle;

class TestAsset extends AssetBundle
{
    public $basePath='@webroot';
    public $baseUrl='@web';
    public $css=[
      'css/site_test.css'
    ];
}