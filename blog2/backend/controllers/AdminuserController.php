<?php

namespace backend\controllers;

use backend\models\SignupForm;
use backend\models\ResetpwdForm;
use common\models\AuthAssignment;
use common\models\AuthItem;
use Yii;
use common\models\Adminuser;
use common\models\AdminuserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminuserController implements the CRUD actions for Adminuser model.
 */
class AdminuserController extends Controller
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
     * Lists all Adminuser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminuserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Adminuser model.
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
     * Creates a new Adminuser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if($user=$model->signup()){
                return $this->redirect(['view', 'id' => $user->id]);
            }
        }
            return $this->render('create', [
                'model' => $model,
            ]);
    }

    /**
     * Updates an existing Adminuser model.
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
     * Deletes an existing Adminuser model.
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
     * Finds the Adminuser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Adminuser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Adminuser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionResetpwd($id)
    {
        $model = new resetpwdForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->resetPassword($id)){
                return $this->redirect(['index']);
            }
        }
        return $this->render('resetpwd', [
            'model' => $model,
        ]);
    }

    public function actionPrivilege($id){
        //step1.找出所有权限(似乎不是权限而应该是角色，因为where(['type'=>1]))，提供给checkboxlist
        $allPrivileges=AuthItem::find()->select(['name','description'])
            ->where(['type'=>1])->orderBy('description')->all();

        foreach($allPrivileges as $pri){
            $allPrivilegesArray[$pri->name]=$pri->description;  //这里似乎是用一个数组来存储上面查到的记录
        }
        //step2. 当前用户的权限  **应为当前id的角色安排（可能不只一个角色）
        $AuthAssignments=AuthAssignment::find()->select(['item_name'])
            ->where(['user_id'=>$id])->all();

        $AuthAssignmentsArray=array();

        foreach($AuthAssignments as $AuthAssignment){
            array_push($AuthAssignmentsArray,$AuthAssignment->item_name); //把当前id担当的所有角色挨个放入数组$AuthAssignmentsArray中
        }

        //step3. 从表单提交的数据，来更新AuthAssignment表，从而用户的角色发生变化，这部分代码在先做好step4后再做（要从step4的表单获取数据）。
        if(isset($_POST['newPri'])){
            AuthAssignment::deleteAll('user_id=:id',[':id'=>$id]);
            $newPri=$_POST['newPri']; //$newPri是一个数组，存放多选框选定的角色名字，比如文章操作员等
            $arrlength=count($newPri);//count()函数可获取数组元素的个数

            for($x=0;$x<$arrlength;$x++){ //把接收到的表单数据，存入数据库,如果是多选，表示当前id被赋予多个角色，这是允许的
                $aPri=new AuthAssignment();
                $aPri->item_name=$newPri[$x];
                $aPri->user_id=$id;
                $aPri->created_at=time();
                $aPri->save();
            }
            return $this->redirect(['index']); //改好后转到用户列表页

        }

        //step4. 渲染checkBoxList表单
        return $this->render('privilege',['id'=>$id,'AuthAssignmentArray'=>$AuthAssignmentsArray,
                 'allPrivilegeArray'=>$allPrivilegesArray]);

    }
}
