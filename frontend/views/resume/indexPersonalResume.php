<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\resume\ResumeEmployHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'My Resume';
$this->params['breadcrumbs'][] = $this->title;
//$academicList = $dataProviderAcademic->getModels();
//$employList = $dataProviderEmploy->getModels();
?>
<style>
    .table-sm tr td{
        margin-top: 0px;
        margin: 0px;
        padding: 2px 10px 2px 10px;
    }
</style>
<div class="resume-employ-history-index">

    <h3><?= Html::encode($this->title) ?> 
        <?= Html::a('PDF <i class="far fa-file-pdf"></i>', yii\helpers\Url::to(['/resume/generate-personal-resume', 'type' => 'pdf']), ['class' => 'btn btn-primary', 'target' => '_blank']) ?>
        <?= Html::a('DOC <i class="far fa-file-word"></i>', yii\helpers\Url::to(['/resume/generate-personal-resume', 'type' => 'docx']), ['class' => 'btn btn-primary', 'target' => '_blank']) ?>
    </h3>



    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Academic Qualifications:</legend>
        <p><?= Html::a('Academy Record <i class="fas fa-plus"></i>', ['create-academic-qualification'], ['class' => 'btn btn-success']) ?></p>
        <div id="div_academic_ajax"></div>
    </fieldset>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Employment History:</legend>
        <p><?= Html::a('Employment History <i class="fas fa-plus"></i>', ['create-employment-history'], ['class' => 'btn btn-success']) ?></p>
        <div id="div_employhistory_ajax"></div>
    </fieldset>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Project References:</legend>
        <p><?= Html::a('Project Reference <i class="fas fa-plus"></i>', ['create-project-ref'], ['class' => 'btn btn-success']) ?></p>
        <div id="div_projectref_ajax"></div>
        <h6>From Project Module:</h6>


        <table class="table table-sm table-bordered table-striped">
            <?php
            foreach ($projectList as $key => $project) {
                ?>
                <tr>
                    <td style="width: 30%" class="font-weight-bold"></td>
                    <td>
                        <span class="font-weight-bolder">- </span><?= $project->title_long ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </fieldset>
</div>
<script>

    $(function () {
        loadAcademicDiv();
        loadEmployhistoryDiv();
        loadProjectRefDiv();
    });


    function loadAcademicDiv() {
        $("#div_academic_ajax").load('<?= \yii\helpers\Url::to(['/resume/personal-academic-ajax', 'userId' => Yii::$app->user->id]) ?>');
    }
    function loadEmployhistoryDiv() {
        $("#div_employhistory_ajax").load('<?= \yii\helpers\Url::to(['/resume/personal-employment-history-ajax', 'userId' => Yii::$app->user->id]) ?>');
    }
    function loadProjectRefDiv() {
        $("#div_projectref_ajax").load('<?= \yii\helpers\Url::to(['/resume/personal-project-ref-ajax', 'userId' => Yii::$app->user->id]) ?>');
    }





</script>