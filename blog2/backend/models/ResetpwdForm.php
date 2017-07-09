<?php
namespace backend\models;

use yii\base\Model;
use common\models\Adminuser;
use yii\helpers\VarDumper;

/**
 * Signup form
 */
class ResetpwdForm extends Model  //class 后的名字一定要和类名一致，折腾
{
    public $password;
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['password_repeat','compare','compareAttribute'=>'password','message'=>'两次输入的密码不一致'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => '密码',
            'password_repeat' => '重输密码',
        ];
    }


    public function resetPassword($id)
    {
        if (!$this->validate()) {
            return null;
        }

        $user = Adminuser::findOne($id);
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

//        $user->save();
//        header('content-type:text/html;charset=utf-8');
//        VarDumper::dump($user->errors);
//        exit();

        $user->generatePasswordResetToken(); //根据上面调试语句发现错误提示为PasswordResetToken不能为空，特加此句
        $user->password=$this->password;  //加上此句才会修改数据库的password，前面只是改了hash. 当然如果不加此句数据库的password只条显示第一次给的，而每次修改的密码都体现在hash中，加此句就同步了。

        return $user->save() ?true : false;
    }
}
