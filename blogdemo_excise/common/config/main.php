<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
  //  'language'=>'zh-CN', //自己测试加的，这里一变首页第一行显示也会变.但这里即便不设置此句在首页中写入中文，比如home换成首页也没问题，那么加这句有什么用处呢
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //RBAC 基于角色的授权管理
        'authManager'=>[
            'class'=>'yii\rbac\DbManager',
        ],

    ],
];
