<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventorySupplier */

$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Supplier', 'url' => ['supplier-list']];
$this->params['breadcrumbs'][] = $model->code;
?>
<div class="inventory-supplier-cmms-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formSupplier', [
        'model' => $model,
    ]) ?>

</div>
