<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/07/06
 * Time: 09:53
 */
namespace console\controllers;
use yii\console\Controller;
use yii\console\controllers;
use common\models\Post;

class HelloController extends Controller{

    public $rev;

    public function options($actionId){//括号里必须加$actionId
        return ['rev'];
    }

    public function optionAliases(){ //设置别名，简化工作
        return ['r'=>'rev'];
    }

    public function actionIndex(){
        if($this->rev==1){
            echo strrev("Hello World!")."\n";
        }else{
            echo "Hellow World!";
        }
    }

    /*
    public function actionIndex(){ //index是默认动作
        echo "Hello World! \n";
    }
    */

    public function actionList(){
        $posts=Post::find()->all();
        foreach($posts as $key=>$aPost){
           // echo $key."\n"; //键名0，1，...10。11条记录的索引
            echo ($aPost['id']." - ".iconv("utf-8","gb2312",$aPost['title'])."\n");
        }
    }

    public function actionWho($name){
        echo("Hello ".$name."!\n");
    }

    public function actionBoth($name,$another){
        echo("Hello ".$name." and ". $another ."!\n");
    }

    public function actionAll(array $names){
        var_dump($names);
    }

}













