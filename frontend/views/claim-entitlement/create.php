<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimEntitlement */

$this->title = 'Create';
$this->params['breadcrumbs'][] = ['label' => 'Claim Entitlements - HR', 'url' => ['pending-approval']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claim-entitlement-create">

    <?=
    $this->render('_form', [
        'model' => $model,
        'claimType' => $claimType,
        'claimDetails' => $claimDetails,
        'staffList' => $staffList,
        'yearsList' => $yearsList,
        'selectYear' => $selectYear,
        'hr' => true
    ])
    ?>

</div>
