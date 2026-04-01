<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\PrereqFormMaster */

$this->title = 'Update: ' . $master->prf_no;
$this->params['breadcrumbs'][] = ['label' => 'Pre-Requisition Form - Personal', 'url' => ['personal-pending-approval']];
$this->params['breadcrumbs'][] = ['label' => $master->prf_no, 'url' => ['view', 'id' => $master->id, 'moduleIndex' => $moduleIndex]];
//$this->params['breadcrumbs'][] = ['label' => $master->id, 'url' => ['view', 'id' => $master->id, 'moduleIndex' => $moduleIndex]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prereq-form-master-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?= $this->render('_form', [
        'master' => $master,
        'items' => $items,
        'vmodel' => $vmodel,
        'isUpdate' => $isUpdate,
        'isView' => $isView,
        'moduleIndex' => $moduleIndex,
        'worklists' => $worklists,
        'hasSuperiorUpdate' => $hasSuperiorUpdate,
        'departmentList' => $departmentList,
//        'supplierList' => $supplierList,
//        'brandList' => $brandList,
//        'modelList' => $modelList
    ]) ?>

</div>
