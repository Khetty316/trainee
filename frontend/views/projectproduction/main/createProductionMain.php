<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectProduction\ProjectProductionMaster */

$this->title = 'New Project';
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <p>Import from quotation:</p>
    <?php
    $form = ActiveForm::begin([
        'id' => 'myFinalizeForm',
    ]);
    ?>
    <div class="row">
        <div class="col-md-8">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2">Project Detail:</legend>
                <div class="hidden col-5">
                    <?= $form->field($model, 'client_id')->textInput()->label(false) ?>
                    <?= $form->field($model, 'quotation_id')->textInput()->label(false) ?>
                    <?= $form->field($model, 'revision_id')->textInput()->label(false) ?>
                </div>
                <div class="form-row">
                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                        <?= $form->field($model, 'quotationNo')->textInput(['readonly' => true]) ?>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-9">
                        <?= $form->field($model, 'quotationName')->textInput(['readonly' => true]) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 col-xl-4">
                        <?= $form->field($model, 'projectType')->textInput(['readonly' => true]) ?>
                    </div>
                    <div class="col-md-4 col-xl-4">
                        <?= $form->field($model, 'amount')->textInput(['readonly' => true]) ?>
                    </div>
                    <div class="col-md-4 col-xl-4">
                        <?= $form->field($model, 'clientName')->textInput(['readonly' => true]) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12 col-xl-12">
                        <?= $form->field($model, 'name')->textInput() ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-8 col-xl-8">
                        <?= $form->field($model, 'remark')->textarea(['rows' => 2]) ?>
                    </div>
                    <div class="col-sm-4 col-md-4 col-xl-4">
                        <?=
                                $form->field($model, 'current_target_date')
                                ->label('Target Completion Date <span class="text-danger">*</span>', ['encode' => false])
                                ->textInput([
                                    'type' => 'date',
                                    'class' => 'form-control',
                                    'required' => true,
                                    'value' => (!empty($model->current_target_date) ? date('Y-m-d', strtotime($model->current_target_date)) : '')
                                ])
                        ?>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12 col-xl-9">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2  m-0 ">Panels:</legend>
                    <?php
                    $panels = $model->revision->projectQPanels;

                    if ($panels) {
                        array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                        ?> 
                        <table class="table table-sm table-striped table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th class="tdnowrap text-center">#</th>
                                    <th>Panel's Type</th>
                                    <th>Panel's Name</th>
                                    <th class="tdnowrap text-center">Quantity</th>
                                    <th class="text-right" >Unit Price (<?= $model->revision->currency->currency_sign ?>)</th>
                                    <th class="tdnowrap text-center align-top">Select<br/>
                                        <?= Html::checkbox("", false, ['id' => 'checkAllControl', 'onclick' => 'checkAllItems(this)']) ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="itemDisplayTable">
                                <?php
                                foreach ($panels as $key => $panel) {
                                    $panelCheckboxOptions = [
                                        'value' => $panel->id,
                                        'class' => 'itemToFinalize',
                                    ];
                                    echo '<tr>';
                                    echo '<td class="text-right px-2">' . ($key + 1) . '</td>';
                                    echo '<td class="col-1 tdnowrap">' . ($panel->panelType->project_type_name ?? '') . '</td>';
                                    echo '<td style="">' . $panel->panel_description . '</td>';
                                    echo '<td class="text-right px-3 tdnowrap">' . MyFormatter::asDecimal2($panel->quantity) . " " . $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") . '</td>';
                                    echo '<td class="text-right px-2 tdnowrap">' . MyFormatter::asDecimal2($panel->amount ?? 0) . '</td>';
                                    echo '<td class="text-center">';
                                    if (empty($panel->finalized_at)) {
                                        echo Html::checkbox("finalizeBox[]", false, $panelCheckboxOptions);
                                        $hasPendingPanel = true;
                                    }
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    } else {
                        echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
                    }
                    ?>    
                    <?php ?>
                </fieldset>
            </div>
        </div>
        <?php
        echo Html::a('Save', 'javascript:validateAndPush()',
                ["class" => "btn btn-success mb-2 mt-0 px-4"]);
        ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script>
    function checkAllItems(me) {
        let toCheck = $(me).is(':checked');
        $("#myFinalizeForm").find(".itemToFinalize").each(function (idx, elem) {
            $(elem).prop('checked', toCheck);
        });
    }

//    function validateAndPush() {
//        let checkedPanels = [];
//        $("#myFinalizeForm").find(".itemToFinalize:checked").each(function (idx, elem) {
//            checkedPanels.push($(elem).val());
//        });
//        console.log(checkedPanels);
//
//        $("#myFinalizeForm").submit();
//
//    }
    function validateAndPush() {
        let checkedPanels = [];
        $("#myFinalizeForm").find(".itemToFinalize:checked").each(function (idx, elem) {
            checkedPanels.push($(elem).val());
        });

        let targetDate = $('input[name*="[current_target_date]"]').val();
        if (!targetDate || targetDate === '') {
            alert('Please select a Target Completion Date');
            return;
        }
        if (checkedPanels.length === 0) {
            alert('No Panel is selected');
            return; // Don't submit the form
        }

        $("#myFinalizeForm").submit();
    }
</script>
