<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhTravelAllowanceMaster */

$this->title = 'Update Eh Travel Allowance Master: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Eh Travel Allowance Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="eh-travel-allowance-master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
