<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\resume\ResumeAcademicQualifications */

$this->title = 'New Academic Qualifications';
$this->params['breadcrumbs'][] = ['label' => 'My Resume', 'url' => ['index-personal']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-academic-qualifications-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formResumeAcademicQualifications', [
        'model' => $model,
    ]) ?>

</div>
