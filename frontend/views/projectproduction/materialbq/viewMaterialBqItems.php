<?php

use yii\helpers\Html;


$this->title = Html::encode($bqMaster->bq_no);
$panel = $bqMaster->projProdPanel;
$this->params['breadcrumbs'][] = ['label' => 'B.Q. List (By Projects)', 'url' => ['/production/material-bq/index-material-bq-by-projects']];
$this->params['breadcrumbs'][] = ['label' => $panel->projProdMaster->project_production_code, 'url' => ['/production/material-bq/view-material-bq', 'id' => $panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => $panel->project_production_panel_code, 'url' => ['view-material-bq-panel', 'panelId' => $panel->id]];
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->sort = false;
$dataProvider->getModels();
$dataProvider->pagination = false;
?>

<div class="project-qpanels-view">
    <h3><?= Html::encode($this->title . " (" . ($bqMaster->bqStatus->status_name ?? null) . ")") ?></h3>
    <div class="row">
        <div class="col-md-5 order-md-2">
            <?= $this->render("_detailViewProjProdDetail", ['projProdMaster' => $panel->projProdMaster, 'panel' => $bqMaster->projProdPanel]) ?>
        </div>
        <div class="col-md-7 order-md-1">
            <?=
            $this->render("_gridViewMaterialBqItems", [
                'dataProvider' => $dataProvider
            ])
            ?>
        </div>

    </div>
</div>