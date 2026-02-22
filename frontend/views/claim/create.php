<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimMaster */

$this->title = 'New Claim Application';
$this->params['breadcrumbs'][] = ['label' => 'My Claims - Personal', 'url' => ['personal-claim-pending']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claim-master-create">

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
