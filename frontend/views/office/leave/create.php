<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\office\leave\RefLeaveType;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\leave\LeaveMaster */

if (isset($formType) && $formType === RefLeaveType::codeTravel) {
    $this->title = 'New Work Traveling Requisition';
    $this->params['breadcrumbs'][] = ['label' => 'Work Traveling Requisition', 'url' => ['work-travel-req']];
} else {
    $this->title = 'New Leave Request';
    $this->params['breadcrumbs'][] = ['label' => 'Leave Application', 'url' => ['personal-leave']];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    /*    .ui-datepicker-current {
            display: none;
        }*/

</style>
<div class="leave-master-create">

    <h5><?= Html::encode($this->title) ?></h5>

    <?php
    $form = ActiveForm::begin([
//                'id' => 'newquotation-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
            'horizontalCssClasses' => [
                'label' => 'col-sm-12',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-6',
                'error' => '',
                'hint' => '',
            ],
        ],
//                'enableAjaxValidation' => true,
        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off', 'id' => 'mainForm']
    ]);
    ?>

    <?= $form->field($model, 'requestor_id')->hiddenInput(["value" => Yii::$app->user->id])->label(false) ?>
    <?= $form->field($model, 'emergency_leave')->hiddenInput(["value" => 0])->label(false) ?>
    <div class="form-row">
        <div class="col-md-4 col-sm-12">
            <?php
            if (isset($formType) && $formType === RefLeaveType::codeTravel) {
                echo $form->field($model, 'leave_type_code')->dropDownList($leaveTypeList, ['value' => RefLeaveType::codeTravel, 'disabled' => true])->label("Apply for");
                echo Html::activeHiddenInput($model, 'leave_type_code', ['value' => RefLeaveType::codeTravel]);
            } else {
                echo $form->field($model, 'leave_type_code')->dropDownList($leaveTypeList, ['prompt' => 'Select...'])->label("Leave Type");
            }
            ?>
            <?php //= $form->field($model, 'leave_type_code')->dropDownList($leaveTypeList, ['prompt' => 'Select...'])->label("Leave Type") ?>
        </div>
        <div class="col-md-4 col-sm-12">
            <?= $form->field($model, 'scannedFile')->fileInput()->label("Support Document (if any)") ?>
        </div>
    </div>
    <!--    <div class="form-row">
            <div class="col-md-4 col-sm-12">-->
    <?php
    // =
//                    $form->field($model, 'emergency_leave')->checkbox(['class' => 'custom-control-input'])
//                    ->label('Emergency Leave <a href="#" data-toggle="tooltip" data-placement="right" data-original-title="Only check when applying for emergency leave."><i class="fas fa-info-circle"></i></a>')
    ?>
    <!--        </div>
        </div>-->
    <!--    <div class="form-row">
            <div class="col-md-4 col-sm-12">-->
    <?php
    //=
//                    $form->field($model, 'back_date')->checkbox(['class' => 'custom-control-input'])
//                    ->label('Back Date <a href="#" data-toggle="tooltip" data-placement="right" data-original-title="Only check when applying for a backdated leave."><i class="fas fa-info-circle"></i></a>')
    ?>
    <!--        </div>
        </div>-->
    <div class="form-row">
        <div class="col-md-4 col-sm-12">
            <?= $form->field($model, 'relief_user_id')->dropDownList($userList, ['prompt' => 'Select...'])->label('Relief') ?>
        </div>
        <div class="col-md-4 col-sm-12">
            <?= $form->field($model, 'superior_id')->dropDownList($userList, ['prompt' => 'Select...', 'value' => Yii::$app->user->identity->superior_id, 'disabled' => true])->label('Superior') ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-8 col-sm-12">
            <?= $form->field($model, 'reason')->textarea(['rows' => 6])->label("Reason") ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?=
                    $form->field($model, 'start_date', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                    ->widget(\yii\jui\DatePicker::className(), [
                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                        'clientOptions' => [
                            'dateFormat' => 'dd/mm/yy',
                            'showButtonPanel' => true,
                            'closeText' => 'Close',
                            'beforeShow' => new \yii\web\JsExpression('function (input, instance) {
                                                    $(input).datepicker("option", "dateFormat", "dd/mm/yy");
                                                    }'),
                        ],
                    ])
                    ->label("Start Date");
            ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?=
                    $form->field($model, 'end_date', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                    ->widget(\yii\jui\DatePicker::className(), [
                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                        'clientOptions' => [
                            'dateFormat' => 'dd/mm/yy',
                            'showButtonPanel' => true,
                            'closeText' => 'Close',
                            'beforeShow' => new \yii\web\JsExpression('function (input, instance) {
                                                    $(input).datepicker("option", "dateFormat", "dd/mm/yy");
                                                    }'),
                        ],
                    ])
                    ->label("End Date");
            ?>
        </div>
        <div class="hidden">
            <?= $form->field($model, 'start_section')->dropDownList($leaveSectionList, ['prompt' => 'Select...', 'value' => '1'])->label("(am/pm)") ?>
            <?= $form->field($model, 'end_section')->dropDownList($leaveSectionList, ['prompt' => 'Select...', 'value' => '2'])->label("(am/pm)") ?>
        </div>
    </div>


    <div class="form-row">
        <div class="col-8">
            <div class="form-group">
                <div class="pull-right">
                    <?= Html::a('Submit', "javascript:", ['class' => 'btn btn-success', 'id' => 'submitButton']) ?>
                </div>
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<script>
    $(function () {
        $("#submitButton").click(function () {
            if (validateScannedFile()) {
                checkDate();
            }
        });
        $("#leavemaster-start_date").on("change", function () {
            autoUpdateDateTo();
        });
        $("#leavemaster-leave_type_code").on('change', function () {
            validateScannedFile();
            validateRelief();
            validateReason();
        });
        $('[data-toggle="tooltip"]').tooltip();
    });

    function validateRelief() {
        var $reliefDiv = $('.field-leavemaster-relief_user_id');
        var leaveTypeCode = $('#leavemaster-leave_type_code').val();

        if (leaveTypeCode === "<?= RefLeaveType::codeAnnual ?>" || leaveTypeCode === "<?= RefLeaveType::codeUnpaid ?>") {
            $reliefDiv.addClass('required');
        } else {
            $reliefDiv.removeClass('required');
        }
    }

    function validateReason() {
        var $reasonDiv = $('.field-leavemaster-reason');
        var leaveTypeCode = $('#leavemaster-leave_type_code').val();

        if (leaveTypeCode === "<?= RefLeaveType::codeAnnual ?>") {
            $reasonDiv.removeClass('required');
        } else {
            $reasonDiv.addClass('required');
        }
    }

    $(document).ready(function () {
//        $('input[type="checkbox"]').change(function () {
//            if ($(this).prop('checked')) {
//                $('input[type="checkbox"]').not(this).prop('checked', false);
//                $div.addClass('required');
//            } else {
//                $div.removeClass('required');
//            }
//        });

        var startInput = document.getElementById('leavemaster-start_date');
        var startValue = startInput.value;

        if (startValue.trim() !== '') {
            var startDate = new Date(startValue);
            var formattedStartDate = ('0' + startDate.getDate()).slice(-2) + '/' + ('0' + (startDate.getMonth() + 1)).slice(-2) + '/' + startDate.getFullYear();
            startInput.value = formattedStartDate;
        }

        var endInput = document.getElementById('leavemaster-end_date');
        var endValue = endInput.value;
        if (startValue.trim() !== '') {
            var endDate = new Date(endValue);
            var formattedEndDate = ('0' + endDate.getDate()).slice(-2) + '/' + ('0' + (endDate.getMonth() + 1)).slice(-2) + '/' + endDate.getFullYear();
            endInput.value = formattedEndDate;
        }
    });

    function validateScannedFile() {
        var leaveTypeCode = $('#leavemaster-leave_type_code').val();
        var scannedFileInput = $('.field-leavemaster-scannedfile');
        var fileinput = $('#leavemaster-scannedfile').val();
        var sickCode = "<?= \frontend\models\office\leave\RefLeaveType::codeSick ?>";
        var annualCode = "<?= \frontend\models\office\leave\RefLeaveType::codeAnnual ?>";
        var unpaidCode = "<?= \frontend\models\office\leave\RefLeaveType::codeUnpaid ?>";

//        if (leaveTypeCode == sickCode) {
//            $('#leavemaster-emergency_leave').prop('checked', true);
//        }

        if ((leaveTypeCode != annualCode && leaveTypeCode != unpaidCode) && !fileinput) {
            scannedFileInput.prop('required', true);
            var errorTarget = $(".field-leavemaster-scannedfile").find(".invalid-feedback");
            errorTarget.html('Must include supporting document/s for this leave.');
            errorTarget.show();
            return false;
        } else if ((leaveTypeCode == annualCode || leaveTypeCode == unpaidCode) && !fileinput) {
            var errorTarget = $(".field-leavemaster-scannedfile").find(".invalid-feedback");
            errorTarget.hide();
            return true;
        } else {
            scannedFileInput.prop('required', false);
            return true;

        }
    }

    function checkDate() {
        $("#mainForm").submit();
    }

    function autoUpdateDateTo() {
        var dateFrom = getDateFromRead($("#leavemaster-start_date").val());
        var dateTo = getDateFromRead($("#leavemaster-end_date").val());
        if (dateFrom > dateTo || $("#leavemaster-end_date").val() === "") {
            $("#leavemaster-end_date").val($("#leavemaster-start_date").val());
            return;
        }
    }

</script>