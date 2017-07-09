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
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($commentModel,'content')->textarea(['rows'=>4]);?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('发表评论' , ['class' =>'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
