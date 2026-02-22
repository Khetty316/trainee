<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\VClaimMasterDetails */

$this->title = 'Update V Claim Master Details: ' . $model->claim_master_id;
$this->params['breadcrumbs'][] = ['label' => 'V Claim Master Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->claim_master_id, 'url' => ['view', 'id' => $model->claim_master_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="vclaim-master-details-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
