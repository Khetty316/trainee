<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;
use yii\web\JsExpression;
use common\models\myTools\MyFormatter;

$this->title = 'Report - Quotation Hit';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><?= Html::encode($this->title) ?></h3>
<div class="work-assignment-master-form">
    <?php
    $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-sm-6\">{input}\n{error}</div>",
                    'labelOptions' => ['class' => 'col-sm-6 control-label'],
                ],
                'options' => ['autocomplete' => 'off']
    ]);
    ?>
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <?=
                        $form->field($model, 'dateFrom', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(DatePicker::className(), [
                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                            'dateFormat' => 'dd/MM/yyyy',
                            'clientOptions' => [
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                            ],
                        ])->label("Quotation Date From");
                ?>
            </div>

        </div>
    </div>
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">

                <?=
                        $form->field($model, 'dateTo', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(DatePicker::className(), [
                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                            'dateFormat' => 'dd/MM/yyyy',
                            'clientOptions' => [
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                            ],
                        ])->label("Quotation Date To");
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <?= Html::submitButton('Search <i class="fas fa-search"></i>', ['class' => 'btn btn-primary float-right']) ?>
            </div>
        </div>
    </div>
    <div class="form-group" id='viewArea1'>
        <div class="col-md-1">
            <?php if (!empty($summary)) { ?>
                <table class="table table-sm table-bordered table-striped tdnowrap">
                    <thead>
                        <tr>
                            <th rowspan="2">Type</th>
                            <th colspan="3" class="text-center">Quotations</th>
                            <th colspan="1" class="text-center">Panels</th>
                            <th>Amount (RM)</th>
                        </tr>
                        <tr>
                            <th class="text-center">Quoted</th>
                            <th class="text-center">Confirmed</th>
                            <th class="text-center">Hit Rate</th>
                            <th class="text-center">Confirmed Qty</th>
                            <th class="text-center">Confirmed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($summary as $key => $detail) {
                            ?>
                            <tr>
                                <td><?= $key ?></td>
                                <td class="text-right"><?= MyFormatter::asDecimal0($detail['totalQuotation']) ?></td>
                                <td class="text-right"><?= MyFormatter::asDecimal0($detail['totalConfirmed']) ?></td>
                                <td class="text-right"><?= MyFormatter::asDecimal2_emptyZero($detail['totalConfirmed'] / $detail['totalQuotation'] * 100) ?> %</td>
                                <td class="text-right"><?= MyFormatter::asDecimal0($detail['totalPanelQty']) ?></td>
                                <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($detail['totalAmount']) ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo "<div class='tdnowrap'>-- No Record --</div>";
            }
            ?>
        </div>
    </div>
    <div class="form-group col-12" id='viewArea2'>
        <?php if (!empty($VQMasterHasRevisions)) { ?>
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Quotation No</th>
                        <!--<th>Project Name</th>-->
                        <th>Confirmed</th>
                        <th>Panel Qty</th>
                        <th>Total Amt</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($VQMasterHasRevisions as $key => $projQType) {
                        ?>
                        <tr>
                            <td width="1px" class="text-right"><?= $key + 1 ?></td>
                            <td><?= $projQType->q_type_name ?></td>
                            <td><?= $projQType->quotation_display_no ?></td>
                            <!--<td><?php //= $projQType->project_name                ?></td>-->
                            <td><?= $projQType->is_finalized ? "YES" : "" ?></td>
                            <td class="text-right"><?= $projQType->is_finalized ? MyFormatter::asDecimal0($projQType->totalPanels) : "" ?></td>
                            <td class="text-right"><?= $projQType->is_finalized ? MyFormatter::asDecimal2_emptyDash($projQType->active_revision_amount) : "" ?></td>
                            <td><?= MyFormatter::asDate_Read($projQType->created_at) ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        <?php } ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<script>
    $(function () {

        $("input").on('change', function () {
            $("#viewArea1, #viewArea2").html('');
        });
    });

</script>