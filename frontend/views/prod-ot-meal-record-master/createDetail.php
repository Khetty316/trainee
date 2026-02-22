<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster */
$this->title = $model->ref_code;
$this->params['breadcrumbs'][] = ['label' => 'Production Overtime Meal Record', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]]; 
$this->params['breadcrumbs'][] = 'Create Daily Record';

?>
<div class="prod-ot-meal-record-master-create">

    <h1><?php //= Html::encode($this->title)  ?></h1>
    <?php
    $form = ActiveForm::begin([
        'action' => ['add-new-record', 'id' => $model->id], 
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
