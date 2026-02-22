<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\form\CovidStatusForm */

$this->title = 'Create Covid Status Form';
$this->params['breadcrumbs'][] = ['label' => 'Covid Status Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="covid-status-form-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
