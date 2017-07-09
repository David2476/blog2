<?php
namespace backend\models;

use yii\base\Model;
use common\models\Adminuser;
use yii\helpers\VarDumper;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $nickname;
    public $email;
    public $password;
    public $password_repeat;
    public $profile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\Adminuser', 'message' => '该用户名已存在.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\Adminuser', 'message' => '该邮箱地址已存在.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['password_repeat','compare','compareAttribute'=>'password','message'=>'两次输入的密码不一致'],
            ['nickname','required'],
            ['nickname','string','max'=>128],
            ['profile','string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'nickname' => '昵称',
            'password' => '密码',
            'password_repeat' => '重输密码',
            'email' => 'Email',
            'profile' => '简介',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new Adminuser();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->nickname=$this->nickname;
        $user->profile=$this->profile;
        $user->setPassword($this->password);
        $user->generateAuthKey();


        //以下两句根据错误提示添加，虽然这里没有，但不是不能为空，所以随便给个值
        $user->password=$this->password;
        $user->generatePasswordResetToken(); //随机产生


//        $user->save();
//        header('content-type:text/html;charset=utf-8');
//        VarDumper::dump($user->errors);
//        exit();

        return $user->save() ? $user : null;
    }
}
