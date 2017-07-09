<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增文章', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel, //这条语句如果注释掉。那文章管理中的搜索框会消失。这说明本语句会生成一个查询表单
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'], //序号列，从1开始，自动增长，这里是多余的，去掉

           // 'id',   //想控制id这列的宽度，可使用Class yii\grid\Column的公共的html属性contentOptions,如下：
            ['attribute'=>'id','contentOptions'=>['width'=>'30px','align'=>'center']],
            'title',
           // 'author_id', //可以写作['attribute'=>'author_id']，一样的，但我们可在此基础上加上value,如下一语句所示,这样就把作者id换成了名字
            //['attribute'=>'author_id','value'=>'author.nickname'], //这里的author.nickname是用getAuthor方法获得的，在gridView中简化了，不需要使用$model->author->nickname,model去掉了，后面用了点语法。
            ['attribute'=>'authorName','value'=>'author.nickname','label'=>'作者'], //本句试图把上句的author_id换做authorName,在postSearch类中也要做相应的变化：添加：authorName属性，联表查询数据，样做是为了真正的用用户名来查询
//            ['attribute'=>'authorName','value'=>'author.username','label'=>'作者'], //博客的作者就是管理员，故仍用上句
            // 'content:ntext', //内容是文章全文，放在这里显然不合适，所以去掉
            'tags:ntext',
            //'status',
            ['attribute'=>'status','value'=>'status0.name',  //***这里attributer如果用label代替，虽然，status0.name可以显示出来，但是上面的搜索框没了，当然下面做的下拉菜单也就无法显示了。
                'filter'=>\common\models\Poststatus::find() //加了下拉菜单
                    ->select(['name','id'])
                    ->orderBy('position')
                    ->indexBy('id')
                    ->column(),
            ],
            // 'create_time:datetime', //不需要该项，去掉
           // 'update_time:datetime',
            ['attribute'=>'update_time','format'=>['date','php:Y-m-d H:i:s']],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
