<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hr\HrPayslip */


$user = $model->user;



$this->title = (date("M", mktime(null, null, null, $model['pay_month'], 10)) . "-" . $model['pay_year']);
$this->params['breadcrumbs'][] = ['label' => 'HR Payroll', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Pay Slip List (' . $user->fullname . ')', 'url' => ['index-by-staff?userId=' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
    th{
        /*padding-left: 15px!important;*/
    }
</style>
<div class="hr-payslip-view">

    <h3><?= $user->fullname . " (" . $user->staff_id . ")" ?></h3>
    <h4><?= Html::encode($this->title) ?></h4>

    <p>
        <?= Html::a('Update <i class="far fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php
        /* echo Html::a('Delete', ['delete', 'id' => $model->id], [
          'class' => 'btn btn-danger',
          'data' => [
          'confirm' => 'Are you sure you want to delete this item?',
          'method' => 'post',
          ],
          ]); */
        ?>
    </p>
    <div class="row">
        <div class="col-md-7">
            <table id="w0" class="table table-striped table-bordered table-sm detail-view">
                <tbody>
                    <tr>
                        <th class="pl-3"width="50%">Position</th>
                        <td><?= ($model->designation) ?></td>
                    </tr>
                    <tr>
                        <th>Pay Period</th>
                        <td><?= MyFormatter::asDate_Read($model->pay_period) ?></td>
                    </tr>
                    <tr>
                        <th class="pl-3">Basic Salary <span class="float-right mr-2">(RM+)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->basic_salary) ?></td>
                    </tr>
                    <tr>
                        <th class="pl-3">Bonus <span class="float-right mr-2">(RM+)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->bonus) ?></td>
                    </tr>
                    <tr>
                        <th class="pl-3">Commission <span class="float-right mr-2">(RM+)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->commission) ?></td>

                    </tr>
                    <tr>
                        <th class="pl-3">Director Fee <span class="float-right mr-2">(RM+)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->director_fee) ?></td>
                    </tr>

                    <tr>
                        <th class="text-primary" colspan="2">Commission</th>
                    </tr>
                    <?php
                    $commList = $model['hrPayslipCommissions'] ? $model['hrPayslipCommissions'] : [];
                    foreach ($commList as $key => $comm) {

                        echo '<tr><th class="pl-3">' . $comm['description'] . ' <span class="float-right mr-2">(RM+)</span></th>'
                        . '<td class="text-right">'
                        . MyFormatter::asDecimal2_emptyDash($comm['amount'])
                        . '</td></tr>';
                    }

                    if (!$commList) {
                        ?>
                        <tr>
                            <th class="pl-3 text-center" colspan="2">----- (No Commission) -----</th>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <th class="text-primary" colspan="2">Allowance</th>
                    </tr>

                    <?php
                    $myAllow = yii\helpers\ArrayHelper::map($model->hrPayslipAllowances, 'allowance_code', 'amount');

                    foreach ($allowanceList as $allowances) {
                        echo '<tr><th class="pl-3"> - ' . $allowances['allowance_name'] . ' <span class="float-right mr-2">(RM+)</span></th>'
                        . '<td class="text-right">'
                        . MyFormatter::asDecimal2_emptyDash(array_key_exists($allowances['code'], $myAllow) ? $myAllow[$allowances['code']] : "")
                        . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <th class="text-primary" colspan="2">Overtime</th>
                    </tr>
                    <?php
                    $myOT = yii\helpers\ArrayHelper::map($model->hrPayslipOvertimes, 'overtime_code', 'amount');

                    foreach ($OTList as $OT) {
                        echo '<tr><th class="pl-3"> - ' . $OT['overtime_name'] . ' <span class="float-right mr-2">(RM+)</span></th>'
                        . '<td class="text-right">'
                        . MyFormatter::asDecimal2_emptyDash(array_key_exists($OT['code'], $myOT) ? $myOT[$OT['code']] : "")
                        . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <th class="text-primary" colspan="2">Advance</th>
                    </tr>
                    <?php
                    $advList = $model['hrPayslipAdvances'] ? $model['hrPayslipAdvances'] : [];
                    foreach ($advList as $key => $adv) {

                        echo '<tr><th class="pl-3">' . $adv['description'] . ' <span class="float-right mr-2">(RM-)</span></th>'
                        . '<td class="text-right">'
                        . MyFormatter::asDecimal2_emptyDash($adv['amount'])
                        . '</td></tr>';
                    }

                    if (!$advList) {
                        ?>
                        <tr>
                            <th class="pl-3 text-center" colspan="2">----- (No Advance) -----</th>
                        </tr>
                        <?php
                    }
                    ?>


                    <tr>
                        <th class="text-primary" colspan="2">Employee Contribution</th>
                    </tr>
                    <tr>
                        <th class="pl-3">EPF <span class="float-right mr-2">(RM-)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->epf) ?></td>
                    </tr>
                    <tr>
                        <th class="pl-3">Socso <span class="float-right mr-2">(RM-)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->socso) ?></td>

                    </tr>
                    <tr>
                        <th class="pl-3">EIS/SIP <span class="float-right mr-2">(RM-)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->eis_sip) ?></td>
                    </tr>
                    <tr>
                        <th class="pl-3">Income Tax <span class="float-right mr-2">(RM-)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->income_tax) ?></td>
                    </tr>
                    <tr>
                        <th class="text-primary" colspan="2">Leave</th>
                    </tr>
                    <tr>
                        <th class="pl-3">Unpaid Leave <span class="float-right mr-2">(RM-)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->unpaid_leave) ?></td>

                    </tr>
                    <tr>
                        <th class="pl-3">Paid Annual Leave <span class="float-right mr-2">(RM+)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->annual_leave_pay) ?></td>

                    </tr>
                    <tr style="background-color:#fffdd8">
                        <th>Net Salary Payable <span class="float-right mr-2">(RM)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->net_salary) ?></td>
                    </tr>
                    <tr>
                        <th class="text-primary" colspan="2">Employer Contribution</th>
                        <td class="hidden "><span class="not-set">(not set)</span></td>
                    </tr>
                    <tr>
                        <th class="pl-3">EPF <span class="float-right mr-2">(RM)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->employer_epf) ?></td>

                    </tr>
                    <tr>
                        <th class="pl-3">Socso <span class="float-right mr-2">(RM)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->employer_socso) ?></td>

                    </tr>
                    <tr>
                        <th class="pl-3">EIS/SIP <span class="float-right mr-2">(RM)</span></th>
                        <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($model->employer_eis_sip) ?></td>
                    </tr>

                    <tr>
                        <th class="text-primary" colspan="2">Gift / Others</th>
                    </tr>
                    <?php
                    $giftList = $model['hrPayslipGifts'] ? $model['hrPayslipGifts'] : [];
                    foreach ($giftList as $key => $gift) {

                        echo '<tr><th class="pl-3">' . $gift->giftCode->gift_name . '(' . $gift['description'] . ') <span class="float-right mr-2">(RM)</span></th>'
                        . '<td class="text-right">'
                        . MyFormatter::asDecimal2_emptyDash($gift['amount'])
                        . '</td></tr>';
                    }


                    if (!$giftList) {
                        ?>
                        <tr>
                            <th class="pl-3 text-center" colspan="2">----- (No Gift) -----</th>
                        </tr>
                        <?php
                    }
                    ?>


                    <tr>
                        <th>Created</th>
                        <td><?= $model->createdBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->created_at) ?></td>
                    </tr>
                    <tr>
                        <th>Updated</th>
                        <td><?= ($model->updated_at) ? ($model['updatedBy']['fullname'] . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->updated_at)) : "-" ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>


</div>
