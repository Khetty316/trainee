<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\quotation\QuotationMasters */

$this->title = 'New Request';
$this->params['breadcrumbs'][] = ['label' => 'Request For Quotation', 'url' => ['staff-view-quotation-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-masters-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formQuotation', [
        'model' => $model,
    ]) ?>

</div>
