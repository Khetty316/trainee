<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\ClientReminderLetterTemplate */
/* @var $form yii\widgets\ActiveForm */

if ($model->isNewRecord) {

    $this->title = 'Create';
    $this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => 'Debt Reminder Letter Template', 'url' => ['index-debt-reminder-letter-template']];
    $this->params['breadcrumbs'][] = $this->title;
} else {

    $this->title = 'Update';
    $this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => 'Debt Reminder Letter Template', 'url' => ['index-debt-reminder-letter-template']];
    $this->params['breadcrumbs'][] = ['label' => $model->letter_name, 'url' => ['view-client-reminder-letter-template', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = $this->title;
}
?>

<link href="/css/summernote.css" rel="stylesheet">
<script src="/js/summernote.min.js" type="text/javascript"></script>

<div class="client-reminder-letter-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?= $form->field($model, 'letter_name')->textInput(['maxlength' => true, 'autocomplete' => 'off',]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?=
            $form->field($model, 'content')->textarea([
                'rows' => 8,
                'class' => 'form-control reminder-content-editor',
            ])
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <?=
                    $form->field($model, 'active_sts')
                    ->dropDownList([
                        0 => 'Yes',
                        1 => 'No'
                    ])
                    ->label('Active Status')
            ?>
        </div>
    </div>

    <!--
    <?= $form->field($model, 'created_by')->textInput() ?>
    <?= $form->field($model, 'created_at')->textInput() ?>
    <?= $form->field($model, 'updated_by')->textInput() ?>
    <?= $form->field($model, 'updated_at')->textInput() ?> -->

    <div class="row">
        <div class="col-md-4">
            <?= Html::submitButton('Save <i class="fas fa-check"></i>', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

    <script type="text/javascript">
        $(function () {
            $('.reminder-content-editor').summernote({
                height: 400,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['paragraph']],
                    ['height', ['height']],
//                    ['table', ['table']],
                    ['insert', ['link', 'hr', 'picture']],
//                    ['view', ['codeview']]
                ]
            });
        });
    </script>
</div>
