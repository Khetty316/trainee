<?php

use yii\helpers\Html;
use frontend\models\test\TestMaster;
use yii\widgets\ActiveForm;
use frontend\models\test\TestFormAts;
use frontend\models\test\RefTestStatus;

/** @var yii\web\View $this */
/** @var frontend\models\test\TestFormAtsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'ATS Functionality';
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $master->testMain->panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => 'Test Panel Details', 'url' => ['/test/testing/index-panel', 'id' => $master->testMain->panel->id]];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = $this->title;

$currSts = ($model->status != RefTestStatus::STS_READY_FOR_TESTING && $model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) ? 1 : 0;
?>

<style>
    .radio-inline {
        display: inline-block;
    }

    .form-group{
        margin-bottom:0;
    }

    #myForm input{
        border: none !important;
    }
</style>

<div class="test-form-ats-index">

    <div class="row col-12">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <div>
        <?php
        if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) {
            echo $this->render('..\_formModalAddPunchlist', [
                'id' => $master->id,
                'formType' => TestMaster::CODE_ATS
            ]);
        }
        ?>
    </div>

    <?php
    $form = ActiveForm::begin([
        'options' => ['autocomplete' => 'off', 'id' => 'myForm']
    ]);
    ?>

    <div id="acot-form" class="mt-3">
        <h5>1. Auto Change Over Test (5 sec)</h5>
        <?=
        $this->render('_formAcot', [
            'form' => $form,
            'model' => $model,
            'details' => $detailAcots,
            'currSts' => $currSts
        ]);
        ?>
    </div>

    <div id="mcot-form" class="mt-3">
        <h5>2. Manual Change Over Test (With no control on power supply)</h5>
        <?=
        $this->render('_formMcot', [
            'form' => $form,
            'model' => $model,
            'details' => $detailMcots,
            'currSts' => $currSts
        ]);
        ?>
    </div>

    <div id="cbvc-form" class="mt-3">
        <h5>3. Circuit Breaker Voltage Check</h5>
        <?=
        $this->render('_formCbvc', [
            'form' => $form,
            'model' => $model,
            'details' => $detailCbvcs,
            'currSts' => $currSts
        ]);
        ?>
    </div>

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

    <div class="row mb-3">
        <div class="col-12">
            <?= $form->field($model, 'submitSts')->hiddenInput(['value' => ''])->label(false); ?>
            <?php
            if ($model->status == RefTestStatus::STS_SETUP) {
                echo Html::a('Delete Form &nbsp;<i class="fa fa-trash"></i>', ["delete-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2', 'data-confirm' => 'Delete this form?']);
                echo Html::submitButton('Save and Ready to Test &nbsp;<i class="far fa-clipboard"></i>', ["class" => "float-right btn btn-success ml-2 save-and-status", 'data-status' => RefTestStatus::STS_READY_FOR_TESTING]);
            } else if ($model->status == RefTestStatus::STS_IN_TESTING) {
                echo Html::submitButton('Save and Fail &nbsp;<i class="fas fa-times"></i>', ['class' => 'float-right btn btn-danger ml-2 save-and-status', 'data-status' => RefTestStatus::STS_FAIL]);
                echo Html::submitButton('Save and Pass &nbsp;<i class="fas fa-clipboard-check"></i>', ['class' => 'float-right btn btn-success ml-2 save-and-status', 'data-status' => RefTestStatus::STS_COMPLETE]);
            }
            ?>
            <div class="text-right">
                <?php
                if ($currSts) {
//                    echo Html::submitButton('Save Temporarily', ['class' => 'btn btn-success mb-3']);
                }

                if ($model->status === RefTestStatus::STS_READY_FOR_TESTING || $model->status === RefTestStatus::STS_FAIL || $model->status === RefTestStatus::STS_COMPLETE) {
                    echo Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger revert ml-2 mb-3', 'data-confirm' => 'Revert this form?']);
                }
                ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="mb-3">
        <?php if ($witnessList) { ?>
            <h5>Witnesses</h5>
        <?php } ?>
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
<script>

    window.onload = function () {
        var status = <?= $currSts ?>;
        if (!status) {
            disableCustomForm('myForm');
        }
    };

    function disableCustomForm(formId) {
        var form = document.getElementById(formId);
        var inputs = form.querySelectorAll('input, textarea, button, a, select');
        var excludedInputs = ['.revert'];

        inputs.forEach(function (input) {
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

    $('.save-and-status, .revert').click(function () {
        var statusValue = $(this).data('status');
        $('#<?= Html::getInputId($model, 'submitSts') ?>').val(statusValue);
        if (statusValue === <?= RefTestStatus::STS_COMPLETE ?> || statusValue === <?= RefTestStatus::STS_FAIL ?>) {
            var form = document.getElementById('myForm');
            var inputs = form.querySelectorAll('input');
            inputs.forEach(function (input) {
                input.required = true;
            });
        }

        window.scrollTo(0, 0);
        eraseCookie('jumpToScrollPosition');
    });


    function addColumn(key, type, formType) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['test/ats/ajax-add-detail-column']) ?>',
            dataType: 'html',
            data: {
                key: key,
                type: type,
                formType: formType
            }
        });
    }

    function addRow(key, formType) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['test/ats/ajax-add-detail-row']) ?>',
            dataType: 'html',
            data: {
                key: key,
                formType: formType
            }
        });

    }

    function deleteColumn(key, attribute, formType) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['test/ats/ajax-delete-column']) ?>',
            dataType: 'html',
            data: {
                key: key,
                attribute: attribute,
                formType: formType
            }
        });
    }
    function deleteRow(key, formType) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['test/ats/ajax-delete-row']) ?>',
            dataType: 'html',
            data: {
                key: key,
                formType: formType
            }
        });
    }

    function maxColumn() {
        alert('Maximum number of columns reached!');
    }

    //Update ON/OFF state
    const valbuttons = document.querySelectorAll('.toggleButton');
    const resbuttons = document.querySelectorAll('.toggleButtonResult');
    const values = [0, 1];
    const valtexts = ['OFF', 'ON'];
    const restexts = ['FAIL', 'PASS'];

    function handleButtonClick(button, texts) {
        const currentValue = texts.indexOf(button.textContent) !== -1 ? texts.indexOf(button.textContent) : values.indexOf(parseInt(button.textContent));
        const oppositeIndex = (currentValue + 1) % 2;
        button.textContent = texts[oppositeIndex];
        const id = button.dataset.id;
        const detailid = button.dataset.detailid;
        saveValueToBackend(id, values[oppositeIndex], detailid);
    }

    valbuttons.forEach(button => {
        button.addEventListener('click', function () {
            handleButtonClick(button, valtexts);
        });
    });

    resbuttons.forEach(button => {
        button.addEventListener('click', function () {
            handleButtonClick(button, restexts);
        });
    });


    function saveValueToBackend(attribute, value, detailid) {
        $.ajax({
            type: 'POST',
            url: '/test/ats/mark-attribute-result',
            data: {attribute: attribute, value: value, detailid: detailid},
            success: function (response) {
            },
            error: function () {
            }
        });
    }

    //Saving text input
    let typingTimers = {};
    const doneTypingInterval = 1000;

    $('.textInput').on('input', function () {
        const inputField = $(this);
        clearTimeout(typingTimers[inputField.attr('id')]);
        typingTimers[inputField.attr('id')] = setTimeout(function () {
            if (inputField.attr('data-attribute') == 'mode') {
                sendDataToBackendMode(inputField, inputField.attr('data-formid'), inputField.attr('data-attribute'));
            } else {
                sendDataToBackend(inputField, inputField.attr('data-formid'), inputField.attr('data-attribute'));
            }
        }, doneTypingInterval);
    });

    function sendDataToBackend(inputField, formid, attribute) {
        const userInput = inputField.val();
        $.ajax({
            type: 'POST',
            url: '/test/ats/save-text-input',
            data: {userInput: userInput, formid: formid, attribute: attribute},
            success: function (response) {
            },
            error: function () {
            }
        });
    }

    function sendDataToBackendMode(inputField, formid, attribute) {
        const userInput = inputField.val();
        $.ajax({
            type: 'POST',
            url: '/test/ats/save-text-input-mode',
            data: {userInput: userInput, formid: formid, attribute: attribute},
            success: function (response) {
            },
            error: function () {
            }
        });
    }

    window.addEventListener('beforeunload', function () {
        var currentYOffset = window.pageYOffset;
        setCookie('jumpToScrollPosition', currentYOffset, 1);
    });

    var jumpTo = getCookie('jumpToScrollPosition');
    if (jumpTo !== null && jumpTo !== "undefined") {
        window.scrollTo(0, jumpTo);
        eraseCookie('jumpToScrollPosition');

    }
</script>
