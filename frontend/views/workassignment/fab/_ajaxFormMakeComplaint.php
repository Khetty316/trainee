<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\projectproduction\RefProjProdTaskErrors;

$panel = $task->projProdPanel;
$project = $panel->projProdMaster;
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
                <legend class="w-auto px-2 m-0">Complaint:</legend>
                <div class="row">
                    <div class="col-5">
                        <div class="form-row">
                            <div class="col-12">
                                <?php
                                echo $form->field($model, 'error_code')->dropdownList(RefProjProdTaskErrors::getDropDownList($task->fab_task_code))->label("Error:");
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="form-row">
                            <div class="col-12">
                                <?= $form->field($model, 'remark')->textarea(['rows' => 4]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>


    </div>

    <div class="form-group">
        <?= Html::submitButton("Submit Complaint", ['class' => 'btn btn-success float-right m-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
