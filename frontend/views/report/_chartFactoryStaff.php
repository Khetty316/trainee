<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\User;
use yii\jui\DatePicker;

$this->title = 'Report - Individual';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><?= Html::encode($this->title) ?></h3>
<div class="chart-quotation-hit mt-3">
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
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                        $form->field($model, 'userId', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->dropdownList(ArrayHelper::map(User::getStaffList_productionAssignee(), "id", "fullname"))
                        ->label("Staff");
                ?>
            </div>
        </div>
    </div>
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
                        ])->label("Complete Date From");
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
                        ])->label("Complete Date To");
                ?>
            </div>
        </div>
    </div>
    
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
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
    
    <?php if ($worker !== null) { ?>
        <div class="row">
            <?php
            $tasksCompletionElecDataArray = json_decode($tasksCompletionElecData, true);
            if (!empty($tasksCompletionElecDataArray['datasets'][0]['data'])) {
                ?>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <?=
                    $this->renderAjax('_chartTaskCompletionFactoryStaffElec', [
                        'model' => $model,
                        'dateFrom' => $dateFrom,
                        'dateTo' => $dateTo,
                        'dataElec' => $tasksCompletionElecData,
                        'totalTaskAmountElec' => $totalAmountTaskElec,
                        'worker' => $worker
                    ])
                    ?>
                </div>
                <?php
            }
            ?>

            <?php
            $tasksCompletionFabDataArray = json_decode($tasksCompletionFabData, true);
            if (!empty($tasksCompletionFabDataArray['datasets'][0]['data'])) {
                ?>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <?=
                    $this->renderAjax('_chartTaskCompletionFactoryStaffFab', [
                        'model' => $model,
                        'dateFrom' => $dateFrom,
                        'dateTo' => $dateTo,
                        'dataFab' => $tasksCompletionFabData,
                        'totalTaskAmountFab' => $totalAmountTaskFab,
                        'worker' => $worker
                    ])
                    ?>
                </div>
                <?php
            }

            if (empty($tasksCompletionElecDataArray['datasets'][0]['data']) && empty($tasksCompletionFabDataArray['datasets'][0]['data'])) {
                echo "<div class='tdnowrap'>-- No Record --</div>";
            }
            ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>