<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster */

$this->title = 'Create Prod Ot Meal Record Master';
$this->params['breadcrumbs'][] = ['label' => 'Prod Ot Meal Record Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prod-ot-meal-record-master-create">

    <h1><?php //= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
