<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestCompType;
use frontend\models\test\TestDetailComponent;
use frontend\models\test\RefTestStatus;
use common\models\myTools\MyCommonFunction;

$this->title = 'Component Check';
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $master->testMain->panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => 'Test Panel Details', 'url' => ['/test/testing/index-panel', 'id' => $master->testMain->panel->id]];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = $this->title;

$currSts = ($model->status != RefTestStatus::STS_READY_FOR_TESTING && $model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) ? 1 : 0;
?>

<style>
    .dynamic-table{
        border: none;
    }
</style>
<div class="test-form-component-index">

    <div class="col-12">
        <div class="row justify-content-between">
            <div>
                <h3 class="mb-3"><?= Html::encode($this->title) ?></h3>
            </div>
            <div>
                <?php
                if ($model->status == RefTestStatus::STS_SETUP || $model->status == RefTestStatus::STS_IN_TESTING) {
                    echo Html::a("Add Component <i class='fas fa-plus'></i>", "javascript:", [
                        'title' => "Add a component form",
                        "value" => yii\helpers\Url::to(['/test/component/add-component-form', 'id' => $model->id]),
                        "class" => "modalButton btn btn-success",
                        'data-modaltitle' => "Add a Component Form",
                        'id' => 'addComponentButton',
                    ]);

                    if ($addComponentForm) {
                        $this->registerJs("$('#addComponentButton').click();");
                    }
                }
                if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) {
                    echo $this->render('..\_formModalAddPunchlist', [
                        'id' => $master->id,
                        'formType' => TestMaster::CODE_COMPONENT
                    ]);
                }
                ?>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <?php if ($model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) : ?>
            <?php
            $form = ActiveForm::begin([
                'action' => ['/test/component/save-component'],
                'options' => ['autocomplete' => 'off', 'id' => 'myForm'],
            ]);
            ?>

            <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

            <?php
            foreach ($details as $key => $detail) {
                $header = $model->status == RefTestStatus::STS_SETUP ? $detail['form']->compType->name : (
                        ($detail['form']->comp_type == RefTestCompType::TYPE_OTHER) ? $detail['form']->comp_name . " " . $detail['form']->pou0->name . '-' . $detail['form']->pou_val : $detail['form']->compType->name . " " . $detail['form']->pou0->name . '-' . $detail['form']->pou_val
                );
                ?>
                <div class="row">
                    <div class="col-12">
                        <fieldset class="form-group border p-3">
                            <legend class="w-auto px-2 m-0"><h5 class="m-0">
                                    <?php
                                    $id = $detail['form']['id'];
                                    echo Html::a("<i class='fa fa-minus-circle text-danger'></i>", ["delete-component", 'id' => $id], ['data-confirm' => 'Delete this component?']);
                                    ?>
                                    Details of <?= $header ?> : </h5></legend>

                            <div class="row">
                                <?php foreach ($detail['attributetorender'] as $attribute) { ?>
                                    <?php
                                    if (($attribute === TestDetailComponent::ATTRIBUTE_POU || $attribute === TestDetailComponent::ATTRIBUTE_POUVAL) && $model->status != RefTestStatus::STS_SETUP) {
                                        continue;
                                    }

                                    if ($attribute != TestDetailComponent::ATTRIBUTE_ACCESSORY) {
                                        ?>
                                        <div class="col-3">
                                            <div class="col-12 p-0 m-0 mt-1"><label for="testDetailComponent_<?= $key ?>_<?= $attribute ?>"><?= $detail['form']->attributeLabels()["$attribute"] ?>:</label></div>

                                            <?php
                                            switch ($attribute) {
                                                case TestDetailComponent::ATTRIBUTE_POU:
                                                    $list = ($detail['form']->compType->code == RefTestCompType::TYPE_BUSBAR) ? $pointBusbarList : $pointMeterList;
                                                    echo Html::dropDownList("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], $list, ['class' => 'form-control']);
                                                    break;
                                                case TestDetailComponent::ATTRIBUTE_POUVAL:
                                                    echo Html::textInput("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], ['class' => 'form-control']);
                                                    break;
                                                case TestDetailComponent::ATTRIBUTE_MAKE:
                                                    echo MyCommonFunction::htmlFormAutocompleteInput("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], TestDetailComponent::getAutoCompleteListAttr($attribute, $detail['form']->comp_type), 'form-control');
                                                    break;
                                                case TestDetailComponent::ATTRIBUTE_FUNCTIONTYPE:
                                                    echo Html::dropDownList("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], $funcList, ['class' => 'form-control']);
                                                    break;
                                                case TestDetailComponent::ATTRIBUTE_TYPE:
                                                    if ($detail['form']->compType->code == RefTestCompType::TYPE_METER) {
                                                        echo Html::dropDownList("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], ['Analog', 'Digital'], ['class' => 'form-control']);
                                                    } else {
                                                        echo MyCommonFunction::htmlFormAutocompleteInput("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], TestDetailComponent::getAutoCompleteListAttr($attribute, $detail['form']->comp_type), 'form-control');
                                                    }
                                                    break;
                                                case TestDetailComponent::ATTRIBUTE_PARTICULARFUNCTION:
                                                    echo Html::dropDownList("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], $pointParticularFnList, ['class' => 'form-control']);
                                                    break;
                                                case TestDetailComponent::ATTRIBUTE_PROTECTIONMODE:
                                                    echo Html::dropDownList("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], $pointProtectionModeList, ['class' => 'form-control']);
                                                    break;
                                                default:
                                                    echo Html::textInput("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], ['class' => 'form-control']);
                                                    break;
                                            }
                                            ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-12">
                                            <div class="col-12 p-0 m-0 mt-1"><label for="testDetailComponent_<?= $key ?>_<?= $attribute ?>"><?= $detail['form']->attributeLabels()["$attribute"] ?>:</label></div>
                                            <?php
                                            echo Html::checkboxList("testDetailComponent[$key][$attribute]", $detail['form'][$attribute], $accesList, ['itemOptions' => ['class' => 'big-checkbox card mx-1', 'labelOptions' => ['class' => 'mr-2']], 'class' => 'card-column']);
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                if ($detail['form']->compType->code == RefTestCompType::TYPE_OTHER) {
                                    ?>
                                    <div class="col-12 mt-3">
                                        <table class="table table-sm col-sm-12 table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="col-6">Component Detail</th>
                                                    <th class="col-6" colspan="2">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody name="listTBodyOther-<?= $key ?>">
                                                <?php foreach ($detail['otheritem'] as $key0 => $item): ?>
                                                    <?= $this->render('_formItemOther', ['detailId' => $detail['form']->id, 'form' => $form, 'key0' => $key0, 'item' => $item]) ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="12">
                                                        <a class='btn btn-sm btn-primary float-right' href='javascript:addRowOther(<?= $detail['form']->id ?>, <?= $key ?>)'> <i class="fas fa-plus-circle"></i></a>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-12">
                    <fieldset class="form-group border p-3">
                        <legend class="w-auto px-2 m-0"><h5>Components Conformity</h5></legend>
                        <div class="form-row">
                            <table class="table table-sm col-sm-12 table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="col-6">Non-conform Component</th>
                                        <th class="col-6" colspan="2">Remark</th>
                                    </tr>
                                </thead>
                                <tbody id="listTBody">
                                    <?php foreach ($conformities as $key2 => $conformity): ?>
                                        <?= $this->render('_formConformityItem', ['form' => $form, 'key2' => 100 + $key2, 'conformity' => $conformity]) ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="border-0">
                                    <tr>
                                        <td colspan="12">
                                            <a class='btn btn-sm btn-primary float-right' href='javascript:addRow()'> <i class="fas fa-plus-circle"></i></a>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </fieldset>
                    <div class="row">
                        <div class="col-12">
                            <?= $form->field($model, 'submitSts')->hiddenInput(['value' => ''])->label(false); ?>
                            <?php
                            if ($model->status == RefTestStatus::STS_SETUP) {
                                echo Html::a('Delete Form &nbsp;<i class="fa fa-trash"></i>', ["delete-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2', 'data-confirm' => 'Delete this form?']);
//                                echo Html::a('Save and Ready to Test &nbsp;<i class="far fa-clipboard"></i>', ["component-status", 'id' => $model->id, 'sts' => RefTestStatus::STS_READY_FOR_TESTING], ['class' => 'float-right btn btn-success ml-2']);
                                echo Html::submitButton('Save and Ready to Test &nbsp;<i class="far fa-clipboard"></i>', ["class" => "float-right btn btn-success ml-2 save-and-status", 'data-status' => RefTestStatus::STS_READY_FOR_TESTING]);
                            } else if ($model->status == RefTestStatus::STS_IN_TESTING) {
                                echo Html::submitButton('Save and Fail &nbsp;<i class="fas fa-times"></i>', ['class' => 'float-right btn btn-danger ml-2 save-and-status', 'data-status' => RefTestStatus::STS_FAIL]);
                                echo Html::submitButton('Save and Pass &nbsp;<i class="fas fa-clipboard-check"></i>', ['class' => 'float-right btn btn-success ml-2 save-and-status', 'data-status' => RefTestStatus::STS_COMPLETE]);
                            }
                            ?>
                            <div class="text-right">
                                <?php
                                if ($currSts) {
                                    echo Html::submitButton('Save Temporarily', ['class' => 'btn btn-success mb-3 save-and-status']);
                                }

                                if ($model->status === RefTestStatus::STS_READY_FOR_TESTING) {
                                    echo Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger revert ml-2 mb-3', 'data-confirm' => 'Revert this form?']);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="mb-5 pb-5">
                <?=
                $this->render('_viewComponent', [
                    'model' => $model,
                    'details' => $details,
                    'accesList' => $accesList,
                    'funcList' => $funcList,
                    'conformities' => $conformities,
                ])
                ?>
                <?= Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger revert ml-2', 'data-confirm' => 'Revert this form?']) ?>
            </div>
            <div class="mb-5 pb-5">
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
        <?php endif; ?>
    </div>
</div>

<script>
    var currentKey = <?= sizeof($conformities) ?>;
    var currentKey2 = 100 + <?= sizeof($conformities) ?>;

    window.onload = function () {
        var currentStatus = <?= $model->status ?>;
        disableCustomForm('myForm', currentStatus);
    };

    $('.save-and-status, .revert').click(function () {
        var statusValue = $(this).data('status');
        $('#<?= Html::getInputId($model, 'submitSts') ?>').val(statusValue);
        var form = document.getElementById('myForm');
        var inputs = form.querySelectorAll('input, textarea');
        var excludedInputs = ['.nonconform, .remark, .hidden'];
        if (statusValue === <?= RefTestStatus::STS_COMPLETE ?> || statusValue === <?= RefTestStatus::STS_FAIL ?>) {
            inputs.forEach(function (input) {
                var isExcluded = excludedInputs.some(function (selector) {
                    return input.matches(selector);
                });

                if (isExcluded) {
                    return;
                }

                if (input.type !== 'checkbox') {
                    input.required = true;
                }
            });
        } else {
            inputs.forEach(function (input) {
                input.required = false;
            });
        }

        window.scrollTo(0, 0);
        eraseCookie('jumpToScrollPosition');
    });

    function disableCustomForm(formId, currentStatus) {
        var form = document.getElementById(formId);
        var inputs = form ? form.querySelectorAll('input, textarea, button, a, select') : null;
        var excludedInputs = ['.revert'];

        if (currentStatus === <?= RefTestStatus::STS_SETUP ?> || currentStatus === <?= RefTestStatus::STS_IN_TESTING ?>) {
            form.querySelectorAll('.form-control').forEach(function (input) {
                input.placeholder = 'Enter a value';
            });
            form.querySelectorAll('.compDetail, .nonconform').forEach(function (input) {
                input.placeholder = 'Component detail';
            });
            form.querySelectorAll('.remark').forEach(function (input) {
                input.placeholder = 'remark';
            });
        } else if (currentStatus === <?= RefTestStatus::STS_READY_FOR_TESTING ?> || currentStatus === <?= RefTestStatus::STS_FAIL ?> || currentStatus === <?= RefTestStatus::STS_COMPLETE ?>) {
            if (inputs !== null) {
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
                    if (input.type === 'checkbox') {
                        input.disabled = true;
                    }
                });
            }

            form.querySelectorAll('.revert').forEach(function (input) {
                input.disabled = true;
            });

        }
    }


    function addRowOther(idDetail, key) {
        var currentStatus = <?= $model->status ?>;
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-other-component']) ?>',
            dataType: 'html',
            data: {
                key0: currentKey++,
                idDetail: idDetail
            }
        }).done(function (response) {
            $("[name='listTBodyOther-" + key + "']").append(response);
            disableCustomForm('myForm', currentStatus);
        });
    }

    function addRow() {
        var currentStatus = <?= $model->status ?>;
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-conformity-item']) ?>',
            dataType: 'html',
            data: {
                key2: currentKey2++
            }
        }).done(function (response) {
            $("#listTBody").append(response);
            disableCustomForm('myForm', currentStatus);
        });
    }

    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $("#tr_" + rowNum).hide();
            $("#toDelete-" + rowNum).val("1");
        }
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