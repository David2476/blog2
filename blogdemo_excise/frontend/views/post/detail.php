<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/07/05
 * Time: 10:05
 */
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\helpers\arrayHelper;
use yii\helpers\HtmlPurifier;
use common\models\Comment;
?>

<div class="container">
    <div class="row">
        <div class="col-md-9">
           <ol class="breadcrumb">
               <li><a href="<?= Yii::$app->homeUrl;?>">首页</a></li>
               <li><a href="<?= Yii::$app->homeUrl;?>?r=post/index">文章列表</a></li>
               <li class="active"><?= $model->title?></li>
           </ol>

            <div class="post">
                <div class="title">
                    <h2><a href="<?= $model->url;?>"><?= Html::encode($model->title) ;?></a></h2>
                </div>
                <div class="author">
                    <span class="glyphicon glyphicon-time" aria-hidden="true"></span><em><?= date('Y-m-d H:i:s',$model->create_time)?></em>&nbsp;&nbsp;
                    <!--            <span class="glyphicon glyphicon-time" aria-hidden="true"></span><em>--><?php // echo Html::encode($model->author->nickname) ?><!--</em>-->
                    <!--上面这条语句调用了模型post文件的getter方法getAuthor()方法来获取post表的新增属性$model->author,返回的是adminuser表的联查数据，$model->author->nickname就是adminuser表
                    的字段nickname,这时视频中的作法，我感觉这里稍有不妥，因为这里是前台，你要展示的是发表文章的作者的名字，这个属性应该是user表里而不是在管理员表adminuser表中的，所以我对getAuthor()
                    方法略作修改,让其返回user表的联查数据，同时上一语句改为下一语句，把$model->author->nickname替换为$model->author->username，这样展示的就是真正文章作者的名字-->
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span><em><?= Html::encode($model->author->username) ?></em>
                </div>
            </div>

            <br>
            <div class="content">
                <?= HTMLPurifier::process($model->content);?>
            </div>

            <br>
            <div class="nav">
                <span class="glyphicon glyphicon-tag" aria-hidden="true"></span>
                <?= implode(', ',$model->tagLinks);?>
                <br>
                <?= Html::a("评论({$model->commentCount})",$model->url.'#comments');?>
                最后修改于<?= date('Y-m-d H:i:s',$model->update_time);?>
            </div>


        <div id="comments">
            <?php if($added) {?>
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4>谢谢您的回复，我们会尽快审核后发表出来</h4>
                    <br>
                   <blockquote><?= nl2br($commentModel->content);?></blockquote>
                    <br>
                    <span class="glyphicon glyphicon-time" aria-hidden="true"></span><em><?= date('Y-m-d H:i:s',time())?></em>&nbsp;
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span><em><?= Html::encode($model->author->nickname) ?></em>
                </div>
            <?php }?>

            <?php if($model->commentCount>=1) {?>
                <h5><?= $model->commentCount. '条评论'?></h5> <!--评论放到——comment视图去展示-->
                <br>
                <?= $this->render('_comment',['post'=>$model,'comments'=>$model->activeComments,'postObj'=>$postObj]);?>
            <?php }?>

            <h5>发表评论</h5>
            <?php   //生成发表评论部分，视图_guestform生成相应的表单
            //$postComment=new Comment(); //视频有，但感觉没啥用
            echo $this->render('_guestform',[
                    'id'=>$model->id,
                    'commentModel'=>$commentModel,
                    'recentComments'=>$recentComments,
                    'added'=>$added,
            ]);
            ?>
        </div>

        </div>

        <!-- 下面是页面右侧部分-->
        <div class="col-md-3">
            <div class="searchBox">
                <ul class="list-group">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>查找文章
                    </li>
                    <li class="list-group-item">
                        <form class="form-inline" action="index.php?r=post/index" id="w0" method="get">

                            <div class="form-group">
                                <input type="text" class="form-control" name="PostSearch[title]" id="w0input" placeholder="按标题">
                                <input type="text" class="form-control" name="PostSearch[authorName]" id="w0input1" placeholder="按作者">
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
                        <?= \frontend\components\TagsCloudWidget::widget(['tags'=>$tags]);?>
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
