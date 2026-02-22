<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\appraisal\ShortAppraisalMaster */

$this->title = 'Update Short Appraisal Master: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Short Appraisal Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="short-appraisal-master-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formShortappraisal', [
        'model' => $model,
    ]) ?>

</div>
