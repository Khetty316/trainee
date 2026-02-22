<?php
use yii\helpers\Html;
use common\models\myTools\MyFormatter;

$currency = $po->currency;
$currencySign = $currency ? trim($currency->currency_sign) : 'RM';
?>

<!-- Amount in Words -->
<table width="100%" style="margin-bottom:0;">
    <tr>
        <td style="border-bottom:1px solid #000; padding:8px;">
            <?= strtoupper($currency->currency_name ?? 'RINGGIT MALAYSIA') ?> :
            <?= strtoupper($po->amountWords) ?>
        </td>
    </tr>
</table>

<!-- Summary Section -->
<table width="100%" cellspacing="0" cellpadding="0" style="margin-top:10px;">
    <tr>
        <!-- LEFT -->
        <td width="60%" valign="top">
            <table width="100%">
                <tr>
                    <td style="text-align:right; padding:2px;">
                        TOTAL QUANTITY
                    </td>
                    <td style="text-align:right; padding:2px;">
                        <?= number_format($po->total_qty, 0) ?>
                    </td>
                </tr>
            </table>
        </td>

        <td width="1%"></td>

        <!-- RIGHT -->
        <td width="40%" valign="top">
            <table width="100%" style="border:1px solid #000; border-collapse: collapse;" cellpadding="2" cellspacing="0">
                <tr>
                    <td>TOTAL</td>
                    <td align="right"><?= number_format($po->total_amount, 2) ?></td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000;">DISCOUNT</td>
                    <td align="right" style="border-bottom: 1px solid #000;">
                        <?= number_format($po->total_discount, 2) ?>
                    </td>
                </tr>
                <tr>
                    <td>NET</td>
                    <td align="right"><?= number_format($po->net_amount, 2) ?></td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000;">TAX</td>
                    <td align="right" style="border-bottom: 1px solid #000;"><?= number_format($po->tax_amount, 2) ?></td>
                </tr>
                <tr>
                    <td>GROSS</td>
                    <td align="right">
                        <?= Html::encode($currencySign) ?>
                        <?= number_format($po->gross_amount, 2) ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Signature -->
<table width="100%" style="margin-top:20px;">
    <tr>
        <td width="30%" style="border-top:1px solid #000; text-align:center; padding-top:5px;">
            AUTHORISED SIGNATURE
        </td>
        <td width="3%"></td>
        <td width="25%" style="border-top:1px solid #000; text-align:center; padding-top:5px;">
            RECEIVED BY
        </td>
        <td width="42%"></td>
    </tr>
</table>