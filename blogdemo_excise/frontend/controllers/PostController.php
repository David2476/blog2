<?php

namespace frontend\controllers;

use Yii;
use common\models\Post;
use common\models\PostSearch;
use common\models\Tag;
use common\models\Comment;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\User;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    public $added=0; //0代表还没有新回复
    public $test1=0;//仅为测试

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

            'access'=>[
                'class'=>AccessControl::className(),
                'rules'=>[
                    [
                        'actions'=>['index'],
                        'allow'=>true,
                        'roles'=>['?'],
                    ],
                    [
                        'actions'=>['index','detail'],
                        'allow'=>true,
                        'roles'=>['@'],
                    ],
                ],
            ],

            'pageCache'=>[
                'class'=>'yii\filters\PageCache',
                'only'=>['index'],
                'duration'=>600,
                'variations'=>[
                    yii::$app->request->get('page'),
                    yii::$app->request->get('PostSearch'),
                    yii::$app->user->isGuest,  //main-local.php中已关闭了Crsf验证,本语句针对退出当前页面时出现缓存的问题。

                ],
                'dependency'=>[  //
                    'class'=>'yii\caching\DbDependency',
                    'sql'=>'select count(*) from post',
                ],
            ],

            'httpCache'=>[
                'class'=>'yii\filters\HttpCache',
                'only'=>['detail'],
                'lastModified'=>function($action,$params){
                    $q=new \yii\db\Query();
                    return $q->from('post')->max('update_time');
                },
                'etagSeed'=>function($action,$params){
                    $post=$this->findModel(Yii::$app->request->get('id')); //获取文章内容
                    $commentCount=Comment::find()->where(['post_id'=>Yii::$app->request->get('id'),'status'=>2])->count(); //起作用了
                    return serialize([$post->title,$post->content,$commentCount]);
                 },
               //  'cacheControlHeader'=>'public,max-age=600',
            ]

        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        //下面这段测试encode前后有何区别，
//        $string = '<script>alert(1);</script>';
////        echo $string;
//        echo Html::encode($string);
//        exit();
        $tags=Tag::findTagWeights();
        $recentComments=Comment::findRecentComments();

        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tags'=>$tags,
            'recentComments'=>$recentComments,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
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
        $model = new Post();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
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
        $model = $this->findModel($id);

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

    public function actionDetail($id){
        //step1. 准备数据模型
        $model=$this->findModel($id);
        $tags=Tag::findTagWeights();
        $recentComments=Comment::findRecentComments();

        $userMe=\common\models\User::findOne(Yii::$app->user->id);
        $commentModel=new Comment();
        $commentModel->email=$userMe->email;
        $commentModel->userid=$userMe->id;

        //step2. 当评论提交时，处理评论, 完成这段后，把$this->added在step3中传给视图
        if($commentModel->load(Yii::$app->request->post())){ //Yii::$app->request相当于$_POST
            $commentModel->status=1;//新评论默认状态为 pending 也就是待审核
            $commentModel->post_id=$id;
            if($commentModel->save()){
                $this->added=1;
            }
        }

        //step3. 传数据给视图渲染
        return $this->render('detail',[
            'model'=>$model,
            'tags'=>$tags,
            'recentComments'=>$recentComments,
            'commentModel'=>$commentModel,
            'added'=>$this->added, //完成step2后，加此句
        ]);
    }
}
