<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
?>
<div class="claim-master-index">
    <?= $this->render('__claimEntitlementNavBarHr', ['pageKey' => '3']) ?>

    <?=
    $this->render('__entitlementSummary', [
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