<?php //发表评论部分的表单输入

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin([
        'action'=>['post/detail','id'=>$id,'#'=>'comments'], //#指提交后的定位点
        'method'=>'post',
       // 'options'=>['autocomplete'=>'off','name'=>"_csrf",'type'=>"hidden",'id'=>"_csrf",'value'=>Yii::$app->request->csrfToken], //本意是解决'Bad Request (#400)'问题,但是前台mian-local里crsf验证为true时也不报错了，所以先屏蔽此句
    ]); ?>

    <div class="row">
        <div class="col-md-12" >
            <?= $form->field($commentModel,'content',[ 'inputOptions' => [ //看源码后可知，这种办法给input加属性是可以的，但是'autocomplete'=>'off'没有效果
                'placeholder' => '请输入评论！','autocomplete'=>'false' ]])->textarea(['rows'=>4]);//'autocomplete'=>'false' ,无论是false还是off都不起作用?>

        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('发表评论' , ['class' =>'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
