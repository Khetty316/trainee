<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\documentreminder\DocumentReminderMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-reminder-master-form">

    <?php
    $form = ActiveForm::begin([
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
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
//                'action' => $formAction,
//                'id' => 'form_receiveAsset'
    ]);
    ?>
    <div class="form-row">
        <div class="col-sm-12 col-lg-6 pb-3">

            <?php
            $data = \frontend\models\working\documentreminder\DocumentReminderMaster::find()
                    ->select(['category as id', 'category as label'])
                    ->where("active_sts = 1")
                    ->distinct()
                    ->orderBy(['category' => SORT_ASC])
                    ->asArray()
                    ->all();

            echo $form->field($model, 'category')->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => $data,
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 500,
                ],
                'options' => ['class' => 'form-control']
            ]);
            ?>

        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-lg-6 pb-3">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-lg-3 pb-3">
            <?php
            if ($model->filename) {
                echo $form->field($model, 'scannedFile')->fileInput()->label("Document");
                echo Html::a("Current File (" . substr($model->filename, 15) . ") <i class='far fa-file-alt m-1' ></i>", ["/working/documentreminder/get-file", 'id' => $model->id],
                        ['target' => "_blank"]);
            } else {
                echo $form->field($model, 'scannedFile')->fileInput(['class' => 'required'])->label("Document", ['class' => 'req col-sm-12']);
            }
            ?>

        </div>
        <div class="col-sm-12 col-lg-3 pb-3">
            <?= $form->field($model, 'expiry_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']) ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-lg-3 pb-3"> 
            <div class="form-group row">
                <label class="col-sm-12" for="">Remind before:</label> 
                <div class="col-sm-4">
                    <?php
                    echo $form->field($model, 'remind_period')->textInput(['type' => 'number', 'min' => '0', 'step' => '1', 'placeholder' => '(Number)'])->label(false);
                    ?>
                </div>
                <div class="col-sm-8 pl-0">
                    <?php
                    echo $form->field($model, 'remind_period_unit')
                            ->dropDownList(['Day(s)' => 'Day(s)', 'Week(s)' => 'Week(s)', 'Month(s)' => 'Month(s)', 'Year(s)' => 'Year(s)'], ['prompt' => '(Unit)'])->label(false);
                    ?>
                </div>
            </div> 
        </div>
        <div class="col-sm-12 col-lg-3 pb-3">
            <?= $form->field($model, 'remind_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']) ?>
            <span id="dayReminder" class="text-danger"></span>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-lg-6 pb-3">
            <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success', 'id' => 'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>

    $(function () {
//remindPeriodUnit

//documentremindermaster-remind_date documentremindermaster-expiry_date



        $("#documentremindermaster-remind_date").on("change", function () {
            $("#documentremindermaster-remind_period_unit, #documentremindermaster-remind_period").val('');
        });

        $("#documentremindermaster-remind_date").on("change", function () {
            var expiryDate = getDateFromRead($("#documentremindermaster-expiry_date").val());
            var remindDate = getDateFromRead($("#documentremindermaster-remind_date").val());

            if (expiryDate < remindDate) {
                var ans = confirm("Remind Date is later than Expiry Date, are you sure?");
                if (!ans) {
                    $("#documentremindermaster-remind_date").val('');
                }
            }
        });


        $("#documentremindermaster-remind_period_unit, #documentremindermaster-remind_period, #documentremindermaster-expiry_date, #documentremindermaster-remind_date").on("change", function () {

//            var date = $("#documentremindermaster-expiry_date").val();
            var remindPeriod = parseInt($("#documentremindermaster-remind_period").val());
            var remindPeriodUnit = $("#documentremindermaster-remind_period_unit").val();
            var expiryDate = $("#documentremindermaster-expiry_date").val();

            var day = parseInt(expiryDate.substr(0, 2));
            var month = parseInt(expiryDate.substr(3, 2));
            var year = parseInt(expiryDate.substr(6, 4));
            expiryDate = getDateFromRead(expiryDate);

            var remindDate = new Date(year, month - 1, day);

            if (!isNaN(expiryDate.getTime()) && !isNaN(remindPeriod) && remindPeriodUnit !== "") {

                if (remindPeriodUnit === 'Day(s)') {
                    remindDate.setDate(remindDate.getDate() - remindPeriod);
                } else if (remindPeriodUnit === 'Week(s)') {
                    remindDate.setDate(remindDate.getDate() - (remindPeriod * 7));
                } else if (remindPeriodUnit === 'Month(s)') {
                    remindDate = new Date(year, month - 1 - remindPeriod, day);
                } else if (remindPeriodUnit === 'Year(s)') {
                    remindDate = new Date(year - remindPeriod, month - 1, day);
                }

                $("#documentremindermaster-remind_date").val(remindDate.toInputFormat());
            } else {
                remindDate = getDateFromRead($("#documentremindermaster-remind_date").val());
            }

            checkDate(remindDate, expiryDate);

        });

        //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
        Date.prototype.toInputFormat = function () {
            var yyyy = this.getFullYear().toString();
            var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
            var dd = this.getDate().toString();
            return (dd[1] ? dd : "0" + dd[0]) + "/" + (mm[1] ? mm : "0" + mm[0]) + "/" + yyyy;
//        yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]); // padding
        };
    });

    function checkDate(dateRemind, dateExpiry) {
        var displayStr = "";
        console.log("------------------------------------");
        if (dateRemind.getTime() === dateExpiry.getTime()) {
            displayStr += "Alert: Reminding date is same as expiry date";
        } else if (dateRemind.getTime() > dateExpiry.getTime()) {
            displayStr += "Alert: Reminding date is later than expiry date";
        }else{
            
        }

        $("#dayReminder").html(displayStr);
    }
</script>