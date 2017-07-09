<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Post;

/**
 * PostSearch represents the model behind the search form about `common\models\Post`.
 */
class PostSearch extends Post
{
    //要想在搜索框中对authorName进行搜索，必须在postSearch类中有authorName这个属性，而要给postSearch类添加属性必须重写attributes方法
    public function attributes()
    {
        return array_merge(parent::attributes(),['authorName']); //通过array_merge方法在原有attributes基础上增加authorName属性,注意：光此句视图只能出现authorName,没搜索框，所以还不行，还得增加数据规则。
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'create_time', 'update_time', 'author_id'], 'integer'],
          //  [['title', 'content', 'tags'], 'safe'],
            [['title', 'content', 'tags','authorName'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();  //调用最顶层的model类的scenarios()可作废掉父类的scenarios()
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Post::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //$dataProvider 这部分内容中以下都是新加的。注意一点，$dataProvider中sort这个属性，类参考文件没有，但是在指南中有，以后也要这样找。反正就只有这两个主要参考资料。
            'pagination'=>['pageSize'=>6],
            'sort'=>[
                'defaultOrder'=>['id'=>SORT_ASC],
                'attributes'=>['id','title'], //利用sort的attributes属性，可以设置参加排序的字段。运行后，除这两个字段外其它字段都不能点了。试了下，如屏蔽本语句，默认的是所有字段参与排序。**注意：不参与排序仍可搜索
            ],
        ]);

        //下面测试一下dataProvider实现的方法：
        /*
        echo "<pre>";
        print_r($dataProvider->getPagination());
        echo "<hr>";
        print_r($dataProvider->getSort());
        echo "<hr>";
        print_r($dataProvider->getCount());
        echo "<hr>";
        print_r($dataProvider->getTotalCount());
        echo "<pre>";
        exit(0);
        */

       // /* 为研究dataProvider,先屏蔽下面这段代码，这样，index页面的查询就不能用了。上面$dataProvider这段代码只是最简单的设置，'query' => $query,光提供了数据，我们增加其它设置看看
        $this->load($params); //把表单数据块赋值给当前对象的属性，节省空间时间

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'post.id' => $this->id, //这里post.id不能写成id，因为Adminuser表和post表联查后，有两个id,要指明是哪个id，否则会出二义
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
           // 'author_id' => $this->author_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'tags', $this->tags]);
//*/
        //要对添加的属性authorName进行过滤，过滤之前，首先要联表查询获取adminuser表的信息,因为authorName就是adminuser表中的nickname
        $query->join('INNER JOIN','Adminuser','post.author_id=Adminuser.id');
//          $query->join('INNER JOIN','User','post.author_id=User.id'); // 和文章联查，应该是文章的作者，其id应是用户的id而不是管理员的id。**博客，还是应用上句
        $query->andFilterWhere(['like','Adminuser.nickname',$this->authorName]);//这里Adminuser.nickname也可直接用nickname代替，因为在联表查询后，两表中只有一个nickname字段，不会发生混淆。
        // $query->andFilterWhere(['like', 'User.username', $this->authorName]); //博客，还是应该用上句
        //给authorName排序，视频上讲可以利用前面dataProbider实例化时添加排序属性，其它没问题，但autorName不行，报错，？？需要琢磨一下。视频还提供了另外一种方法如下：
        $dataProvider->sort->attributes['authorName']=
            [
                'asc'=>['Adminuser.nickname'=>SORT_ASC],
                'desc'=>['Adminuser.nickname'=>SORT_DESC],
            ];

        return $dataProvider;
    }
}
