<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventoryMaterialRequest */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Material Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="inventory-material-request-view">

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
            'user_id',
            'reference_type',
            'reference_id',
            'desc',
            'inventory_detail_id',
            'request_qty',
            'approved_qty',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
            'approved_by',
            'approved_at',
            'status',
        ],
    ]) ?>

</div>
