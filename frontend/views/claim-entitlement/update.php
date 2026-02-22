<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimEntitlement */

$this->title = 'Update Claim Entitlement ' . $model->year . ' : ' . $model->user->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Claim Entitlements - HR', 'url' => ['pending-approval']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claim-entitlement-update">
    
    <?=
    $this->render('_form', [
        'model' => $model,
        'claimType' => $claimType,
        'claimDetails' => $claimDetails,
        'staffList' => $staffList,
        'yearsList' => $yearsList,
        'selectYear' => $selectYear,
        'entitleStatus' => $entitleStatus,
        'hr' => $hr
    ])
    ?>

</div>
