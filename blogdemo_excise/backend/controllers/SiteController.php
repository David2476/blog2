<?php
namespace backend\controllers;

use common\models\Adminuser;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\AdminLoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
    	//$this->layout="wx"; //布局为wx.php
    	//$this->layout=false;   //不使用布局
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) { //如果不是游客（已经登录过），则直接转到首页
            return $this->goHome();
        }

        $model = new AdminLoginForm();//AdminLoginForm()是一个用于后台的模型类，对应的是登录表单，也有一般模型类的属性，验证规则和业务逻辑
        if ($model->load(Yii::$app->request->post()) && $model->login()) { //前面是块赋值，后面是调用login方法，如果调用成功，表明验证规则通过了
            return $this->goBack();//登录成功，返回登录前的页面
        } else {
            return $this->render('login', [
                'model' => $model, //登录失败，登录页展示错误，让用户填写正确的用户名和密码
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
