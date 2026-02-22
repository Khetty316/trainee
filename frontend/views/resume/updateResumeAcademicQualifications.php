<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\resume\ResumeAcademicQualifications */

$this->title = 'Update Academic Qualifications';
$this->params['breadcrumbs'][] = ['label' => 'My Resume', 'url' => ['index-personal']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-academic-qualifications-update">

    <h3><?= Html::encode($this->title) ?></h3>
    <?=
    Html::a('Delete <i class="far fa-trash-alt"></i>', ['delete-academic-qualification', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ])
    ?>
    <?=
    $this->render('_formResumeAcademicQualifications', [
        'model' => $model,
    ])
    ?>

</div>
