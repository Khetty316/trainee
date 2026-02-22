<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster */

$this->title = $model->ref_code;
$this->params['breadcrumbs'][] = ['label' => 'Prod Ot Meal Record Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prod-ot-meal-record-master-update">

    <h5><?= Html::encode($this->title)  ?></h5>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
