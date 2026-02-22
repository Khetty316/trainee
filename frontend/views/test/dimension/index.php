<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestStatus;
use frontend\models\test\TestFormDimension;

$this->title = 'Dimensions Check';
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $master->testMain->panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => 'Test Panel Details', 'url' => ['/test/testing/index-panel', 'id' => $master->testMain->panel->id]];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = $this->title;

$currSts = ($model->status != RefTestStatus::STS_READY_FOR_TESTING && $model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) ? 1 : 0;
$setThreshold = ($model->status == RefTestStatus::STS_SETUP) ? 1 : 0;
?>
<style>
    .custom-dropdown {
        border: none;
        appearance: none;
        text-align:center;
    }

    .custom-dropdown::before {
        position: absolute;
        pointer-events: none;
    }
    .table td {
        vertical-align: middle;
        text-align: center;
        padding: 0;
    }
</style>
<div class="test-form-dimension-index">

    <div class="col-12 mb-3">
        <div class="row justify-content-between">
            <div>
                <h3 class="mb-3"><?= Html::encode($this->title) ?></h3>
            </div>
            <div>
                <?=
                Html::a("Edit Procedures <i class='far fa-edit'></i>", "javascript:", [
                    'title' => "Edit Procedures",
                    "value" => yii\helpers\Url::to(['/test/dimension/edit-procedure', 'id' => $model->id]),
                    "class" => "modalButton btn btn-success",
                    'data-modaltitle' => "Edit Procedures"
                ])
                ?>
                <?php
                if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) {
                    echo $this->render('..\_formModalAddPunchlist', [
                        'id' => $master->id,
                        'formType' => TestMaster::CODE_DIMENSION
                    ]);
                }
                ?>
                <?php
//                if ($model->status == RefTestStatus::STS_SETUP) {
//                    echo Html::a("Edit Procedures <i class='far fa-edit'></i>", "javascript:", [
//                        'title' => "Edit Procedures",
//                        "value" => yii\helpers\Url::to(['/test/dimension/edit-procedure', 'id' => $model->id]),
//                        "class" => "modalButton btn btn-success",
//                        'data-modaltitle' => "Edit Procedures"
//                    ]);
//                } else if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) {
//                    echo $this->render('..\_formModalAddPunchlist', [
//                        'id' => $master->id,
//                        'formType' => TestMaster::CODE_DIMENSION
//                    ]);
//                }
                ?>
            </div>
        </div>
    </div>

    <div class="col-12 p-0">
        <div class="row">
            <?php
            if ($template) {
                $modelProcedures = explode('|', $procedures);
                echo '<div class="p-3">' . (isset($modelProcedures[0]) ? $modelProcedures[0] : '') . '</div>';
            } else {
                echo '<div>' . (isset($procedures->proctest1) ? $procedures->proctest1 : '') . '</div>';
            }
            ?>
        </div>

        <div class="row mt-4 mb-4 thresholddiv" style="display: none;">
            <div class="col-md-12 col-sm-12">
                <h5>Threshold 
                    <span> 
                        <?=
                        Html::a("<i class='far fa-edit fa-md'></i>",
                                "javascript:",
                                [
                                    "onclick" => "event.preventDefault();",
                                    "value" => \yii\helpers\Url::to(['ajax-edit-threshold', 'id' => $model->id]),
                                    "class" => "btn btn-sm btn-success modalButtonMedium",
                                ]
                        )
                        ?>
                    </span>
                </h5>
            </div>
        </div>

        <div class="row col-12 mt-2">
            <h5>Test Result 
                <?php if ($currSts) { ?>
                    <a class='btn btn-sm btn-primary mr-2 btn-dis' href='javascript:addRow()'><i class="fas fa-plus-circle"></i></a>
                <?php } ?> 
            </h5>
        </div>
        <?php if ($model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) { ?>

            <?php
            $form = ActiveForm::begin([
                'action' => ['/test/dimension/update-dimension', 'id' => $model->id],
                'options' => ['class' => 'w-100', 'autocomplete' => 'off', 'id' => 'myForm'],
            ]);
            ?>
            <div>
                <table class="table table-sm table-bordered text-center">
                    <tr>
                        <th>Panel</th>
                        <th>Subject</th>
                        <th>H (mm)</th>
                        <th>W (mm)</th>
                        <th>D (mm)</th>
                    </tr>
                    <tbody id="listTBody">
                        <?php
                        foreach ($dimensionList as $key => $dimension) {
                            echo $this->render('_formDimensionItem', ['status' => $model->status, 'form' => $form, 'key' => $key++, 'dimension' => $dimension, 'panel' => $panel]);
                        }
                        ?>
                    </tbody>
                </table>
                <div class="row">
                    <?php
                    if ($model->got_custom_content == 1) {
                        ?>    
                        <div class="col-sm-12 col-md-12 mt-5">
                            <div class="mb-3">
                                <h6 style="border-bottom: 2px solid #28a745; padding-bottom: 5px; margin-bottom: 15px;">
                                    Custom Content Section <?= Html::a('Edit Custom Content <i class="far fa-edit"></i>', ["add-custom-content", 'id' => $model->id], ['class' => 'btn btn-sm btn-success']) ?>
                                </h6>
                            </div>
                            <?php
                            if (!empty($customContents)) {
                                ?>
                                <?php
                                foreach ($customContents as $index => $content) {
                                    ?>
                                    <div class="custom-content-display mb-4" style="border: 1px solid #28a745; border-radius: 5px; padding: 15px; background-color: #f8fff9;">
                                        <div class="content-wrapper">
                                            <?= $content->content ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="col-sm-12 col-md-12 mt-5">
                            <div class="mb-3">
                                <h6 style="border-bottom: 2px solid #28a745; padding-bottom: 5px; margin-bottom: 15px;">
                                    Custom Content Section 
                                    <?= Html::a('Add Custom Content <i class="fas fa-plus"></i>', ["add-custom-content", 'id' => $model->id], ['class' => 'btn btn-sm btn-success']) ?>
                                </h6>
                            </div>
                            <div class="custom-content-display mb-4" style="border: 1px solid #28a745; border-radius: 5px; padding: 15px; background-color: #f8fff9; height: 100px;">
                                <div class="content-wrapper">
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="text-right">
                            <?php
                            if ($model->status == RefTestStatus::STS_SETUP) {
//                                echo Html::a('Delete Form &nbsp;<i class="fa fa-trash"></i>', ["delete-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2', 'data-confirm' => 'Delete this form?']);
                                echo Html::a('Delete Form  ',
                                        ['delete-form', 'id' => $model->id],
                                        [
                                            'class' => 'float-right btn btn-danger ml-2 delete-form',
                                            'data-confirm' => 'Delete this form?',
                                        ]
                                );
                                echo Html::submitButton('Save and Ready to Test &nbsp;<i class="far fa-clipboard"></i>', ["class" => "float-right btn btn-success ml-2 save-and-status", 'data-status' => RefTestStatus::STS_READY_FOR_TESTING]);
                            } else if ($model->status == RefTestStatus::STS_IN_TESTING) {
                                echo Html::submitButton('Save and Fail &nbsp;<i class="fas fa-times"></i>', ['class' => 'float-right btn btn-danger ml-2 save-and-status', 'data-status' => RefTestStatus::STS_FAIL]);
                                echo Html::submitButton('Save and Pass &nbsp;<i class="fas fa-clipboard-check"></i>', ['class' => 'float-right btn btn-success ml-2 save-and-status', 'data-status' => RefTestStatus::STS_COMPLETE]);
                            }

                            if ($currSts) {
                                echo Html::submitButton('Save Temporarily', ['class' => 'btn btn-success mb-3 save-and-status', 'data-status' => $model->status]);
                            }

                            if ($model->status === RefTestStatus::STS_READY_FOR_TESTING) {
                                echo Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger revert ml-2 mb-3', 'data-confirm' => 'Revert this form?']);
                            }

                            echo $form->field($model, 'status')->hiddenInput(['value' => ''])->label(false);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            ActiveForm::end();
        } else {
            ?>
            <div class="mb-5 pb-3">
                <?php
                echo $this->render('_viewDimension', [
                    'model' => $model,
                    'dimensionList' => $dimensionList
                ]);
                if (!empty($customContents)) {
                    ?>
                    <div class="mb-3">
                        <h6 style="border-bottom: 2px solid #28a745; padding-bottom: 5px; margin-bottom: 15px;">
                            Custom Content Section
                    </div>
                    <?php
                    foreach ($customContents as $index => $content) {
                        ?>
                        <div class="custom-content-display mb-4" style="border: 1px solid #28a745; border-radius: 5px; padding: 15px; background-color: #f8fff9;">
                            <div class="content-wrapper">
                                <?= $content->content ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                echo Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger revert ml-2', 'data-confirm' => 'Revert this form?']);
                ?>
            </div>
            <div class="mb-5 pb-3">
                <?php if ($witnessList) { ?>
                    <h5>Witnesses </h5>
                    <?php
                    echo $this->render('../__signatureForm', [
                        'model' => $model,
                        'witnessList' => $witnessList
                    ]);
                }
                ?>
            </div>
            <?php
        }
        ?>
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

    $('.delete-form').on('click', function (e) {
        e.preventDefault(); // ADDED: Prevent default link behavior
        e.stopPropagation();

        var href = $(this).attr('href');
        var confirmMsg = $(this).data('confirm');

        if (confirmMsg) {
            if (confirm(confirmMsg)) {
                window.location.href = href;
            }
        } else {
            window.location.href = href;
        }

        return false; // ADDED: Prevent any further action
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

        if (!activeElement.is('.save-and-status, .revert, .delete-form, .btn-success')) {
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

    var currentKey = <?= sizeof($dimensionList) ?>;
    window.onload = function () {
        var currentStatus = <?= $model->status ?>;
        var setThreshold = <?= $setThreshold ?>;
        var form = document.getElementById('myForm');
        var thresholddiv = form ? form.querySelectorAll('.thresholddiv') : null;

        disableCustomForm('myForm', currentStatus);

        if (setThreshold) {
            if (thresholddiv !== null) {
                thresholddiv.forEach(function (input) {
                    input.style.display = 'block';
                });
            }
        }

    };

    function disableCustomForm(formId, currentStatus) {
        var form = document.getElementById(formId);
        var allInputs = form ? form.querySelectorAll('.form-control, a') : null;
        var excludedInputs = ['.revert'];

        if (currentStatus === <?= RefTestStatus::STS_SETUP ?>) {
            form.querySelectorAll('.hDraw,.wDraw,.dDraw').forEach(function (input) {
                input.placeholder = 'Enter a value';
            });
            form.querySelectorAll('.panel_desc').forEach(function (input) {
                input.placeholder = 'Enter panel description';
            });
            form.querySelectorAll('.hBuilt,.wBuilt,.dBuilt,.hError,.wError,.dError').forEach(function (input) {
                input.disabled = true;
            });
        } else if (currentStatus === <?= RefTestStatus::STS_READY_FOR_TESTING ?> || currentStatus === <?= RefTestStatus::STS_FAIL ?> || currentStatus === <?= RefTestStatus::STS_COMPLETE ?>) {
            if (allInputs !== null) {
                allInputs.forEach(function (input) {
                    var isExcluded = excludedInputs.some(function (selector) {
                        return input.matches(selector);
                    });

                    if (isExcluded) {
                        return;
                    }

                    input.classList.add('custom-disabled');
                    if (input.tagName.toLowerCase() === 'a') {
                        input.classList.add('hidden');
                    }
                });
            }
        } else if (currentStatus === <?= RefTestStatus::STS_IN_TESTING ?>) {
            form.querySelectorAll('.hDraw,.wDraw,.dDraw,.hBuilt,.wBuilt,.dBuilt').forEach(function (input) {
                input.placeholder = 'Enter a value';
            });
            form.querySelectorAll('.panel_desc').forEach(function (input) {
                input.placeholder = 'Enter panel description';
            });
            form.querySelectorAll('.hError,.wError,.dError').forEach(function (input) {
                input.disabled = true;
            });
            form.querySelectorAll('.h_res_sts,.w_res_sts,.d_res_sts').forEach(function (input) {
                input.disabled = true;
            });
        }
    }

    $('.save-and-status').click(function () {
        var statusValue = $(this).data('status');
        $('#<?= Html::getInputId($model, 'status') ?>').val(statusValue);
        var form = document.getElementById('myForm');
        var inputs = form.querySelectorAll('input, textarea');
        if (statusValue === <?= RefTestStatus::STS_COMPLETE ?> || statusValue === <?= RefTestStatus::STS_FAIL ?>) {
            inputs.forEach(function (input) {
                input.required = true;
            });
        } else {
            inputs.forEach(function (input) {
                input.required = false;
            });
        }
    });

    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $(".tr_" + rowNum).hide();
            $("#toDelete-" + rowNum).val("1");
        }
    }

    function addRow() {
        var currentStatus = <?= $model->status ?>;
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-dimension-item']) ?>',
            dataType: 'html',
            data: {
                key: currentKey++,
                masterId: '<?= $master->id ?>',
                status: '<?= $model->status ?>',
                id: '<?= $model->id ?>'
            }
        }).done(function (response) {
            $("#listTBody").append(response);
            disableCustomForm('myForm', currentStatus);
        });
    }

    $(document).ready(function () {
        $('.hDraw, .hBuilt').trigger('input');
        $('.wDraw, .wBuilt').trigger('input');
        $('.dDraw, .dBuilt').trigger('input');
    });

    $('.hDraw, .hBuilt').on('input', function () {
        var key = $(this).data('key');
        updateResultHeight(key);
    });

    $('.wDraw, .wBuilt').on('input', function () {
        var key = $(this).data('key');
        updateResultWidth(key);
    });

    $('.dDraw, .dBuilt').on('input', function () {
        var key = $(this).data('key');
        updateResultDepth(key);
    });

    function updateResultHeight(key) {
        var hDraw = parseFloat($(`.tr_${key} .hDraw`).val());
        var hBuilt = parseFloat($(`.tr_${key} .hBuilt`).val());
        if (isNaN(hDraw) || isNaN(hBuilt)) {
            $(`.tr_${key} .hError`).val(null);
            $(`.tr_${key} .h_res_sts`).text(null);
            $(`.tr_${key} .h_res`).val(null);
        } else {
            if (hDraw >= <?= TestFormDimension::MEASUREMENT_A_MIN ?> && hDraw <= <?= TestFormDimension::MEASUREMENT_A_MAX ?>) {
                var error_tolerance = <?= $model->treshold_a ?>;
            } else if (hDraw >= <?= TestFormDimension::MEASUREMENT_B_MIN ?> && hDraw <= <?= TestFormDimension::MEASUREMENT_B_MAX ?>) {
                var error_tolerance = <?= $model->treshold_b ?>;
            }

            var hError = ((hDraw - hBuilt) / hDraw) * -100;
            $(`.tr_${key} .hError`).val(hError.toFixed(2));

            if (hError.toFixed(2) >= -error_tolerance.toFixed(2) && hError.toFixed(2) <= error_tolerance) {
                $(`.tr_${key} .h_res_sts`).val("Pass");
                $(`.tr_${key} .h_res`).val(<?= TestFormDimension::RESULT_PASS['value'] ?>);
            } else {
                $(`.tr_${key} .h_res_sts`).val("Fail");
                $(`.tr_${key} .h_res`).val(<?= TestFormDimension::RESULT_FAIL['value'] ?>);
            }
        }
    }

    function updateResultWidth(key) {
        var wDraw = parseFloat($(`.tr_${key} .wDraw`).val());
        var wBuilt = parseFloat($(`.tr_${key} .wBuilt`).val());
        if (isNaN(wDraw) || isNaN(wBuilt)) {
            $(`.tr_${key} .wError`).val(null);
            $(`.tr_${key} .w_res_sts`).val(null);
            $(`.tr_${key} .w_res`).val(null);
        } else {
            if (wDraw >= <?= TestFormDimension::MEASUREMENT_A_MIN ?> && wDraw <= <?= TestFormDimension::MEASUREMENT_A_MAX ?>) {
                var error_tolerance = <?= $model->treshold_a ?>;
            } else if (wDraw >= <?= TestFormDimension::MEASUREMENT_B_MIN ?> && wDraw <= <?= TestFormDimension::MEASUREMENT_B_MAX ?>) {
                var error_tolerance = <?= $model->treshold_b ?>;
            }

            var wError = ((wDraw - wBuilt) / wDraw) * -100;
            $(`.tr_${key} .wError`).val(wError.toFixed(2));

            if (wError.toFixed(2) >= -error_tolerance.toFixed(2) && wError.toFixed(2) <= error_tolerance) {
                $(`.tr_${key} .w_res_sts`).val("Pass");
                $(`.tr_${key} .w_res`).val(<?= TestFormDimension::RESULT_PASS['value'] ?>);
            } else {
                $(`.tr_${key} .w_res_sts`).val("Fail");
                $(`.tr_${key} .w_res`).val(<?= TestFormDimension::RESULT_FAIL['value'] ?>);
            }
        }
    }

    function updateResultDepth(key) {
        var dDraw = parseFloat($(`.tr_${key} .dDraw`).val());
        var dBuilt = parseFloat($(`.tr_${key} .dBuilt`).val());
        if (isNaN(dDraw) || isNaN(dBuilt)) {
            $(`.tr_${key} .dError`).val(null);
            $(`.tr_${key} .d_res_sts`).text(null);
            $(`.tr_${key} .d_res`).val(null);
        } else {
            if (dDraw >= <?= TestFormDimension::MEASUREMENT_A_MIN ?> && dDraw <= <?= TestFormDimension::MEASUREMENT_A_MAX ?>) {
                var error_tolerance = <?= $model->treshold_a ?>;
            } else if (dDraw >= <?= TestFormDimension::MEASUREMENT_B_MIN ?> && dDraw <= <?= TestFormDimension::MEASUREMENT_B_MAX ?>) {
                var error_tolerance = <?= $model->treshold_b ?>;
            }

            var dError = ((dDraw - dBuilt) / dDraw) * -100;
            $(`.tr_${key} .dError`).val(dError.toFixed(2));

            if (dError.toFixed(2) >= -error_tolerance.toFixed(2) && dError.toFixed(2) <= error_tolerance) {
                $(`.tr_${key} .d_res_sts`).val("Pass");
                $(`.tr_${key} .d_res`).val(<?= TestFormDimension::RESULT_PASS['value'] ?>);
            } else {
                $(`.tr_${key} .d_res_sts`).val("Fail");
                $(`.tr_${key} .d_res`).val(<?= TestFormDimension::RESULT_FAIL['value'] ?>);
            }
        }
    }

</script>