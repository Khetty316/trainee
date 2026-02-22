<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectproduction\ProjectProductionPanelFabBqMaster */

$this->title = "Update BQ: ".Html::encode($model->bq_no);
$this->params['breadcrumbs'][] = ['label' => 'B.Q. List (All)', 'url' => ['index-material-bq']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-panel-fab-bq-master-create">
    <?=
    $this->render('_formMaterialBq', [
        'model' => $model,
    ])
    ?>
</div>
