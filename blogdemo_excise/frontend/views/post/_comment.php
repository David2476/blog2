<?php //显示某文章所有评论
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/07/05
 * Time: 14:18
 */
error_reporting(E_ALL || ~E_NOTICE); //显示除去 E_NOTICE 之外的所有错误信息,故不会显示警告
frontend\assets\CommentAsset::register($this); //仅为测试资源包用
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Comment;

?>

    <!--
    <div class="comment">
        <div class="row">
            <div class="col-md-12">
                <div class="comment_detail">
                    <p class="bg-info">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        <em><?//= Html::encode($comment->user->username).':';?></em>
                        <br>
                        <?//= nl2br($comment->content);?>
                        <br>
                        <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
                        <em><?//= date('Y-m-d H:i:s',$comment->create_time)?></em>&nbsp;
                    </p>
                </div>
            </div>
        </div>
    </div>
    -->

    <div class="commentAll" style="width:100%;padding: 20px;border: 1px solid #ededed;margin: 20px auto;">
        <!--评论区域 begin-->

<!--        <div class="reviewArea clearfix">-->
<!--            <textarea  class="content comment-input" placeholder="Please enter a comment&hellip;" "onkeyup="keyUP(this)"></textarea>-->
<!---->
<!--            <a href="javascript:;" class="plBtn">评论</a>-->
<!--        </div>-->
        <!--评论区域 end-->
        <?php foreach($comments as $comment){?>
        <!--回复区域 begin-->
        <div class="comment-show">
            <div class="comment-show-con clearfix">
                <div class="comment-show-con-img pull-left"><img src="images/header-img-comment_03.png" alt=""></div>
                <div class="comment-show-con-list pull-left clearfix">
                    <div class="pl-text clearfix">
                        <a href="#" class="comment-size-name"><?= Html::encode($comment->user->username)?> : </a>
                        <span class="my-pl-con">&nbsp; <?= nl2br($comment->content);?></span>
                    </div>
                    <div class="date-dz">
                        <span class="date-dz-left pull-left comment-time"><?= date('Y-m-d H:i:s',$comment->create_time)?></span>
                        <div class="date-dz-right pull-right comment-pl-block">
                            <a href="javascript:;" class="removeBlock">删除</a>
                            <a href="javascript:;" class="date-dz-pl pl-hf hf-con-block pull-left">回复</a>
                            <span class="pull-left date-dz-line">|</span>
                            <a href="javascript:;" class="date-dz-z pull-left"><i class="date-dz-z-click-red"></i>赞 (<i class="z-num">666</i>)</a>
                        </div>
                    </div>
                <!--    <?php //if($reply_index==1){?>
                    <?php foreach($commentReplies as $commentReply){?>
                    <div class="hf-list-con"><?php var_dump($commentReply->content); ?></div>
                    <?php }?>
                    <?php//}?>  -->
                </div>
            </div>
        </div>

        <?php }?>
        <!--回复区域 end-->
        <script type="text/javascript">
            $('.comment-show').on('click','.pl-hf',function(){
                //获取回复人的名字
                var fhName = $(this).parents('.date-dz-right').parents('.date-dz').siblings('.pl-text').find('.comment-size-name').html();
                //回复@
                var fhN = '回复@'+fhName;
                //var oInput = $(this).parents('.date-dz-right').parents('.date-dz').siblings('.hf-con');
             //   var fhHtml = '<div class="hf-con pull-left"> <textarea class="content comment-input hf-input" placeholder="" onkeyup="keyUP(this)"></textarea> <a href="javascript:;" class="hf-pl">评论</a></div>';
                var fhHtml = '<div class="hf-con pull-left"> <textarea class="content comment-input hf-input" placeholder="" ></textarea> <a href="javascript:;" class="hf-pl">评论</a></div>';
                //显示回复
                if($(this).is('.hf-con-block')){
                    $(this).parents('.date-dz-right').parents('.date-dz').append(fhHtml);  //增加回复文本输入框
                    $(this).removeClass('hf-con-block');
                    $('.content').flexText();
                    $(this).parents('.date-dz-right').siblings('.hf-con').find('.pre').css('padding','6px 15px');
                    //console.log($(this).parents('.date-dz-right').siblings('.hf-con').find('.pre'))
                    //input框自动聚焦
                    $(this).parents('.date-dz-right').siblings('.hf-con').find('.hf-input').val('').focus().val(fhN);
                }else {
                    $(this).addClass('hf-con-block');
                    $(this).parents('.date-dz-right').siblings('.hf-con').remove();
                }
            });
            </script>

        <!--评论回复块创建-->
        <script type="text/javascript">
            $('.comment-show').on('click','.hf-pl',function(){ //点击回复-评论事件
                var oThis = $(this);
                var myDate = new Date();
                //获取当前年
                var year=myDate.getFullYear();
                //获取当前月
                var month=myDate.getMonth()+1;
                //获取当前日
                var date=myDate.getDate();
                var h=myDate.getHours();       //获取当前小时数(0-23)
                var m=myDate.getMinutes();     //获取当前分钟数(0-59)
                if(m<10) m = '0' + m;
                var s=myDate.getSeconds();
                if(s<10) s = '0' + s;
                var now=year+'-'+month+"-"+date+" "+h+':'+m+":"+s;
                //获取输入内容
                var oHfVal = $(this).siblings('.flex-text-wrap').find('.hf-input').val();
                console.log(oHfVal)
                var oHfName = $(this).parents('.hf-con').parents('.date-dz').siblings('.pl-text').find('.comment-size-name').html();
                var oAllVal = '回复@'+oHfName;
                var id=<?=$post->id; ?>;

                window.location="index.php?r=post/reply&content="+oHfVal+'&id='+id;
            });
        </script>



    </div>



