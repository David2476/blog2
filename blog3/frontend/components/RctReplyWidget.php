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

class RctReplyWidget extends Widget{
    public $recentComments; //前面第1步准备好的数组,注意这里设置属性要和页面传递的参数一致
    //public $tags; //仅为测试， 如果页面RctReplyWidget::widget把$tags传过来，本语句有定义了同名的属性，那下面就可以打印出相应的传过来的值，说明页面调用了这个widget类文件RctReplyWidget。

    public function init(){ //一般用来处理数据，这里我们已经准备好，可直接到run方法里渲染
        parent::init();

        //        var_dump($this->recentComments);
        //var_dump($this->tags);
//        exit();
    }

    public function run(){
        $commentString=''; //用来保存结果

        foreach($this->recentComments as $comment){
            $commentString.='<div class="post">'.'<div class="title">'.
                '<p style="color:#777777;font-style:italic;">'.nl2br($comment->content).'</p>'.
                '<p class="text"> <span class="glyphicon glyphicon-user" aria-hidden="true">
                                    </span> '.Html::encode($comment->user->username).'</p>'.
                '<p style="font-size:8pt;color:blue">《<a href="'.$comment->post->url.'">'.
                                    Html::encode($comment->post->title).'</a>》</p>'.'<hr></div></div>';
        }
        return $commentString;
    }


}
















