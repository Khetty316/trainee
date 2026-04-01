<?php

if ($moduleIndex === 'superior') {
    $url = ['/cmms/cmms-corrective-work-order-master/view-superior'];
    $pageName = 'Superior';
} elseif ($moduleIndex === 'assigned_pic') {
    $url = ['/cmms/cmms-corrective-work-order-master/view-assigned-tasks'];
    $pageName = 'Assigned Task';
} else {
    $url = ['/cmms/cmms-corrective-work-order-master/index'];
    $pageName = 'Work Orders';
}

$this->title = 'Selected Part/Tool List';
$this->params['breadcrumbs'][] = ['label' => 'Corrective Work Orders', 'url' => $url];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Work Order #' . $model->id];
?>
<?=

$this->render('../cmms-wo-material-request/_materialRequestDetailCm', [
    'model' => $model,
    'materialMaster' => $materialMaster,
    'faults' => $faults,
    'partToolList' => $partToolList,
    'moduleIndex' => $moduleIndex,
    'wotype' => $wotype,
]);
?>
