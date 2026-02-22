<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\claim\ClaimsDetail */

$this->title = 'Update Claims Detail: ' . $model->claims_detail_id;
$this->params['breadcrumbs'][] = ['label' => 'Claims Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->claims_detail_id, 'url' => ['view', 'id' => $model->claims_detail_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="claims-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
