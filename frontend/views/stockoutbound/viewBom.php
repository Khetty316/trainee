<?php

$this->title = 'Bill of Materials';
$production = $bomMaster->productionPanel->projProdMaster;
$this->params['breadcrumbs'][] = ['label' => 'Stock Outbound', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['view-panels', 'id' => $production->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<?=

$this->render('..\bom\_bom', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'bomMaster' => $bomMaster,
    'justCreated' => $justCreated
])
?>
