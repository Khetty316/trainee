<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsPreventiveWorkOrderMaster */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'PM Schedules', 'url' => ['view-superior']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cmms-preventive-work-order-master-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'id',
            'next_date',
            'start_date',
            'end_date',
            'active_sts',
            'duration',
            'remarks',
            'progress_status_id',
            'assigned_by',
            'cmms_asset_id',
            'frequency_id',
            'created_at',
        ],
    ]) ?>

</div>