<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\PrereqFormMaster */

if($moduleIndex === 'projcoor'){
    $url = 'projcoor-pre-requisition-pending-approval';
}else{
    $url = 'executive-pre-requisition-pending-approval';
}

$this->title = 'Pre-Requisition Form';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Purchasing - New Item', 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
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
        'moduleIndex' => $moduleIndex,
        'key' => 1
    ])
    ?>

</div>
