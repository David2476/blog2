<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/07/01
 * Time: 17:17
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Adminuser;

/* @var $this yii\web\View */
/* @var $model common\models\Adminuser */

$model=Adminuser::findOne($id); //id已经由AminuserController传过来了

$this->title = '权限设置: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => '管理员', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '权限设置';
?>
<div class="adminuser-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="adminuser-privilege-form">

        <?php $form = ActiveForm::begin(); ?>
        <?= Html::checkboxList('newPri',$AuthAssignmentArray,$allPrivilegeArray) ;?>

        <div class="form-group">
            <?= Html::submitButton('设置') ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


</div>