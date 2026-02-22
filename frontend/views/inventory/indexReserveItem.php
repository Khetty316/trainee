<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventoryReserveItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inventory Reserve Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-reserve-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Inventory Reserve Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'inventory_detail_id',
            'reserved_qty',
            'dispatched_qty',
            //'available_qty',
            //'created_by',
            //'created_at',
            //'updated_by',
            //'updated_at',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
