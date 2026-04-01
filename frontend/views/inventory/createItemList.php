<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventorySupplier */
if ($moduleIndex === 'execStock') {
    $pageName = 'Stock - Executive';
    $url = 'inventory/inventory/item-list?type=execStock';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Stock - Assistant';
    $url = 'inventory/inventory/item-list?type=assistStock';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Stock - Project Coordinator';
    $url = 'inventory/inventory/item-list?type=projcoorStock';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Stock - Head of Maintenance';
    $url = 'inventory/inventory/item-list?type=maintenanceHeadStock';
}

$this->title = 'Add Item';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = $pageName;
$this->params['breadcrumbs'][] = ['label' => 'Item List', 'url' => [$url]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-supplier-create">

    <h1><?php //= Html::encode($this->title)  ?></h1>

    <?=
    $this->render('_formItemList', [
        'itemList' => $itemList,
        'supplierList' => $supplierList,
        'modelBrandList' => $modelBrandList,
        'departmentList' => $departmentList,
        'currencyList' => $currencyList
    ])
    ?>

</div>
