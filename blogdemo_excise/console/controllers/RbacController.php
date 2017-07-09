<?php
/**
 * Created by PhpStorm. 用于权限分配
 * User: Administrator
 * Date: 2017/06/30
 * Time: 11:19
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // 添加 "createPost" 权限
        $createPost = $auth->createPermission('createPost');
        $createPost->description = '新增文章';
        $auth->add($createPost);

        // 添加 "updatePost" 权限
        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = '修改文章';
        $auth->add($updatePost);

        // 添加 "deletePost" 权限
        $deletePost = $auth->createPermission('deletePost');
        $deletePost->description = '删除文章';
        $auth->add($deletePost);

        // 添加 "approveComment" 权限
        $approveComment = $auth->createPermission('approveComment');
        $approveComment->description = '审核评论';
        $auth->add($approveComment);


        // 添加 "postAdmin" 角色并赋予 "updatePost" "deletePost" "createPost"
        $postAdmin = $auth->createRole('postAdmin');
        $auth->add($postAdmin);
        $auth->addChild($postAdmin, $updatePost);
        $auth->addChild($postAdmin, $deletePost);
        $auth->addChild($postAdmin, $createPost);

        // 添加 "postoperator" 角色并赋予 "deletePost"权限
        $postOperator = $auth->createRole('postOperator');
        $auth->add($postOperator);
        $auth->addChild($postOperator, $deletePost);

        // 添加 "commentAuditor" 角色并赋予 "approveComment"权限
        $commentAuditor = $auth->createRole('commentAuditor');
        $auth->add($commentAuditor);
        $auth->addChild($commentAuditor, $approveComment);

        // 添加 "admin" 角色并赋予 所有其它角色所拥有的权限
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $postAdmin);
        $auth->addChild($admin, $commentAuditor);

        // 为用户指派角色。其中 1 和 2 是由 IdentityInterface::getId() 返回的id （译者注：user表的id）
        // 通常在你的 User 模型中实现这个函数。  ** 这里实际上是给管理员表的id指派角色,修改角色就在这里修改，其实一点也不复杂。
        $auth->assign($admin, 1);
        $auth->assign($postAdmin, 2);
        $auth->assign($postOperator, 3);
        $auth->assign($commentAuditor, 4);
    }
}