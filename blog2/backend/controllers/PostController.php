<?php

namespace backend\controllers;

use Yii;
use common\models\Post;
use common\models\PostSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            //ACF:存取控制过滤器
            'access'=>[
                    'class'=>AccessControl::className(),
                    'rules'=>[
                        [ //这段里actions指'?'即角色为游客能够访问的动作
                            'actions'=>['login','error'],
                           // 'actions'=>['index','view'],
                            'allow'=>'true',
                            'roles'=>['?'],
                        ],
                        [ //这段里actions指'@'即角色为注册用户能够访问的动作
                            //'actions'=>['logout','index'],
                            'actions'=>['view','index','update','create','logout','delete'],
                            'allow'=>'true',
                            'roles'=>['@'],
                        ],
                    ],
            ],
        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
//     	header('content-type:text/html;charset=utf-8');
//     	$posts=yii::$app->db->createCommand('select * from post')->queryAll();
//     	var_dump($posts);
//     	exit(0);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        //检查权限
        if(!Yii::$app->user->can('createPost')){
            throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        }


        $model = new Post(); //new了一个空对象

        /* 下面两句代码和update动作中被屏蔽的那句类似代码都可以起到修改_form页面中创建时间和修改时间格式的作用，但是这样的业务逻辑写在控制器里不太理想，我们可以利用ActiveRecord的生命周期，重写其中的beforeSave()方法，把这段业务逻辑放到model中
        $model->create_time=time();
        $model->update_time=time();
        */

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model, //渲染页面时， $model这个空对象是没有数据的
            ]);
        }
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        //检查权限
        if(!Yii::$app->user->can('updatePost')){
            throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        }

        $model = $this->findModel($id);
//        $model->update_time=time();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        //检查权限
        if(!Yii::$app->user->can('deletePost')){
            throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
