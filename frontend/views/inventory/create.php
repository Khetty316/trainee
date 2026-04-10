<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventoryMaterialRequest */

$this->title = 'Create Inventory Material Request';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Material Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-material-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
