<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$panel = $model->projProdPanel;
$project = $panel->projProdMaster;
$model->tempTaskName = $model->prodElecTask->elecTaskCode->name;

$this->title = "Assigning Task - " . $model->tempTaskName;
$this->params['breadcrumbs'][] = ['label' => 'Electrical Task Assignment'];
$this->params['breadcrumbs'][] = ['label' => 'Project List', 'url' => ['index-elec-project-list']];
$this->params['breadcrumbs'][] = ['label' => $project->project_production_code, 'url' => ['index-elec-project-panels', 'id' => $project->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="work-assignment-master-form">
    <h4><?= Html::encode($this->title) ?></h4>
    <?php
    echo $this->render("_formAssignTask",[
        'panel' => $panel,
        'project' => $project,
        'model' => $model,
        'task' => $task,
        'staffList'=>$staffList
    ]);
    ?>


</div>
