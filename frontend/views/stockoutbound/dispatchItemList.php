<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\bom\VStockDispatchMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ($action === 'adjust' ? 'Adjust Dispatch Quantity' : 'Return Dispatched Quantity');
$production = $bomMaster->productionPanel->projProdMaster;
$this->params['breadcrumbs'][] = ['label' => 'Stock Outbound', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['view-panels', 'id' => $production->id]];
$url = ($action === 'adjust' ? 'stock-adjustment' : 'stock-return');
$this->params['breadcrumbs'][] = ['label' => $bomMaster->productionPanel->project_production_panel_code, 'url' => [$url, 'productionPanelId' => $bomMaster->productionPanel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="table-responsive">
    <h4>Dispatch No: <?= $model->dispatch_no ?></h4>

    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?productionPanelId=' . $bomMaster->productionPanel->id . '&dispatchId=' . $model->id . '&action=' . $action, ['class' => 'btn btn-primary']) ?>    
    </p>
    <?=
    $this->render('..\v-stock-dispatch-master\indexDispatchMaster', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'action' => $action
    ])
    ?>
</div>
