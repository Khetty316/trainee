<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\projectquotation\ProjectQPanels;

$panel = $model->projProdPanel;
$project = $panel->projProdMaster;
$model->tempTaskName = $model->prodElecTask->elecTaskCode->name;
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
                                    $staffNameList = $model->taskAssignElecStaff;
                                    foreach ($staffNameList as $key => $name) {
                                        echo "<li>";
                                        echo Html::encode($name->user->fullname) . " (<span class='text-success'>" . Html::encode($name->complete_qty ?? 0) . "</span>/" . Html::encode($model->quantity) . ")";
                                        echo "</li>";
                                    }
                                    ?>
                                </ol>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <?php
                                echo $form->field($model, 'tempTaskName')->textInput(['disabled' => true])->label("Task");
                                ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'quantity')->textInput(['type' => 'number', 'step' => '1', 'min' => 1, 'max' => $model->quantity, 'disabled' => true]) ?>
                            </div>
                            <div class="col-md-6">
                                <?=
                                $form->field($model, 'start_date')->widget(yii\jui\DatePicker::className(),
                                        ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'disabled' => true]
                                            , 'dateFormat' => 'dd/MM/yyyy'])
                                ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <?php
                                if (isset($allowDateChange) && $allowDateChange) {
                                    echo $form->field($model, 'complete_date', ['options' => ['class' => 'form-group required']])->widget(yii\jui\DatePicker::className(),
                                            ['options' => ['class' => 'form-control required', 'placeholder' => 'dd/mm/yyyy', 'required' => true]
                                                , 'dateFormat' => 'dd/MM/yyyy']);
                                } else {
                                    echo $form->field($model, 'complete_date', ['options' => ['class' => 'form-group required']])->widget(yii\jui\DatePicker::className(),
                                            ['options' => ['class' => 'form-control required', 'placeholder' => 'dd/mm/yyyy', 'required' => true, 'disabled' => true]
                                                , 'dateFormat' => 'dd/MM/yyyy']);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="form-row">
                            <div class="col-12">
                                <?= $form->field($model, 'comments')->textarea(['rows' => 4, 'disabled' => true]) ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12">
                                <?= $form->field($model, 'extraComment')->textarea(['rows' => 4]) ?>
                            </div>
                        </div>
                        <!--                        <div class="form-row">
                                                    <div class="col-0">
                                                        Completed Panel : <span class="text-success"><?php //= $model->complete_qty ?? 0       ?></span>/<?php //= $model->quantity       ?>
                                                    </div>
                                                </div>-->
                        <?php // if ($limit != 0) {  ?>
                        <!--                            <div class="form-row">
                                                        <div class="col-0">
                                                            <label for="addComplete-counter">Add Completed Amount</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-row" id="addComplete-counter">
                                                        <div class="col-6">
                        <?php //= $form->field($model, 'addComplete')->label(false)->textInput(['type' => 'number', 'id' => 'counter-input', 'value' => 0, 'min' => 1, 'max' => $limit])  ?>
                                                        </div>
                                                        <div class="col-0">
                                                            <button type="button" class="btn btn-warning" onmousedown="startDecrement()" onmouseup="stopChanging()" onmouseleave="stopChanging()" ontouchstart="startDecrement()" ontouchend="stopChanging()">-1</button>
                                                        </div>
                                                        <div class="col-0">
                                                            <button type="button" class="btn btn-warning" onmousedown="startIncrement()" onmouseup="stopChanging()" onmouseleave="stopChanging()" ontouchstart="startIncrement()" ontouchend="stopChanging()">+1</button>
                                                        </div>
                                                    </div>-->
                        <?php // } ?>
                        <?php if (isset($updateAllStaff) && $updateAllStaff) { ?>
                            <!-- Show input for all assigned staff -->
                            <?php
                            foreach ($staffNameList as $key => $staff) {
                                $staffLimit = $model->quantity - ($staff->complete_qty ?? 0);
                                if ($staffLimit > 0) {
                                    ?>
                                    <div class="form-row mt-3">
                                        <div class="col-12">
                                            <h6><?= Html::encode($staff->user->fullname) ?></h6>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label for="addComplete-counter-<?= $staff->id ?>">Completed Panel : <span class="text-success"><?= $staff->complete_qty ?? 0 ?></span>/<?= $model->quantity ?></label>
                                        </div>
                                    </div>
                                    <div class="form-row" id="addComplete-counter-<?= $staff->id ?>">
                                        <div class="col-6">
                                            <?=
                                            Html::textInput("staffComplete[{$staff->id}]", 0, [
                                                'type' => 'number',
                                                'id' => "counter-input-{$staff->id}",
                                                'class' => 'form-control',
                                                'min' => 1,
                                                'max' => $staffLimit
                                            ])
                                            ?>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-warning" onclick="changeStaffCounter(<?= $staff->id ?>, -1)">-1</button>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-warning" onclick="changeStaffCounter(<?= $staff->id ?>, 1)">+1</button>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } else if ($limit != 0) { ?>
                            <?php
                            foreach ($staffNameList as $key => $staff) {
                                $staffLimit = $model->quantity - ($staff->complete_qty ?? 0);
                                if ($staffLimit > 0 && $staff->user_id == Yii::$app->user->id) {
                                    ?>
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label for="addComplete-counter-<?= $staff->id ?>">Completed Panel : <span class="text-success"><?= $staff->complete_qty ?? 0 ?></span>/<?= $model->quantity ?></label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-0">
                                            <label for="addComplete-counter">Add Completed Amount</label>
                                        </div>
                                    </div>
                                    <div class="form-row" id="addComplete-counter-<?= $staff->id ?>">
                                        <div class="col-6">
                                            <?=
                                            Html::textInput("staffComplete[{$staff->id}]", 0, [
                                                'type' => 'number',
                                                'id' => "counter-input-{$staff->id}",
                                                'class' => 'form-control',
                                                'min' => 1,
                                                'max' => $staffLimit
                                            ])
                                            ?>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-warning" onclick="changeStaffCounter(<?= $staff->id ?>, -1)">-1</button>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-warning" onclick="changeStaffCounter(<?= $staff->id ?>, 1)">+1</button>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </fieldset>
        </div>


    </div>

    <div class="form-group">
        <?= Html::submitButton("Submit Completed Panel", ['class' => 'btn btn-success float-right m-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    var counterInput = document.getElementById('counter-input');
    var limit = <?= $limit ?>; // Set the upper limit here
    var interval;

    function startDecrement() {
        interval = setInterval(function () {
            var currentValue = parseInt(counterInput.value) || 0;
            if (currentValue > 0) {
                counterInput.value = currentValue - 1;
            }
        }, 70);
    }

    function startIncrement() {
        interval = setInterval(function () {
            var currentValue = parseInt(counterInput.value) || 0;
            if (currentValue < limit) {
                counterInput.value = currentValue + 1;
            }
        }, 70);
    }

    function stopChanging() {
        clearInterval(interval);
    }

    // Function for handling individual staff counters (referenced in the multiple staff section)
    function changeStaffCounter(staffId, change) {
        var input = document.getElementById('counter-input-' + staffId);
        var currentValue = parseInt(input.value) || 0;
        var newValue = currentValue + change;
        var max = parseInt(input.getAttribute('max'));
        var min = parseInt(input.getAttribute('min'));

        if (newValue >= min && newValue <= max) {
            input.value = newValue;
        }
    }
</script>

