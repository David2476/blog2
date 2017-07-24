<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/07/04
 * Time: 17:25
 */
namespace frontend\components;

use yii\base\widget;
use yii\helpers\Html;
use Yii;

class TagsCloudWidget extends Widget{
    public $tags; //前面第1步准备好的数组

    public function init(){ //一般用来处理数据，这里我们已经准备好，可直接到run方法里渲染
        parent::init();
    }

    public function run(){
        $tagString=''; //用来保存结果
        // $fontStyle用来保存5个档次的大小和颜色
        $fontStyle=array("6"=>"danger",
                    "5"=>"info",
                    "4"=>"warning",
                    "3"=>"primary",
                    "2"=>"success",
        );

        //下面遍历中，根据每个标签的档次生成相应的html代码并累加
//        var_dump(Yii::$app->homeUrl); //string(40) "/blogdemo2_excise/frontend/web/index.php  根目录
//        exit();
        foreach($this->tags as $tag=>$weight){
        //注意空格，下面第2行中，' <h' ，' style...' 写成'<h' ，'style...'的话，格式就乱了，无法正确显示
            $tagString.='<a href="'.\Yii::$app->homeUrl.'?r=post/index&PostSearch[tags]='.$tag.'">'.
                ' <h'.$weight.' style="display:inline-block;"><span class="label label-'.$fontStyle[$weight].
                '">'.$tag.'</span></h'.$weight.'></a>';
        }
       // sleep(3); //测试片段缓存、页面缓存、http缓存使用
        return $tagString;
    }


}
















