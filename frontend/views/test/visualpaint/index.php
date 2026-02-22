<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestStatus;
use frontend\models\test\TestFormVisualpaint;

$this->title = 'Visual and Painting Inspection';
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

    .invalid-feedback{
        text-align: center;
    }
</style>
<div class="test-form-visualpaint-index">

    <div class="col-12 mb-3 pr-0">
        <div class="row col-12 justify-content-between pr-0">
            <div>
                <h3><?= Html::encode($this->title) ?></h3>
            </div>
            <div>
                <?=
                Html::a("Edit Procedures <i class='far fa-edit'></i>", "javascript:", [
                    'title' => "Edit Procedures",
                    "value" => yii\helpers\Url::to(['/test/visualpaint/edit-procedure', 'id' => $model->id]),
                    "class" => "modalButton btn btn-success",
                    'data-modaltitle' => "Edit Procedures"
                ])
                ?>
                <?php
                if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) {
                    echo $this->render('..\_formModalAddPunchlist', [
                        'id' => $master->id,
                        'formType' => TestMaster::CODE_VISUALPAINT
                    ]);
                }
                ?>
                <?php
//                if ($model->status == RefTestStatus::STS_SETUP) {
//                    echo Html::a("Edit Procedures <i class='far fa-edit'></i>", "javascript:", [
//                        'title' => "Edit Procedures",
//                        "value" => yii\helpers\Url::to(['/test/visualpaint/edit-procedure', 'id' => $model->id]),
//                        "class" => "modalButton btn btn-success",
//                        'data-modaltitle' => "Edit Procedures"
//                    ]);
//                } else if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) {
//                    echo $this->render('..\_formModalAddPunchlist', [
//                        'id' => $master->id,
//                        'formType' => TestMaster::CODE_VISUALPAINT
//                    ]);
//                }
                ?>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <?php
            $form = ActiveForm::begin([
                'options' => ['autocomplete' => 'off', 'id' => 'myForm'],
            ]);
            ?>
            <div class="mb-4">
                <div class="row">
                    <div class="col-12">
                        <?php
                        if ($template) {
                            $modelProcedures = explode('|', $procedures);
                            echo '<div class="col-12">' . (isset($modelProcedures[0]) ? $modelProcedures[0] : '') . '</div>';
                        } else {
                            echo '<div class="col-12">' . (isset($procedures->proctest1) ? $procedures->proctest1 : '') . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <h5>Test Result</h5>
                <?=
                $this->render('_formVisual', [
                    'form' => $form,
                    'model' => $model,
                    'currSts' => $currSts
                ])
                ?>
            </div>
            <div class="mt-5">
                <div class="row">
                    <div class="col-12">                  
                        <?php
                        if ($template) {
                            echo '<div class="col-12">' . (isset($modelProcedures[1]) ? $modelProcedures[1] : '') . '</div>';
                        } else {
                            echo '<div class="col-12">' . (isset($procedures->proctest1) ? $procedures->proctest2 : '') . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="row mt-3 ml-1 thresholddiv">
                <div class="col-md-12 col-sm-12">
                    <h5>Threshold (μm)
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
            <div class="col-12 mt-2">
                <h5>Test Result</h5>
                <?=
                $this->render('_formPaint', [
                    'form' => $form,
                    'model' => $model,
                    'currSts' => $currSts
                ])
                ?>
            </div>
            <div class="col-12">
                <?php
                echo $form->field($model, 'status')->hiddenInput(['value' => $model->status])->label(false);
                if ($model->status == RefTestStatus::STS_SETUP) {
//                    echo Html::a('Delete Form &nbsp;<i class="fa fa-trash"></i>', ["delete-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2', 'data-confirm' => 'Delete this form?']);
                    echo Html::a('Delete Form  ',
                            ['delete-form', 'id' => $model->id],
                            [
                                'class' => 'float-right btn btn-danger ml-2 delete-form', // Added 'delete-form' class
                                'data-confirm' => 'Delete this form?',
                            ]
                    );
                    echo Html::submitButton('Save and Ready to Test &nbsp;<i class="far fa-clipboard"></i>', ['class' => 'float-right btn btn-success ml-2 setup-input save-and-status', 'data-status' => RefTestStatus::STS_READY_FOR_TESTING]);
                } else if ($model->status == RefTestStatus::STS_IN_TESTING) {
                    echo Html::submitButton('Save and Fail &nbsp;<i class="fas fa-times"></i>', ['class' => 'float-right btn btn-danger ml-2 setup-input save-and-status', 'data-status' => RefTestStatus::STS_FAIL]);
                    echo Html::submitButton('Save and Pass &nbsp;<i class="fas fa-clipboard-check"></i>', ['class' => 'float-right btn btn-success ml-2 save-and-status', 'data-status' => RefTestStatus::STS_COMPLETE]);
                }
                ?>
                <div class="text-right">
                    <?php
                    if ($model->status != RefTestStatus::STS_SETUP && $currSts) {
                        echo Html::submitButton('Save Temporarily', ['class' => 'btn btn-success save-and-status', 'data-status' => $model->status]);
                    }

                    if ($model->status == RefTestStatus::STS_READY_FOR_TESTING || $model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                        echo Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'btn btn-danger ml-2 revert-form', 'data-confirm' => 'Revert this form?']);
                    }
                    ?>
                </div>
            </div>
            <?php
            ActiveForm::end();
            ?>
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
            ?>
            <?php
            if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                if ($witnessList) {
                    echo $this->render('../__signatureForm', [
                        'model' => $model,
                        'witnessList' => $witnessList
                    ]);
                }
            }
            ?>
        </div>
    </div>

</div>

<script>
//    $('#myForm').submit(function () {
//        var form = document.getElementById('myForm');
//        var inputs = form.querySelectorAll('input, select, button');
//
//        inputs.forEach(function (input) {
//            input.disabled = false;
//        });
//
//        return true;
//    });

//    $('.revert-form').on('click', function (e) {
//        e.stopPropagation(); // Prevent event from bubbling to form
//
//        var href = $(this).attr('href');
//        var confirmMsg = $(this).data('confirm');
//
//        if (confirmMsg) {
//            if (confirm(confirmMsg)) {
//                window.location.href = href;
//            }
//        } else {
//            window.location.href = href;
//        }
//
//        return false; // Prevent default link behavior
//    });
//
//    $('.delete-form').on('click', function (e) {
//        e.stopPropagation(); // Prevent event from bubbling to form
//    });
//
//    $('#myForm').submit(function (e) {
//        var activeElement = $(document.activeElement);
//
//        if (activeElement.closest('.signature-modal').length > 0 ||
//                activeElement.hasClass('sign-btn') ||
//                activeElement.hasClass('close-modal') ||
//                activeElement.attr('id') === 'clear-expanded-signature' ||
//                activeElement.attr('id') === 'cancel-signature' ||
//                activeElement.attr('id') === 'save-signature' ||
//                (activeElement.attr('id') && activeElement.attr('id').startsWith('clear-signature-'))) {
//            e.preventDefault();
//            return false;
//        }
//
//        if (!activeElement.is('.save-and-status, .revert-form, .delete-form, .btn-success')) {
//            e.preventDefault();
//            return false;
//        }
//
//        var form = document.getElementById('myForm');
//        var inputs = form.querySelectorAll('input, textarea');
//        inputs.forEach(function (input) {
//            input.disabled = false;
//        });
//
//        return true;
//    });

    $('.revert-form').on('click', function (e) {
        e.preventDefault(); // Add this
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

        return false;
    });

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

    $(document).ready(function () {
        var csrfInput = $('input[name="_csrf-frontend"]');
        csrfInput.addClass('setup-input');
    });

    window.onload = function () {
        var status = <?= $model->status ?>;
        var setThreshold = <?= $setThreshold ?>;
        var form = document.getElementById('myForm');
        var thresholddiv = form.querySelectorAll('.thresholddiv');

        disableCustomForm('myForm', status);

        if (!setThreshold) {
            thresholddiv.forEach(function (input) {
                input.style.display = 'none';
            });
        }
    };

    function disableCustomForm(formId, status) {
        var form = document.getElementById(formId);
        var inputs = form.querySelectorAll('input, select, button');

        inputs.forEach(function (input) {
            if (status == <?= RefTestStatus::STS_SETUP ?> && !input.classList.contains('setup-input')) {
                input.disabled = true;
            } else if (status == <?= RefTestStatus::STS_READY_FOR_TESTING ?> || status == <?= RefTestStatus::STS_FAIL ?> || status == <?= RefTestStatus::STS_COMPLETE ?>) {
                if (!input.classList.contains('able')) {
                    input.classList.add('custom-disabled');
                }
            } else if (status === <?= RefTestStatus::STS_IN_TESTING ?>) {
                input.placeholder = 'Enter a value';
            }
        });

        form.querySelectorAll('.avg').forEach(function (input) {
            if (status === <?= RefTestStatus::STS_IN_TESTING ?>) {
                input.disabled = true;
                input.removeAttribute('placeholder');
            }
        });

        form.querySelectorAll('.remark').forEach(function (input) {
            if (status === <?= RefTestStatus::STS_IN_TESTING ?>) {
                input.placeholder = 'remark';
            }
        });

    }

    $('.save-and-status').click(function () {
        var statusValue = $(this).data('status');
        $('#<?= Html::getInputId($model, 'status') ?>').val(statusValue);
    });

</script>
