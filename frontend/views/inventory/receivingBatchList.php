<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\inventoryPurchaseOrderReceiveBatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($moduleIndex === 'exec') {
    $pageName = 'Receiving - Executive';
    $module = 'execReceiving';
    $key = 3;
} else if ($moduleIndex === 'assist') {
    $pageName = 'Receiving - Assistant';
    $module = 'assistReceiving';
    $key = 3;
}else if ($moduleIndex === 'maintenanceHead') {
    $pageName = 'Receiving - Head of Maintenance';
    $module = 'maintenanceHeadReceiving';
    $key = 3;
}

$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-purchase-order-receive-batch-index">
    <?= $this->render('__inventoryNavBar', ['module' => $module, 'pageKey' => $key]) ?>

    <?=
    $this->render('_receivingList', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'moduleIndex' => $moduleIndex
    ])
    ?>

</div>
