<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\quotation\QuotationMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quotation Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-masters-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Quotation Masters', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'requestor_id',
            'project_code',
            'description:ntext',
            'proc_approval',
            //'proc_remark:ntext',
            //'proc_approve_by',
            //'requestor_approval',
            //'requestor_remark:ntext',
            //'requestor_approve_by',
            //'manager_approval',
            //'manager_remark:ntext',
            //'manager_approve_by',
            //'created_by',
            //'created_at',
            //'updated_by',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
