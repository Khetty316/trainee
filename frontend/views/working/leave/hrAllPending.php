<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Pending Leave';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '2']) ?>

    <p class="font-weight-lighter text-success">Leave requests which are pending for Superior / Director's approval.</p>

    <div class="table-responsive">
        <?php
        echo $this->render('__gridviewLeave', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'Direct' => false]);
        ?>
    </div>

</div>

<script>


</script>