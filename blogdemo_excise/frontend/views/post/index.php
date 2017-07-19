<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\helpers\arrayHelper;
use common\models\Post;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//因本视图要作为博客的前台首页，因此下原来生成的代码都不用了
?>
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <?= ListView::widget([
                   // 'id'=>'postList',  //此语句似乎可有可无
                    'dataProvider'=>$dataProvider,
                    'itemView'=>'_listitem',//子视图，显示一篇文章的标题等内容。
                    'layout'=>'{items}{pager}',  //此语句也似乎可有可无
                    'pager'=>[   //如果没有这段代码，也会显示分页，但是不会显示上一页、下一页，而是显示符号《 或 》
                            'maxButtonCount'=>10,
                            'nextPageLabel'=>Yii::t('app','下一页'),
                            'prevPageLabel'=>Yii::t('app','上一页'),
                    ],

            ])?>
        </div>
        <!-- 下面是页面右侧部分-->
        <div class="col-md-3">
            <div class="searchBox">
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>查找文章
                        <?php //数据缓存部分与缓存过期
                        /*
                        $data=Yii::$app->cache->get('postcount');
                        if($data===false){
                            $data=Post::find()->count();
                             sleep(5);
//                            Yii::$app->cache->set('postCount',$data); //如果不清除缓存，即便增加了文章，显示的文章条数仍然是缓存里的，所以通常要设置一个缓存时间，如下句：
                            Yii::$app->cache->set('postCount',$data,60); //缓存60秒过期，设置了缓存过期时间，这样一旦过期，系统会自动更新缓存
                        }
                                echo $data;
                       */
                        ?>
                        <?php //数据缓存部分：缓存依赖, 加了缓存依赖后，即便没有到缓存过期时间，缓存还是被刷新了
                        /*
                        $data=Yii::$app->cache->get('postcount');
                        $dependency=new \yii\caching\DbDependency(['sql'=>'select count(id) from post']);
                        if($data===false){
                            $data=Post::find()->count();
                            sleep(5);
                            Yii::$app->cache->set('postCount',$data,600,$dependency); //
                        }
                        echo $data;
                          */
                        ?>
<!--                       <?//= Post::find()->count(); //使用片段缓存时需要将上面数据缓存代码屏蔽，以免交叉影响?> -->
                    </li>
                    <li class="list-group-item">
                        <form class="form-inline" action="index.php?r=post/index" id="w0" method="get">

                            <div class="form-group">
                                <input type="text" class="form-control" name="PostSearch[title]" id="w0input" placeholder="按标题">
                                <input type="text" class="form-control" name="PostSearch[content]" id="w0input1" placeholder="按内容">
                                <input type="text" class="form-control" name="PostSearch[tags]" id="w0input2" placeholder="按标签">
                            </div>
                            <br>
                            <br>
                            <button type="submit" class="btn btn-default">搜索</button>
                        </form>
                    </li>
                </ul>
            </div>



            <div class="tagCloudBox">
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-tags" aria-hidden="true"></span>标签云</li>
                    <li class="list-group-item">
                       <?php //片段缓存部分
                       /*
                        $dependency=new \yii\caching\DbDependency(['sql'=>'select count(id) from post']);
//                        if($this->beginCache('cache',['dependency'=>$dependency])){
                        if($this->beginCache('cache',['duration'=>10],['dependency'=>$dependency])){ //和上一句相比增加了缓存过期的时间,好使，10秒过后就没缓存了
                            echo \frontend\components\TagsCloudWidget::widget(['tags'=>$tags]);
                            $this->endCache();
                        }
                        */
                       ?>

                        <?= \frontend\components\TagsCloudWidget::widget(['tags'=>$tags]); //使用上面片段缓存时屏蔽本句，使用页面缓存时打开本句
                        ?>
                    </li>
                </ul>
            </div>

            <div class="commentBox">
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-comment" aria-hidden="true"></span>最新回复</li>
                    <li class="list-group-item">
                        <?= \frontend\components\RctReplyWidget::widget(['recentComments'=>$recentComments]);?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>















