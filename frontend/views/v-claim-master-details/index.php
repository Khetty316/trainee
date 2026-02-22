<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\claim\VClaimMasterDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'V Claim Master Details';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vclaim-master-details-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create V Claim Master Details', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'claim_master_id',
            'claim_code',
            'claimant_id',
            'claimant_fullname',
            'claim_type',
            //'claim_type_name',
            //'superior_id',
            //'superior_fullname',
            //'claims_status',
            //'claims_status_name',
            //'master_created_date',
            //'master_updated_date',
            //'master_updated_by',
            //'master_updated_by_fullname',
            //'is_deleted',
            //'detail_id',
            //'ref_filename',
            //'ref_code',
            //'receipt_date',
            //'description',
            //'receipt_amount',
            //'amount_to_be_paid',
            //'detail_created_date',
            //'detail_updated_date',
            //'detail_updated_by',
            //'detail_updated_by_fullname',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
