<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\PrereqFormMaster */

$this->title = $master->prf_no;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Purchasing - New Item', 'url' => ['executive-pre-requisition-pending-approval']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view-pre-requisition', 'id' => $master->id, 'moduleIndex' => $moduleIndex]];
$this->params['breadcrumbs'][] = ['label' => "Update"];
?>
<div class="prereq-form-master-create">

    <?=
    $this->render('_prereq_form_unified', [
        'master' => $master,
        'items' => $items,
        'vmodel' => $vmodel,
        'isUpdate' => $isUpdate,
        'isView' => $isView,
        'moduleIndex' => $moduleIndex,
        'worklists' => $worklists,
        'hasSuperiorUpdate' => $hasSuperiorUpdate,
        'departmentList' => $departmentList,
        'supplierList' => $supplierList,
        'brandList' => $brandList,
        'currencyList' => $currencyList,
        'moduleIndex' => $moduleIndex
    ])
    ?>

</div>
