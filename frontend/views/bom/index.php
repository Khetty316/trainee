<?php

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\bom\bomdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bill of Materials';
$production = $bomMaster->productionPanel->projProdMaster;
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['/production/production/view-production-main', 'id' => $production->id]];
$this->params['breadcrumbs'][] = $this->title;
$canReverse = 1;
?>
<?=

$this->render('_bom', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'bomMaster' => $bomMaster,
    'justCreated' => $justCreated,
    'canReverse' => $canReverse
])
?>
