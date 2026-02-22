<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQPanelsTemplate */

$this->title = 'Update Project Q Panels Template: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Project Q Panels Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="project-qpanels-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
