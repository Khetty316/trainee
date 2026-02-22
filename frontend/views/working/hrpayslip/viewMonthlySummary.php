<?php

use common\models\myTools\MyFormatter;

$file = "monthlySummary.xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
?>
<div class="hr-payslip-view">


    <div class="row">
        <div class="col-md-12">
            <table  style="border: 1px solid black;font-size: 14pt;white-space: nowrap;">
                <thead>                   
                    <tr>
                        <th width="70%" style="text-align:left;background-color:orange">Payroll Monthly Summary - Year <?= $model['payYear'] ?></th>
                        <th style='text-align: right;background-color:orange'> &nbsp;<?= date("F", mktime(null, null, null, $model['payMonth'], 10)); ?> - <?= substr($model['payYear'], 2, 2) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($model->bonus) {
                        echo '<tr><td>Basic Salary</td><td class="text-right">' . MyFormatter::asDecimal2($model->basicSalary) . '</td></tr>';
                    }

                    foreach ($model->allowances as $allowance) {
                        echo '<tr><td>' . $allowance['allowance_name'] . '</td><td class="text-right">' . MyFormatter::asDecimal2($allowance['allowances']) . '</td></tr>';
                    }

                    if ($model->bonus) {
                        echo '<tr><td>Bonus</td><td class="text-right">' . MyFormatter::asDecimal2($model->bonus) . '</td></tr>';
                    }
                    if ($model->commission) {
                        echo '<tr><td>Comission</td><td class="text-right">' . MyFormatter::asDecimal2($model->commission) . '</td></tr>';
                    }
                    if ($model->directorFee) {
                        echo '<tr><td>Director Fee</td><td class="text-right">' . MyFormatter::asDecimal2($model->directorFee) . '</td></tr>';
                    }

                    foreach ($model->overtimes as $overtime) {
                        echo '<tr><td>' . $overtime['overtime_name'] . '</td><td class="text-right">' . $overtime['overtimes'] . '</td></tr>';
                    }

                    $totalGift = 0;
                    foreach ($model->gifts as $gift) {
                        echo '<tr><td>' . $gift['gift_name'] . '</td><td class="text-right">' . $gift['gifts'] . '</td></tr>';
                        $totalGift += $gift['gifts'];
                    }

                    if ($model->annualLeavePay) {
                        echo '<tr><td>Paid Annual Leave</td><td class="text-right">' . MyFormatter::asDecimal2($model->annualLeavePay) . '</td></tr>';
                    }
                    if ($model->unpaidLeave) {
                        echo '<tr><td>Unpaid Leave</td><td class="text-right">(' . MyFormatter::asDecimal2($model->unpaidLeave) . ')</td></tr>';
                    }
                    if ($model->advances) {
                        echo '<tr><td><b>Advance Deduction</b></td><td class="text-right"></td></tr>';
                        foreach ($model->advances as $advance) {
                            echo '<tr><td>' . $advance['description'] . '</td><td class="text-right">(' . $advance['advance'] . ')</td></tr>';
                        }
                    }

                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td class="font-weight-bold"><u>Employee Contribution:</u></td><td class="text-right"></td></tr>';

                    if ($model->epf) {
                        echo '<tr><td> - EPF</td><td class="text-right">(' . MyFormatter::asDecimal2($model->epf) . ')</td></tr>';
                    }
                    if ($model->socso) {
                        echo '<tr><td> - Socso</td><td class="text-right">(' . MyFormatter::asDecimal2($model->socso) . ')</td></tr>';
                    }
                    if ($model->eisSip) {
                        echo '<tr><td> - EIS</td><td class="text-right">(' . MyFormatter::asDecimal2($model->eisSip) . ')</td></tr>';
                    }
                    if ($model->incomeTax) {
                        echo '<tr><td> - Income Tax (PCB)</td><td class="text-right">(' . MyFormatter::asDecimal2($model->incomeTax) . ')</td></tr>';
                    }
                    if ($model->netSalary) {
                        echo '<tr><td class="font-weight-bold">Net Salary</td><td class="text-right">' . MyFormatter::asDecimal2($model->netSalary) . '</td></tr>';
                        echo '<tr><td class="font-weight-bold">Net Salary + Festival Incentive</td><td class="text-right">' . MyFormatter::asDecimal2($model->netSalary + $totalGift) . '</td></tr>';
                    }

                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td class="font-weight-bold"><u>Employer Contribution:</u></td><td class="text-right"></td></tr>';

                    if ($model->employerEpf) {
                        echo '<tr><td> - EPF</td><td class="text-right">(' . MyFormatter::asDecimal2($model->employerEpf) . ')</td></tr>';
                    }
                    if ($model->employerSocso) {
                        echo '<tr><td> - Socso</td><td class="text-right">(' . MyFormatter::asDecimal2($model->employerSocso) . ')</td></tr>';
                    }
                    if ($model->employerEisSip) {
                        echo '<tr><td> - EIS</td><td class="text-right">(' . MyFormatter::asDecimal2($model->employerEisSip) . ')</td></tr>';
                    }

                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td class="font-weight-bold"><u>Total Contribution:</u></td><td class="text-right"></td></tr>';

                    if ($model->employerEpf) {
                        echo '<tr><td> - EPF</td><td class="text-right">(' . MyFormatter::asDecimal2($model->employerEpf + $model->epf) . ')</td></tr>';
                    }
                    if ($model->employerSocso) {
                        echo '<tr><td> - Socso</td><td class="text-right">(' . MyFormatter::asDecimal2($model->employerSocso + $model->socso) . ')</td></tr>';
                    }
                    if ($model->employerEisSip) {
                        echo '<tr><td> - EIS</td><td class="text-right">(' . MyFormatter::asDecimal2($model->employerEisSip + $model->eisSip) . ')</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


</div>
