<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsPreventiveWorkOrderMaster */

$this->title = 'Create PM Schedule';
$this->params['breadcrumbs'][] = ['label' => 'PM Schedule', 'url' => ['view-superior']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cmms-preventive-work-order-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'assignedPICs' => $assignedPICs,
        'isUpdate' => $isUpdate,
    ]) ?>

</div>