<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\quotation\QuotationMasters */

$this->title = 'Update Quotation Masters: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quotation Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quotation-masters-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
