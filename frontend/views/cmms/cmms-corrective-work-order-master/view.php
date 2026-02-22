<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsCorrectiveWorkOrderMaster */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cmms Corrective Work Order Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cmms-corrective-work-order-master-view">

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
            'supervisor_id',
            'cmms_machine_detail_id',
            'machine_priority_id',
            'fault_reported_by',
            'fault_reported_at',
            'current_revision_id',
            'active_sts',
        ],
    ]) ?>

</div>
