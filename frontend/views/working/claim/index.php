<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Claims Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claims-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Claims Detail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'claims_detail_id',
            'claim_master_id',
            'claim_type',
            'date1',
            'date2',
            //'company_name',
            //'receipt_no',
            //'detail',
            //'project_account',
            //'amount',
            //'receipt_lost',
            //'filename',
            //'is_submitted',
            //'is_deleted',
            //'created_at',
            //'created_by',
            //'update_at',
            //'update_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
