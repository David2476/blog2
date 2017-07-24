<?php //显示某文章所有评论
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/07/05
 * Time: 14:18
 */
frontend\assets\CommentAsset::register($this); //仅为测试资源包用
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php foreach($comments as $comment){?>
    <div class="comment">
        <div class="row">
            <div class="col-md-12">
                <div class="comment_detail">
                    <p class="bg-info">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        <em><?= Html::encode($comment->user->username).':';?></em>
                        <br>
                        <?= nl2br($comment->content);?>
                        <br>
                        <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                        <em><?= date('Y-m-d H:i:s',$comment->create_time)?></em>&nbsp;
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php }?>
