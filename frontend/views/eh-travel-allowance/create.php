<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhTravelAllowanceMaster */

$this->title = 'Create Eh Travel Allowance Master';
$this->params['breadcrumbs'][] = ['label' => 'Eh Travel Allowance Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eh-travel-allowance-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
