<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
use yii\helpers\ArrayHelper;
use common\models\myTools\MyCommonFunction;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hr\HrPayslip */

$this->title = 'New Payroll - ' . $user->fullname;
$this->params['breadcrumbs'][] = ['label' => 'HR Payroll', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Pay Slip List (' . $user->fullname . ')', 'url' => ['index-by-staff?userId=' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .firstCol{
        width: 20%;
    }
    .secondCol{
        width: 8%;
    }
    .thirdCol{
        width: 20%;
    }
    .fourthCol{
        width:1%;
    }
    .fifthCol{
        width:10%;
    }
    .sixthCol{
        width:41%;
    }
    .form-group{
        padding:0%;
        margin-bottom: 0%
    }

    .form-control{
        height: calc(1em + .375rem + 2px) !important;
        padding: .125rem .25rem !important;
        /*font-size: .75rem !important;*/
        line-height: 1.5;
        border-radius: .2rem;
    }

    td{
        padding: 0px!important;
        white-space: nowrap;
    }


</style>
<div class="hr-payslip-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row">
        <div class="col-12">
            <div class="hr-payslip-form">
                <?php
                $form = ActiveForm::begin([
                            'id' => 'hr-payslip-form',
                            'layout' => 'horizontal',
                            'fieldConfig' => [
                                'template' => "{label}{input}{error}{hint}",
                                'horizontalCssClasses' => [
                                    'label' => 'col-sm-3',
                                    'offset' => 'col-sm-offset-4',
                                    'wrapper' => 'col-sm-6',
                                ],
                            ],
                            'options' => ['autocomplete' => 'off', 'class']
                ]);
                ?>
                <?= $form->field($model, 'user_id', ['options' => ['class' => 'hidden']])->textInput(['value' => $user->id]) ?>
                <fieldset class="form-group border p-1">
                    <table class="table table-sm table-borderless col-12 pl-3">
                        <tr>
                            <td class="firstCol">Pay Slip of (Month/Year)</td>
                            <td class="secondCol"></td>
                            <td class="thirdCol"> 
                                <?php
                                $monthList = [];
                                for ($i = 1; $i <= 12; $i++) {
                                    $monthList[$i] = date("M", mktime(null, null, null, $i, 10));
                                }
                                $yearList = [];
                                for ($i = -2; $i <= 2; $i++) {
                                    $y = date("Y") + $i;
                                    $yearList[$y] = $y;
                                }
                                echo Html::dropDownList('HrPayslip[pay_month]', date('n'), $monthList, ['class' => 'form-control d-inline col-5', 'style' => 'padding:0px!important', 'id' => 'hrpayslip-pay_month']);
                                echo Html::dropDownList('HrPayslip[pay_year]', date("Y"), $yearList, ['class' => 'form-control d-inline col-5', 'style' => 'padding:0px!important', 'id' => 'hrpayslip-pay_month']);
                                ?>
                            </td>
                            <td class="fourthCol">&nbsp;&nbsp;</td>
                            <td class="fifthCol text-right">
                                <?= $lastRecord['pay_month'] ? (date("M", mktime(null, null, null, $lastRecord['pay_month'], 10)) . "-" . $lastRecord['pay_year']) : '' ?>
                            </td>
                            <td class="sixthCol"></td>
                        </tr>
                        <tr>
                            <td>Position</td>
                            <td></td>
                            <td><?= Html::textInput('HrPayslip[designation]', $user['designation0']['design_name'], ['class' => 'form-control']) ?></td>
                            <td></td>
                            <td colspan="2" class="fifthCol" ><?= $lastRecord['designation'] ?></td>
                        </tr>
                        <tr>
                            <td>Staff No.</td>
                            <td></td>
                            <td><?= Html::textInput('HrPayslip[staffId]', $user['staff_id'], ['class' => 'form-control', 'readonly' => 'readonly']) ?></td>
                            <td></td>
                            <td class="text-right"><?= $user['staff_id'] ?></td>
                        </tr>
                        <tr>
                            <td>Pay Period</td>
                            <td></td>
                            <td>  
                                <?=
                                        $form->field($model, 'pay_period', ['options' => ['class' => 'm-0 p-0'], 'errorOptions' => ['class' => 'invalid-feedback-show']])->widget(yii\jui\DatePicker::className(),
                                                ['options' => ['class' => 'form-control', 'placeholder' => '(dd/MM/yyyy)'], 'dateFormat' => 'dd/MM/yyyy'])
                                        ->label(false)
                                ?>
                            </td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDate_Read($lastRecord['pay_period']) ?></td>
                        </tr>
                        <tr>
                            <td>Basic Salary</td>
                            <td>(RM) (+)</td>
                            <td><?= Html::textInput('HrPayslip[basic_salary]', MyFormatter::asDecimal2NoSeparator($lastRecord['basic_salary']), ['class' => 'form-control text-right countNet', 'type' => 'number', 'step' => '.01', 'style' => '']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['basic_salary']) ?></td>
                            <td></td>
                        </tr>
                        <tr style="padding:0px;margin:0px">
                            <td>Bonus</td>
                            <td>(RM) (+)</td>
                            <td><?= Html::textInput('HrPayslip[bonus]', '', ['class' => 'form-control text-right countNet', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['bonus']) ?></td>
                        </tr>
<!--                        <tr>
                            <td>Commission</td>
                            <td>(RM) (+)</td>
                            <td><?= Html::textInput('HrPayslip[commission]', '', ['class' => 'form-control text-right countNet', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['commission']) ?></td>
                        </tr>-->
                        <tr>
                            <td>Director Fee</td>
                            <td>(RM) (+)</td>
                            <td><?= Html::textInput('HrPayslip[director_fee]', '', ['class' => 'form-control text-right countNet', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['director_fee']) ?></td>
                        </tr>
                    </table>
                </fieldset>

                <!-- Commission -->
                <fieldset class="form-group border p-1">
                    <legend class="w-auto px-2 m-0">Commission</legend>
                    <table class="table table-sm table-borderless" id='table_Comm'>
                        <tr>
                            <td class="firstCol"></td>
                            <td class="secondCol"></td>
                            <td class="thirdCol"></td>
                            <td class="fourthCol"></td>
                            <td class="fifthCol"></td>
                            <td class="sixthCol"></td>
                        </tr>
                        <?php
                        $lastRecordComms = $lastRecord['hrPayslipCommissions'] ? $lastRecord['hrPayslipCommissions'] : [];
                        foreach ($lastRecordComms as $key => $lastRecordComm) {
                            ?>
                            <tr>
                                <td><input type="text" class="form-control isCommDesc" name="commDesc[]" value="" placeholder="Description"></td>
                                <td>(RM) (+)</td>
                                <td><input type="number" class="form-control text-right countNet" name="commAmount[]" value="0.00" step=".01" disabled="true"/></td>
                                <td></td>
                                <td class="text-right">
                                    <?= MyFormatter::asDecimal2($lastRecordComm['amount']) ?>
                                </td>
                                <td class="pl-2">
                                    <?= $lastRecordComm['description'] ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                    </table>
                    <a class="pl-3" href="javascript:" id="btn_addComm"><i class="fas fa-plus-circle text-primary fa-lg" title="Add New Row" ></i></a>
                </fieldset>


                <!--Allowance-->
                <fieldset class="form-group border p-1 ">
                    <legend class="w-auto px-2  m-0">Allowance</legend>
                    <table class="table table-sm table-borderless pb-0 mb-0">
                        <?php
                        $lastRecordAllowance = $lastRecord['hrPayslipAllowances'] ? ArrayHelper::map($lastRecord['hrPayslipAllowances'], 'allowance_code', 'amount') : [];
                        foreach ($allowanceList as $key => $allowanceType) {
                            $isOutDuty = ($allowanceType['code'] == 'out_duty');
                            $claimAmount = null;
                            $xx = '';
                            $claimIds = '';
                            if ($isOutDuty) {
                                foreach ($travelClaims as $claim) {
                                    $claimAmount += $claim['total_amount'];
                                    $xx .= $claim['claims_id'] . " - " . $claim['total_amount'] . "\n";
                                    $claimIds .= ($claimIds == '' ? '' : ',') . $claim['claims_master_id'];
                                }
                            }
                            ?>
                            <tr>
                                <td class="firstCol">- <?= $allowanceType['allowance_name'] ?></td>
                                <td class="secondCol">(RM) (+)</td>
                                <td class="thirdCol">
                                    <div class="input-group">
                                        <?php
                                        if ($isOutDuty && $travelClaims) {
                                            echo Html::textInput('claimIds', $claimIds, ['class' => 'hidden']);
                                            echo "<div class='input-group-prepend'>"
                                            . "<span class='input-group-text form-control' id='basic-addon3'><i class='far fa-question-circle' title='$xx'></i></span>"
                                            . "</div>";
                                        }
                                        ?>
                                        <?= Html::textInput('allowanceAmount[]', MyFormatter::asDecimal2NoSeparator($claimAmount), ['class' => 'form-control text-right countNet', 'type' => 'number', 'step' => '.01']) ?>
                                    </div>
                                    <?= Html::textInput('allowanceCode[]', $allowanceType['code'], ['class' => 'form-control hidden']) ?>

                                </td>
                                <td class="fourthCol"></td>
                                <td class="fifthCol text-right"><?=
                                    $lastRecord['hrPayslipAllowances'] ? ((array_key_exists($allowanceType['code'], $lastRecordAllowance)) ? MyFormatter::asDecimal2($lastRecordAllowance[$allowanceType['code']]) : "") : ""
                                    ?>
                                </td>
                                <td class="sixthCol"></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </fieldset>

                <!--Overtime-->
                <fieldset class="form-group border p-1 ">
                    <legend class="w-auto px-2  m-0">Overtime</legend>
                    <table class="table table-sm table-borderless pb-0 mb-0">
                        <?php
                        $lastRecordOvertime = $lastRecord['hrPayslipOvertimes'] ? ArrayHelper::map($lastRecord['hrPayslipOvertimes'], 'overtime_code', 'amount') : [];
                        foreach ($OTList as $key => $OT) {
                            ?>
                            <tr>
                                <td class="firstCol">- <?= $OT['overtime_name'] ?></td>
                                <td class="secondCol">(RM) (+)</td>
                                <td class="thirdCol">
                                    <?= Html::textInput('otAmount[]', '', ['class' => 'form-control text-right countNet', 'type' => 'number', 'step' => '.01']) ?>
                                    <?= Html::textInput('otCode[]', $OT['code'], ['class' => 'form-control hidden']) ?>
                                </td>
                                <td class="fourthCol"></td>
                                <td class="fifthCol text-right"><?=
                                    (array_key_exists($OT['code'], $lastRecordOvertime)) ? MyFormatter::asDecimal2($lastRecordOvertime[$OT['code']]) : ""
                                    ?>
                                </td>
                                <td class="sixthCol"></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </fieldset>

                <!--Advance-->
                <fieldset class="form-group border p-1">
                    <legend class="w-auto px-2 m-0">Advance</legend>
                    <table class="table table-sm table-borderless" id='table_Adv'>
                        <tr>
                            <td class="firstCol"></td>
                            <td class="secondCol"></td>
                            <td class="thirdCol"></td>
                            <td class="fourthCol"></td>
                            <td class="fifthCol"></td>
                            <td class="sixthCol"></td>
                        </tr>
                        <?php
                        $lastRecordAdvs = $lastRecord['hrPayslipAdvances'] ? $lastRecord['hrPayslipAdvances'] : [];
                        foreach ($lastRecordAdvs as $key => $lastRecordAdv) {
                            ?>
                            <tr>
                                <td><input type="text" class="form-control isAdvDesc" name="advDesc[]" value="" placeholder="Description"></td>
                                <td>(RM) (-)</td>
                                <td><input type="number" class="form-control text-right countNet isNegative" name="advAmount[]" value="0.00" step=".01" disabled="true"/></td>
                                <td></td>
                                <td class="text-right">
                                    <?= MyFormatter::asDecimal2($lastRecordAdv['amount']) ?>
                                </td>
                                <td class="pl-2">
                                    <?= $lastRecordAdv['description'] ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                    </table>
                    <a class="pl-3" href="javascript:" id="btn_addAdv"><i class="fas fa-plus-circle text-primary fa-lg" title="Add New Row" ></i></a>
                </fieldset>

                <!--Employee Contribution-->
                <fieldset class="form-group border p-1">
                    <legend class="w-auto px-2 m-0">Employee Contribution</legend>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="firstCol">EPF</td>
                            <td class="secondCol">(RM) (-)</td>
                            <td class="thirdCol"><?= Html::textInput('HrPayslip[epf]', '', ['class' => 'form-control text-right countNet isNegative', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td class="fourthCol"></td>
                            <td class="fifthCol text-right"><?= MyFormatter::asDecimal2($lastRecord['epf']) ?></td>
                            <td class="sixthCol"></td>
                        </tr>
                        <tr>
                            <td>Socso</td>
                            <td>(RM) (-)</td>
                            <td><?= Html::textInput('HrPayslip[socso]', '', ['class' => 'form-control text-right countNet isNegative', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['socso']) ?></td>
                        </tr>
                        <tr>
                            <td>EIS/SIP</td>
                            <td>(RM) (-)</td>
                            <td><?= Html::textInput('HrPayslip[eis_sip]', '', ['class' => 'form-control text-right countNet isNegative', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['eis_sip']) ?></td>
                        </tr>
                        <tr>
                            <td>Income Tax</td>
                            <td>(RM) (-)</td>
                            <td><?= Html::textInput('HrPayslip[income_tax]', '', ['class' => 'form-control text-right countNet isNegative', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['income_tax']) ?></td>
                        </tr>
                    </table>
                </fieldset>

                <!--Leave-->
                <fieldset class="form-group border p-1">
                    <legend class="w-auto px-2 m-0">Leave</legend>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="firstCol">Unpaid Leave</td>
                            <td class="secondCol">(RM) (-)</td>
                            <td class="thirdCol"><?= Html::textInput('HrPayslip[unpaid_leave]', '', ['class' => 'form-control text-right countNet isNegative', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td class="fourthCol"></td>
                            <td class="fifthCol text-right"><?= MyFormatter::asDecimal2($lastRecord['unpaid_leave']) ?></td>
                            <td class="sixthCol"></td>
                        </tr>
                        <tr>
                            <td>Paid Annual Leave</td>
                            <td>(RM) (+)</td>
                            <td><?= Html::textInput('HrPayslip[annual_leave_pay]', '', ['class' => 'form-control text-right countNet', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['annual_leave_pay']) ?></td>
                            <td></td>
                        </tr>
                    </table>
                </fieldset>

                <!--Final Salary Payable-->
                <fieldset class="form-group border p-1">
                    <legend class="w-auto px-2 m-0">Net Salary Payable</legend>
                    <table class="table table-sm table-borderless mt-2">
                        <tr>
                            <td class="firstCol">Total</td>
                            <td class="secondCol">(RM)</td>
                            <td class="thirdCol"><?= Html::textInput('HrPayslip[net_salary]', MyFormatter::asDecimal2NoSeparator($lastRecord['basic_salary']), ['class' => 'form-control text-right', 'type' => 'number', 'id' => 'net_salary', 'step' => '.01', 'style' => 'background-color:#fffdd8']) ?></td>
                            <td class="fourthCol"></td>
                            <td class="fifthCol text-right"><?= MyFormatter::asDecimal2($lastRecord['net_salary']) ?></td>
                            <td class="sixthCol"></td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset class="form-group border p-1">
                    <legend class="w-auto px-2 m-0">Employer Contribution</legend>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="firstCol">EPF</td>
                            <td class="secondCol">(RM)</td>
                            <td class="thirdCol"><?= Html::textInput('HrPayslip[employer_epf]', '', ['class' => 'form-control text-right', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td class="fourthCol"></td>
                            <td class="fifthCol text-right"><?= MyFormatter::asDecimal2($lastRecord['employer_epf']) ?></td>
                            <td class="sixthCol"></td>
                        </tr>
                        <tr>
                            <td>Socso</td>
                            <td>(RM)</td>
                            <td><?= Html::textInput('HrPayslip[employer_socso]', '', ['class' => 'form-control text-right', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['employer_socso']) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>EIS/SIP</td>
                            <td>(RM)</td>
                            <td><?= Html::textInput('HrPayslip[employer_eis_sip]', '', ['class' => 'form-control text-right', 'type' => 'number', 'step' => '.01']) ?></td>
                            <td></td>
                            <td class="text-right"><?= MyFormatter::asDecimal2($lastRecord['employer_eis_sip']) ?></td>
                            <td></td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset class="form-group border p-1">
                    <legend class="w-auto px-2 m-0">Gift / Others</legend>
                    <table class="table table-sm table-borderless" id='tableGift'>
                        <tr>
                            <td class="firstCol"></td>
                            <td class="secondCol"></td>
                            <td class="thirdCol"></td>
                            <td class="fourthCol"></td>
                            <td class="fifthCol"></td>
                            <td class="sixthCol"></td>
                        </tr>
                        <?php
                        $lastRecordGifts = $lastRecord['hrPayslipGifts'] ? $lastRecord['hrPayslipGifts'] : [];
                        foreach ($lastRecordGifts as $key => $lastRecordGift) {
                            ?>
                            <tr>
                                <td>
                                    <?= MyCommonFunction::myDropDown(ArrayHelper::map($giftList, 'code', 'gift_name'), 'giftCode[]', 'form-control p-0 m-0 isGiftList'); ?>
                                    <input type="text" class="form-control" name="giftDesc[]" value="" placeholder="Description" disabled="true"/>
                                </td>
                                <td>(RM)</td>
                                <td><input type="number" class="form-control text-right isGiftAmount" name="giftAmount[]" value="0.00" step=".01"  disabled="true"/></td>
                                <td></td>
                                <td class="text-right">
                                    <?= MyFormatter::asDecimal2($lastRecordGift['amount']) ?>
                                </td>
                                <td class="pl-2">
                                    <?= $lastRecordGift['giftCode']['gift_name'] . ' - ' . $lastRecordGift['description'] ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <a class="pl-3" href="javascript:" id="btn_addGift"><i class="fas fa-plus-circle text-primary fa-lg" title="Add New Row" ></i></a>
                </fieldset>

                <input type="text" name="nextStaffId" id="nextStaffId" value="" style="display:none"/>

                <div class="form-group pt-3">
                    <?php
                    echo Html::a('Save', 'javascript:submit("")', ['class' => 'btn btn-success mr-2']);
                    echo $nextUser['id'] ? Html::a('Save & Next (' . $nextUser['staff_id'] . ' ' . $nextUser['fullname'] . ')', 'javascript:submit("' . $nextUser['id'] . '")', ['class' => 'btn btn-success']) : Html::a('(LAST STAFF ID)', '', ['class' => 'btn btn-success disabled']);
                    ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>



</div>
<script>
    $(function () {
        initiateEvents();
        initiateGiftEvents();

        // Row increment in Commission
        var wrapperCommission = $("#table_Comm");
        var templateCommission = '<tr><td class="firstCol"><?= Html::textInput('commDesc[]', '', ['class' => 'form-control isCommDesc', 'placeholder' => 'Description']) ?></td><td class="secondCol">(RM) (+)</td><td>'
                + '<?= Html::textInput('commAmount[]', '0.00', ['class' => 'form-control text-right countNet', 'type' => 'number', 'step' => '.01', 'disabled' => 'true']) ?></td></tr>';
        $("#btn_addComm").click(function (e) {
            e.preventDefault();
            $(wrapperCommission).append(templateCommission); //add input box
            initiateEvents();
        });

        // Row increment in Advance
        var wrapper = $("#table_Adv");
        var template = '<tr><td class="firstCol"><?= Html::textInput('advDesc[]', '', ['class' => 'form-control isAdvDesc', 'placeholder' => 'Description']) ?></td><td class="secondCol">(RM) (-)</td><td>'
                + '<?= Html::textInput('advAmount[]', '0.00', ['class' => 'form-control text-right countNet isNegative', 'type' => 'number', 'step' => '.01', 'disabled' => 'true']) ?></td></tr>';
        $("#btn_addAdv").click(function (e) {
            e.preventDefault();
            $(wrapper).append(template); //add input box
            initiateEvents();
        });

        // Row increment in Gift
        var wrapperGift = $("#tableGift");
<?php $str = MyCommonFunction::myDropDown(ArrayHelper::map($giftList, 'code', 'gift_name'), 'giftCode[]', 'form-control p-0 m-0 isGiftList'); ?>
        var templateGift = '<tr><td><?= $str ?>' +
                '<input type="text" class="form-control" name="giftDesc[]" value="" placeholder="Description"  disabled="true"></td><td>(RM)</td>' +
                '<td><input type="number" class="form-control text-right isGiftAmount" name="giftAmount[]" value="0.00" step=".01"  disabled="true"></td><td colspan="3"></td></tr>';
        $("#btn_addGift").click(function (e) {
            e.preventDefault();
            $(wrapperGift).append(templateGift);
            initiateGiftEvents();
        });
    });

    function initiateEvents() {
        // format all numbers to 2 decimals
        $(".text-right").change(function () {
            var to2 = ((parseFloat($(this).val()) || 0)).toFixed(2);
            $(this).val(to2);
        });

        // Calculate Net Payable Salary
        $(".countNet").change(function () {
            calculateNet();
        });

        $(".isAdvDesc").change(function () {
            checkIfAdvDescInserted($(this));
        });

        $(".isCommDesc").change(function () {
            checkIfCommDescInserted($(this));
        });
    }

    function initiateGiftEvents() {
        // format all numbers to 2 decimals
        $(".text-right").change(function () {
            var to2 = ((parseFloat($(this).val()) || 0)).toFixed(2);
            $(this).val(to2);
        });

        $(".isGiftList").change(function () {
            checkIfGiftSelected($(this));
        });

    }

    function calculateNet() {
        var total = 0;
        $(".countNet").each(function () {
            if ($(this).hasClass('isNegative')) {
                total -= (parseFloat($(this).val()) || 0);
            } else {
                total += (parseFloat($(this).val()) || 0);
            }
        });
        $("#net_salary").val(total.toFixed(2));
    }

    function submit(nextStaffId) {
        $("#nextStaffId").val(nextStaffId);

        if ($("#hrpayslip-pay_period").val() == "") {
            alert('Pay Period cannot be blank.');
            $("#hrpayslip-pay_period").focus();
            return;
        } else if ($("#net_salary").val() == "") {
            alert('Net Salary Payable cannot be blank.');
            $("#net_salary").focus();
            return;
        }

        var ans = confirm("Save?");
        if (ans) {
            $("#hr-payslip-form").submit();
        }
        // Disable submit button after clicked
        $(document).on('beforeSubmit', 'form', function (event) {
            $(".btn-success").attr('disabled', true).addClass('disabled');
        });

    }

    /* Control Gift Field
     * if not selected, then disable the rest
     */
    function checkIfGiftSelected(ele) {
        var parent = ele.parent();
        var inputAmt = parent.parent().find('input[name ="giftAmount[]"]');
        var inputDesc = parent.find('input[name ="giftDesc[]"]');
        if (ele.val() !== "") {
            inputAmt.attr('disabled', false);
            inputDesc.attr('disabled', false);
        } else {
            inputAmt.val('0.00').attr('disabled', true);
            inputDesc.val('').attr('disabled', true);
        }
    }

    /* Control Advance Description
     * if not entered, then disable the rest
     */
    function checkIfAdvDescInserted(ele) {//advDesc
        var inputAmt = ele.parent().parent().find('input[name ="advAmount[]"]');
        if (ele.val() !== "") {
            inputAmt.attr('disabled', false);
        } else {
            inputAmt.val('0.00').attr('disabled', true);
        }
    }

    /* Control Commission Description
     * if not entered, then disable the rest
     */
    function checkIfCommDescInserted(ele) {//advDesc
        var inputAmt = ele.parent().parent().find('input[name ="commAmount[]"]');
        if (ele.val() !== "") {
            inputAmt.attr('disabled', false);
        } else {
            inputAmt.val('0.00').attr('disabled', true);
        }
    }

</script>