<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\inventoryPurchaseOrderReceiveBatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Receiving History';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Receiving', 'url' => ['executive-pending-receive-purchase-order']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-purchase-order-receive-batch-index">
    <?= $this->render('__receivingNavBar', ['module' => $moduleIndex, 'pageKey' => '3']) ?>

    <?=
    $this->render('_receivingList', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'moduleIndex' => $moduleIndex
    ])
    ?>

</div>
