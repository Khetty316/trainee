<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\covid\form\RefCovidPlaces;
use frontend\models\covid\form\RefCovidPlacesOther;
use frontend\models\covid\form\RefCovidReact;
use common\models\myTools\MyCommonFunction;
use common\models\myTools\MyFormatter;
use frontend\models\covid\testkit\CovidTestkitRecord;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\form\CovidStatusForm */


//MyCommonFunction::countDays($date1, $date2);
$this->title = 'Covid-19 Health Declaration Form';
$this->params['breadcrumbs'][] = $this->title;
$hasPreviousVaccineRecord = false;

$hasPrevious = $previousModel ? true : false;
if ($hasPrevious) {
    if ($previousModel->self_test_is && MyCommonFunction::countDays($previousModel->self_test_date, date("Y-m-d")) <= 7) {
        $model->self_test_is = $previousModel->self_test_is;
        $model->self_test_date = $previousModel->self_test_date;
        $model->self_test_reason = $previousModel->self_test_reason;
        $model->self_test_kit_type = $previousModel->self_test_kit_type;
        $model->self_test_result = $previousModel->self_test_result;
        $model->self_test_result_attachment = $previousModel->self_test_result_attachment;
        $hasPreviousVaccineRecord = true;
    }

    $model->self_vaccine_dose = $previousModel->self_vaccine_dose;
    $model->other_vaccine_two_dose = $previousModel->other_vaccine_two_dose;
    $model->other_how_many = $previousModel->other_how_many;
}

// Check if today has scanned
$lastCheckinDate = $hasPrevious ? MyFormatter::fromDateTimeSql_toDateSql($previousModel->created_at) : '';
$sameDaySecondScan = ($hasPrevious && (MyCommonFunction::countDays($lastCheckinDate, date("Y-m-d")) <= 0)) ? true : false;
$moreThan7DaysLastCheckin = false;

if (($hasPrevious && (MyCommonFunction::countDays($lastCheckinDate, date("Y-m-d")) > 7)) || !$hasPrevious) {
    $moreThan7DaysLastCheckin = true;
}
?>
<style>
    li.borderless { 
        border: 0 none; 
    }
</style>
<div class="covid-status-form-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <p class="font-weight-lighter text-success mb-0">Please note that the timeline of information requested for this declaration form shall be captured from your last visit to office until now.</p>
    <p class="font-weight-lighter text-warning ">Your last record date is: <span class="text-red"><?= $hasPrevious ? MyFormatter::asDateTime_ReaddmYHi($previousModel->created_at) : '(No Record)' ?></span></p>
    <div class="covid-status-form-form">
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
                    'id' => 'myForm',
                    'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
        ]);
        ?>


        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0 font-weight-bold">Staff information:</legend>
            <?php
            if ($hasPrevious) {
                ?>
            <?php } ?>
            <?= $form->field($model, 'body_temperature')->textInput(['step' => ".1"])->label("<b>➼ Temperature:</b>") ?>

            <?= $form->field($model, 'spo2')->textInput(['step' => ".1"])->label("<b>➼ SPO2 (Blood Oxygen Level):</b>") ?>

            <?= $form->field($model, 'self_vaccine_dose')->textInput(["type" => "number"])->label('<b>➼ Doses of Covid-19 vaccine taken:</b>') ?>

            <div class="form-group row ">
                <div class="col-sm-12">
                    <label class="req"><b>➼ Do you have any sickness of (more than 1 selections are applicable): </b></label>
                    <ul class="list-group list-group-flush">
                        <?php
                        $symptomList = \frontend\models\covid\form\RefCovidSymptoms::getDropDownList();

                        foreach ($symptomList as $key => $symptoms) {
                            ?>
                            <li class="list-group-item  pt-0 pb-0 borderless">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input selfSymptoms" id="selfSymptoms_<?= $key ?>" name="selfSymptoms[]" value='<?= $key ?>'/>
                                    <label class="custom-control-label" for="selfSymptoms_<?= $key ?>"><?= $symptoms ?></label>
                                </div>
                            </li>
                            <?php
                        }
                        ?>
                        <li class="list-group-item  pt-0 pb-0 borderless">
                            <div class="form-inline">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input selfSymptoms" id="selfSymptoms_other"/>
                                    <label class="custom-control-label" for="selfSymptoms_other">Other:</label>
                                </div>
                                <input type="text" 
                                       id="covidstatusform-self_symptom_other" 
                                       class="form-control form-control-sm ml-1 col-md-10 col-sm-12" 
                                       name="CovidStatusForm[self_symptom_other]" 
                                       maxlength="255"
                                       placeholder='Other sickness...'
                                       disabled='true' />
                            </div>
                        </li>
                        <!--                     
                        <div class="form-group row col-sm-12 p-0 m-0">
                                                    <input type="text" id="covidstatusform-self_symptom_other" 
                                                           class="form-control form-control-sm ml-3" 
                                                           name="CovidStatusForm[self_symptom_other]" 
                                                           maxlength="255"
                                                           placeholder='Other sickness...'/>
                                                </div>-->
                    </ul>
                </div>
            </div>

            <div class="form-group row ">
                <div class="col-sm-12">
                    <label class="req"><b>➼ Have you been to(more than 1 selections are applicable): </b></label>
                    <ul class="list-group list-group-flush">
                        <?php
                        $placesList = RefCovidPlaces::find()->orderBy(['order' => SORT_ASC])->all();
                        foreach ($placesList as $places) {
                            ?>
                            <li class="list-group-item  pt-0 pb-0 borderless">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input selfPlaces" id="selfPlaces_<?= $places->id ?>" name="selfPlaces[]" value='<?= $places->id ?>'/>
                                    <label class="custom-control-label" for="selfPlaces_<?= $places->id ?>"><?= $places->description ?></label>
                                    <?php
                                    if ($places->react_id == RefCovidReact::placeHaveReason) {
                                        echo Html::input('text', "selfPlaces_" . $places->id . "_reason", '', ['class' => 'form-control-sm form-control', 'placeholder' => 'Reason...', 'id' => 'selfPlaceReason_' . $places->id]);
                                    }
                                    ?>
                                </div>
                            </li>
                            <?php
                        }
                        ?>

                        <li class="list-group-item  pt-0 pb-0 borderless">
                            <div class="form-inline">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input selfPlaces" id="selfPlace_other"/>
                                    <label class="custom-control-label" for="selfPlace_other">Other:</label>
                                </div>
                                <input type="text" 
                                       id="covidstatusform-self_place_other"
                                       class="form-control form-control-sm ml-3 col-md-10 col-sm-12" 
                                       name="CovidStatusForm[self_place_other]"
                                       maxlength="255"
                                       placeholder='Other places...'
                                       disabled='true'/>
                            </div>
                        </li>


                    </ul>
                </div>
            </div>

            <div class="form-group row ">
                <div class="col-sm-12">
                    <label class="req"><b>➼ Do you do any Covid-19 test?:</b></label>
                    <p class="text-success font-weight-lighter m-0">Your test detail will automatically retrieve for 1 week</p>
                    <div class="form-check">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="CovidStatusForm[self_test_is]" id="selftestRadioNo" value="0" <?= $model->self_test_is ? '' : 'checked' ?>/>
                            <label class="form-check-label" for="selftestRadioNo">No</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="CovidStatusForm[self_test_is]" id="selftestRadioYes" value="1" <?= $model->self_test_is ? 'checked' : '' ?>/>
                            <label class="form-check-label" for="selftestRadioYes">Yes</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group ml-3">
                <?= $form->field($model, 'self_test_reason')->textInput(['maxlength' => true, 'placeholder' => 'If answer above is yes, what is the reason?'])->label(false) ?>

                <?= $form->field($model, 'self_test_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control', 'placeholder' => 'Date of test taken'], 'dateFormat' => 'dd/MM/yyyy'])->label(false) ?>

                <?php
                $testKitList = frontend\models\covid\form\RefCovidTestkitType::getDropDownList();
                echo $form->field($model, 'self_test_kit_type')->dropDownList($testKitList, ['prompt' => '(Select test-kit type...)'])->label(false);

                $companyTestKit = CovidTestkitRecord::getMyUnusedTestKitDropdownList();
                echo $form->field($model, 'self_covid_kit_id', ['options' => ['id' => 'self_covid_kit_id', 'style' => 'display:none']])->dropDownList($companyTestKit, ['prompt' => '(Select Kit by Company)'])->label(false);


                $testResultOption = ['negative' => 'Negative (-)', 'postive' => 'Positive (+)', 'awaiting' => "Awaiting For Result"];
                echo $form->field($model, 'self_test_result')->dropDownList($testResultOption, ['prompt' => '(Select Result)'])->label(false);
                ?>
                <div class="custom-file">
<!--                    <input type="file" class="custom-file-input" id="covidstatusform-scannedfile" name='CovidStatusForm[scannedFile]'>
                    <label class="custom-file-label" for="customFile" id="customFileLabel">Test result attachment</label>-->
                    <?= $form->field($model, 'scannedFile')->fileInput()->label(false) ?>
                </div>

                <?php
                if ($model->self_test_result_attachment) {
                    echo Html::a("(Click to view previous attachment)", "/covidform/get-file?filename=" .
                            urlencode($model->self_test_result_attachment), ['target' => "_blank", 'class' => 'mr-2']);
                }
                ?>
            </div>
        </fieldset>

        <?php
        if (!$sameDaySecondScan) {
            ?>

            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0 font-weight-bold">Persons under same roof:</legend>

                <?= $form->field($model, 'other_how_many')->textInput(["type" => "number"])->label('<b class="req">➼ How many persons are you staying together under the same house (excluding you)?:</b>') ?>

                <?= $form->field($model, 'other_vaccine_two_dose')->textInput(["type" => "number"])->label('<b class="req">➼ How many of the persons above have complete the covid-19 vaccination (2 doses)?:</b>') ?>

                <div class="form-group row ">
                    <div class="col-sm-12">
                        <label class="req"><b>➼ Do they have any sickness of: (more than 1 selections are applicable): </b></label>
                        <ul class="list-group list-group-flush">
                            <?php
                            foreach ($symptomList as $key => $symptoms) {
                                ?>
                                <li class="list-group-item  pt-0 pb-0 borderless">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input othersSymptoms" id="othersSymptoms_<?= $key ?>" name="othersSymptoms[]" value='<?= $key ?>'/>
                                        <label class="custom-control-label" for="othersSymptoms_<?= $key ?>"><?= $symptoms ?></label>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>

                            <li class="list-group-item  pt-0 pb-0 borderless">
                                <div class="form-inline">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input othersSymptoms" id="othersSymptoms_other"/>
                                        <label class="custom-control-label" for="othersSymptoms_other">Other:</label>
                                    </div>
                                    <input type="text" 
                                           id="covidstatusform-other_symptom_other" 
                                           class="form-control form-control-sm ml-1 col-md-10 col-sm-12"
                                           name="CovidStatusForm[other_symptom_other]" 
                                           maxlength="255"
                                           placeholder='Other sickness...' 
                                           disabled='true'
                                           />
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <label class="req"><b>➼ Have they been to (more than 1 selections are applicable): </b></label>
                        <ul class="list-group list-group-flush">
                            <?php
                            $otherPlacesList = RefCovidPlacesOther::find()->orderBy(['order' => SORT_ASC])->all();
                            foreach ($otherPlacesList as $places) {
                                ?>
                                <li class="list-group-item  pt-0 pb-0 borderless">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input othersPlaces" id="othersPlaces_<?= $places->id ?>" name="othersPlaces[]" value='<?= $places->id ?>'/>
                                        <label class="custom-control-label" for="othersPlaces_<?= $places->id ?>"><?= $places->description ?></label>
                                        <?php
                                        if ($places->react_id == RefCovidReact::placeHaveReason) {
                                            echo Html::input('text', "othersPlaces_" . $places->id . "_reason", '', ['class' => 'form-control-sm form-control', 'placeholder' => 'Reason...', 'id' => 'othersPlaceReason_' . $places->id]);
                                        }
                                        ?>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>

                            <li class="list-group-item  pt-0 pb-0 borderless">
                                <div class="form-inline">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input othersPlaces" id="othersPlaces_other"/>
                                        <label class="custom-control-label" for="othersPlaces_other">Other:</label>
                                    </div>

                                    <input type="text" 
                                           id="covidstatusform-other_place_other"
                                           class="form-control form-control-sm ml-1 col-md-10 col-sm-12"
                                           name="CovidStatusForm[other_place_other]"
                                           maxlength="255"
                                           placeholder='Other places...'
                                           disabled='true'
                                           />
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="form-group row ">
                    <div class="col-sm-12">
                        <label class="req"><b>➼ Do they do any Covid-19 test?:</b></label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="CovidStatusForm[other_test_is]" id="otherTestRadioNo" value="0" checked/>
                                <label class="form-check-label" for="otherTestRadioNo">No</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="CovidStatusForm[other_test_is]" id="otherTestRadioYes" value="1"/>
                                <label class="form-check-label" for="otherTestRadioYes">Yes</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group ml-3">
                    <?= $form->field($model, 'other_test_result')->dropDownList($testResultOption, ['prompt' => '(Select Result)'])->label(false) ?>
                    <?= $form->field($model, 'other_test_reason')->textInput(['maxlength' => true, 'placeholder' => 'If answer above is yes, what is the reason?'])->label(false) ?>
                </div>
            </fieldset>
            <?php
        }
        ?>
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0 font-weight-bold">Declaration</legend>
            <div class="col-sm-12">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="declareConfirmation"/>
                    <label class="custom-control-label" for="declareConfirmation">
                        <b><span style="color: red">* * </span>I hereby declare that the information provided is true and correct. I also understand that any willful dishonesty may render for losses of company and endanger the health of staffs.<span style="color: red"> * * </span>
                            <br/>
                            <span class="text-red">Action to be taken against those who violate SOP, misconduct and willful dishonest acts.</span></b>
                    </label>
                </div>
            </div>
        </fieldset>

        <div class="form-group">
            <div class="hidden">
                <input type="text" value="<?= $hasPreviousVaccineRecord ? 1 : 0 ?>" name="hasPreviousVaccineRecord" id="hasPreviousVaccineRecord"/>
                <input type="text" value="<?= $model->self_test_result_attachment ?>" name="previousResult" id="previousResult"/>
            </div>
            <?php
            echo Html::a('Submit',
                    "javsacript:",
                    [
                        'title' => 'Submit',
                        'class' => 'btn btn-success',
                        'id' => 'checkFormBtn'
                    ]
            );
            ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="modal fade" id="modalDeclaration" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="workingModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Dear Colleagues, as you may be aware that we have recent positive cases occurred in our work places. It is important to remind all colleagues to stay high awareness and be extra carefully maintain social distance or isolation if required. Should there be any family members or friends with close contact, please update the declaration and inform the management immediately. Everyone plays a very important role to help NPL fighting this Pandemic. Stay safe and high awareness.
                </div>
                <div class="modal-footer">
                    <?= Html::a('Proceed Submission', 'javascript:', ['class' => 'btn btn-success', 'id' => 'submitButton']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="7DaysAlert" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="workingModalLabel">GET TEST KIT!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Your last check-in date is more than 7 days ago!<br/>
                    Kindly get a test kit from office and run a test immediately before you enter the workplace.<br/>
                    You may update the Health Declaration Form once the result is out.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


</div>
<script>
    $(function () {
<?php if ($moreThan7DaysLastCheckin) { ?>
            $('#7DaysAlert').modal('show');
<?php } ?>
        checkSelfTestInput();
        checkOtherTestInput();
        $("#selftestRadioYes, #selftestRadioNo").click(function () {
            checkSelfTestInput();
        });

        $("#otherTestRadioYes, #otherTestRadioNo").click(function () {
            checkOtherTestInput();
        });



        $("#submitButton").click(function () {
            $("#myForm").submit();
        });

        $("#selfSymptoms_other").click(function () {
            $("#covidstatusform-self_symptom_other").prop("disabled", !$(this).is(":checked"));
        });

        $("#selfPlace_other").click(function () {
            $("#covidstatusform-self_place_other").prop("disabled", !$(this).is(":checked"));
        });

        $("#othersSymptoms_other").click(function () {
            $("#covidstatusform-other_symptom_other").prop("disabled", !$(this).is(":checked"));
        });

        $("#othersPlaces_other").click(function () {
            $("#covidstatusform-other_place_other").prop("disabled", !$(this).is(":checked"));
        });


        $("#checkFormBtn").click(function () {

            if ($("#covidstatusform-body_temperature").val() === "") {
                alert("Insert temperature");
                $("#covidstatusform-body_temperature").focus();
                return false;
            } else if ($("#covidstatusform-body_temperature").val() < 33 || $("#covidstatusform-body_temperature").val() > 44) {
                alert("Please insert accurate body temperature (33 - 44)");
                $("#covidstatusform-body_temperature").focus();
                return false;
            }
            
            if ($("#covidstatusform-spo2").val() === "") {
                alert("Insert body oxygen level");
                $("#covidstatusform-spo2").focus();
                return false;
            } else if ($("#covidstatusform-spo2").val() < 50 || $("#covidstatusform-spo2").val() > 100) {
                alert("Please insert accurate body oxygen level (50 - 100)");
                $("#covidstatusform-spo2").focus();
                return false;
            }
            
            


            if ($("#covidstatusform-self_vaccine_dose").val() === "") {
                alert("Insert your vaccine taken");
                $("#covidstatusform-self_vaccine_dose").focus();
                return false;
            }
            if (!$('.selfSymptoms').is(":checked")) {
                alert("Select sickness symptoms");
                $("#selfSymptoms_1").focus();
                return false;
            }

            if ($('#selfSymptoms_other').is(":checked") && $("#covidstatusform-self_symptom_other").val() === "") {
                alert("Insert other sickness");
                $("#covidstatusform-self_symptom_other").focus();
                return false;
            }

            if (!$('.selfPlaces').is(":checked")) {
                alert("Select places you've been");
                $("#selfPlaces_1").focus();
                return false;
            }

            if ($('#selfPlace_other').is(":checked") && $("#covidstatusform-self_place_other").val() === "") {
                alert("Insert other places");
                $("#covidstatusform-self_place_other").focus();
                return false;
            }
            checkSelfVaccineRecord();

            if ($('#selftestRadioYes').is(":checked")) {
                if ($('#covidstatusform-self_test_kit_type').val() === "" || $('#covidstatusform-self_test_date').val() === "" || $('#covidstatusform-self_test_reason').val() === "") {
                    alert("Complete the test detail as you had went for Covid-19 Test");
                    $("#selftestRadioYes").focus();
                    return false;
                }

                if ($("#hasPreviousVaccineRecord").val() === '0') {
                    if ($("#covidstatusform-self_test_kit_type").val() === "3" && $("#covidstatusform-self_covid_kit_id").val() === "") {
                        alert("Please select the kit from office");
                        $("#covidstatusform-self_covid_kit_id").focus();
                        return false;
                    }

                    if ($("#covidstatusform-scannedfile").val() === "" && $("#covidstatusform-self_test_result").val() !== 'awaiting') {
                        alert("Please attach your result");
                        $("#covidstatusform-scannedfile").focus();
                        return false;
                    }
                }
            }

<?php if (!$sameDaySecondScan) { ?>
                if ($("#covidstatusform-other_how_many").val() === "") {
                    alert("Insert housemates number");
                    $("#covidstatusform-other_how_many").focus();
                    return false;
                }
                if ($("#covidstatusform-other_vaccine_two_dose").val() === "") {
                    alert("Insert number of vaccine completed");
                    $("#covidstatusform-other_vaccine_two_dose").focus();
                    return false;
                }

                if (!$('.othersSymptoms').is(":checked")) {
                    alert("Select sickness symptoms");
                    $("#othersSymptoms_1").focus();
                    return false;
                }

                if ($('#othersSymptoms_other').is(":checked") && $("#covidstatusform-other_symptom_other").val() === "") {
                    alert("Insert other sickness");
                    $("#covidstatusform-other_symptom_other").focus();
                    return false;
                }

                if (!$('.othersPlaces').is(":checked")) {
                    alert("Select places they've been");
                    $("#othersPlaces_1").focus();
                    return false;
                }

                if ($('#othersPlaces_other').is(":checked") && $("#covidstatusform-other_place_other").val() === "") {
                    alert("Insert other places");
                    $("#covidstatusform-other_place_other").focus();
                    return false;
                }

                if ($('#otherTestRadioYes').is(":checked")) {
                    if ($('#covidstatusform-other_test_reason').val() === "") {
                        alert("Complete the test detail as they had went for Covid-19 Test");
                        $("#covidstatusform-other_test_reason").focus();
                        return false;
                    }
                }
<?php } ?>

            if (!$('#declareConfirmation').is(":checked")) {
                alert("Kindly make declaration.");
                $("#declareConfirmation").focus();
                return false;
            }



            $("#modalDeclaration").modal('show');
        });

//        $('#covidstatusform-scannedfile').on('change', function () {
//            //get the file name
//            var fileName = $(this).val();
//            //replace the "Choose a file" label
//            $(this).next('#customFileLabel').html(fileName);
//        });


        $('#covidstatusform-self_test_kit_type').on('change', function () {
            if ($(this).val() === '3') {
                $("#self_covid_kit_id").show();
            } else {
                $("#self_covid_kit_id").hide();
            }
        });




    });


    function checkSelfTestInput() {
        var hasSelfTest = $("#selftestRadioNo").is(":checked");

        $("#covidstatusform-self_test_reason").prop("disabled", hasSelfTest);
        $("#covidstatusform-self_test_date").prop("disabled", hasSelfTest);
        $("#covidstatusform-self_test_kit_type").prop("disabled", hasSelfTest);
        $("#covidstatusform-scannedfile").prop("disabled", hasSelfTest);
        $("#covidstatusform-self_covid_kit_id").prop("disabled", hasSelfTest);
        $("#covidstatusform-self_test_result").prop("disabled", hasSelfTest);
    }

    function checkOtherTestInput() {
        var hasSelfTest = $("#otherTestRadioNo").is(":checked");
        $("#covidstatusform-other_test_reason").prop("disabled", hasSelfTest);
        $("#covidstatusform-other_test_result").prop("disabled", hasSelfTest);
    }




    function checkSelfVaccineRecord() {

        var testReason = $("#covidstatusform-self_test_reason").val();
        var testDate = $("#covidstatusform-self_test_date").val();
        var testKitType = $("#covidstatusform-self_test_kit_type").val();
        if ($("#selftestRadioYes").is(":checked")
                && testReason === "<?= $model->self_test_reason ?>"
                && testDate === "<?= MyFormatter::asDate_Read($model->self_test_date) ?>"
                && testKitType === "<?= $model->self_test_kit_type ?>") {
            $("#hasPreviousVaccineRecord").val('1');
        } else {
            $("#hasPreviousVaccineRecord").val('0');
        }

    }

</script>