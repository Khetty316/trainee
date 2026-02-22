<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster */

$this->title = $model->ref_code;
$this->params['breadcrumbs'][] = ['label' => 'Production Overtime Meal Record', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]]; 
$this->params['breadcrumbs'][] = 'Daily Record Form';
\yii\web\YiiAsset::register($this);
?>
<div class="prod-ot-meal-record-master-view">
    <?php
    $form = ActiveForm::begin([
        'action' => ['update-detail-record', 'id' => $detail->id],
        'method' => 'post',
    ]);
    ?>

    <?=
    $this->render('_formDetail', [
        'model' => $model,
        'detail' => $detail,
        'selectedStaffIds' => $selectedStaffIds,
        'staffListFab' => $staffListFab,
        'staffListElec' => $staffListElec,
        'staffListFabElec' => $staffListFabElec,
        'form' => $form
    ])
    ?>
<?php ActiveForm::end(); ?>

</div>
