<?php
/**
 * Created by PhpStorm. 评论短信提醒
 * User: Administrator
 * Date: 2017/06/30
 * Time: 11:19
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Comment;

class SmsController extends Controller
{
    public function actionSend()
    {  //查询未提醒的、未审核的评论数.注意这里外加一个条件'status'=>1，因为remind这个字段是后加的，默认为0，不加的话所有评论都会满足。
        //['remind'=>0,'status'=>1]意味着两者都要满足

        $newCommentCount=Comment::find()->where(['remind'=>0,'status'=>1])->count();

       if($newCommentCount>0){

           $content='有'.$newCommentCount.'条新评论待审核。';

           $result=$this->vendorSmsService($content);

           if($result['status']=='success'){

               Comment::updateAll(['remind'=>1]);//把提醒标志全部设为已提醒

               echo '['.date('Y-m-d H:i:s',$result['dt']).'] '.iconv("UTF-8", "gb2312", $content).'['.$result['length'].']'."\r\n";
           }
           return 0; //退出代码
       }
    }

    protected function vendorSmsService($content)
    {
        //实现第三方短信供应商提供的短信发送接口。

//             	$username = 'companyname';		//用户账号
//             	$password = 'pwdforsendsms';	//密码
//             	$apikey = '577d265efafd2d9a0a8c2ed2a3155ded7e01';	//密码
//             	$mobile	 = $adminuser->mobile;	//号手机码
//
//             	$url = 'http://sms.vendor.com/api/send/?';
//             	$data = array
//             	(
//             			'username'=>$username,				//用户账号
//             			'password'=>$password,				//密码
//             			'mobile'=>$mobile,					//号码
//             			'content'=>$content,				//内容
//             			'apikey'=>$apikey,				    //apikey
//             	);
//             	$result= $this->curlSend($url,$data);			//POST方式提交
//             	return $result;    //返回发送状态，发送时间，字节数等数据
//             	}

        $result=array("status"=>"success","dt"=>time(),"length"=>46);  //模拟数据
        return $result;

    }
}