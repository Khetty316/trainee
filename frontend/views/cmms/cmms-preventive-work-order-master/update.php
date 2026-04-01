<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsPreventiveWorkOrderMaster */

$this->title = 'Update PM Schedule: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'PM Schedules', 'url' => ['view-superior']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cmms-preventive-work-order-master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'assignedPICs' => $assignedPICs,
        'isUpdate' => $isUpdate,
    ]) ?>
</div>