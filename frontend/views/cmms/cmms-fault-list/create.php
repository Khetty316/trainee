<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsFaultList */

$this->title = 'Report New Fault';
$pendingUrl = 'personal-pending';
if ($moduleIndex === 'superior') {
    $pendingUrl = 'superior-pending';
}
//    $this->params['breadcrumbs'][] = ['label' => 'Active Faults', 'url' => [$pendingUrl]];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cmms-fault-list-create">

    <!--<h1><? Html::encode($this->title) ?></h1>-->

    <?= $this->renderAjax('_fault_details_form', [
            'model' => $model,
            'isUpdate' => $isUpdate,
            'moduleIndex' => $moduleIndex
    ]) ?>

</div>
