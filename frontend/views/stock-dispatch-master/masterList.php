<?php

$this->params['breadcrumbs'][] = ['label' => 'Stock Dispatch Masters', 'url' => 'index'];
$this->params['breadcrumbs'][] = $dispatchMaster->dispatch_no;
?>
<?=

$this->render('dispatchItemList', [
    'dispatchMaster' => $dispatchMaster,
    'pendingDispatch' => $pendingDispatch,
    'pendingAdjust' => $pendingAdjust,
    'pendingReturn' => $pendingReturn,
    'confirmedDispatch' => $confirmedDispatch,
    'confirmedAdjust' => $confirmedAdjust,
    'confirmedReturn' => $confirmedReturn,
    'status' => $status
]);
?>

