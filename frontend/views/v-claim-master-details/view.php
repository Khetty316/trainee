<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\VClaimMasterDetails */

$this->title = $model->claim_master_id;
$this->params['breadcrumbs'][] = ['label' => 'V Claim Master Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="vclaim-master-details-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->claim_master_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->claim_master_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'claim_master_id',
            'claim_code',
            'claimant_id',
            'claimant_fullname',
            'claim_type',
            'claim_type_name',
            'superior_id',
            'superior_fullname',
            'claims_status',
            'claims_status_name',
            'master_created_date',
            'master_updated_date',
            'master_updated_by',
            'master_updated_by_fullname',
            'is_deleted',
            'detail_id',
            'ref_filename',
            'ref_code',
            'receipt_date',
            'description',
            'receipt_amount',
            'amount_to_be_paid',
            'detail_created_date',
            'detail_updated_date',
            'detail_updated_by',
            'detail_updated_by_fullname',
        ],
    ]) ?>

</div>
