<?php

//use yii\helpers\Html;
//use yii\grid\GridView;
//use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'To Be Confirmed';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '3']) ?>
    <p class="font-weight-lighter text-success">Approved leave requests, to be confirmed at the end of the month (in tab "Monthly Summary").</p>

    <?php
    echo $this->render('__gridviewLeave', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'Super' => false,
        'HrCancelLeave' => true]); // Allow HR to recall leave
    ?>
</div>
