<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\form\CovidStatusForm */

$this->title = 'Update Covid Status Form: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Covid Status Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="covid-status-form-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
