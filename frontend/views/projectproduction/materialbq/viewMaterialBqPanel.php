<?php

use yii\helpers\Html;

$this->title = $panel->project_production_panel_code;
$this->params['breadcrumbs'][] = ['label' => 'B.Q. List (By Projects)', 'url' => ['/production/material-bq/index-material-bq']];
$this->params['breadcrumbs'][] = ['label' => $panel->projProdMaster->project_production_code, 'url' => ['/production/material-bq/view-material-bq', 'id' => $panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->sort = false;
?>

<div class="row">
    <div class="col-md-5 order-md-2">
        <?= $this->render("_detailViewProjProdDetail", ['projProdMaster' => $panel->projProdMaster, 'panel' => $panel]) ?>
    </div>
    <div class="col-md-7 order-md-1">
        <?= Html::a("New B.Q. <i class='fas fa-plus'></i>", ['create-material-bq', 'panelId' => $panel->id], ['class' => 'btn btn-primary']) ?>

        <?php
        echo $this->render("_gridViewMaterialBq", [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
        ?>
        <?= Html::a("New B.Q. <i class='fas fa-plus'></i>", ['create-material-bq', 'panelId' => $panel->id], ['class' => 'btn btn-primary']) ?>

    </div>

</div>
