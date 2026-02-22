<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hr\HrPayslip */

$this->title = 'Update Hr Payslip: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Hr Payslips', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hr-payslip-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
