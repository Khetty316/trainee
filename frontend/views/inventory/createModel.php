<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\cmms\InventorySupplierCmms */

$this->title = 'Add New Model';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control - CMMS'];
$this->params['breadcrumbs'][] = ['label' => 'Brand', 'url' => ['model-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-supplier-cmms-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formModel', [
        'model' => $model,
    ]) ?>

</div>
