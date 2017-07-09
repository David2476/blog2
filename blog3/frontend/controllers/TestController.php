<?php

namespace frontend\controllers;

use Yii;
use common\models\Post;
use common\models\PostSearch;
use common\models\Tag;
use common\models\Comment;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\User;
use yii\helpers\HtmlPurifier;

/**
 * PostController implements the CRUD actions for Post model.
 */
class TestController extends Controller
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

        //下面这段代码测试HTMLPurifier::process与Html::encode的区别：HTMLPurifier::process直接把js标签去掉了，Html::encode只是把HTML字符转换为Html实体，能显示出原有的标签，如script标签，但无法运行了（所以不会显示world）
        $dirty_html =' <h1>Hello</h1> <script>alert("world");</script> ';
        echo $dirty_html; // 显示：：Hello即js的world, 检查源码：<h1>Hello</h1> <script>alert("world");</script>
        echo "<hr>";
        echo  HtmlPurifier::process($dirty_html);;//显示：Hello， 检查源码：<h1>Hello</h1>  过滤掉了script标签，不会显示world
        echo "<hr>";
        echo Html::encode($dirty_html);  //显示：<h1>Hello</h1> <script>alert("world");</script>，检查源码：&lt;h1&gt;Hello&lt;/h1&gt; &lt;script&gt;alert(&quot;world&quot;);&lt;/script&gt; 从结果看Html::encode($dirty_html)与HTMLPurifier::process
        exit();

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

        //step2. 当评论提交时，处理评论
        if($commentModel->load(Yii::$app->request->post())){ //Yii::$app->request相当于$_POST

        }

        //step3. 传数据给视图渲染
        return $this->render('detail',[
            'model'=>$model,
            'tags'=>$tags,
            'recentComments'=>$recentComments,
            'commentModel'=>$commentModel,
        ]);
    }
}


