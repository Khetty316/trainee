<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsFaultList */
//$pendingUrl = 'personal-pending';
//if ($moduleIndex === 'superior') {
//    $pendingUrl = 'superior-pending';
//}
//$this->title = 'Update Cmms Fault List: ' . $model->id;
//$this->params['breadcrumbs'][] = ['label' => 'Cmms Fault Lists', 'url' => [$pendingUrl]];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id, 'moduleIndex' => $moduleIndex]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cmms-fault-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_fault_details_form', [
        'model' => $model,
        'isUpdate' => $isUpdate,
        'moduleIndex' => $moduleIndex
    ]) ?>

</div>
