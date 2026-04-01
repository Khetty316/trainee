<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\PrereqFormMaster */

$moduleIndex = 'inventory';

if ($module === 'execPendingPurchasing') {
    $pageName = 'Purchasing - Executive';
    $pageName2 = 'Pending Requisition Approval';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingInventory'];
} else if ($module === 'execAllPurchasing') {
    $pageName = 'Purchasing - Executive';
    $pageName2 = 'All Pre-Requisitions';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'allInventory'];
} else if ($module === 'assistPendingPurchasing') {
    $pageName = 'Purchasing - Assistant';
    $pageName2 = 'Pending Requisition Approval';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingInventory'];
} else if ($module === 'assistAllPurchasing') {
    $pageName = 'Purchasing - Assistant';
    $pageName2 = 'All Pre-Requisitions';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'allInventory'];
} else if ($module === 'projcoorPendingApproval') {
    $pageName = 'Purchasing - Project Coordinator';
    $pageName2 = 'Pending Requisition Approval';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingApprovalInventoryProjcoor'];
} else if ($module === 'projcoorReadyForProcurement') {
    $pageName = 'Purchasing - Project Coordinator';
    $pageName2 = 'Ready for Procurement';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingProcurementInventoryProjcoor'];
} else if ($module === 'projcoorAllApproval') {
    $pageName = 'Purchasing - Project Coordinator';
    $pageName2 = 'All Pre-Requisitions';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'allInventoryProjcoor'];
} else if ($module === 'maintenanceHeadPendingApproval') {
    $pageName = 'Purchasing - Head of Maintenance';
    $pageName2 = 'Pending Requisition Approval';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingApprovalInventoryMaintenanceHead'];
} else if ($module === 'maintenanceHeadReadyForProcurement') {
    $pageName = 'Purchasing - Head of Maintenance';
    $pageName2 = 'Ready for Procurement';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingProcurementInventoryMaintenanceHead'];
} else if ($module === 'maintenanceHeadAllApproval') {
    $pageName = 'Purchasing - Head of Maintenance';
    $pageName2 = 'All Pre-Requisitions';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'allInventoryMaintenanceHead'];
}

$this->title = $master->prf_no;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = ['label' => $pageName2, 'url' => $url];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => $url2 = ['view-pre-requisition', 'id' => $master->id, 'moduleIndex' => $module]];
$this->params['breadcrumbs'][] = ['label' => "Update"];
\yii\web\YiiAsset::register($this);
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
