<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimMaster */

$this->title = 'Update Work Request: ';
$this->params['breadcrumbs'][] = ['label' => 'My Corrective Work Requests - Personal', 'url' => ['#']];
//$this->params['breadcrumbs'][] = ['label' => $model->claim_code, 'url' => ['personal-view-claim', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="work-request-create">

    <h5><?= Html::encode($this->title) ?></h5>

    <?=
    $this->render('_work_request_form', [
        'workRequest' => $workRequest,
    ])
    ?>

</div>
