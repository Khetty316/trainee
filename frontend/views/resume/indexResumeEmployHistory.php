<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\resume\ResumeEmployHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resume Employ Histories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-employ-history-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Resume Employ History', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'employ_role',
            'employ_company',
            'employ_period',
            //'employ_detail:ntext',
            //'active_sts',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
