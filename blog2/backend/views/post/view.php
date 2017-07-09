<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Post */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '文章管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定删除这篇文章吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'content:ntext',
            'tags:ntext',
            //'status',
            ['label'=>'状态',	'value'=>$model->status0->name,],
            //'create_time:datetime', // 与前面的 'content:ntext'一样，冒号后面表示显示的格式，可参考手册，当手册给出的格式仍然不太合适时（比如本句的datatime），可调用php函数
            ['attribute'=>'create_time','value'=>date('Y-m-d H-i-s',$model->create_time)],
            //'update_time:datetime',
            ['attribute'=>'update_time','value'=>date('Y-m-d H-i-s',$model->update_time)],
            //'author_id',
            ['label'=>'作者','value'=>$model->author->nickname], //这里的author和上面的status0一样，都是post模型类中getAuthor或getStatus0中get后面跟着的方法名字，比如，文章类若想获取作者的nickname，要先调用getAuthor方法，得到一个返回的adminuser对象，再使用这个对象的属性，由于是get方法，可以直接用$model->author调用getAuthor方法，再调用nickname属性即可，合起来就可写成：$model->author->nickname。
//            ['label'=>'作者','value'=>$model->author->username], //博客，还是应该用上句，作者就是管理员
        ],
            //用'template'可调节小部件中每一行的展示模板
        'template'=>'<tr><th style="width:120px;">{label}</th><td>{value}</td></tr>', //label是标签，value是数据，相当于给标签列宽度加样式
        'options'=>['class' => 'table table-striped table-bordered detail-view'],//加上此项没看见有啥不同的地方
    ]) ?>

</div>
