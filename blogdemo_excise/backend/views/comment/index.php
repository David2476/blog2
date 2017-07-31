<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '评论管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
<!-- 评论不需要管理员创建，是用户创建的，因此这里屏蔽掉      <?//= Html::a('Create Comment', ['create'], ['class' => 'btn btn-success']) ?> -->
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute'=>'id',
                'contentOptions'=>['width'=>'30px'],
            ],

           // 'content:ntext',
            //下面这段是控制内容长度的第1种方法
         /*   ['attribute'=>'content',
             'value'=>function($model){
                     //去掉内容的标签
                    $tmpStr=strip_tags($model->content);
                    //获取内容字符串长度
                    $tmpLen=mb_strlen($tmpStr);
                    //当内容大于20个字符返回内容+'...'，否则只返回内容
                    return mb_substr($tmpStr,0,20,'utf-8').(($tmpLen>20)?'...':'');
             }
            ],
         */
            //下面这段是控制内容长度的第2种方法. 当使用这个功能的页面比较多时，使用上面第1种方法需要在每个页面都加一个匿名函数，比较麻烦，此时可在模型文件中建立一个函数来实现此功能。
            [
             'attribute'=>'content',
             'value'=>'beginnig',   //读取value时会到模型类的getter方法中找，getBeginnig()方法中定义了属性beginnig
            ],
            //'status',
            [
                'attribute'=>'status',
                'value'=>'status0.name',
                'filter'=>\common\models\Commentstatus::find()
                     ->select(['name','id'])
                     ->orderBy('position')
                     ->indexBy('id')
                     ->column(),
                //给待审核加颜色
                'contentOptions'=>
                    function($model){ //这里必须把$model传进来，不然下面函数内部不可以使用，会报错
                        return($model->status==1)?['class'=>'bg-danger']:[];
                    }
            ],

            //'userid',
            [
                'attribute'=>'user.username', //实际调用getUser()方法了
                'value'=>'user.username',
                'label'=>'作者', //把显示的英文改为中文
            ],
            // 'email:email',
            // 'url:url',

            //   'create_time:datetime',
            [
                'attribute'=>'create_time',
                'format'=>['date','php:Y-m-d H:i:s'],

            ],

            'reply_id',
            // 'post_id',
            'post.title', //写成题目以便知道对哪篇文章进行的评论。这里对题目进行搜索和排序和前面是一样的，就不再做了。

            //['class' => 'yii\grid\ActionColumn'],
            //增加一个评审通过按钮
            [
              'class' => 'yii\grid\ActionColumn',
              'template'=>'{view}{update}{delete}{approve}', //这里大括号里的每一个动作都对应于控制器中的actionID,所以对于新增的按钮approve，在控制器中必须有相应的动作
              'buttons'=>
                        [
                          'approve' =>function($url,$model,$key){

                                $options=[
                                        'title'=>Yii::t('yii','审核'),
                                        'aria-label'=>Yii::t('yii','审核'),
                                        'data-confirm'=>Yii::t('yii','你确定通过这条评论吗？'),
                                        'data-method'=>'post',
                                        'data-pjax'=>'0',
                                         ];
                                return Html::a('<span class="glyphicon glyphicon-check"></span>',$url,$options);
                          },
                        ],
              ],
           ],
    ]); ?>
</div>
