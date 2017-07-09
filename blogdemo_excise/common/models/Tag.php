<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property integer $id
 * @property string $name
 * @property integer $frequency
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'frequency' => 'Frequency',
        ];
    }

    public static function string2array($tags){//字符串转为数组
        return preg_split('/\s*,\s*/',trim($tags),-1,PREG_SPLIT_NO_EMPTY);
    }

    public static function array2string($tags){//数组转为字符串
        return implode(',',$tags);
    }

    public static function addTags($tags){//$tags是要增加的标签，是一个数组
        if(empty($tags)) return;
        foreach($tags as $name){//把输入的标签数组遍历，看数据库中有无同名的标签
            $aTag=Tag::find()->where(['name'=>'$name'])->one(); //Tag不能写成tag，一个坑
            $aTagCount=Tag::find()->where(['name'=>$name])->count();//下面判断用

            if(!$aTagCount){ //如果数据库中没有该标签，则在tag表中新增一个对象或这说一条记录，把相应的属性赋给它，其中frequency=1。
                $tag=new Tag;
                $tag->name=$name;
                $tag->frequency=1;
                $tag->save();
            }
            else{ //如果数据库中已经有有该标签,则给它的frenquency加1。
                $aTag->frequency+=1;
                $aTag->save();
            }
        }
    }
//注意不要考虑太多，一切以array_diff获得的差集数组来考虑问题，新标签集-旧标签集剩下的就是要新增的标签，反过来旧标签集-新标签集剩下的标签就是要减少的标签（标签的frequency=0为特例，此时删除此标签记录），
    public static function removeTags($tags){
        if(empty($tags)) return;
        foreach($tags as $name){
            $aTag=Tag::find()->where(['name'=>$name])->one();
            $aTagCount=Tag::find()->where(['name'=>$name])->count();

            if($aTagCount){
                if($aTagCount && $aTag->frequency<=1){ //前半个条件没用，因为前面已有，所以$aTagCount的值一定为真
                    $aTag->delete();
                }else{
                    $aTag->frequency-=1;
                    $aTag->save();
                }
            }

        }
    }

    //计算标签数的主要程序，其它几个都是在这里调用的
    public static function updateFrequency($oldTags,$newTags){
        if(!empty($oldTags) || !empty($newTags)){
            $oldTagsArray=self::string2array($oldTags);
            $newTagsArray=self::string2array($newTags);

            self::addTags(array_values(array_diff($newTagsArray,$oldTagsArray)));
            self::removeTags(array_values(array_diff($oldTagsArray,$newTagsArray)));
        }
    }

    public static function findTagWeights($limit=20){
        $tag_size_level=5; //标签分为5个档次

        //把所有标签取出来
        $models=Tag::find()->orderBy('frequency desc')->limit($limit)->all();
        $total=Tag::find()->limit($limit)->count();

        //算出每个档次要放几个标签
        $stepper=ceil($total/$tag_size_level);

        $tags=array();
        $counter=1;

        if($total>0){
            //循环中给tags数组赋值,数组的键名为标签，值为归入的档次。**这里注意：这里用到的由前面查询获取的$models数据已经按标签的frequency由大到小排序
            foreach($models as $model){
                $weight=ceil($counter/$stepper)+1;   //很巧妙，本句控制标签所在的档次
                $tags[$model->name]=$weight;
                $counter++;
            }
            ksort($tags);//按键名排好序
        }
        return $tags;

    }
}












