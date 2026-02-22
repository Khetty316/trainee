<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;

$this->title = 'Report - Department Task Completion';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><?= Html::encode($this->title) ?></h3>
<div class="chart-department-task-completion">
    <?php
    $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-sm-6\">{input}\n{error}</div>",
            'labelOptions' => ['class' => 'col-sm-6 control-label'],
        ],
        'options' => ['autocomplete' => 'off']
    ]);
    ?>
    <?php
//    =
//    $this->render('_dateForm', [
//        'model' => $model
//    ])
    ?>
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                        $form->field($model, 'dateFrom', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(DatePicker::className(), [
                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                            'dateFormat' => 'dd/MM/yyyy',
                            'clientOptions' => [
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                            ],
                        ])->label("Period From");
                ?>
            </div>

        </div>
    </div>
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">

                <?=
                        $form->field($model, 'dateTo', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(DatePicker::className(), [
                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                            'dateFormat' => 'dd/MM/yyyy',
                            'clientOptions' => [
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                            ],
                        ])->label("To");
                ?>
            </div>
        </div>
    </div>
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                        $form->field($model, 'is_internalProject')
                        ->dropDownList(\frontend\models\report\ReportingModel::PROJECT_TYPE_OPTIONS)
                        ->label("Project Type");
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?= Html::submitButton('Search <i class="fas fa-search"></i>', ['class' => 'btn btn-primary float-right']) ?>
                <?= Html::a('Reset', '?', ['class' => 'btn btn-primary float-right mr-1']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="row mt-3">
        <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
            <?=
            $this->renderAjax('/report/_chartDepartmentTaskCompletionFab', [
                'model' => $model,
                'fabricationData' => $fabricationData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ])
            ?>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
            <?=
            $this->renderAjax('/report/_chartDepartmentTaskCompletionElec', [
                'model' => $model,
                'electricalData' => $electricalData,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ])
            ?>
        </div>
    </div>
</div>
