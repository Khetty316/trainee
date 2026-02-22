<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestStatus;
use frontend\models\test\TestFormInsuhipot;

$this->title = 'Insulation and Hipot Test';
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $master->testMain->panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => 'Test Panel Details', 'url' => ['/test/testing/index-panel', 'id' => $master->testMain->panel->id]];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = $this->title;

$currentStatus = ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING && $model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) ? 1 : 0;
$showResult = ($model->status != RefTestStatus::STS_IN_TESTING && $model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) ? 1 : 0;
$setThreshold = ($model->status == RefTestStatus::STS_SETUP) ? 1 : 0;
?>
<style>
    .custom-disabled0{
        pointer-events: none;
        opacity: 1;
        background-color: #e9ecef;
    }
    #myForm input{
        border: none;
        text-align: center;
    }
    .exc-input{
        text-align: left;
    }
    .table td {
        vertical-align: middle;
        text-align: center;
        padding: 0;
    }
    .form-group {
        margin: 0;
    }
</style>
<div class="test-form-insuhipot-index">
    <div class="col-12">
        <div class="row justify-content-between">
            <div>
                <h3 class="mb-3"><?= Html::encode($this->title) ?></h3>
            </div>
            <div>
                <div class="col pr-0">
                    <?=
                    Html::a("Edit Procedures <i class='far fa-edit'></i>", "javascript:", [
                        'title' => "Edit Procedures",
                        "value" => yii\helpers\Url::to(['/test/insuhipot/edit-procedure', 'id' => $model->id]),
                        "class" => "modalButton btn btn-success",
                        'data-modaltitle' => "Edit Procedures"
                    ]);
                    ?>
                </div>
                <?php if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
                    <div>
                        <?php
                        echo $this->render('..\_formModalAddPunchlist', [
                            'id' => $master->id,
                            'formType' => TestMaster::CODE_INSUHIPOT
                        ]);
                        ?>
                    </div>
                <?php } ?>
                <?php // if ($model->status == RefTestStatus::STS_SETUP) {  ?>
                <!--<div class="col pr-0">-->
                <?php
//                        =
//                        Html::a("Edit Procedures <i class='far fa-edit'></i>", "javascript:", [
//                            'title' => "Edit Procedures",
//                            "value" => yii\helpers\Url::to(['/test/insuhipot/edit-procedure', 'id' => $model->id]),
//                            "class" => "modalButton btn btn-success",
//                            'data-modaltitle' => "Edit Procedures"
//                        ]);
                ?>
                <!--</div>-->
                <?php // } elseif ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) {   ?>
                <!--<div>-->
                <?php
//                        echo $this->render('..\_formModalAddPunchlist', [
//                            'id' => $master->id,
//                            'formType' => TestMaster::CODE_INSUHIPOT
//                        ]);
                ?>
                <!--</div>-->
                <?php // }   ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?php
            $form = ActiveForm::begin([
                'options' => ['autocomplete' => 'off', 'id' => 'myForm'],
            ]);
            ?>

            <div class="mb-4">
                <?php
                if ($template) {
                    $modelProcedures = explode('|', $procedures);
                    echo '<div>' . (isset($modelProcedures[0]) ? $modelProcedures[0] : '') . '</div>';
                } else {
                    echo '<div>' . (isset($procedures->proctest1) ? $procedures->proctest1 : '') . '</div>';
                }
                ?>

                <div class="row mt-4 thresholddiv">
                    <div class="col-md-12 col-sm-12">
                        <h5>Threshold (MOhm)
                            <span> 
                                <?=
                                Html::a("<i class='far fa-edit fa-md'></i>",
                                        "javascript:",
                                        [
                                            "onclick" => "event.preventDefault();",
                                            "value" => \yii\helpers\Url::to(['ajax-edit-threshold', 'id' => $model->id, 'type' => "treshold_a"]),
                                            "class" => "btn btn-sm btn-success modalButtonMedium",
                                        ]
                                )
                                ?>
                            </span>
                        </h5>
                    </div>
                </div>

                <h6><b>Test Results</b></h6>
                <table class="table table-sm table-bordered text-center">
                    <thead>
                        <tr>
                            <th rowspan="2">Phase </th>
                            <th>Before Pressure Test</th>
                            <th>After Pressure Test</th>
                            <th rowspan="2" id="result">Result</th>
                        </tr>
                        <tr>
                            <th>Insulation Resistance (MOhms)</th>
                            <th>Insulation Resistance (MOhms)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="tr_1">
                            <td>R-E</td>
                            <td><?= $form->field($model, 're1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 1])->label(false) ?></td>
                            <td><?= $form->field($model, 're2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 1])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 1])->label(false) ?>                                             
                                <?= $form->field($model, 'res_re')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 1])->label(false) ?>
                            </td>

                        </tr>
                        <tr id="tr_2">
                            <td>Y-E</td>  
                            <td><?= $form->field($model, 'ye1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 2])->label(false) ?></td>
                            <td><?= $form->field($model, 'ye2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 2])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 2])->label(false) ?>                                                                        
                                <?= $form->field($model, 'res_ye')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 2])->label(false) ?>
                            </td>
                        </tr>
                        <tr id="tr_3">
                            <td>B-E</td>   
                            <td><?= $form->field($model, 'be1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 3])->label(false) ?></td>
                            <td><?= $form->field($model, 'be2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 3])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 3])->label(false) ?>                                             
                                <?= $form->field($model, 'res_be')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 3])->label(false) ?>
                            </td>
                        </tr>
                        <tr id="tr_4">
                            <td>N-E</td> 
                            <td><?= $form->field($model, 'ne1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 4])->label(false) ?></td>
                            <td><?= $form->field($model, 'ne2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 4])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 4])->label(false) ?>                                             
                                <?= $form->field($model, 'res_ne')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 4])->label(false) ?>
                            </td>
                        </tr>
                        <tr id="tr_5">
                            <td>R-N</td>
                            <td><?= $form->field($model, 'rn1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 5])->label(false) ?></td>
                            <td><?= $form->field($model, 'rn2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 5])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 5])->label(false) ?>                                             
                                <?= $form->field($model, 'res_rn')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 5])->label(false) ?>
                            </td>   
                        </tr>
                        <tr id="tr_6">
                            <td>Y-N</td>
                            <td><?= $form->field($model, 'yn1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 6])->label(false) ?></td>
                            <td><?= $form->field($model, 'yn2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 6])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 6])->label(false) ?>                                             
                                <?= $form->field($model, 'res_yn')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 6])->label(false) ?>
                            </td>  
                        </tr>
                        <tr id="tr_7">
                            <td>B-N</td>
                            <td><?= $form->field($model, 'bn1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 7])->label(false) ?></td>
                            <td><?= $form->field($model, 'bn2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 7])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 7])->label(false) ?>                                             
                                <?= $form->field($model, 'res_bn')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 7])->label(false) ?>
                            </td>
                        </tr>
                        <tr id="tr_8">
                            <td>R-Y</td>
                            <td><?= $form->field($model, 'ry1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 8])->label(false) ?></td>
                            <td><?= $form->field($model, 'ry2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 8])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 8])->label(false) ?>                                                                      
                                <?= $form->field($model, 'res_ry')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 8])->label(false) ?>
                            </td>  
                        </tr>
                        <tr id="tr_9">
                            <td>Y-B</td>
                            <td><?= $form->field($model, 'yb1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 9])->label(false) ?></td>
                            <td><?= $form->field($model, 'yb2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 9])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 9])->label(false) ?>                                             
                                <?= $form->field($model, 'res_yb')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 9])->label(false) ?>
                            </td> 
                        </tr>
                        <tr id="tr_10">
                            <td>B-R</td>
                            <td><?= $form->field($model, 'br1')->input('number', ['step' => 'any', 'class' => 'form-control p1_1', 'data-key' => 10])->label(false) ?></td>
                            <td><?= $form->field($model, 'br2')->input('number', ['step' => 'any', 'class' => 'form-control p1_2', 'data-key' => 10])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p1_res_sts', 'data-key' => 10])->label(false) ?>                      
                                <?= $form->field($model, 'res_br')->hiddenInput(['class' => 'form-control p1_res', 'data-key' => 10])->label(false) ?>
                            </td>   
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="my-4">
                <?php
                if ($template) {
                    echo '<p>' . (isset($modelProcedures[1]) ? $modelProcedures[1] : '') . '</p>';
                } else {
                    echo '<p>' . (isset($procedures->proctest1) ? $procedures->proctest2 : '') . '</p>';
                }
                ?>

                <div class="row mt-4 thresholddiv">
                    <div class="col-md-12 col-sm-12">
                        <h5>Threshold (mA)
                            <span> 
                                <?=
                                Html::a("<i class='far fa-edit fa-md'></i>",
                                        "javascript:",
                                        [
                                            "onclick" => "event.preventDefault();",
                                            "value" => \yii\helpers\Url::to(['ajax-edit-threshold', 'id' => $model->id, 'type' => "treshold_b"]),
                                            "class" => "btn btn-sm btn-success modalButtonMedium",
                                        ]
                                )
                                ?>
                            </span>
                        </h5>
                    </div>
                </div>
                <h6><b>Test Results</b></h6>
                <table class="table table-sm table-bordered text-center">
                    <thead>
                        <tr>
                            <th style="width:20%;">Between </th>
                            <th>Leakage Current Starting <br>(mA)</th>
                            <th>Leakage Current Ending <br>(mA)</th>
                            <th>Lapsed Time (s)</th>
                            <th id="result">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="tr_11">
                            <td>R and Y+B+N+earth</td>
                            <td><?= $form->field($model, 'r_start')->input('number', ['step' => 'any', 'class' => 'form-control p2_1', 'data-key' => 11])->label(false) ?></td>
                            <td><?= $form->field($model, 'r_end')->input('number', ['step' => 'any', 'class' => 'form-control p2_2', 'data-key' => 11])->label(false) ?></td>
                            <td><?= $form->field($model, 'r_time')->textInput(['class' => 'form-control lapsedTime', 'data-key' => 11])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p2_res_sts', 'data-key' => 11])->label(false) ?>                        
                                <?= $form->field($model, 'res_r')->hiddenInput(['class' => 'form-control p2_res', 'data-key' => 11])->label(false) ?>
                            </td> 
                        </tr>
                        <tr id="tr_12">
                            <td>Y and B+R+N+earth</td>  
                            <td><?= $form->field($model, 'y_start')->input('number', ['step' => 'any', 'class' => 'form-control p2_1', 'data-key' => 12])->label(false) ?></td>
                            <td><?= $form->field($model, 'y_end')->input('number', ['step' => 'any', 'class' => 'form-control p2_2', 'data-key' => 12])->label(false) ?></td>
                            <td><?= $form->field($model, 'y_time')->textInput(['class' => 'form-control lapsedTime', 'data-key' => 12])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p2_res_sts', 'data-key' => 12])->label(false) ?>                      
                                <?= $form->field($model, 'res_y')->hiddenInput(['class' => 'form-control p2_res', 'data-key' => 12])->label(false) ?>
                            </td> 
                        </tr>
                        <tr id="tr_13">
                            <td>B and R+Y+N+earth</td>   
                            <td><?= $form->field($model, 'b_start')->input('number', ['step' => 'any', 'class' => 'form-control p2_1', 'data-key' => 13])->label(false) ?></td>
                            <td><?= $form->field($model, 'b_end')->input('number', ['step' => 'any', 'class' => 'form-control p2_2', 'data-key' => 13])->label(false) ?></td>
                            <td><?= $form->field($model, 'b_time')->textInput(['class' => 'form-control lapsedTime', 'data-key' => 13])->label(false) ?></td>
                            <td>
                                <?= $form->field($model, 'result_text')->textInput(['class' => 'form-control p2_res_sts', 'data-key' => 13])->label(false) ?>
                                <?= $form->field($model, 'res_b')->hiddenInput(['class' => 'form-control p2_res', 'data-key' => 13])->label(false) ?>
                            </td>  
                        </tr>
                    </tbody>
                </table>
                <div>
                    <h6>Remarks :</h6>
                    <?= $form->field($model, 'remark')->textarea(['class' => 'form-control exc-input border remark', 'rows' => 4, 'style' => 'width: 100%', 'maxlength' => true])->label(false) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-3">
                    <?php
                    if ($model->status == RefTestStatus::STS_SETUP) {
                        echo Html::a('Delete Form &nbsp;<i class="fa fa-trash"></i>', ["delete-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2 delete-form', 'data-confirm' => 'Delete this form?']);
                        echo Html::submitButton('Save and Ready to Test &nbsp;<i class="far fa-clipboard"></i>', ["class" => "float-right btn btn-success ml-2"]);
                        echo $form->field($model, 'status')->hiddenInput(['value' => RefTestStatus::STS_READY_FOR_TESTING])->label(false);
                    } else if ($model->status == RefTestStatus::STS_IN_TESTING) {
                        echo $form->field($model, 'status')->hiddenInput(['value' => ''])->label(false);
                        echo Html::submitButton('Save and Fail &nbsp;<i class="fas fa-times"></i>', ['class' => 'float-right btn btn-danger ml-2 save-and-status', 'data-status' => RefTestStatus::STS_FAIL]);
                        echo Html::submitButton('Save and Pass &nbsp;<i class="fas fa-clipboard-check"></i>', ['class' => 'float-right btn btn-success ml-2 save-and-status', 'data-status' => RefTestStatus::STS_COMPLETE]);
                        echo Html::submitButton('Save Temporarily', ['class' => 'float-right btn btn-success save-and-status', 'data-status' => $model->status]);
                    }

                    if ($model->status == RefTestStatus::STS_READY_FOR_TESTING || $model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                        echo Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2 revert-form', 'data-confirm' => 'Revert this form?']);
                    }
                    ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-12">
            <?php
            if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                if ($witnessList) {
                    ?>
                    <h5>Witnesses</h5>
                    <?php
                }
            }

            if ($witnessList) {
                echo $this->render('../__signatureForm', [
                    'model' => $model,
                    'witnessList' => $witnessList
                ]);
            }
            ?>
        </div>
    </div>

</div>

<script>
//    $('#myForm').submit(function () {
//        var form = document.getElementById('myForm');
//        var inputs = form.querySelectorAll('input, textarea');
//
//        inputs.forEach(function (input) {
//            input.disabled = false;
//        });
//
//        return true;
//    });

    $('.revert-form').on('click', function (e) {
        e.stopPropagation(); // Prevent event from bubbling to form

        var href = $(this).attr('href');
        var confirmMsg = $(this).data('confirm');

        if (confirmMsg) {
            if (confirm(confirmMsg)) {
                window.location.href = href;
            }
        } else {
            window.location.href = href;
        }

        return false; // Prevent default link behavior
    });

    $('.delete-form').on('click', function (e) {
        e.stopPropagation(); // Prevent event from bubbling to form
    });

    $('#myForm').submit(function (e) {
        var activeElement = $(document.activeElement);

        if (activeElement.closest('.signature-modal').length > 0 ||
                activeElement.hasClass('sign-btn') ||
                activeElement.hasClass('close-modal') ||
                activeElement.attr('id') === 'clear-expanded-signature' ||
                activeElement.attr('id') === 'cancel-signature' ||
                activeElement.attr('id') === 'save-signature' ||
                (activeElement.attr('id') && activeElement.attr('id').startsWith('clear-signature-'))) {
            e.preventDefault();
            return false;
        }

        if (!activeElement.is('.save-and-status, .revert-form, .delete-form, .btn-success')) {
            e.preventDefault();
            return false;
        }

        var form = document.getElementById('myForm');
        var inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(function (input) {
            input.disabled = false;
        });

        return true;
    });

    window.onload = function () {
        var currentStatus = <?= $model->status ?>;
        var setThreshold = <?= $setThreshold ?>;
        var showResult = <?= $showResult ?>;
        var form = document.getElementById('myForm');
        var thresholddiv = form.querySelectorAll('.thresholddiv');
        var resultCells = form.querySelectorAll('#result, .p1_res_sts, .p2_res_sts');

        disableCustomForm('myForm', currentStatus);

        if (!setThreshold) {
            thresholddiv.forEach(function (input) {
                input.style.display = 'none';
            });
        }

        if (showResult) {
            resultCells.forEach(function (cell) {
                cell.style.display = 'none';
            });
        }
    };

    function disableCustomForm(formId, currentStatus) {
        var form = document.getElementById(formId);
        var inputs = form.querySelectorAll('input, textarea');
        var excludedInputs = ['#testforminsuhipot-treshold_a', '#testforminsuhipot-treshold_b'];
        var result = form.querySelectorAll('.p1_res_sts, .p2_res_sts');
        var numberInputs = document.querySelectorAll('input[type="number"]');
        var lapsedTime = document.querySelectorAll('.form-control.lapsedTime');

        inputs.forEach(function (input) {
            if (excludedInputs.includes('#' + input.id)) {
                return;
            }
            if (currentStatus === <?= RefTestStatus::STS_SETUP ?>) {
                input.disabled = true;

            } else if (currentStatus === <?= RefTestStatus::STS_READY_FOR_TESTING ?> || currentStatus === <?= RefTestStatus::STS_FAIL ?> || currentStatus === <?= RefTestStatus::STS_COMPLETE ?>) {
                input.classList.add('custom-disabled');
            }

        });

        if (currentStatus === <?= RefTestStatus::STS_IN_TESTING ?>) {
            numberInputs.forEach(function (input) {
                input.placeholder = 'Enter a value';
            });
            lapsedTime.forEach(function (input) {
                input.placeholder = 'Enter time taken';
            });
            form.querySelectorAll('.remark').forEach(function (input) {
                input.placeholder = 'remark';
            });
            result.forEach(function (input) {
                input.disabled = true;
            });
        }

    }

    $('.save-and-status').click(function () {
        var statusValue = $(this).data('status');
        $('#<?= Html::getInputId($model, 'status') ?>').val(statusValue);
    });

    $(document).ready(function () {
        $('.p1_1, .p1_2').trigger('input');
        $('.p2_1, .p2_2').trigger('input');
    });

    $('.p1_1, .p1_2').on('input', function () {
        var key = $(this).data('key');
        updateResult1(key);
    });

    $('.p2_1, .p2_2').on('input', function () {
        var key = $(this).data('key');
        updateResult2(key);
    });

    function updateResult1(key) {
        var p1 = parseFloat($(`#tr_${key} .p1_1`).val());
        var p2 = parseFloat($(`#tr_${key} .p1_2`).val());
        var threshold_a = <?= $model->treshold_a ?>;

        if (isNaN(p1) || isNaN(p2)) {
            $(`#tr_${key} .p1_res`).val(null);
            $(`#tr_${key} .p1_res_sts`).val("");
        } else {
            if (p1 > threshold_a && p2 > threshold_a) {
                $(`#tr_${key} .p1_res_sts`).val("Pass");
                $(`#tr_${key} .p1_res`).val(<?= TestFormInsuhipot::RESULT_PASS['value'] ?>);
            } else {
                $(`#tr_${key} .p1_res_sts`).val("Fail");
                $(`#tr_${key} .p1_res`).val(<?= TestFormInsuhipot::RESULT_FAIL['value'] ?>);
            }
        }
    }

    function updateResult2(key) {
        var p1 = parseFloat($(`#tr_${key} .p2_1`).val());
        var p2 = parseFloat($(`#tr_${key} .p2_2`).val());
        var threshold_b = <?= $model->treshold_b ?>;

        if (isNaN(p1) || isNaN(p2)) {
            $(`#tr_${key} .p2_res`).val(null);
            $(`#tr_${key} .p2_res_sts`).val("");
        } else {
            if (p1 < threshold_b && p2 < threshold_b) {
                $(`#tr_${key} .p2_res_sts`).val("Pass");
                $(`#tr_${key} .p2_res`).val(<?= TestFormInsuhipot::RESULT_PASS['value'] ?>);
            } else {
                $(`#tr_${key} .p2_res_sts`).val("Fail");
                $(`#tr_${key} .p2_res`).val(<?= TestFormInsuhipot::RESULT_FAIL['value'] ?>);
            }
        }
    }

</script>




