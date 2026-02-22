<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectproduction\ProjectProductionPanelFabBqMaster */

$this->title = "New BQ: ";
$this->params['breadcrumbs'][] = ['label' => 'BQ - Project List', 'url' => ['/production/material-bq/index-material-bq']];
$this->params['breadcrumbs'][] = ['label' => $panel->projProdMaster->project_production_code, 'url' => ['/production/material-bq/view-material-bq', 'id' => $panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => $panel->project_production_panel_code, 'url' => ['view-material-bq-panel', 'panelId' => $panel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-panel-fab-bq-master-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formMaterialBq', [
        'model' => $model,
    ]) ?>

</div>
