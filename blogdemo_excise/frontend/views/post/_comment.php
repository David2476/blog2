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
use common\models\Post;

?>
        <div class="row">
            <div class="col-md-12">
                <ul class="media-list " id="ul">
                    <?php foreach($comments as $comment){?>
                        <li class="media" style="border-top:1px solid #EDEDED;padding-top:10px;padding-top:20px;overflow: auto;min-height:100px; height:auto!important; height:100px;"  >
                            <div class="media-left">
                                <img src="images/header-img-comment_03.png" alt="" class="media-object">
                            </div>
                            <div class="media-body" style="border:0px solid green;">
                                <h5 class="meida-heading" style="margin-left:5px;color:#339B53;margin-bottom:10px;"><?= Html::encode($comment->user->username).':'?></h5>
                                <p style="margin-left:5px;margin-bottom:15px;margin-top:5px;"><?= nl2br($comment->content);?></p>
                                <span style="margin-left:5px;display:inline-block;width:300px;border:0px solid green;margin-top:20px;margin-bottom:10px;"><?= date('Y-m-d H:i:s',$comment->create_time)?></span><a class="comment" style="margin-left:100px;border:0px solid purple;"  title="<?= $comment->user->username; ?>" id="<?= $comment->id; ?>" >回复</a>

                                <?php
                                $replyCount= $postObj->commentReplyCount($comment->id); //此时get方法不起作用了？？不是，后面加括号，使用函数而不是属性了。
                                $commentReplies = $postObj->activeCommentReplies($comment->id);
                                ?>
                                <?php if($replyCount){ //如果当前评论的回复数不为零，则对回复进行遍历 ?>
                                    <?php foreach( $commentReplies as $commentReply) {?>
                                        <div class="media" style="border-top:1px solid #EDEDED;margin-top:1px;padding-top:20px;overflow: auto;min-height:100px; height:auto!important; height:100px;background:#EAEAEC" >
                                            <div class="media-left" style="margin-left:10px;border:0px solid green;">
                                                <img src="images/header-img-comment_03.png" alt="" class="media-object">
                                            </div>
                                            <div class="media-body" style="border:0px solid red;margin-left:10px;">
                                                <h5 class="media-heading" style="margin-left:5px;color:#339B53"><?= Html::encode($commentReply->user->username).': '?></h5>
                                                <p style="margin-left:5px;margin-bottom:10px;margin-top:5px;"><?= nl2br($commentReply->content);?></p>
                                                <span style="margin-left:5px;display:inline-block;width:300px;border:0px solid green;margin-top:0px;margin-bottom:5px;"><?= date('Y-m-d H:i:s',$commentReply->create_time)?></span><a class="comment_reply" style="margin-left:100px" title="<?= $commentReply->user->username; ?>" id="<?= $commentReply->id; ?>">回复</a>
                                            </div>
                                        </div>
                                    <?php }?>
                                <?php }?>

                            </div>
                        </li>
                    <?php }?>
                </ul>
            </div>
        </div>

<script>
    var oUl=document.getElementById("ul");
   // alert (oUl.className);
   // var aLi=oUl.getElementsByTagName("Li");
  //  alert(aLi[0].id);
    var aA=oUl.getElementsByTagName("a");
   // alert(aA.length);


    for(var i=0;i<aA.length;i++){
        aA[i].index=i;
        aA[i].commentId=aA[i].id;
        aA[i].onclick=function(){
            var inp=document.createElement("input");
            inp.className="form-control";
            var butt=document.createElement("button");
            butt.className="btn btn-info";
            butt.style.float="right";
            butt.innerHTML="提 交";
            butt.style.display="block";
            insertAfter(inp, aA[this.index]);
            insertAfter(butt, inp);
            var divs=document.createElement("div"); //加此目的是为了是对评论回复时，butt按钮能占一行，看上去好看一些。
            divs.style.height="33px";
            insertAfter(divs, butt);
            window.commentId= this.commentId;
            window.index= this.index;
            butt.onclick=function(){
                if(inp.value===""){
                    alert('输入不能为空！');
                }
                var id=<?php echo $post->id; ?>;

//                var content='<?php //if($com) {
//                    echo '回复@' . $commentReply->user->username;
//                }else{
//                    echo '回复@' . $comment->user->username;
//                }
//                              ?>//'+': '+inp.value;

                if(aA[window.index].className=="comment"){
                               var content=' <?php echo '回复@'; ?>'+aA[window.index].title +': '+inp.value;
                            }else{
                                   content=' <?php echo '回复@'; ?>'+aA[window.index].title +': '+inp.value;
                            }

                var reply_id=window.commentId;
             //   inp.parentNode.removeChild(inp); //删除inp节点
             //   inp=null; //从内存中清除inp
//                inp.parentNode.removeChild(butt);
               // window.location="index.php?r=post/reply&id="+id+"&content1="+content1+"&reply_id="+reply_id+"&#comments";

                //利用ajax传递数据
                $.ajax({
                    type:"post",
                    url:"index.php?r=post/detail&id="+id,
                    data:'id='+id+"&content="+content+"&reply_id="+reply_id,
                    //dataType:"html", //显示调试完后，要屏蔽本语句，用下面设置为json类型的语句拿结果
                    dataType:"json",
                    success:function(re){ //re:服务器返回的结果
//                        //alert(re); //此语句可配合前面 “dataType:"html"使用。当dataType设为html类型时，可在这里alert(re),会显示返回的结果。但类型改为json后，可拿到结果，但无法alert显示出alert结果，此时alert会显示object
//                        //拿到返回结果
//                        var price=re.price;
//                        var totalPrice=re.totalPrice;
//                        $("#totalSpan").html('￥'+totalPrice);
//                        $("#td"+carId).html(price);
                    }
                });
                var radom=parseInt(Math.random()*1000+1);
                window.location="index.php?r=post/detail&id="+id+'&radom='+radom+"#comments";
              //  location.replace("index.php?r=post/detail&id="+id+'&radom='+radom+"#comments");

            }
       };
    }

    function insertAfter(newEl, targetEl)//在当前元素后插入新元素，填写评论回复时点击回复后要出现一个输入框和一个提交按钮
    {
        var parentEl = targetEl.parentNode;
        if(parentEl.lastChild == targetEl)
        {
            parentEl.appendChild(newEl);
        }else
        {
            parentEl.insertBefore(newEl,targetEl.nextSibling);
        }
    }


</script>

·
