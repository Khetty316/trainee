<?php

if ($moduleIndex === 'superior') {
    $url = ['/cmms/cmms-preventive-work-order-master/view-superior'];
    $pageName = 'Superior';
} elseif ($moduleIndex === 'assigned_tasks') {
    $url = ['/cmms/cmms-preventive-work-order-master/view-assigned-tasks'];
    $pageName = 'Assigned Task';
} else {
    $url = ['/cmms/cmms-corrective-work-order-master/index'];
    $pageName = 'Work Orders';
}

$this->title = 'Selected Part/Tool List';
$this->params['breadcrumbs'][] = ['label' => 'Preventive Work Order Schedule', 'url' => $url];
$this->params['breadcrumbs'][] = ['label' => 'PM Schedule', 'url' => ['/cmms/cmms-preventive-work-order-master/pm-wo-form', 'id' => $model->id, 'moduleIndex' => $moduleIndex]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Work Order #' . $model->id];
?>
<?=

$this->render('../cmms-wo-material-request/_materialRequestDetailPm', [
    'model' => $model,
    'materialMaster' => $materialMaster,
    'materialDetails' => $materialDetails,
    'partToolList' => $partToolList,
    'moduleIndex' => $moduleIndex,
    'wotype' => $wotype,
]);
?>
