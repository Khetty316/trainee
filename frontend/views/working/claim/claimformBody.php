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
        vertical-align: top;
    }
</style>

<div>
    <table class='detailTable' width="100%"  style='font-size: 8pt;font-family: calibri;border-collapse: collapse; '>
        <thead>
            <tr>
                <th>Date</th>
                <th>Detail</th>
                <th>Company Name</th>
                <th>Receipt No.</th>
                <th>Project Code</th>
                <th style="width:110px">Total Amount(RM)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($model->claimsDetails as $key => $claimsDetail) {
                $i++;
                ?>

                <tr <?= $i % 2 == 0 ? "style='background-color:#ebfbff'" : "" ?>>
                    <td style="text-align: center"><?= MyFormatter::asDate_Read($claimsDetail->date1) ?></td>
                    <td><?= $claimsDetail->showDetail() ?></td>
                    <td><?= $claimsDetail->company_name ?></td>
                    <td><?= $claimsDetail->receipt_no ?></td>
                    <td><?= $claimsDetail->project_account ?></td>
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



