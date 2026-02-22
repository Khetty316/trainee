<?php
use yii\helpers\Html;

$this->title = ($module === 'finance' ? 'Claim Review - Finance' : 'My Claims - Personal');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claim-master-index">
    <?= $this->render('__claimNavBar', ['module' => $module, 'pageKey' => $key]) ?>

    <?=
    $this->render('../claim-entitlement/__entitlementSummary', [
        'claimSummarys' => $claimSummarys,
        'hasEntitlement' => $hasEntitlement,
        'yearList' => $yearList,
        'monthList' => $monthList,
        'claimTypes' => $claimTypes,
        'staffList' => $staffList,
        'intMonth' => $intMonth,
        'year' => $year,
        'month' => $month,
        'claimType' => $claimType,
        'staff' => $staff,
        'module' => $module,
    ])
    ?>
</div>
