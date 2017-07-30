<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

   header('content-type:text/html;charset=utf-8');

/**
 * This is the model class for table "post". //分别对应于数据表post中的字段
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $tags
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $author_id
 *
 * @property Comment[] $comments
 * @property Adminuser $author
 * @property Poststatus $status0
 */
class Post extends \yii\db\ActiveRecord
{
    private $old_tags; //修改文章时对标签进行调整时使用

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'status', 'author_id'], 'required'],
            [['content', 'tags'], 'string'],
            [['status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['author_id' => 'id']],
//            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Poststatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '题目',
            'content' => '内容',
            'tags' => '标签',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'author_id' => '作者',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()        //注意：$reply_id=0时，comment存放的是文章的评论；而$reply_id不等于0时comment存放的是对评论id为$reply_id的回复
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id'])->where(['reply_id'=>0]);
    }

    public function getActiveComments() //获取已审核的评论
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id'])
           // ->where('status=:status',[':status'=>2])->orderBy('create_time DESC');
          // ->where('status=:status','reply=:reply',[':status'=>2,':reply'=>0])->orderBy('create_time DESC');
           ->where(['status'=>2,'reply_id'=>0])->orderBy('create_time DESC');
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor() //**对于前台用户来说，这里似乎应该是post表和user表联查，而不是adminuser。** 博客应该是Adminuser发表的，user只能评论
    {
       return $this->hasOne(Adminuser::className(), ['id' => 'author_id']);
       // return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Poststatus::className(), ['id' => 'status']);
    }

    public function beforeSave($insert){
        if(parent::beforeSave($insert)){ //重写覆盖beforeSave()方法时必须先调用父类的该方法
            if($insert){ //$insert为true，表明当前操作是插入或新增，为false时当前操作是修改
                $this->create_time=time();
                $this->update_time=time();
            }else{
               // var_dump($insert);
                $this->update_time=time();
            }
            return true;
        }else{

            return false;
        }
    }

    //当你通过 find() 方法查询数据时,每个AR实例都将有yii\db\ActiveRecord::afterFind()这个生命周期
    public function afterFind(){ //新增文章不走这里,修改文章时因首先要显示原来的文章，因此会通过find()方法查询数据变显示到页面，因此要走这里.**需要注意的是，许多地方都可能要用find()查询，所以$this->old_tags可能被多次赋值。而我们要进行文章修改时首先要查询数据并把其显示到页面，这时赋给$this->old_tags的当前的$this->tags就是执行afterSave()之前最后一遍执行afterFind()所获取的，也就是我们这里用到的旧的标签、
        parent::afterFind();
        $this->old_tags=$this->tags;  //把查到的数据库中的标签（修改前的）赋给$old_tags。除非数据库没有数据或者以往输入的数据中从未输入过标签，此时标签是空的
       // echo "<br>";
      //  var_dump($this->old_tags) ;
//        exit(0);
    }

    public function afterSave($insert,$changedAttributes){ //本方法新增文章和修改文章两种情形都会用.**insert可以用来判断当前操作是新增还是修改
        parent::afterSave($insert,$changedAttributes);

//        echo "<br>";
//        var_dump($insert);
//        var_dump($this->old_tags) ;
//        var_dump($this->tags) ;
//      exit(0);
        Tag::updateFrequency($this->old_tags,$this->tags);//经测试，对于新增情形：实际前面也执行了afterFind()方法（post/index），但这里的$this->old_tags经测试没被赋值过，还是最开始定义时的值，默认为空。很奇怪，$this->old_tags这个属性是全局属性，只要前面执行了afterFind()，肯定赋值过，那什么原因到这里就成为null了呢？是作用域的原因吗？待查！！
    }                                                       //上面问题可能的原因是对于新增情形，afterFind()与afterSave()是不同的对象执行的，虽然都具有$this->old_tags这个属性。但它们的属性值不同（同一个类但对象不同，afetrFind()（Post/index）是dataProvider提供的数据，相当于遍历，在此过程中$this_tags不断变化；而afterSave()在新增情形下是new了一个post类的新对象model.换句话说：两个$this不同，试想：$obj1->old_tags与$obj2->old_tags有多大关系？？注意：$obj.old_tags,即用点语法成js了。
    public function afterDelete(){
        parent::afterDelete();
        Tag::updateFrequency($this->tags,'');
    }

    public function getUrl(){
        return Yii::$app->urlManager->createUrl(
            ['post/detail','id'=>$this->id,'title'=>$this->title]
        );
    }

    //截取字符串，主要用于前台首页post/index左侧文章列表显示
    public function getBeginning($length=288){
        $tmpStr=strip_tags($this->content);
        $tmpLen=mb_strlen($tmpStr);

        $tmpStr=mb_substr($tmpStr,0,$length,'utf-8');
        return $tmpStr.($tmpLen>$length?'...':'');
    }

    public function getTagLinks(){
        $links=array();//用一个数组来存放标签及链接
        foreach(Tag::string2array($this->tags) as $tag){
            $links[]=Html::a(Html::encode($tag),array('post/index','PostSearch[tags]'=>$tag));  //用Html助手类把数组当中的每个标签都加上链接
        }
        return $links;
    }

    public function getCommentCount(){
        return Comment::find()->where(['post_id'=>$this->id,'status'=>2,'reply_id'=>0])->count();
    }

    public function commentReplyCount($reply_id){
        return Comment::find()->where(['status'=>2,'reply_id'=>$reply_id])->count();
    }

    public function activeCommentReplies($reply_id) //获取已审核的评论回复(针对某一评论的回复)
    {
        return  Comment::find()->where(['status'=>2,'reply_id'=>$reply_id])->orderBy('create_time DESC')->all();

    }

    public function CommentCount0($postId){
        return Comment::find()->where(['post_id'=>$postId,'status'=>2,'reply_id'=>0])->count();
    }

    public function CommentReplies($commentId)
    {
        return Comment::find()->where(['id'=>$commentId])->one();
    }
}




















