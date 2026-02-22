<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectProduction\ProjectProductionMaster */

$this->title = 'Update Project Production Master: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-production-master-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formProductionMain', [
        'model' => $model,
    ]) ?>

</div>
