<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\appraisal\ShortAppraisalMaster */

$this->title = 'Create Short Appraisal';
$this->params['breadcrumbs'][] = ['label' => 'Short Appraisal', 'url' => ['personal']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="short-appraisal-master-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formShortappraisal', [
        'model' => $model,
    ]) ?>

</div>
