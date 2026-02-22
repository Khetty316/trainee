<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefGeneralReferences;

$projectQMaster = $revision->projectQType->project;
$sst = RefGeneralReferences::getValue("sst_value")->value;
$company = $revision->projectQType->project->companyGroupCode;
$conSst = $company->sst_value;
$sstAmount = 0;
$totalAmount = 0;
$totalSST = 0;
$totalAmtWithSST = 0;
$totalAmtWithoutSST = 0;
$model->with_sst = false;
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
                <td colspan="3"></td>
                <td colspan="3" style='font-size: 15pt;font-weight: bold'>Quotation</td>
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
                    <?php
                    $newCode = 'TKM';
                    ?>
                    <?= str_replace('TKTKM', $newCode, $model->quotation_no) ?>
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
                    <?= $model->proj_title ?> <p></p>Strictly the supply of components only
                </td>
            </tr>
            <tr>
                <td colspan='6' class="p5px italic">
                    <br/>
                    Further to your kind invitation, we are pleased to append herewith our quotation as follows :
                </td>
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

                        <?php if ($model->with_sst) { ?>
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
                        $_unitAmt = ($panel->amount / 2);
                        $_totAmt = ($_unitAmt * $panel->quantity);
                        $_sstAmt = ($_unitAmt * $sst / 100);
                        $_totAmtWSst = ($_sstAmt * $panel->quantity + $_totAmt);
                        $totalAmount += $_totAmt;
//                        $totalSST += $_sstAmt * $panel->quantity;
//                        $totalAmtWithSST += $_totAmtWSst
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
                                <?php if ($model->with_sst) { ?>
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
                                <?php if ($model->with_sst) { ?>
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
                                        <td class='p5px br bl vtop text-right'>
                                            <?= number_format($item['amount'] / 2, 2) ?>
                                        </td>

                                        <?php if ($model->with_sst): ?>
                                            <td class='text-right tdnowrap p5px br bl vtop'>       
                                                <?= number_format(($itemSstAmt = (($item['amount'] / 2) * $sst / 100)), 2) ?>
                                            </td>
                                            <td class='text-right tdnowrap p5px br bl vtop'>
                                                <?= number_format((($itemSstAmt + ($item['amount'] / 2)) * $tempItemQty), 2) ?>
                                            </td>
                                        <?php else: ?>
                                            <td class='text-right tdnowrap p5px br bl vtop'>
                                                <?= number_format((($item['amount'] / 2) * $tempItemQty), 2) ?>
                                            </td>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <td class='br bl'></td>
                                        <td class='br bl'></td>
                                        <td class='br bl'></td>
                                        <?php if ($model->with_sst): ?>
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
                                    <?php if ($model->with_sst) { ?>
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
//                                $discountTotal = $totalAmount * $model['discount_amt'] / 100 * -1;
                                $discountTotal = $model['discount_amt_from_con'];
                            }
                        }

                        $totalSST = ($totalAmount + $discountTotal) * $sst / 100;
                        $totalAmtWithSST = ($totalAmount + $discountTotal) + $totalSST;
                        $totalAmtWithoutSST = ($totalAmount + $discountTotal);

                        if ($model->with_sst) {
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
//                    if ($model->q_switchboard_standard) {
                    ?>
                <!--<td style='width:50%' class='bold under'>Switchboard Standard</td>-->
                    <?php // } ?>
                </tr>

                <tr style='padding:0px;margin: 0px'>
                    <?php if ($model->q_material_offered) { ?>
                        <td style='vertical-align: top'>
                            <?= nl2br(Html::encode($model->q_material_offered)) ?>
                        </td>
                        <?php
                    }
//                    if ($model->q_switchboard_standard) {
                    ?>
                <!--<td style='vertical-align: top'>-->
                    <?php //= nl2br(Html::encode($model->q_switchboard_standard)) ?>
                    <!--</td>-->
                    <?php // } ?>
                </tr>
            </table>
        <?php } ?>
        <br/>
        <table class='table table-sm table-borderless f10 vtop'>
            <tr>
                <td class='tdnowrap' style="padding-right:20px">QUOTATION</td>
                <td class='tdnowrap' style="padding-right:10px">:</td>
                <td><?= $model['currency']['currency_sign'] . ' ' . MyFormatter::asDecimal2($totalAmtWithoutSST) ?></td> 


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
        <span style="font-family: times;font-size: 14px" class="bold">Please refer to the consolidated quotation summarized for easy reference.</span>
        <br/><br/>
        <table class="table table-borderless vtop f9" style="font-family: times">
            <tr>
                <td  style="font-family: times;padding-bottom: 7px" colspan="2">
                    OFFER TERMS AND CONDITIONS
                </td>
            </tr>
            <tr>
                <td class="px10px">1.</td>
                <td>THE TOTAL PRICES QUOTED ARE BASED ON QUANTITIES OF SWITCHBOARDS AS STATED IN OUR OFFER.</td>
            </tr>
        </table>
        <table class="table table-borderless vtop f9" style="font-family: times">

            <tr>
                <td class="px10px">2.</td>
                <td>ALL THE TAXED RELATED CHARGES SHALL BORNE BY THE CLIENTS.</td>
            </tr>
            <tr>
                <td class="px10px">3.</td>
                <td>IN THE CASE OF THE QUOTATION PREPARED IN FOREIGN CURRENCIES, THE PRICES OFFERED SHALL ONLY BE VALID IF THE FOREX FALLS WITHIN 2% OF THAT OF THE DATE OF QUOTATION.</td>
            </tr>
            <tr>
                <td class="px10px">4.</td>
                <td>FOR LOCAL & OUTSTATION PROJECTS, SITE TESTING FEES ARE NOT INCLUDED IN OUR OFFER.
                    SUFFICIENT TEMPORARY POWER SUPPLY FOR TESTING EQUIPMENT SHALL BE PROVIDED BY YOUR COMPANY.</td>
            </tr>
            <tr>
                <td class="px10px">5.</td>
                <td>THE TOTAL PRICE QUOTED ARE EXCLUSIVE FOR ANY CHARGES OR COST ASSOCIATED WITH EXPORT OR IMPORT AND/OR ANY OTHER APPROPRIATE AUTHORITY ALONG WITH ANY DUTIES TAXES.</td>
            </tr>
            <tr>
                <td class="px10px">6.</td>
                <td>COMPONENT OFFERED ARE AS PER OUR QUOTATION. ANY CHANGES/ADDITIONS OF COMPONENT AS COMPARED TO OUR OFFER, PRICES WILL BE ADJUSTED ACCORDINGLY.</td>
            </tr>
            <tr>
                <td class="px10px">7.</td>
                <td>AFTER ACCEPTANCE OF YOUR ORIGINAL ORDER OR VERBAL CONFIRMATION. SHOULD THERE BE ANY CANCELLATION OF AN ORDER, CANCELLATION FEES WILL BE CHARGED AS FOLLOWS:
                    <br/>
                    <br/>
                    <table class="table">
                        <tr>
                            <td class="px10px">A)</td>
                            <td>10% OF TOTAL ORDER PRIOR TO SUBMISSION OF SHOP DRAWING.</td>
                        </tr>
                        <tr>
                            <td class="px10px">B)</td>
                            <td>10% OF TOTAL ORDER IF WITHDRAWN 1 WEEK OR LESS FROM DATE OF ISSUE.</td>
                        </tr>
                        <tr>
                            <td class="px10px">C)</td>
                            <td>20% OF TOTAL ORDER AFTER SUBMISSION OF SHOP DRAWING.</td>
                        </tr>
                        <tr>
                            <td class="px10px">D)</td>
                            <td>30% OF TOTAL ORDER IF WITHDRAWN 1 MONTH OR LESS FROM DATE OF ISSUE.</td>
                        </tr>
                        <tr>
                            <td class="px10px">E)</td>
                            <td>50% OF TOTAL ORDER IF WITHDRAWN AFTER 1 MONTH FROM DATE OF ISSUE.</td>
                        </tr>
                        <tr>
                            <td class="px10px">F)</td>
                            <td>NO CANCELLATION IS ALLOWED AFTER YOUR OFFICIAL OR VERBAL INSTRUCTION TO COMMENCE MANUFACTURING REGARDLESS OF WHETHER OUR SHOPDRAWINGS ARE OFFICIALLY APPROVED OR NOT.</td>
                        </tr>
                    </table>
                    <br/>
                </td>
            </tr>
            <tr>
                <td class="px10px">8.</td>
                <td>PLEASE BE ADVISED THAT MODE OF PAYMENT IS BASED ON WHAT HAD BEEN AGREED UPON CONFIRMATION OF ORDERS. ANY CHANGES IN THE MODE OF PAYMENT MIGHT VARY INCREASE THE TOTAL CONTRACT VALUES. DELAYED PAYMENT WILL BE CHARGED AN INTEREST RATE OF 1.5% PER MONTH OF THE TOTAL CONTRACT VALUE PLUS ANY SERVICES/INCONVENIENCE CHARGES.</td>
            </tr>
            <tr>
                <td class="px10px">9.</td>
                <td>WARRANTY:&ensp;ALL OUR EQUIPMENT SUPPLIED ARE GUARANTEED AGAINST POOR WORKMANSHIP AND/OR DEFECTIVE COMPONENTS FOR A PERIOD OF 12 MONTH FROM THE DATE OF COMMISSIONING OR 18 MONTH FROM DATE OF DELIVERY WHICHEVER IS EARLIER. THIS WARRANTY DOES NOT APPLY TO THE FOLLOWINGS:<br>
                    <table class="table">
                        <tr>
                            <td class="px10px">(i)</td>
                            <td>DEFECTS ARISING EITHER FROM MATERIALS SUPPLIER AND/OR ITS RELATED DESIGN REQUESTED BY YOU.</td>
                        </tr>
                        <tr>
                            <td class="px10px">(ii)</td>
                            <td>REPLACEMENT AND/OR REPAIRS RESULTING FROM NORMAL WEAR & TEAR OR DAMAGE CAUSED BY IMPROPER HANDLING/OPERATION OR INSUFFICIENT MAINTENANCE AS RECOMMENDED.</td>
                        </tr>
                        <tr>
                            <td class="px10px">(iii)</td>
                            <td>MAINTENANCE SERVICING IS EXCLUDED WHILST THE SWITCHBOARD (S) IS/ARE WITHIN THE WARRANTY PERIOD.</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="f9 pt5px">
                    We trust the above is in order and look forward to your much valued order. Thank you.
                </td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <table class='table table-sm table-borderless' style="page-break-inside: avoid;font-family: Arial">
            <tr>
                <td style='width: 30%'>Approved By :</td>
                <td style='width: 5%'></td>
                <td style='width: 30%'>Prepared By :</td>
                <td style='width: 5%'></td>
                <td style='width: 30%'>Accepted by Customer :</td>
            </tr>
            <tr>
                <?php
                $directorSignDate = ($model->md_approval_date === null) ? '-' : date("d/m/Y", strtotime($model->md_approval_date));
                ?>
                <td class='bb text-center'>
                    <?php if ($model->md_approval_status == \frontend\models\projectquotation\QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED) { ?>
                        <br><div class='digital'>Digitally signed by <br> <?= $model->mdUser->fullname ?> </div>
                        <div class='digital'>Contact No.: <?= $model->mdUser->contact_no ?> </div>
                        <div class='digital'>Date: <?= $directorSignDate ?> </div>
                    <?php } ?>
                </td><br>
            <td></td>
            <td class='bb text-center'>
                <?php
                $prepared = ($model->created_by === null ? Yii::$app->user->identity : $model->createdBy);
                ?>
                <br><div class='digital'>Digitally signed by <br> <?= $prepared->fullname ?></div>
                <div class='digital'>Contact No.: <?= $prepared->contact_no ?></div>
                <div class='digital'>Date: <?= $model->q_date ?></div>
            </td><br>
            <td></td>
            <td class='bb'></td>
            </tr>
            <tr>
                <td colspan='2'>
                    <span class='italic'>LAU KIEW YUNG</span><br/>
                    Managing Director
                </td>
                <td colspan='2'>
                    <?php
                    $pName = ($model->created_by === null ? Yii::$app->user->identity : $model->createdBy);
                    ?>
                    <span class='italic'><?= strtoupper($prepared->fullname) ?></span><br/>
                    Project Coordinator
                </td>
                <td>
                    <span class='italic'>AUTHORIZED CHOP & SIGN</span><br/>
                    Date :
                </td>
            </tr>
        </table>
    </div>

</div>


