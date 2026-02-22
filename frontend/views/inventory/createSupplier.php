<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventorySupplier */

$this->title = 'Add New Supplier';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Supplier', 'url' => ['supplier-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-supplier-create">

    <h1><?php //= Html::encode($this->title) ?></h1>

    <?= $this->render('_formSupplier', [
        'model' => $model,
    ]) ?>

</div>
