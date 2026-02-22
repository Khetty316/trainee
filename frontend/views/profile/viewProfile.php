<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'User Profile'; // . " - " . $model->fullname;
//$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <h3> <?= Html::encode(Yii::$app->user->identity->fullname) ?> </h3>

    <?= $this->render('__ProfileNavBar', ['module' => 'account_claims', 'pageKey' => '1']); ?>
    <p class=" ">
        <?= Html::a('Change Password <i class="fas fa-key"></i>', ['reset-password'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="col-12">
        <?= $this->render('/sysadmin/user/_detailViewUser', ['model' => $model]) ?>
    </div>
</div>