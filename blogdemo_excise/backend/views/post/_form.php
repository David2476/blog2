<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
header('content-type:text/html;charset=utf-8');
/* @var $this yii\web\View */
/* @var $model common\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
<!--注意，这里时post表中的输入，在post表中tags是text类型，也是一类字符串-->
    <?= $form->field($model, 'tags')->textarea(['rows' => 6]) ?>

    <!-- 下面这样写是硬编码，不好，我们的数据应该是从数据库取出来的
    <?= $form->field($model,'status')
        ->dropDownList([1=>'草稿',2=>'已发布']
        ,['prompt'=>'请选择状态']);?> -->
    <?php //下面使用queryBuilder即查询构建器方式
//    $allStatus=(new \yii\db\Query())
//        ->select(['name','id'])
//        ->from('poststatus')
//        ->indexBy('id')  //不按正常的索引排序，而是按id排序
//        //->all();
//        /* 使用all() 得到的结果,索引[1]等是id
//         Array
//       (
//           [1] => Array
//               (
//                   [name] => 草稿
//                   [id] => 1
//               )
//           [2] => Array
//               (
//                   [name] => 已发布
//                   [id] => 2
//               )
//           [3] => Array
//               (
//                   [name] => 已归档
//                   [id] => 3
//               )
//       )
//          */
//        ->column();
//    /* 使用column()得到的结果正好是如下形式，该形式也就是我们下拉菜单所需要的数组
//     Array
//(
//    [1] => 草稿
//    [2] => 已发布
//    [3] => 已归档
//)
    //    * */
    //另外一种方法：AR的find()方法,这种方法不需要用map转换数组，也不用实例化一个（new）对象，视频推荐
    $allStatus=\common\models\Poststatus::find()  //因AR的find()方法返回的是ActiveQuery对象的实例，而ActiveQuery继承自yii/db/query, 故可使用下列查询构建器方法构建查询.
            ->select(['name','id'])
           //->from('poststatus') //既然用了AR方法，本身带有表名，所以这里用from就没啥意义了，可以去掉（当然留着也没影响)
            ->orderBy('position')
            ->indexBy('id')  //不按正常的索引排序，而是按id排序
            ->column();  //获得结果与上面方法完全一致
//        echo "<pre>";
//        print_r($allStatus);
//        echo "</pre>";
//        exit(0);
    ?>

    <?= $form->field($model,'status')
        ->dropDownList($allStatus  //使用上面查询构建器或者AR的find()方法获得的$allStatus，**这里也可以把$allStatus直接用上面的$allStatus的表达式取代，后面的显示作者部分用了这样的方式
            ,['prompt'=>'请选择状态']);?>
<!--  这里先屏蔽掉下列语句，相应的修改放在model里面完成
    <?= $form->field($model, 'create_time')->textInput() ?>
    <?= $form->field($model, 'update_time')->textInput() ?>
-->
   <!--  <?= $form->field($model, 'author_id')->textInput() ?>-->

    <?php $allStatus1=\common\models\Adminuser::find()
        ->select(['nickname','id'])
//        ->dropDownList(\common\models\User::find()  //博客的作者就是管理员，故仍用上句
//            ->select(['username','id'])
        ->indexBy('id')  //不按正常的索引排序，而是按id排序
        ->column()
    ?>
    <?= $form->field($model,'author_id')
        ->dropDownList($allStatus1
        ,['prompt'=>'请选择作者']);
    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
