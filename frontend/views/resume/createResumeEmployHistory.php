<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\resume\ResumeEmployHistory */
$this->title = 'New Employment History';
$this->params['breadcrumbs'][] = ['label' => 'My Resume', 'url' => ['index-personal']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-employ-history-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formResumeEmployHistory', [
        'model' => $model,
    ]) ?>

</div>
