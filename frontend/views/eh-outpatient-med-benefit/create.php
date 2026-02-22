<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhOutpatientMedMaster */

$this->title = 'Create Eh Outpatient Med Master';
$this->params['breadcrumbs'][] = ['label' => 'Eh Outpatient Med Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eh-outpatient-med-master-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
