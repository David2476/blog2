<?php
//文章类控制器
namespace frontend\controllers;

use common\models\CommentReply;
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
                        'actions'=>['index','detail','reply'],
                        'allow'=>true,
                        'roles'=>['@'],
                    ],
                ],
            ],
/*
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
*/
        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
//        $obj=new Post();
//        $a=$obj-> activeCommentReplies(227) ;
//        return $a;

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

    public function actionDetail($id,$content='',$reply_id=0)
    {

        //step1. 准备数据模型
        $postObj= new Post();
        $model = $this->findModel($id);
        $tags = Tag::findTagWeights();
        $recentComments = Comment::findRecentComments();
        //$recentCommentReplies = Comment::findRecentCommentReplies($reply_id);

        //把当前用户的资料传递给commmentModel对象
        $userMe = \common\models\User::findOne(Yii::$app->user->id);
        $commentModel = new Comment();
        $commentModel->email = $userMe->email;
        $commentModel->userid = $userMe->id;

        //step2.
        if ($reply_id == 0) { //当评论提交时，
            if ($commentModel->load(Yii::$app->request->post())) { //Yii::$app->request相当于$_POST
                $commentModel->status = 2;//新评论默认状态为 pending 也就是待审核，应该设为1，如果设为2，表示已审核，那页面会直接显示了
                $commentModel->post_id = $id;
                if ($commentModel->save()) {
                    $this->added = 1;
                }
            }
        } else { //当$reply_index!==0时，即是评论的回复时，**注意是评论的回复而不是评论
            // $id=$_GET['id'];
            $commentModel->status = 2;//新评论回复默认状态为 pending 也就是待审核，应该设为1，如果设为2，表示已审核，那页面会直接显示了
            $commentModel->post_id = $id;
            $commentModel->content = $content;

            //把对评论回复的回复都放到相应的评论里面，以方便显示
            $obj=$postObj->commentReplies($reply_id);//获取评论id为$reply_id的一条评论记录
            if($obj->reply_id==0){//此时是对评论的回复，而不是对评论回复的回复
                $commentModel->reply_id=$reply_id;
            }else{//此时是对评论回复的回复，要上追到相关的评论，也就是是说，这里的$commentModel->reply_id一定是评论的回复，不然在页面中无法显示出来，我们要把一个评论下各种回复（包括回复的回复）都放到改评论下。
                $commentModel->reply_id=$obj->reply_id;
            }
            if ($commentModel->save()) {
                $this->added = 1;
            }
        }


        //step3. 传数据给视图渲染
        return $this->render('detail', [
            'postObj'=>$postObj,
            'model' => $model,
            'tags' => $tags,
            'recentComments' => $recentComments,
           //'recentCommentReplies' => $recentCommentReplies,
            'commentModel' => $commentModel,
            'added' => $this->added, //完成step2后，加此句
        ]);

    }

    public function actionReply(){

          $id=$_GET['id'];
          $content=$_GET['content1'];
          $reply_id=$_GET['reply_id'];
//          exit('Reply');
        //$this->redirect(array('/post/detail','id'=>$id,'content'=>$content));

          return Yii::$app->runAction('post/detail',['id'=>$id,'content'=>$content,'reply_id'=>$reply_id]);

    }


}
