<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\quotation\QuotationMasters */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quotation Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quotation-masters-view">

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
            'requestor_id',
            'project_code',
            'description:ntext',
            'proc_approval',
            'proc_remark:ntext',
            'proc_approve_by',
            'requestor_approval',
            'requestor_remark:ntext',
            'requestor_approve_by',
            'manager_approval',
            'manager_remark:ntext',
            'manager_approve_by',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ],
    ]) ?>

</div>
