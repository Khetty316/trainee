<?php

use common\models\myTools\MyFormatter;

foreach ($models as $key => $model) {
    ?>
    <div>
        <div style="margin:5px 0px 5px 0px;font-size: 10pt;font-family: calibri;text-align: justify;font-weight: normal;font-style: normal;line-height: 2" >
            <div>
                <p style='font-weight: bold;line-height: 2'>LOST RECEIPT</p>
                <p style='line-height: 2'>
                    If a duplicate cannot be obtained, for reasonable expenses, the employee / claimant must submit the following signed form with their Expense Claim for reimbursement to the Accounting Department.  This form should also be submitted with Corporate Credit Card Statements (if any) if receipts have been lost.
                </p>
                <p style='font-weight: bold;line-height: 2'>PLEASE NOTE: You must fill out one form per lost receipt.  This form is not meant to replace obtaining receipts.</p>


            </div>
            <div style="border-style: solid;  border-width: 2px;  border-color: black; width: 100%;padding: 0px  8px " >
                <p style='font-weight: bold'>Re: Original Receipt</p>
                <p>I, <span style='text-decoration: underline'>&nbsp;&nbsp;&nbsp;<?= $model->claimant->fullname ?>&nbsp;&nbsp;&nbsp;</span>
                    hereby declare that I have lost / never received the original receipt. 
                    I further declare that I have not and will not use this receipt (if found) to claim reimbursement 
                    from any other source, or to support any claim for income tax deductions in the future.</p>



                <p style='font-weight: bold'>A detailed list of the goods or services purchased is as follows:</p>


                <table  style="font-size: 10pt;font-family: calibri;text-align: justify;font-weight: normal;font-style: normal; border-collapse: collapse;
                        width:50%">
                    <tr>
                        <td style='white-space: nowrap;width:1px'>
                            <p style='line-height: 2'>Vendor Name:&nbsp;</p>
                        </td>
                        <td style='border-bottom:1px solid black'>
                            <?= $model->company_name ?>
                        </td>
                    </tr>

                    <tr>
                        <td style='white-space: nowrap;width:100px;padding-top:15px'>
                            Date Of Purchase:&nbsp;
                        </td>
                        <td style='border-bottom:1px solid black;padding-top:15px'>
                            <?= MyFormatter::asDate_Read($model->date1) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style='white-space: nowrap;width:100px;padding-top:15px'>
                            Amount Of Purchase: RM &nbsp;
                        </td>
                        <td style='border-bottom:1px solid black;padding-top:15px'>
                            <?= MyFormatter::asDecimal2($model->amount) ?>
                        </td>
                    </tr>
                </table>
                <table  style="font-size: 10pt;font-family: calibri;text-align: justify;font-weight: normal;font-style: normal; border-collapse: collapse;
                        width:100%">
                    <tr>
                        <td style='white-space: nowrap;width:1px;padding-top:15px'>
                            <p style='line-height: 2'> Description of goods/ services purchased:&nbsp;</p>
                        </td>
                        <td style='border-bottom:1px solid black'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' style='border-bottom:1px solid black;padding-top:15px'>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan='2' style='border-bottom:1px solid black;padding-top:15px;'>&nbsp;</td>
                    </tr>
                </table>

                <br/>
            </div>

        </div>
    </div>
    <?php
    if (sizeof($models) != $key + 1) {
        echo "<pagebreak/>";
    }
}
?>