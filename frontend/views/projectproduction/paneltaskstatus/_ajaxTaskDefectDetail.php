<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\projectproduction\RefProjProdTaskErrors;

$taskAssign->tempTaskName = ($department === "elec" ? $taskAssign->prodElecTask->elecTaskCode->name : $taskAssign->prodFabTask->fabTaskCode->name);
$task_code = ($department === "elec" ? $task->elec_task_code : $task->fab_task_code);
?>

<div class="work-assignment-master-form">

    <?php
    $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]);
    ?>
    <div class="row" style="height: 100%">
        <div class="col-12">
            <fieldset class="border p-1">
                <legend class="w-auto px-2 m-0">Panel Detail:</legend>
                <table class="table table-sm table-striped table-bordered">
                    <tr>
                        <td class="col-3">Panel Code</td>
                        <td class="col-9"><?= $panel->project_production_panel_code ?></td>
                    </tr>
                    <tr>
                        <td>Panel Name</td>
                        <td><?= $panel->panel_description ?></td>
                    </tr>
                    <tr>
                        <td>Reference File</td>
                        <td><?php
                            if ($panel->filename) {
                                echo Html::a('<i class="fas fa-file-alt fa-lg"></i>',
                                        ['/production/production/get-panel-file-by-panel-id', 'panelId' => $panel->id],
                                        ['class' => 'text-warning m-2', 'target' => '_blank']);
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>

        <div class="col-12">
            <fieldset class="border p-1">
                <legend class="w-auto px-2 m-0">Task Assigning Detail:</legend>
                <div class="row">
                    <div class="col-5">
                        <div class="form-row">
                            <div class="col-12">
                                <label>Selected Staff(s):</label>
                                <ol>
                                    <?php
                                    foreach ($staffNameList as $key => $name) {
                                        echo "<li>";
                                        echo Html::encode($name->user->fullname) . " (<span class='text-success'>" . Html::encode($name->complete_qty ?? 0) . "</span>/" . Html::encode($taskAssign->quantity) . ")";
                                        echo "</li>";
                                    }
                                    ?>
                                </ol>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <?php
                                echo $form->field($taskAssign, 'tempTaskName')->textInput(['disabled' => true])->label("Task");
                                ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <?= $form->field($taskAssign, 'quantity')->textInput(['type' => 'number', 'step' => '1', 'min' => 1, 'max' => $taskAssign->quantity, 'disabled' => true]) ?>
                            </div>
                            <div class="col-md-6">
                                <?=
                                $form->field($taskAssign, 'start_date')->widget(yii\jui\DatePicker::className(),
                                        ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'disabled' => true]
                                            , 'dateFormat' => 'dd/MM/yyyy'])
                                ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <?php
                                echo $form->field($taskAssign, 'complete_date', ['options' => ['class' => 'form-group required']])->widget(yii\jui\DatePicker::className(),
                                        ['options' => ['class' => 'form-control required', 'placeholder' => 'dd/mm/yyyy', 'required' => true, 'disabled' => true]
                                            , 'dateFormat' => 'dd/MM/yyyy']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="form-row">
                            <div class="col-12">
                                <?= $form->field($taskAssign, 'comments')->textarea(['rows' => 4, 'disabled' => true]) ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <?= $form->field($taskAssign, 'extraComment')->textarea(['rows' => 4, 'disabled' => true])->label('Extra comment') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="col-12">
            <fieldset class="border p-1">
                <legend class="w-auto px-2 m-0">Complaint:</legend>
                <div class="row">
                    <div class="col-5">
                        <div class="form-row">
                            <div class="col-12">
                                <?php
                                echo $form->field($complaint, 'error_code')->dropdownList(RefProjProdTaskErrors::getDropDownList($task_code), ['disabled' => true])->label("Error:");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="form-row">
                            <div class="col-12">
                                <?= $form->field($complaint, 'remark')->textarea(['rows' => 4, 'disabled' => true]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="form-group">
        <?php if ($panelTaskErrorStaff->is_read == 1) { ?>
            <?= Html::submitButton('Confirm <i class="fas fa-check"></i>', ['class' => 'btn btn-success float-right m-2']) ?>        
        <?php } else { ?>
            <?= Html::submitButton("Close", ['class' => 'btn btn-danger float-right m-2']) ?>
        <?php } ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

