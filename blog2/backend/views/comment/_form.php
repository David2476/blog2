<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\Commentstatus::find()
                                            ->select(['name','id'])
                                            ->orderBy('position')
                                            ->indexBy('id')
                                            ->column(),
                                            ['prompt'=>'请选择状态'])
    ?>

<!-- 评论发表时间不应该在这里来设置，应该在发表评论时自动生成，到模型类里重写beforeSave()方法   <?= $form->field($model, 'create_time')->textInput() ?>-->

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
