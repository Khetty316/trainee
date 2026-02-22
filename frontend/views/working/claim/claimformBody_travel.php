<?php

use common\models\myTools\MyFormatter;
?>
<style>
    table{
        font-size: 11pt;
        font-family: calibri;
    }

    td, th{
        border: 1px black solid ;
    }

    .detailTable th{
        text-align:center;
        background-color:#99ecff;
        font-weight:normal;
        padding: 5px;
        border:1px solid black
    }

    .detailTable  td{
        padding: 5px;
        border:1px solid black;
    }

    th,td{
        padding-left: 5px;
        padding-right:5px;
    }
</style>

<div>
    <table class='detailTable' width="100%"  style='font-size: 8pt;font-family: calibri;border-collapse: collapse; '>
        <thead>
            <tr>
                <th>Date</th>
                <th>Detail</th>
                <th>Project Code</th>
                <th>Total Days</th>
                <th>Amount/Day(RM)</th>
                <th>Total Amount(RM)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($model->claimsDetails as $key => $claimsDetail) {
                $i++;
                $days = (\common\models\myTools\MyCommonFunction::countDays($claimsDetail->date1, $claimsDetail->date2) + 1);
                ?>
                <tr <?= $i % 2 == 0 ? "style='background-color:#ebfbff'" : "" ?>>
                    <td style="text-align: center"><?= $i." ".MyFormatter::asDate_Read($claimsDetail->date1) . " - " . MyFormatter::asDate_Read($claimsDetail->date2) ?></td>
                    <td><?= $claimsDetail->detail ?></td>
                    <td><?= $claimsDetail->project_account ?></td>
                    <td style="text-align: center"><?= $days ?></td>
                    <td style="text-align: center"><?= MyFormatter::asDecimal2($claimsDetail->amount / $days) ?></td>
                    <td style="text-align: right"><?= MyFormatter::asDecimal2($claimsDetail->amount) ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr style='background-color:#7dd6f0'>
                <td colspan="5" style="text-align:right;padding: 5px;border:1px solid black">
                    <b>Total Of Claim</b>
                </td>
                <td style="text-align: right;padding: 5px;border:1px solid black"><b><?= MyFormatter::asDecimal2($model->total_amount) ?></b></td>
            </tr>
        </tfoot>
    </table>
</div>
<div style="height:60mm">
</div>






