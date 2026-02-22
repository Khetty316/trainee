<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\cmms\InventorySupplierCmms */

$this->params['breadcrumbs'][] = ['label' => 'Inventory Control - CMMS'];
$this->params['breadcrumbs'][] = ['label' => 'Brand', 'url' => ['brand-list']];
$this->params['breadcrumbs'][] = $model->code;
?>
<div class="inventory-supplier-cmms-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formBrand', [
        'model' => $model,
    ]) ?>

</div>
