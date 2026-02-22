<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>

<div class="work-assignment-master-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'assignForm',
        'options' => ['autocomplete' => 'off']
    ]);
    ?>
    <div class="row" style="height: 100%">
        <div class='col-7'>
            <div class="form-row">
                <div class="col-sm-12">
                    <?php
                    echo $this->render("/projectproduction/main/_detailViewProjProdDetail", [
                        'projProdMaster' => $project,
                        'panel' => $panel
                    ]);
                    ?>
                </div>
            </div>
            <div class="form-row">
                <div class="col-12">
                    <fieldset class="border p-1">
                        <legend class="w-auto px-2 m-0">Task Assigning Detail:</legend>
                        <div class="row">
                            <div class="col-5">
                                <div class="col-12">
                                    <?php
                                    echo $form->field($model, 'tempTaskName')->textInput(['disabled' => true])->label("Task");
                                    ?>
                                </div>
                                <div class="col-12">
                                    <?php
//                                    if ($model->isNewRecord) {
//                                        echo $form->field($model, 'quantity')->textInput(['type' => 'number', 'step' => '1', 'min' => 1, 'max' => $model->quantity]);
//                                    } else {
//                                        echo $form->field($model, 'quantity')->textInput(['type' => 'number', 'step' => '1', 'min' => 1, 'max' => ($model->quantity + $task->qty_total - $task->qty_assigned)]);
//                                    }
                                    ?>
                                    <?php
                                    $completedTask = $model->taskAssignFabCompletes;
                                    $inputOptions = ['type' => 'number', 'step' => '1', 'min' => 1];

                                    if (!empty($completedTask)) {
                                        $inputOptions['disabled'] = true;
                                    }

                                    if ($model->isNewRecord) {
                                        $inputOptions['max'] = $model->quantity;
                                    } else {
                                        $inputOptions['max'] = ($model->quantity + $task->qty_total - $task->qty_assigned);
                                    }
                                    echo $form->field($model, 'quantity')->textInput($inputOptions);
                                    ?>
                                </div>
                                <div class="col-12">
                                    <?php
//                                    echo $form->field($model, 'start_date')->widget(yii\jui\DatePicker::className(),
//                                            ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy']
//                                                , 'dateFormat' => 'dd/MM/yyyy'])
                                    $project_target_date = $task->projProdPanel->projProdMaster->current_target_date;
                                    echo $form->field($model, 'start_date')->widget(yii\jui\DatePicker::className(), [
                                        'options' => [
                                            'class' => 'form-control',
                                            'placeholder' => 'dd/mm/yyyy',
                                            'id' => 'start-date'
                                        ],
                                        'dateFormat' => 'dd/MM/yyyy',
                                        'clientOptions' => [
                                            'maxDate' => $project_target_date ? date('d/m/Y', strtotime($project_target_date)) : 0,
                                            'changeMonth' => true,
                                            'changeYear' => true,
                                            'onSelect' => new yii\web\JsExpression("
                                                function(dateText) {
                                                    // update minDate of current_target_date when start_date changes
                                                    $('#current-target-date').datepicker('option', 'minDate', dateText);
                                                }
                                            "),
                                        ],
                                    ]);
                                    ?>
                                </div>
                                <div class="col-12">
                                    <?php
//                                    if (!empty($project_target_date)) {
//                                        // show field with validation
//                                        echo "<strong>Project Target Completion Date:</strong> " . date('d/m/Y', strtotime($project_target_date));
//
//                                        echo $form->field($model, 'current_target_date')->widget(yii\jui\DatePicker::className(), [
//                                            'options' => [
//                                                'class' => 'form-control',
//                                                'required' => true,
//                                                'placeholder' => 'dd/mm/yyyy',
//                                                'id' => 'current-target-date'
//                                            ],
//                                            'dateFormat' => 'dd/MM/yyyy',
//                                            'clientOptions' => [
//                                                'maxDate' => date('Y-m-d', strtotime($project_target_date)),
//                                                'changeMonth' => true,
//                                                'changeYear' => true,
//                                                'onSelect' => new yii\web\JsExpression("
//                                                    function(dateText) {
//                                                        validateTargetDate(dateText);
//                                                    }
//                                                "),
//                                            ],
//                                        ]);
//                                    } else {
//                                        echo "<strong>Project Target Completion Date:</strong> <i>Not Set</i>";
//                                        // show field without validation
//                                        echo $form->field($model, 'current_target_date')->widget(yii\jui\DatePicker::className(),
//                                                ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'required' => true]
//                                                    , 'dateFormat' => 'dd/MM/yyyy']);
//                                    }
                                    if (!empty($project_target_date)) {
                                        echo "<strong>Project Target Completion Date:</strong> " . date('d/m/Y', strtotime($project_target_date));

                                        echo $form->field($model, 'current_target_date')->widget(yii\jui\DatePicker::className(), [
                                            'options' => [
                                                'class' => 'form-control',
                                                'required' => true,
                                                'placeholder' => 'dd/mm/yyyy',
                                                'id' => 'current-target-date'
                                            ],
                                            'dateFormat' => 'dd/MM/yyyy',
                                            'clientOptions' => [
                                                'minDate' => date('d/m/Y', strtotime($model->start_date)),
                                                'maxDate' => date('d/m/Y', strtotime($project_target_date)),
                                                'changeMonth' => true,
                                                'changeYear' => true,
                                                'onSelect' => new yii\web\JsExpression("
                                                    function(dateText) {
                                                        validateTargetDate(dateText);
                                                    }
                                                "),
                                            ],
                                        ]);
                                    } else {
                                        echo "<strong>Project Target Completion Date:</strong> <i>Not Set</i>";
                                        echo $form->field($model, 'current_target_date')->widget(yii\jui\DatePicker::className(), [
                                            'options' => [
                                                'class' => 'form-control',
                                                'required' => true,
                                                'placeholder' => 'dd/mm/yyyy',
                                                'id' => 'current-target-date'
                                            ],
                                            'dateFormat' => 'dd/MM/yyyy',
                                            'clientOptions' => [
                                                'minDate' => date('d/m/Y', strtotime($model->start_date)),
                                                'changeMonth' => true,
                                                'changeYear' => true,
                                            ],
                                        ]);
                                    }
                                    ?>

                                </div>
                                <?php if (!$model->isNewRecord) { ?>
                                    <div class="col-12">
                                        <?php
                                        if ($model->complete_date) {
                                            echo $form->field($model, 'complete_date')->widget(yii\jui\DatePicker::className(),
                                                    ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy']
                                                        , 'dateFormat' => 'dd/MM/yyyy']);
                                        } else {
                                            echo $form->field($model, 'complete_date')->widget(yii\jui\DatePicker::className(),
                                                    ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'disabled' => true]
                                                        , 'dateFormat' => 'dd/MM/yyyy']);
                                        }
                                        ?>
                                    </div>
                                <?php } ?>

                            </div>
                            <div class="col-7">
                                <?= $form->field($model, 'comments')->textarea(['rows' => 8]) ?>
                            </div>
                        </div>
                    </fieldset>
                </div>

            </div>
        </div>
        <div class='col-5'>
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Staff:</legend>
                <div class="input-group mb-2">
                    <input class="form-control mr-2" id="nameFilter" type="text" placeholder="Search.."/>
                    <?= Html::a("Assign", "javascript:submitForm()", ['class' => 'btn btn-success']) ?>
                </div>
                <div  style="max-height: 400px;  overflow:auto" >
                    <table class="table table-sm table-stripped table-bordered" id="myList">
                        <thead>
                            <tr>
                                <th class="col-10">Staff name:</th>
                                <th class="col-0 text-center">Task on Hand</th>
                                <th class="tdnowrap col-0">Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $assignedStaffs = array_column(\yii\helpers\ArrayHelper::toArray($model->taskAssignFabStaff), 'user_id');
                            foreach ((array) $staffList as $key => $staff) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <?= Html::encode($staff['fullname']) ?>
                                    </td>  
                                    <td class="text-right px-2">
                                        <?php
                                        if (!empty($staff['totalTaskOnHand']) && $staff['totalTaskOnHand'] > 0) {
                                            echo Html::a(\common\models\myTools\MyFormatter::asDecimal2($staff['totalTaskOnHand']), "javascript:void(0)", [
                                                "value" => yii\helpers\Url::to('/production/task-assign-ongoing-summary/view-user-ongoing-task?userId=' . $staff['id']),
                                                "class" => "modalButton m-2"]);
                                        } else {
                                            echo "<span class='m-2'>0.00</span>";
                                        }
                                        ?>
                                    </td>  
                                    <td class="text-center col-0">
                                        <?php
                                        echo Html::checkbox('selectStaff[]', null, ['value' => $staff['id'], 'class' => 'form-control form-control-sm',
                                            'checked' => (!empty(in_array($staff['id'], $assignedStaffs)) ? true : false)]);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(function () {
        $("#nameFilter").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myList tbody tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        $("#taskassignfab-quantity").blur(function (e) {
            let $this = $(this);
            let val = parseInt($this.val());
            let max = parseInt($this.attr("max"));
            let min = parseInt($this.attr("min"));
            if (max > 0 && val > max) {
                e.preventDefault();
                $this.val(max);
            } else if (min > 0 && val < min) {
                e.preventDefault();
                $this.val(min);
            }
        });

        $("#taskassignfab-quantity").on('focus', function (e) {
            $(this).select();
        })
    });


//    function submitForm() {
//        if (typeof $("input[name='selectStaff[]']:checked").val() === "undefined") {
//            myAlert('Please select a staff');
//            return false;
//        }
//        $("#assignForm").submit();
//    }

    function submitForm() {
        if ($("input[name='selectStaff[]']:checked").length === 0) {
            myAlert('Please select a staff');
            return false;
        }

        var targetDate = $('#current-target-date').val();
        if (!targetDate || targetDate.trim() === '') {
            myAlert('Current target date is required.');
            return false;
        }

        var datePattern = /^\d{2}\/\d{2}\/\d{4}$/;
        if (!datePattern.test(targetDate)) {
            myAlert('Please enter a valid date in DD/MM/YYYY format.');
            return false;
        }

        $("#assignForm").submit();
    }

//    function submitForm() {
//        // Check staff selection
//        if ($("input[name='selectStaff[]']:checked").length === 0) {
//            myAlert('Please select a staff');
//            return false;
//        }

<?php // if (!empty($project_target_date)):    ?>
    // Check target date validation
//            var currentTargetDate = $('#current-target-date').val();
//            if (currentTargetDate) {
//                var selectedDate = new Date(currentTargetDate.split('/').reverse().join('-'));
//                var projectTargetDate = new Date('<?php // echo date('Y-m-d', strtotime($project_target_date));    ?>');
//                if (selectedDate > projectTargetDate) {
//                    alert('Please correct the target completion date before submitting.');
//                    return false;
//                }
//            }
//            var errorDiv = $('#current-target-date-error');
//            errorDiv.remove();
//            $('#current-target-date').closest('.form-group').removeClass('has-error');
//            if (currentTargetDate === '') {
//                $('#current-target-date').closest('.form-group').addClass('has-error');
//                $('#current-target-date').after(
//                        '<div id="current-target-date-error" class="help-block text-danger">Task Target Completion Date cannot be blank.</div>'
//                        );
//                return false;
//            }
//            if ($('#current-target-date-error').length > 0) {
//                alert('Please correct the target completion date before submitting.');
//                return false;
//            }
<?php // endif;    ?>

    // trigger Yii2 validation
//        var $form = $("#assignForm");
//        if ($form.data("yiiActiveForm")) {
//            $form.yiiActiveForm("validate"); // run Yii2 validation
//
//            // Wait until Yii2 finishes validation
//            setTimeout(function () {
//                if ($form.find(".has-error").length === 0) {
//                    $form.submit(); // submit only if no errors
//                }
//            }, 100);
//
//            return false; // prevent immediate submit
//        }
//
//        return true; // if no Yii2 ActiveForm attached
//    }


<?php //if (!empty($project_target_date)):    ?>
//        function validateTargetDate(selectedDateText) {
//            var selectedDate = new Date(selectedDateText.split('/').reverse().join('-'));
//            var projectTargetDate = new Date('<?php // echo date('Y-m-d', strtotime($project_target_date));    ?>');
//            var errorDiv = $('#current-target-date-error');
//            errorDiv.remove();
//            $('#current-target-date').closest('.form-group').removeClass('has-error');
//            if (selectedDate > projectTargetDate) {
//                $('#current-target-date').closest('.form-group').addClass('has-error');
//                $('#current-target-date').after(
//                        '<div id="current-target-date-error" class="help-block text-danger">' +
//                        'Target completion date cannot exceed project target completion date (<?php // echo date('d/m/Y', strtotime($project_target_date));    ?>)' +
//                        '</div>'
//                        );
//                $('#current-target-date').val('');
//            }
//        }
<?php // endif;    ?>

</script>