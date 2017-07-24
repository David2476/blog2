<?php
use yii\helpers\Html;
use common\models\Comment;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/07/02
 * Time: 10:26
 */
?>
<br>
<div class="post"> <!-- 这个class="post"加不加看不出有何区别-->
    <div class="title">
        <h2><a href="<?= $model->url?>"><?= Html::encode($model->title);?></a></h2> <!-- 这段是显示文章题目，且带超链接-->
        <div class="author">
            <span class="glyphicon glyphicon-time" aria-hidden="true"></span><em><?= date('Y-m-d H:i:s',$model->create_time)?></em>&nbsp;&nbsp;
<!--            <span class="glyphicon glyphicon-time" aria-hidden="true"></span><em>--><?php // echo Html::encode($model->author->nickname) ?><!--</em>-->
            <!--上面这条语句调用了模型post文件的getter方法getAuthor()方法来获取post表的新增属性$model->author,返回的是adminuser表的联查数据，$model->author->nickname就是adminuser表
            的字段nickname,这是视频中的作法，我感觉这里稍有不妥，因为这里是前台，你要展示的是发表文章的作者的名字，这个属性应该是user表里而不是在管理员表adminuser表中的，所以我对getAuthor()
            方法略作修改,让其返回user表的联查数据，同时上一语句改为下一语句，把$model->author->nickname替换为$model->author->username，这样展示的就是真正文章作者的名字-->
            <!-- 后注：博客应发表博主也就是管理员自己的文章，其它注册用户只能那个发表评论，因此视频中是对的。-->
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span><em><?= Html::encode($model->author->nickname) ?></em>
        </div>

    <br>
    <div class="content">  <!-- 这段是显示文章摘要-->
        <?= $model->beginning;?>
    </div>

    <br>
        <div class="nav">
            <span class="glyphicon glyphicon-tag" aria-hidden="true"></span>
            <?= implode(', ',$model->tagLinks);?>
            <br>


<?php //$data= Yii::$app->db->createCommand('select count(*) from comment a inner join post b on a.post_id=b.id')->queryOne()?>
               <?= Html::a("评论({$model->commentCount})",$model->url.'#comments')?>&nbsp;&nbsp;<?= date('Y-m-d H:i:s',$model->update_time)?>



       <!--     <?php //数据缓存部分：缓存依赖, 加了缓存依赖后，即便没有到缓存过期时间，缓存还是被刷新了
/*
            $data=Yii::$app->cache->get('postcount');
            //$results = Yii::$app->db->createCommand('SELECT count(*) from comment')->queryOne();//显示评论条数没问题
               //$dependency=new \yii\caching\DbDependency(['sql'=>'SELECT count(*) from post']);
            $dependency=new \yii\caching\DbDependency(['sql'=>'SELECT count(*) from comment']);
            //$dependency = new \yii\caching\ExpressionDependency(['expression'=>'common\models\Comment::find()->count()']);
            if($data===false){ //后面的判断是使ListView下的文章id变化时也刷新缓存
              // $data=$model->commentCount;
               $data=Comment::find()->where(['post_id'=>$key])->count();
                Yii::$app->cache->set('postCount',$data,6000,$dependency); //
//                Yii::$app->cache->set('postCount',$data,6000);
            }
*/
?> -->
            <!--     <?//= Html::a("评论({$data})",$model->url.'#comments')?> 最后修改于?><? //=date('Y-m-d H:i:s',$model->update_time);?> -->

        </div>
    </div>
</div>















