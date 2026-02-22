<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\claim\ClaimsDetail */

$this->title = $model->claims_detail_id;
$this->params['breadcrumbs'][] = ['label' => 'Claims Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="claims-detail-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->claims_detail_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->claims_detail_id], [
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
            'claims_detail_id',
            'claim_master_id',
            'claim_type',
            'date1',
            'date2',
            'company_name',
            'receipt_no',
            'detail',
            'project_account',
            'amount',
            'receipt_lost',
            'filename',
            'is_submitted',
            'is_deleted',
            'created_at',
            'created_by',
            'update_at',
            'update_by',
        ],
    ]) ?>

</div>
