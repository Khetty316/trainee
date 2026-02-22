<?php

use yii\helpers\Html;

$this->title = "Assigning Task - ";
$this->params['breadcrumbs'][] = ['label' => 'Fabrication Task Assignment'];
$this->params['breadcrumbs'][] = ['label' => 'Project List', 'url' => ['index-fab-project-list']];
$this->params['breadcrumbs'][] = ['label' => $project->project_production_code, 'url' => ['index-fab-project-panels', 'id' => $project->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="work-assignment-master-form">
    <h4><?= Html::encode($this->title) ?></h4>
    <?php
    echo $this->render("_formAssignTaskMultiple", [
        'panel' => $panel,
        'project' => $project,
        'model' => $model,
        'staffList' => $staffList,
        'taskList' => $taskList
    ]);
    ?>


</div>
