<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\cmms\InventorySupplierCmms */

$this->title = 'Add New Item';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control - CMMS'];
$this->params['breadcrumbs'][] = ['label' => 'Item List', 'url' => ['item-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-supplier-cmms-create">

    <h1><?php //= Html::encode($this->title) ?></h1>

    <?= $this->render('_formItemList', [
        'itemList' => $itemList,
        'supplierList' => $supplierList,
        'brandList' => $brandList,
        'modelList' => $modelList,
    ]) ?>

</div>
