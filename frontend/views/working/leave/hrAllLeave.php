<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'All Leave';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '6']) ?>

    <?php //= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary mt-3']) ?>
    <div class="table-responsive">
        <div class="mt-3">
            <?php
            echo $this->render('__gridviewLeave', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'HrCancelLeave' => true,
                'type' => true]);
            ?>
        </div>
    </div>

</div>