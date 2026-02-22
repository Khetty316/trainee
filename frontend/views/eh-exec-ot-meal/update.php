<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhExecOtMeal */

$this->title = 'Update Eh Exec Ot Meal: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Eh Exec Ot Meals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="eh-exec-ot-meal-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
