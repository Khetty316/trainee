<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">


    <?php
    $this->title = "Master Claim List";
    $this->params['breadcrumbs'][] = $this->title;
    
    
//    $model = new frontend\models\working\claim\ClaimsMaster();
    


    $totalOfClaim = 0;
    ?>
    <style>
        table{
            font-size: 11pt;
            font-family: calibri;
        }

        .detailTable td,.detailTable th{
            border: 1px black solid ;
        }

        th{
            text-align:center;
            background-color:#99ecff;
            font-weight:normal;
        }

        td{
            
        }

        th,td{
            padding-left: 5px;
            padding-right:5px;
        }
    </style>
    <div>
        <div style="position:relative;padding-bottom: 0">
            <img src="/images/logo_npl.png" style="height:50px"/>
            <h5 style="width:30%;text-align: right;bottom:0;position: absolute; right:0px" ><?= $model->claimType->claim_name ?><br/>Claim ID: <?= $model->claims_id ?></h5>
        </div>

        <hr style="color:black;border:solid 1px black;margin:5px 0px 5px 0px" />
        <div style="position:relative;margin:5px 0px 5px 0px">
            <table width='100%'>
                <tr>
                    <td>
                        Claimant's Name: <u><?= $model->claimant->fullname ?>   &nbsp;</u>
                    </td>
                    <td style='text-align:right'>
                        Position: _____________________
                    </td>
                </tr>
            </table>
     
        </div>
        <div>
            <table class='detailTable' width="100%">
                <thead>
                    <tr>
                        <th width="1%">Date</th>
                        <th>Detail</th>
                        <th>Company Name</th>
                        <th>Receipt No.</th>
                        <th>Project Code</th>
                        <th width="10%">Total Amount(RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($model->claimsDetails as $claimsDetail) {
                        ?>
                        <tr>
                            <td style="text-align: center"><?= MyFormatter::asDate_Read($claimsDetail->date1) . ($claimsDetail->date2 == "" ? "" : " - " . MyFormatter::asDate_Read($claimsDetail->date2)) ?></td>
                            <td><?= $claimsDetail->detail ?></td>
                            <td><?= $claimsDetail->company_name ?></td>
                            <td><?= $claimsDetail->receipt_no ?></td>
                            <td><?= $claimsDetail->project_account ?></td>
                            <td style="text-align: right"><?= MyFormatter::asDecimal2($claimsDetail->amount) ?></td>
                        </tr>
                        <?php
                        $totalOfClaim += $claimsDetail->amount;
                    }
                    ?>
                </tbody>
                <tfoot style="background-color:#27badb">
                    <tr>
                        <td colspan="5" style="text-align:right">
                            <b>Total Of Claim</b>
                        </td>
                        <td style="text-align: right"><b><?= MyFormatter::asDecimal2($totalOfClaim) ?></b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br/>
        <div>
            <table width='100%'>
                <tr>
                    <td colspan="2">
                        <b style='font-size: 12pt'>DECLARATION</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        I declare that: -
                    </td>
                </tr>
                <tr>
                    <td style='text-align: center;width:150px' >&#9745;</td>
                    <td style='font-size:10pt'>
                        The amounts of expenses claimed, have been actually and reasonably incurred for the purpose of enabling me to perform approved duties as a empoyee of NPL Engineering Sdn. Bhd.
                    </td>
                </tr>
                <tr>
                    <td style='text-align: center'>&#9745;</td>
                    <td style='font-size:10pt'>
                        I have not made any other claim which is not connected to the duties indicated in this form.
                    </td>
                </tr>
                <tr>
                    <td style='text-align: center'>&#9745;</td>
                    <td style='font-size:10pt'>
                        I have attached all necessary receipts in connection with expenses claimed.
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php ?>

</div>

