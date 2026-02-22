<?php
$employee = $model->user;

use common\models\myTools\MyFormatter;
?>
<style>
    td{
        padding:3px 5px 3px 5px;
    }
</style>
<div style="font-family: Calibri">
    <div>
        <img src="/images/logo_only.png" style="height:30px; float: left;margin-left:80px;margin-right:10px;  d"/>
        <h2 style="font-family: Calibri"><i>NPL Engineering Sdn. Bhd. 
                <span style='bottom: 0px; font-family: Calibri; font-size: 8pt;font-weight: bold'>(Co. Reg. No. 1043974-M)</span></i>
        </h2>
        <div style="text-align:center;  font-family: Calibri; font-size: 8pt">
            <?= $companyDetail['address'] ?><br/>
            T: <?= $companyDetail['tel'] ?>   F: <?= $companyDetail['fax'] ?>    E: <?= $companyDetail['email'] ?>
        </div>

        <!--<h5 style="width:30%;text-align: right;bottom:0;position: absolute; right:0px" ></h5>-->
    </div>
    <h1 style='text-align: right;font-size:14pt'>PAY SLIP</h1>
    <div>
        <table width='100%' style='font-size: 10pt;font-family: Calibri'>
            <tr>
                <td style='width:20%'><b>Employee Name:</b></td>
                <td style='width:30%;border-bottom: 1px solid black'><?= $employee->fullname ?></td>
                <td style='width:10%'>&nbsp;</td>
                <td style='width:20%'><b>Staff No.: </b></td>
                <td style='width:20%;border-bottom: 1px solid black'><?= $employee->staff_id ?></td>
            </tr>
            <tr>
                <td><b>Identity Card No.:</b></td>
                <td style='border-bottom: 1px solid black'><?= $employee->ic_no ?></td>
                <td>&nbsp;</td>
                <td><b> Pay Period: </b></td>
                <td style='border-bottom: 1px solid black'><?= MyFormatter::asDate_Read_dnY($model->pay_period) ?></td>
            </tr>
            <tr>
                <td><b>Position:</b></td>
                <td style='border-bottom: 1px solid black'><?= $model->designation ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style='width:20%'><b>Date Of Joining:</b></td>
                <td style='width:30%;border-bottom: 1px solid black'><?= MyFormatter::asDate_Read_dnY($employee->date_of_join) ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <br/>
    <div>  
        <?php
        $earning = [];
        $earning[] = ['display' => 'Basic Salary:', 'val' => MyFormatter::asDecimal2($model->basic_salary)];
        $grossEarning = $model->basic_salary;

        $commissions = $model->hrPayslipCommissions;
        if ($commissions) {
            $earning[] = ['display' => 'Commission:'];
            foreach ($commissions as $commission) {
                $earning[] = ['display' => ' - ' . $commission->description, 'val' => MyFormatter::asDecimal2($commission->amount)];
                $grossEarning += $commission->amount;
            }
        }




        $allowances = $model->hrPayslipAllowances;
        if ($allowances) {
            $earning[] = ['display' => 'Allowance:'];
            foreach ($allowances as $allowance) {
                $earning[] = ['display' => ' - ' . $allowance->allowanceCode->allowance_name, 'val' => MyFormatter::asDecimal2($allowance->amount)];
                $grossEarning += $allowance->amount;
            }
        }
        $overtimes = $model->hrPayslipOvertimes;
        if ($overtimes) {
            $earning[] = ['display' => 'Overtime:'];
            foreach ($overtimes as $ot) {
                $earning[] = ['display' => ' - ' . $ot->overtimeCode->overtime_name, 'val' => MyFormatter::asDecimal2($ot->amount)];
                $grossEarning += $ot->amount;
            }
        }

        if ($model->bonus) {
            $earning[] = ['display' => 'Bonus:', 'val' => MyFormatter::asDecimal2($model->bonus)];
            $grossEarning += $model->bonus;
        }

//        if ($model->commission) {
//            $earning[] = ['display' => 'Commission:', 'val' => MyFormatter::asDecimal2($model->commission)];
//            $grossEarning += $model->commission;
//        }

        if ($model->director_fee) {
            $earning[] = ['display' => 'Director Fee:', 'val' => MyFormatter::asDecimal2($model->director_fee)];
            $grossEarning += $model->director_fee;
        }

        if ($model->annual_leave_pay) {
            $earning[] = ['display' => 'Leave:'];
            $earning[] = ['display' => ' - Paid Annual Leave', 'val' => MyFormatter::asDecimal2($model->annual_leave_pay)];
            $grossEarning += $model->annual_leave_pay;
        }



        $deduct = [];
        $totalDeduction = 0;

        $deduct[] = ['display' => 'EPF:', 'val' => MyFormatter::asDecimal2($model->epf)];
        $totalDeduction += $model->epf;

        $deduct[] = ['display' => 'SOCSO:', 'val' => MyFormatter::asDecimal2($model->socso)];
        $totalDeduction += $model->socso;

        $deduct[] = ['display' => 'EIS / SIP:', 'val' => MyFormatter::asDecimal2($model->eis_sip)];
        $totalDeduction += $model->eis_sip;

        $deduct[] = ['display' => 'Income Tax:', 'val' => MyFormatter::asDecimal2($model->income_tax)];
        $totalDeduction += $model->income_tax;


        if ($model->unpaid_leave) {
            $deduct[] = ['display' => 'Leave:'];
            $deduct[] = ['display' => ' - Unpaid Leave:', 'val' => MyFormatter::asDecimal2($model->unpaid_leave)];
            $totalDeduction += $model->unpaid_leave;
        }


        $advances = $model->hrPayslipAdvances;
        if ($advances) {
            $deduct[] = ['display' => 'Advance:'];
            foreach ($advances as $adv) {
                $deduct[] = ['display' => ' - ' . $adv->description, 'val' => MyFormatter::asDecimal2($adv->amount)];
                $totalDeduction += $adv->amount;
            }
        }

        $totalRows = (sizeof($deduct) < sizeof($earning)) ? sizeof($earning) : sizeof($deduct);
        ?>
        <table style='border:1px solid black!important;font-size: 10pt;font-family: Calibri;border-collapse:collapse'  width='100%'>
            <tr style='background-color:#b8cce4;'>
                <!--#dce6f1-->
                <!--#8db4e2-->
                <td style='width:35%;font-weight: bold;border-bottom: 1px solid black;'>EARNINGS</td>
                <td style='width:15%;font-weight: bold;text-align: right;border: 1px solid black'>MYR</td>
                <td style='width:35%;font-weight: bold;border-bottom: 1px solid black;'>DEDUCTIONS</td>
                <td style='width:15%;font-weight: bold;text-align: right;border: 1px solid black'>MYR</td>
            </tr>
            <?php
            for ($i = 0; $i < $totalRows; $i++) {
                ?>
                <tr>
                    <td style='padding-left:5px;padding-top: 3px;padding-bottom: 3px'><?= (array_key_exists($i, $earning) && array_key_exists('display', $earning[$i])) ? $earning[$i]['display'] : '' ?></td>
                    <td style='text-align: right;padding-right: 5px;border-left: 1px solid black;border-right: 1px solid black;padding-top: 3px;padding-bottom: 3px'><?= (array_key_exists($i, $earning) && array_key_exists('val', $earning[$i])) ? $earning[$i]['val'] : '' ?></td>
                    <td style='padding-left:5px;padding-top: 3px;padding-bottom: 3px'><?= (array_key_exists($i, $deduct) && array_key_exists('display', $deduct[$i])) ? $deduct[$i]['display'] : '' ?></td>
                    <td style='text-align: right;padding-right: 5px;border-left: 1px solid black;border-right: 1px solid black;padding-top: 3px;padding-bottom: 3px'><?= (array_key_exists($i, $deduct) && array_key_exists('val', $deduct[$i])) ? $deduct[$i]['val'] : '' ?></td>
                </tr>
                <?php
            }
            ?>
            <tr style='background-color:#dce6f1;'>
                <td style='padding-left:5px;font-weight: bold;padding-top: 3px;padding-bottom: 3px'>Gross Earnings:</td>
                <td style='text-align: right;padding-right: 5px;border-left: 1px solid black;border-right: 1px solid black;font-weight: bold;padding-top: 3px;padding-bottom: 3px'><?= MyFormatter::asDecimal2($grossEarning) ?></td>
                <td style='padding-left:5px;font-weight: bold;padding-top: 3px;padding-bottom: 3px'>Total Deductions:</td>
                <td style='text-align: right;padding-right: 5px;border-left: 1px solid black;border-right: 1px solid black;font-weight: bold;padding-top: 3px;padding-bottom: 3px'><?= MyFormatter::asDecimal2($totalDeduction) ?></td>
            </tr>
            <tr style='background-color:#8db4e2;'>
                <td style='padding-left:5px;font-weight: bold;text-align: right;border-top:1px solid black;padding-top: 3px;padding-bottom: 3px' colspan='3'>Net Salary Payable&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td style='text-align: right;padding-right: 5px;border-left: 1px solid black;border-right: 1px solid black;border-top:1px solid black;font-weight: bold;padding-top: 3px;padding-bottom: 3px'><?= MyFormatter::asDecimal2($grossEarning - $totalDeduction) ?></td>
            </tr>

        </table>
        <br/>
        <table style='border:1px solid black!important;font-size: 10pt;font-family: Calibri;border-collapse:collapse'  width='100%'>
            <tr style='background-color:#b8cce4'>
                <td style='border-top: 1px solid black;padding:3px 0px 3px 5px;border-bottom:1px solid black;width:35%'>
                    <b>Employer Contribution</b>
                </td>
                <td style='width:15%;font-weight: bold;text-align: right;border-top: 1px solid black;border-bottom:1px solid black'>MYR</td>
                <td style='border-top: 1px solid black;padding:3px 0px 3px 5px;border-bottom:1px solid black' colspan='2'></td>
            </tr>
            <tr>
                <td style='padding:3px 0px 3px 5px;'> - EPF:</td>
                <td style='text-align: right;padding-right: 5px;padding-top: 3px;padding-bottom: 3px'>
                    <?= MyFormatter::asDecimal2($model->employer_epf) ?>
                </td>
                <td colspan='2'></td>
            </tr>
            <tr>
                <td style='padding:3px 0px 3px 5px;'> - SOCSO:</td>
                <td style='text-align: right;padding-right: 5px;padding-top: 3px;padding-bottom: 3px'>
                    <?= MyFormatter::asDecimal2($model->employer_socso) ?>
                </td>
                <td colspan='2'></td>
            </tr>
            <tr>
                <td style='padding:3px 0px 3px 5px;'> - EIS / SIP:</td>
                <td style='text-align: right;padding-right: 5px;padding-top: 3px;padding-bottom: 3px'>
                    <?= MyFormatter::asDecimal2($model->employer_eis_sip) ?>
                </td>
                <td colspan='2'></td>
            </tr>
        </table>
    </div>

</div>


