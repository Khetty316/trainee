<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefGeneralReferences;
use frontend\models\common\RefProjectQShippingMode;

$this->title = $revision->revision_description;
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $revision->projectQType->project->quotation_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $revision->projectQType->project_id]];
$this->params['breadcrumbs'][] = ['label' => $revision->projectQType->type0->project_type_name, 'url' => ['/projectqtype/view-project-q-type', 'id' => $revision->projectQType->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/projectqrevision/view-project-q-revision', 'id' => $revision->id]];
$this->params['breadcrumbs'][] = "Release Pdf";

$projectQMaster = $revision->projectQType->project;
$sst = RefGeneralReferences::getValue("sst_value")->value;
$company = $revision->projectQType->project->companyGroupCode;
//$sst = $company->sst_value;
$sstAmount = 0;
?>
<style>
    formHeader td{
        padding: 0px;
        margin: 0px;
        text-align: center;
    }
</style>
<div class="project-qrevisions-update">
    <div class="project-qrevisions-form">

        <?php
        $form = ActiveForm::begin([
            'fieldConfig' => [
                'template' => "{input}",
                'horizontalCssClasses' => [
                ],
            ],
            'options' => ['autocomplete' => 'off'],
            'id' => 'form_receiveAsset',
        ]);
        ?>
        <?= $form->field($model, 'revision_id', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
        <?= $form->field($model, 'project_q_client_id', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
        <?= $form->field($model, 'currency_id', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
        <?= $form->field($model, 'discount_type', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>
        <?= $form->field($model, 'discount_amt', ['options' => ['class' => 'hidden']])->textInput()->label(false) ?>

        <div class="form-row">
            <div class='col-lg-10 col-sm-12'>
                <table class='table table-sm table-borderless' id='formHeader'>
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="3" class='font-weight-bolder' style='font-size: 15pt'>Quotation</td>
                    </tr>
                    <tr>
                        <td>To</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'to_company', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm'])->label(false) ?>
                        </td>
                        <td>From</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'q_from', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm', 'value' => Yii::$app->user->identity->fullname]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Attn</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'to_pic', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm'])->label(false) ?>
                        </td>
                        <td>Our Ref</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'quotation_no', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm', 'readonly' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tel. No</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'to_tel_no', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm'])->label(false) ?>
                        </td>
                        <td>Your Ref</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'q_your_ref', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Fax No</td><td>:</td>
                        <td>
                            <?= $form->field($model, 'to_fax_no', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm'])->label(false) ?>
                        </td>
                        <td>Date</td><td>:</td>
                        <td>
                            <?php
                            echo $form->field($model, 'q_date', ['options' => ['class' => 'p-0 m-0']])
                                    ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control form-control-sm'], 'dateFormat' => 'dd/MM/yyyy'])
                            ?>
                        </td>
                    </tr>
                    <tr style='font-weight: bold;'>
                        <td>RE</td><td>:</td>       
                        <td colspan='4'>
                            <?= $form->field($model, 'proj_title', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm', 'value' => $projectQMaster->project_name])->label(false) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='6'>
                            <p>Further to your kind invitation, we are pleased to append herewith our quotation as follows :</p>
                            <?php
                            $panels = $revision->projectQPanels;
                            array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                            $totalAmount = 0;
                            $totalSST = 0;
                            $totalAmtWithSST = 0;
                            $totalAmtWithoutSST = 0;
                            if ($panels) {
                                $defaultUnCheckAndDisabled = ($company->code === "TKTKM");
                                if ($defaultUnCheckAndDisabled) {
                                    echo Html::hiddenInput(Html::getInputName($model, 'with_sst'), 0);
                                } else {
                                    echo $form->field($model, 'with_sst', ['options' => ['class' => 'float-right']])->checkbox()->label("Include Sales Tax");
                                }
                                ?>
                                <?php //= $form->field($model, 'with_sst', ['options' => ['class' => 'float-right']])->checkbox()->label("Include Sales Tax") ?>  
                                <?= $form->field($model, 'show_breakdown_price', ['options' => ['class' => 'mr-4 float-right']])->checkbox() ?>  
                                <?= $form->field($model, 'show_breakdown', ['options' => ['class' => 'mr-4 float-right']])->checkbox() ?>  
                                <?php
                                $model->show_panel_description = true; // Default to true, requested by customer 
                                echo $form->field($model, 'show_panel_description', ['options' => ['class' => 'mr-4 float-right']])->checkbox();
                                ?>  
                                <table class='table table-sm'>
                                    <thead>
                                        <tr class='text-center'>
                                            <th class='tdnowrap b px-3 vmiddle' >Item</th>
                                            <th class='b px-3 vmiddle'>Description</th>
                                            <th class='tdnowrap b px-3 vmiddle'>Quantity</th>
                                            <th class='tdnowrap b px-3 vtop'>UOM</th>
                                            <th class='tdnowrap b px-3 vtop'>Unit Price<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                            <!--<th class='tdnowrap b px-3 vmiddle'>Amount (RM)</th>-->
                                            <th class='tdnowrap b px-3 colSST vtop'>Unit Tax<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                            <th class='tdnowrap b px-3 colNoSST vtop'>Total Amount<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                                            <th class='tdnowrap b px-3 colSST vtop'>Total Amount<br/> w Tax (<?= $model['currency']['currency_sign'] ?>)</th>
                                        </tr>
                                    </thead>
                                    <tbody class='b'>
                                        <?php
                                        foreach ($panels as $key => $panel) {
                                            $_unitAmt = $panel->amount ?? 0;
                                            $_totAmt = ($_unitAmt * $panel->quantity);
                                            $_sstAmt = ($_unitAmt * $sst / 100);
                                            $_totAmtWSst = ($_sstAmt * $panel->quantity + $_totAmt);
                                            $totalAmount += $_totAmt;
                                            ?>
                                            <tr>
                                                <td class='px-3 bl'><?= $key + 1 ?></td>
                                                <td class='br bl px-3'>
                                                    <?= $panel->panel_description ?>
                                                </td>
                                                <td class='text-right tdnowrap px-3 br bl'>
                                                    <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                        <?= MyFormatter::asDecimal2($panel->quantity) ?>
                                                    </span>
                                                </td>
                                                <td class='text-right tdnowrap px-3 br bl'>
                                                    <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                        <?= $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") ?>
                                                    </span>
                                                </td>
                                                <td class='text-right br bl px-3'>      
                                                    <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                        <?= MyFormatter::asDecimal2($_unitAmt) ?>
                                                    </span>
                                                </td>
                                                <td class='text-right br bl px-3 colSST'>
                                                    <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                        <?= MyFormatter::asDecimal2($_sstAmt) ?>
                                                    </span>
                                                </td>
                                                <td class='text-right br bl px-3 colNoSST'>
                                                    <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                        <?= MyFormatter::asDecimal2($_totAmt) ?>
                                                    </span>
                                                </td>
                                                <td class='text-right br bl px-3 colSST'>
                                                    <span class="<?= $panel->by_item_price ? "no-bdItemPrice" : "" ?>">
                                                        <?= MyFormatter::asDecimal2($_totAmtWSst) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php
                                            $items = $panel->projectQPanelItems;
                                            array_multisort(array_column($items, 'sort'), SORT_ASC, $items);
                                            $ii = 'a';
                                            foreach ($items as $key2 => $item) {
                                                ?>
                                                <tr class="breakdownItem">
                                                    <td class="px-3 bl"><span class="pl-3">(<?= ($ii++) ?>)</span></td>
                                                    <td class="br bl px-4"><?= nl2br(Html::encode($item['item_description'])) ?></td>
                                                    <?php
                                                    if ($panel->by_item_price) {
                                                        ?>
                                                        <td class='text-right tdnowrap px-3 br bl'>
                                                            <span class="bdItemPrice"><?= number_format($tempItemQty = $item['quantity'] * $panel->quantity, 2) ?></span>
                                                        </td>
                                                        <td class='text-right tdnowrap px-3 br bl'>
                                                            <span class="bdItemPrice"><?= ($item['unitCode']['unit_name']) . ($tempItemQty > 1 ? "S" : "") ?></span>
                                                        </td>
                                                        <td class='br bl text-right px-3'>
                                                            <span class="bdItemPrice"><?= number_format($item['amount'], 2) ?></span>
                                                        </td>
                                                        <td class='br bl text-right px-3  colSST'>
                                                            <span class="bdItemPrice">
                                                                <?= number_format(($itemSstAmt = ($item['amount'] * $sst / 100)), 2) ?>
                                                            </span>
                                                        </td>
                                                        <td class='br bl px-3 text-right colNoSST'>   
                                                            <span class="bdItemPrice">
                                                                <?= number_format(($item['amount'] * $tempItemQty), 2) ?>
                                                            </span> 
                                                        </td>
                                                        <td class='br bl px-3 text-right colSST'>    
                                                            <span class="bdItemPrice">
                                                                <?= number_format((($itemSstAmt + $item['amount']) * $tempItemQty), 2) ?>
                                                            </span>
                                                        </td>

                                                        <?php
                                                    } else {
                                                        ?>
                                                        <td class='text-right tdnowrap px-3 br bl'></td>
                                                        <td class='br bl text-right px-3'></td>
                                                        <td class='br bl text-right colSST'></td>
                                                        <td class='br bl colNoSST'></td>
                                                        <td class='br bl colSST'></td>
                                                    <?php } ?>
                                                </tr>
                                                <?php
                                            }
                                            if (!empty($panel->remark)) {
                                                ?>
                                                <tr class="panelDescription">
                                                    <td class="bl"></td>
                                                    <td class="br bl px-4"><?= nl2br(Html::encode($panel->remark)) ?></td>
                                                    <td class='br bl'></td>
                                                    <td class='br bl'></td>
                                                    <td class='br bl'></td>
                                                    <td class='br bl colSST'></td>
                                                    <td class='br bl colNoSST'></td>
                                                    <td class='br bl colSST'></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class='colSST b' style='font-style: italic;text-align: right ;font-weight: bold'>
                                            <td colspan='6' style='text-align: right;padding-right: 1em'>
                                                <?php
                                                $discountTotal = 0;
                                                $discountDisplay = "";

                                                if ($model['discount_amt'] > 0) {
                                                    if ($model['discount_type'] == 0) {
                                                        $discountDisplay = "Discount (" . $model['currency']['currency_sign'] . ") :<br/>";
                                                        $discountTotal = $model['discount_amt'] * -1;
                                                    } else {
                                                        $discountDisplay = "Discount " . $model['discount_amt'] . "% (" . $model['currency']['currency_sign'] . ") :<br/>";
                                                        $discountTotal = $totalAmount * $model['discount_amt'] / 100 * -1;
                                                    }
                                                }

                                                $totalSST = ($totalAmount + $discountTotal) * $sst / 100;
                                                $totalAmtWithSST = ($totalAmount + $discountTotal) + $totalSST;
                                                $totalAmtWithoutSST = ($totalAmount + $discountTotal);
                                                ?>

                                                Sub-Total (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                                <?= $model['discount_amt'] > 0 ? $discountDisplay : "" ?>
                                                Tax (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                                Total (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                            </td>
                                            <td style='padding-right: 1em'>
                                                <?= MyFormatter::asDecimal2($totalAmount) ?><br/>
                                                <?php
                                                if ($model['discount_amt'] > 0) {
                                                    echo MyFormatter::asDecimal2($discountTotal) . "<br/>";
                                                }
                                                ?>
                                                <?= MyFormatter::asDecimal2($totalSST) ?><br/>
                                                <?= MyFormatter::asDecimal2($totalAmtWithSST) ?><br/>

                                            </td>
                                        </tr>
                                        <tr class='colNoSST b' style='font-style: italic;text-align: right ;font-weight: bold'>
                                            <td colspan='5' style='text-align: right;padding-right: 1em'>
                                                <?= $model['discount_amt'] > 0 ? "Sub-Total" : "Total" ?> (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                                <?php
                                                if ($model['discount_amt'] > 0) {
                                                    echo $discountDisplay;
                                                    echo "Total (" . $model['currency']['currency_sign'] . ") : .<br/>";
                                                }
                                                ?>

                                            </td>
                                            <td style='padding-right: 1em'>
                                                <?= MyFormatter::asDecimal2($totalAmount) ?><br/>
                                                <?php
                                                if ($model['discount_amt'] > 0) {
                                                    echo MyFormatter::asDecimal2($discountTotal) . "<br/>";
                                                    echo MyFormatter::asDecimal2($totalAmtWithoutSST) . "<br/>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
                <table class='table table-sm table-borderless'>
                    <tr style='font-weight: bold; text-decoration: underline'>
                        <td style='width:50%'>Material Offered</td>
                        <td style='width:50%'>Switchboard Standard</td>
                    </tr>
                    <tr>
                        <td>
                            <?= $form->field($model, 'q_material_offered')->textarea(['rows' => 6]) ?>
                        </td>
                        <td>
                            <?= $form->field($model, 'q_switchboard_standard')->textarea(['rows' => 6]) ?>
                        </td>
                    </tr>
                </table>
                <table class='table table-sm table-borderless'>
                    <tr>
                        <td class='tdnowrap'>QUOTATION</td><td class='tdnowrap px-3 text-center'>:</td>
                        <td colspan="5">
                            <?php
                            $model->q_quotation .= " " . $model['currency']['currency_sign'] . " " . ($model->with_sst ? MyFormatter::asDecimal2($totalAmtWithSST) : MyFormatter::asDecimal2($totalAmtWithoutSST));
                            ?>
                            <?= $form->field($model, 'q_quotation', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>DELIVERY</td><td class='tdnowrap px-3 text-center'>:</td>
                        <td style="width:15%">
                            <?php
                            echo $form->field($model, "q_delivery_ship_mode")->widget(yii\jui\AutoComplete::className(), [
                                'clientOptions' => [
                                    'source' => RefProjectQShippingMode::getAutocompleteList(),
                                    'minLength' => '0',
                                    'autoFill' => true,
                                    'delay' => 10,
                                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.id : ''));
			     }"),
                                ],
                                'options' => ['class' => 'form-control', 'placeholder' => 'Shipping Mode']
                            ]);
                            ?>
                        </td>
                        <td style="width: 1%">-</td>
                        <td style="width:20%">
                            <?= $form->field($model, 'q_delivery_destination')->textInput(['maxlength' => true, 'placeholder' => 'Destination'])->label("&nbsp;") ?>
                        </td>
                        <td style="width: 1%">-</td>
                        <td>
                            <?= $form->field($model, 'q_delivery', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>VALIDITY</td><td class='tdnowrap px-3 text-center'>:</td>
                        <td colspan="5">  
                            <?= $form->field($model, 'q_validity', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>PAYMENT</td><td class='tdnowrap px-3 text-center'>:</td>
                        <td colspan="5">   
                            <?= $form->field($model, 'q_payment', ['options' => ['class' => 'p-0 m-0']])->textInput(['maxlength' => true]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style='font-weight: bold'>REMARK</td><td class='tdnowrap px-3 text-center font-weight-bold'>:</td>
                        <td colspan="5"> 
                            <?= $form->field($model, 'q_remark', ['options' => ['class' => 'p-0 m-0']])->textarea(['rows' => 6]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='7'>
                            <p>We trust that our offer would meet your requirement and looking forward to receiving your favourable reply soon.</p>
                        </td>
                    </tr>

                </table>
            </div>
        </div>


        <div class="form-group">
            <?= Html::submitButton('Generate PDF', ['class' => 'btn btn-success submitButton']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
<script>
    $(function () {
        $("#quotationpdfmasters-q_delivery_ship_mode").focus(function () {
            $(this).autocomplete("search", "");
        });
        $("#quotationpdfmasters-with_sst").click(function (e) {
            checkSST();
        });
        $("#quotationpdfmasters-show_breakdown").click(function (e) {
            checkBreakDetail();
        });
        $("#quotationpdfmasters-show_breakdown_price").click(function (e) {
            checkBreakDetailPrice();
        });
        $("#quotationpdfmasters-show_panel_description").click(function (e) {
            checkPanelDesc();
        });

        checkSST();
        checkBreakDetail();
        checkPanelDesc();

    });


    function checkSST() {
        if ($('#quotationpdfmasters-with_sst').is(":checked")) {
            $(".colSST").show();
            $(".colNoSST").hide();

        } else {
            $(".colSST").hide();
            $(".colNoSST").show();
        }
    }

    function checkBreakDetail() {
        if ($('#quotationpdfmasters-show_breakdown').is(":checked")) {
            $(".breakdownItem").show();
        } else {
            if ($('#quotationpdfmasters-show_breakdown_price').is(":checked")) {
                $('#quotationpdfmasters-show_breakdown_price').prop('checked', false);
            }
            $(".breakdownItem").hide();
        }
    }
    function checkBreakDetailPrice() {
        if ($('#quotationpdfmasters-show_breakdown_price').is(":checked")) {
            if (!$('#quotationpdfmasters-show_breakdown').is(":checked")) {
                $('#quotationpdfmasters-show_breakdown').prop('checked', true);
                $(".breakdownItem").show();
            }
            $(".bdItemPrice").show();
            $(".no-bdItemPrice").hide();
        } else {
            $(".bdItemPrice").hide();
            $(".no-bdItemPrice").show();
        }
    }

    function checkPanelDesc() {
        if ($('#quotationpdfmasters-show_panel_description').is(":checked")) {
            $(".panelDescription").show();
        } else {
            $(".panelDescription").hide();
        }
    }

</script>