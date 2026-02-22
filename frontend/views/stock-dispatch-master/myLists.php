<?php

$url = ($status === 'acknowledged' ? 'my-acknowledgement-list' : 'my-pending-acknowledgements');
$this->params['breadcrumbs'][] = ['label' => 'My Acknowledgement List', 'url' => [$url]];
$this->params['breadcrumbs'][] = ($status === 'acknowledged' ? 'Acknowledged' : 'Pending');
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

