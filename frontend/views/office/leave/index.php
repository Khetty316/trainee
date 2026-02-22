<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Leave Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Leave Master', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'requestor_id',
            'leave_type',
            'superior_id',
            'reason:ntext',
            //'start_date',
            //'start_section',
            //'end_date',
            //'end_section',
            //'total_days',
            //'leave_status',
            //'leave_confirm_year',
            //'leave_confirm_month',
            //'days_annual',
            //'days_unpaid',
            //'days_sick',
            //'days_others',
            //'confirm_flag',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
