<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property string $content
 * @property integer $status
 * @property integer $create_time
 * @property integer $userid
 * @property string $email
 * @property string $url
 * @property integer $post_id
 * @property integer $remind
 * @property Post $post
 * @property Commentstatus $status0
 * @property User $user
 * @property integer $reply
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'status', 'userid', 'email', 'post_id'], 'required'],
            [['content'], 'string'],
            [['status', 'create_time', 'userid', 'post_id', 'remind', 'reply'], 'integer'],
            [['email', 'url'], 'string', 'max' => 128],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Commentstatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '内容',
            'status' => '状态',
            'create_time' => '发布时间',
            'userid' => '用户',
            'email' => 'Email',
            'url' => 'Url',
            'post_id' => '文章',
            'remind' => '是否提醒',
            'reply' => '是否评论回复',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Commentstatus::className(), ['id' => 'status']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userid']);
    }

    public function getBeginnig(){  //定义了一个属性beginnig
        $tmpStr=strip_tags($this->content);
        //获取内容字符串长度
        $tmpLen=mb_strlen($tmpStr);
        //当内容大于20个字符返回内容+'...'，否则只返回内容
        return mb_substr($tmpStr,0,10,'utf-8').(($tmpLen>10)?'...':'');
    }

    public function approve(){
        $this->status=2; //设置评论状态为已审核
        return($this->save()?true:false);
    }

    //获取待审核评论数。加气泡用。使用getter方法.  完成本方法后，到布局文件中修改，加气泡，因为导航栏在布局文件中。
    public static function getPendingCommentCount(){
        return Comment::find()->where(['status'=>1])->count();
    }

    //生成评论发表时间
    public function beforeSave($insert){
        if(parent::beforeSave($insert)){
            if($insert){
                $this->create_time=time();
            }
            return true;
        }else return false;
    }

    //用于前台首页
    public static function findRecentComments($limit=10){
        return Comment::find()->where(['status'=>2,'reply'=>0])->orderBy('create_time DESC')
            ->limit($limit)->all();
    }

    public static function findRecentCommentReplies($limit=10){
        return Comment::find()->where(['status'=>2,'reply'=>1])->orderBy('create_time DESC')
            ->limit($limit)->all();
    }

}




