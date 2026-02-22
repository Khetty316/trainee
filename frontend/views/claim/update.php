<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimMaster */

$this->title = 'Update Claim: ' . $model->claim_code;
$this->params['breadcrumbs'][] = ['label' => 'My Claims - Personal', 'url' => ['personal-claim-pending']];
$this->params['breadcrumbs'][] = ['label' => $model->claim_code, 'url' => ['personal-view-claim', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="claim-master-update">

    <h5><?= Html::encode($this->title) ?></h5>

    <?=
    $this->render('_form', [
        'model' => $model,
        'claimTypeList' => $claimTypeList,
        'superior' => $superior,
        'userList' => $userList,
        'claimDetail' => $claimDetail
    ])
    ?>

</div>
