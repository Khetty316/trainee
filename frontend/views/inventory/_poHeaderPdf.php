<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;

$supplier = $po->supplier;
$companyGroup = frontend\models\common\RefCompanyGroupList::findOne($po->company_group);
?>

<!-- Header Section -->
<table width="100%" style="text-align:center; margin-bottom:15px;">
    <tr>
        <td>
            <span style="font-size:16pt; font-weight:bold;">
                <?= strtoupper(Html::encode($companyGroup->company_name)) ?>
            </span><br>
            <?=
            Html::encode(
                    $companyGroup->company_addr_1 .
                    $companyGroup->company_addr_2
            )
            ?><br>
            <?=
            Html::encode(
                    $companyGroup->company_addr_3 .
                    $companyGroup->company_addr_4
            )
            ?><br>

            TEL NO: <?= Html::encode(preg_replace('/<br\s*\/?>/i', ', ', $companyGroup->tel)) ?><br>
            EMAIL: <?= Html::encode($companyGroup->email) ?><br>
            (Company Reg No: <?= Html::encode($companyGroup->company_id) ?>)
            (TIN No: <?= Html::encode($companyGroup->tin_no) ?>))
        </td>
    </tr>
</table>

<!-- Supplier and PO Details -->
<table width="100%">
    <tr>
        <!-- SUPPLIER -->
        <td width="90%" valign="top" style="font-size:18pt;">
            <strong><?= strtoupper(Html::encode($supplier->name)) ?></strong><br>
            <?= Html::encode($supplier->address1) ?><br>
            <?= Html::encode($supplier->address2) ?><br>
            <?= Html::encode($supplier->address3) ?><br>
            ATTN : <?= Html::encode($supplier->contact_name) ?><br>
            TEL  : <?= Html::encode($supplier->contact_number) ?><br>
            FAX  : <?= Html::encode($supplier->contact_fax) ?><br>
            A/C NO : <?= Html::encode($supplier->code) ?>
        </td>
        <!-- PO INFO -->
        <td width="10%" valign="top">
            <table width="100%" style="padding-left: 200px; font-size:16pt;">
                <tr>
                    <td width="100%" align="left" colspan="3"><strong style="font-size:18pt;">PURCHASE ORDER</strong></td>
                </tr>
                <tr>
                    <td width="18%" align="left">NO</td>
                    <td width="2%" align="center" style="padding:0;">:</td>
                    <td width="80%" style="padding-left:4px;">
                        <?= Html::encode($po->po_no) ?>
                    </td>
                </tr>
                <tr>
                    <td align="left">DATE</td>
                    <td align="center" style="padding:0;">:</td>
                    <td style="padding-left:4px;">
                        <?= MyFormatter::asDate_Read($po->po_date) ?>
                    </td>
                </tr>
                <tr>
                    <td align="left">PAGE</td>
                    <td align="center" style="padding:0;">:</td>
                    <td style="padding-left:4px;">
                        {PAGENO}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>