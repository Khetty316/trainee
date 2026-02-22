<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhExecOtMeal */

$this->title = 'Create Eh Exec Ot Meal';
$this->params['breadcrumbs'][] = ['label' => 'Eh Exec Ot Meals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eh-exec-ot-meal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
