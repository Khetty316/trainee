<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefGeneralReferences;

$projectQMaster = $revision->projectQType->project;
$company = $revision->projectQType->project->companyGroupCode;
$sst = $company->sst_value;
$sstAmount = 0;
$totalAmount = 0;
$totalSST = 0;
$totalAmtWithSST = 0;
$totalAmtWithoutSST = 0;
$with_sst = false;
?>
<style>
    @import url("/css/site.css");
    formHeader td{
        padding: 0px;
        margin: 0px;
        text-align: center;
    }

    .table{
        width: 100%;
        border-collapse: collapse;
    }

    .text-right{
        text-align: right;
    }

    .text-center{
        text-align:center;
    }

    .bold{
        font-weight: bold;
    }

    .italic{
        font-style: italic;
    }

    .under{
        text-decoration: underline;
    }

    .p-1{
        padding: 1px;
    }
    .p-2{
        padding: 2px;
    }
    .px-2{
        padding-left: 2px;
        padding-right: 2px;
    }
    .vtop{
        vertical-align: top;
    }
    .vbtm{
        vertical-align: bottom;
    }
    .f2{
        font-size: 2pt;
    }
    .f8{
        font-size:8pt;
    }
    .f9{
        font-size:9pt;
    }
    .f10{
        font-size:10pt
    }
    .f11{
        font-size:11pt
    }
    .pt2px{
        padding-top: 2px;
    }
    .pt5px{
        padding-top: 5px;
    }
    .px10px{
        padding-left: 10px;
        padding-right: 10px;
    }

    .p5px{
        padding:5px;
    }
    .bl{
        border-left: 1px solid black!important;
    }
    .br{
        border-right: 1px solid black!important;
    }
    .bt{
        border-top: 1px solid black!important;
    }
    .btt{
        border-top:2px solid black!important;
    }
    .brt{
        border-right:2px solid black!important;
    }
    .blt{
        border-left:2px solid black!important;
    }
    .bb{
        border-bottom: 1px solid black!important;
    }
    .b{
        border:1px solid black!important;
    }
    .digital {
        font-size: 10pt;
    }
</style>
<div  style="font-family: Arial;">
    <div>
        <table class='table' id='formHeader' style="font-size: 10pt;vertical-align: top">
            <tr>
                <td colspan="2"></td>
                <td colspan="6" style='font-size: 15pt;font-weight: bold;' class="text-right">Consolidated Quotation Summary</td>
            </tr>
            <tr>
                <td style='width:10%;white-space: nowrap;padding: 0px'>To</td>
                <td style='width:3%'>:</td>
                <td style='width:40%'>
                    <?= Html::encode($model->to_company) ?>
                </td>
                <td style='width:10%;white-space: nowrap;padding: 0px'>From</td>
                <td style='width:3%'>:</td>
                <td style='width:34%'>
                    <?= $model->q_from ?>
                </td>

            </tr>
            <tr>
                <td>Attn</td><td>:</td>
                <td>
                    <?= $model->to_pic ?>
                </td>
                <td>Our Ref</td><td>:</td>
                <td>
                    <?= $model->quotation_no ?>
                </td>
            </tr>
            <tr>
                <td>Tel. No</td>
                <td>:</td>
                <td>
                    <?= $model->to_tel_no ?>
                </td>
                <td>Your Ref</td><td>:</td>
                <td>
                    <?= $model->q_your_ref ?>
                </td>
            </tr>
            <tr>
                <td>Fax No</td><td>:</td>
                <td>
                    <?= $model->to_fax_no ?>
                </td>
                <td>Date</td><td>:</td>
                <td>
                    <?= $model->q_date ?>
                </td>
            </tr>
            <tr>
                <td class="bold f11 pt5px">RE</td>
                <td class="bold f11 pt5px">:</td>
                <td colspan='4' class="bold f11 under pt5px">
                    <?= $model->proj_title ?>
                </td>
            </tr>
            <tr>
                <td colspan='6' class="p5px italic">
                    <br/>
                    Further to your kind invitation, we are pleased to append herewith our quotation as follows :
                </td>
            </tr>
        </table>
        <?php
        $panels = $revision->projectQPanels;
        array_multisort(array_column($panels, 'sort'), SORT_ASC, $panels);
        if ($panels) {
            ?>
            <table class='table f9' style="page-break-inside: auto !important; ">
                <thead style="page-break-inside: avoid !important">
                    <tr class='text-center'>
                        <th class='b italic tdnowrap'>Item</th>
                        <th class='b p5px italic'>Description</th>
                        <th class='tdnowrap b p5px italic'>Quantity</th>
                        <th class='tdnowrap b px-3 vmiddle'>UOM</th>
                        <th class='tdnowrap b px-3 vmiddle'>Unit Prices<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                           <!--<th class='tdnowrap b px-3 vmiddle'>Amount (RM)</th>-->

                        <?php if ($model->with_sst && $with_sst) { ?>
                            <th class='tdnowrap b p5px vmiddle'>Unit Tax<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                            <th class='tdnowrap b p5px vmiddle'>Total Amount<br/> w Tax (<?= $model['currency']['currency_sign'] ?>)</th>
                        <?php } else {
                            ?>
                            <th class='tdnowrap b p5px vmiddle'>Total Amount<br/>(<?= $model['currency']['currency_sign'] ?>)</th>
                            <?php
                        }
                        ?>
                    </tr>
                </thead>
                <tbody class='b' style="margin:40px;page-break-inside: avoid !important">
                    <?php
                    foreach ($panels as $key => $panel) {
                        $_unitAmt = ($model->with_sst ? ($panel->amount + ($panel->amount * $sst / 100)) : $panel->amount);
                        $_totAmt = ($_unitAmt * $panel->quantity);
                        $_sstAmt = ($_unitAmt * $sst / 100);
                        $_totAmtWSst = ($_sstAmt * $panel->quantity + $_totAmt);
                        $totalAmount += $_totAmt;
//                        $totalSST += $_sstAmt * $panel->quantity;
                        $totalAmtWithSST += $_totAmtWSst
                        ?>
                        <tr>
                            <td class='bl vtop text-right p5px'><?= $key + 1 ?>)&ensp;&ensp;</td>
                            <td class='br bl p5px vtop'>
                                <?= $panel->panel_description ?>
                            </td>
                            <?php
                            if ($model->show_breakdown_price && $panel->by_item_price) {
                                ?>
                                <td class='br bl'></td>
                                <td class='br bl'></td>
                                <td class='br bl'></td>
                                <td class='br bl'></td>
                                <?php if ($model->with_sst && $with_sst) { ?>
                                    <td class='br bl'></td>
                                    <?php
                                }
                            } else {
                                ?>
                                <td class='text-right tdnowrap p5px br bl vtop'>
                                    <?= MyFormatter::asDecimal2($panel->quantity) ?>
                                </td>
                                <td class='text-left tdnowrap p5px br bl vtop'>
                                    <?= $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") ?>
                                </td>
                                <td class='text-right tdnowrap p5px br bl vtop'>      
                                    <?= MyFormatter::asDecimal2($_unitAmt) ?>
                                </td>
                                <?php if ($model->with_sst && $with_sst) { ?>
                                    <td class='text-right tdnowrap p5px br bl vtop'>       
                                        <?= MyFormatter::asDecimal2($_sstAmt) ?>
                                    </td>
                                    <td class='text-right tdnowrap p5px br bl vtop'>
                                        <?= MyFormatter::asDecimal2($_totAmtWSst) ?>
                                    </td>
                                <?php } else {
                                    ?>
                                    <td class='text-right tdnowrap p5px br bl vtop'>
                                        <?= MyFormatter::asDecimal2($_totAmt) ?>
                                    </td>
                                    <?php
                                }
                            }
                            ?>
                        </tr>
                        <?php
                        if ($model->show_breakdown) {
                            $items = $panel->projectQPanelItems;
                            array_multisort(array_column($items, 'sort'), SORT_ASC, $items);
                            $ii = 'a';
                            foreach ($items as $key2 => $item) {
                                ?>
                                <tr class="breakdownItem">
                                    <td class='p5px bl vtop text-right'>(<?= ($ii++) ?>)</td>
                                    <td class="br bl p5px vtop">&ensp;<?= nl2br(Html::encode($item['item_description'])) ?></td>

                                    <?php if ($panel->by_item_price): ?>
                                        <td class='p5px br bl vtop text-right'>
                                            <?= number_format($tempItemQty = $item['quantity'] * $panel->quantity, 2) ?>
                                        </td>
                                        <td class='p5px br bl vtop'>
                                            <?= ($item['unitCode']['unit_name']) . ($tempItemQty > 1 ? "S" : "") ?>
                                        </td>

                                        <?php if ($model->with_sst): ?>
                                            <!-- Unit Price (with SST included) -->
                                            <td class='p5px br bl vtop text-right'>
                                                <?= number_format($item['amount'] + ($item['amount'] * $sst / 100), 2) ?>
                                            </td>
                                            <!-- Unit Tax -->
                                            <?= number_format(($itemSstAmt = ($item['amount'] * $sst / 100)), 2) ?>
                                            <!-- Total Amount with Tax -->
                                            <td class='text-right tdnowrap p5px br bl vtop'>
                                                <?= number_format((($itemSstAmt + $item['amount']) * $tempItemQty), 2) ?>
                                            </td>
                                        <?php else: ?>
                                            <!-- Unit Price (without SST) -->
                                            <td class='p5px br bl vtop text-right'>
                                                <?= number_format($item['amount'], 2) ?>
                                            </td>
                                            <!-- Total Amount -->
                                            <td class='text-right tdnowrap p5px br bl vtop'>
                                                <?= number_format(($item['amount'] * $tempItemQty), 2) ?>
                                            </td>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <td class='br bl'></td>
                                        <td class='br bl'></td>
                                        <td class='br bl'></td>
                                        <?php if ($model->with_sst && $with_sst): ?>
                                            <td class='br bl'></td>
                                        <?php endif; ?>
                                        <td class='br bl'></td>
                                    <?php endif; ?>
                                </tr>
                                <?php
                            }
                        }
                        if ($model->show_panel_description) {
                            if (!empty($panel->remark)) {
                                ?>
                                <tr class="panelDescription">
                                    <td class="bl"></td>
                                    <td class="br bl p5px vtop"><?= nl2br(Html::encode($panel->remark)) ?></td>
                                    <td class='br bl'></td>
                                    <td class='br bl'></td>
                                    <td class='br bl'></td>
                                    <td class='br bl'></td>
                                    <?php if ($model->with_sst && $with_sst) { ?>
                                        <td class='br bl'></td>
                                    <?php } ?>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>
                </tbody>
                <tfoot style="page-break-inside: avoid !important">
                    <tr style='font-weight: bold;font-style: italic;text-align: right' class='b'>
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
                                $model['discount_amt_from_con'] = $discountTotal;
                            }
                        }

                        $totalSST = ($totalAmount + $discountTotal) * $sst / 100;
                        $totalAmtWithSST = ($totalAmount + $discountTotal) + $totalSST;
                        $totalAmtWithoutSST = ($totalAmount + $discountTotal);

                        if ($model->with_sst && $with_sst) {
                            ?>
                            <td colspan="6" class='text-right bold italic' style='padding-right:10px'>
                                Sub-Total (<?= $model['currency']['currency_sign'] ?>) :  <br/>
                                <?= $model['discount_amt'] > 0 ? $discountDisplay : "" ?>
                                Tax (<?= $model['currency']['currency_sign'] ?>) : <br/>
                                Total (<?= $model['currency']['currency_sign'] ?>) : <br/>
                            </td>
                            <td class='bold italic p5px text-right br bl'>
                                <?= MyFormatter::asDecimal2($totalAmount) ?><br/>
                                <?= ($model['discount_amt'] > 0) ? MyFormatter::asDecimal2($discountTotal) . "<br/>" : "" ?>
                                <?= MyFormatter::asDecimal2($totalSST) ?><br/>
                                <?= MyFormatter::asDecimal2($totalAmtWithSST) ?>
                            </td>
                        <?php } else { ?>
                            <td colspan="5" class='text-right bold italic' style='padding-right:10px'>
                                <?php
                                if ($model['discount_amt'] > 0) {
                                    echo "Sub-Total (" . $model['currency']['currency_sign'] . ") : <br/>";
                                    echo $discountDisplay;
                                    echo "Total (" . $model['currency']['currency_sign'] . ") : <br/>";
                                } else {
                                    echo "Total (" . $model['currency']['currency_sign'] . ") : <br/>";
                                }
                                ?>
                            </td>
                            <td class='bold italic p5px text-right br bl'>
                                <?php
                                if ($model['discount_amt'] > 0) {
                                    echo MyFormatter::asDecimal2($totalAmount) . "<br/>";
                                    echo MyFormatter::asDecimal2($discountTotal) . "<br/>";
                                }
                                echo MyFormatter::asDecimal2($totalAmtWithoutSST);
                                ?>
                            </td>
                        <?php } ?>
                    </tr>
                </tfoot>
            </table>
        <?php } ?>
        <br/>
        <?php if ($model->q_material_offered || $model->q_switchboard_standard) { ?>
            <table class='table f10' style="page-break-inside: avoid">
                <tr>
                    <?php if ($model->q_material_offered) { ?>
                        <td style='width:50%' class='bold under'>Material Offered</td>
                        <?php
                    }
                    if ($model->q_switchboard_standard) {
                        ?>
                        <td style='width:50%' class='bold under'>Switchboard Standard</td>
                    <?php } ?>
                </tr>

                <tr style='padding:0px;margin: 0px'>
                    <?php if ($model->q_material_offered) { ?>
                        <td style='vertical-align: top'>
                            <?= nl2br(Html::encode($model->q_material_offered)) ?>
                        </td>
                        <?php
                    }
                    if ($model->q_switchboard_standard) {
                        ?>
                        <td style='vertical-align: top'>
                            <?= nl2br(Html::encode($model->q_switchboard_standard)) ?>
                        </td>
                    <?php } ?>
                </tr>
            </table>
        <?php } ?>
        <br/>
        <table class='table table-sm table-borderless f10 vtop'>
            <tr>
                <td class='tdnowrap' style="padding-right:20px">QUOTATION</td>
                <td class='tdnowrap' style="padding-right:10px">:</td>
                <?php
                if ($model->with_sst && $with_sst) {
                    $totalAmount = $totalAmtWithSST;
                } else {
                    $totalAmount = $totalAmtWithoutSST;
                }
                ?>
                <td><?= $model['currency']['currency_sign'] . ' ' . MyFormatter::asDecimal2($totalAmount) ?></td>
            </tr>
            <tr>
                <td>DELIVERY</td>
                <td>:</td>
                <td><?= (empty($model->q_delivery_ship_mode) ? "" : ($model->q_delivery_ship_mode . "-")) . (empty($model->q_delivery_destination) ? "" : $model->q_delivery_destination . "-") . $model->q_delivery ?></td>
            </tr>
            <tr>
                <td>VALIDITY</td>
                <td>:</td>
                <td><?= $model->q_validity ?></td>
            </tr>
            <tr>
                <td>PAYMENT</td>
                <td>:</td>
                <td><?= $model->q_payment ?></td>
            </tr>
            <tr>
                <td class="bold">REMARK</td>
                <td class="bold">:</td>
                <td class="bold"><?= nl2br(Html::encode($model->q_remark)) ?></td>
            </tr>
        </table>
        <br/>
        <span style="font-family: times;font-size: 14px" class="bold">This is the consolidated quotation summarized for easy reference. Purchase orders shall be based on or to TK and TKM according to their respective quotations.</span>
    </div>

</div>


