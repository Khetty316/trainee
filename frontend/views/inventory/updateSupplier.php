<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventorySupplier */

if ($moduleIndex === 'execStock') {
    $pageName = 'Stock - Executive';
    $url = 'inventory/inventory/supplier-list?type=execStock';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Stock - Assistant';
    $url = 'inventory/inventory/supplier-list?type=assistStock';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Stock - Project Coordinator';
    $url = 'inventory/inventory/supplier-list?type=projcoorStock';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Stock - Head of Maintenance';
    $url = 'inventory/inventory/supplier-list?type=maintenanceHeadStock';
}

$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = $pageName;
$this->params['breadcrumbs'][] = ['label' => 'Supplier List', 'url' => [$url]];
$this->params['breadcrumbs'][] = $model->code;
?>
<div class="inventory-supplier-cmms-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formSupplier', [
        'model' => $model,
    ]) ?>

</div>
