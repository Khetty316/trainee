<?php
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use yii\helpers\Html;

//$this->title = 'Annual Summary';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
$shortMonthName = date("M", mktime(0, 0, 0, $selectMonth, 10))
?>

<div class="leave-master-index">
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '5']) ?>

    <p class="font-weight-lighter text-success">Staffs' leave summary.</p>

    <p>

        <?php // = Html::a('Send Doc <i class="far fa-paper-plane"></i>', '?', ['class' => 'btn btn-success'])  ?>

        <?php
        $year = (new \yii\db\Query())
                ->select(['min(`leave_confirm_year`) as minYear'])
                ->from('leave_detail_breakdown')
                ->one();
        $minYear = $year['minYear'] ? $year['minYear'] : date("Y");
        $yearsList = [];

        for ($i = date("Y"); $i >= $minYear; $i--) {
            $yearsList[$i] = "Year $i";
        }
        ?>
    </p>
    <?php
    $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['autocomplete' => 'off'],
                'id' => 'myForm',
                'action' => '/working/leavemgmt/hr-final-leave-summary'
    ]);
    ?> 
    <div class="form-group row">
        <?php
        echo MyCommonFunction::myDropDownNoEmpty($yearsList, 'selectYear', 'form-control p-0 m-0 ml-3 col-sm-2', 'selectYear', $selectYear);
        echo Html::submitButton(
                'Show Summary ',
                [
                    'class' => 'btn btn-success ml-3 ',
                ]
        );
        ?>    
    </div>
    <?php
    ActiveForm::end();
    ?>

    <h2>Year <?= $selectYear ?></h2>
    <div class="view">
        <div class="wrapper">
            <table class="table table-sm table-bordered table-striped ">
                <thead class="thead-light">
                    <tr>
                        <th></th>
                        <th colspan="2" class="br"></th>
                        <th colspan="6" class="text-center br bl">Annual Leave (Days)</th>
                        <th colspan="3" class="text-center bl">Sick Leave (Days)</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th class="text-center align-middle">Staff ID</th>
                        <th class="text-center align-middle br">Name</th>
                        <th class="text-center align-middle bl">Brought Forward<br/>(From Last Year)</th>
                        <th class="text-center align-middle">Entitlement (<?= $selectYear ?>)</th>
                        <th class="text-center align-middle">Available<br/>(Up to <?= $shortMonthName . ' ' . $selectYear ?><br/>+<br/>Carried Forward)</th>
                        <th class="text-center align-middle">Leave Taken<br/>/<br/>Confirmed</th>
                        <th class="text-center align-middle">Leave Balance<br/>(Up to <?= $shortMonthName . ' ' . $selectYear ?>)</th>
                        <th class="text-center align-middle br">Annual Leave Balance<br/>(as at 31-12-<?= $selectYear ?>)</th>
                        <th class="text-center align-middle bl">Entitlement</th>
                        <th class="text-center align-middle">Leave Taken<br/>/<br/>Confirmed</th>
                        <th class="text-center align-middle">Leave Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $idx = 0;
                    foreach ($userList as $key => $user) {
                        ?>
                        <tr>
                            <td><?= ++$idx ?></td>
                            <td><?= $user['staff_id'] ?></td>
                            <td class="br"><?= ucwords(strtolower($user['fullname'])) ?></td>
                            <td class="bl text-right"><?= number_format($user['annual_bringForward'], 1) ?></td>
                            <td class="text-right"><?= number_format($user['annual_entitlementYearEnd'], 1) ?></td>
                            <td class="text-right"><?= number_format(($user['annual_entitlementCurrent'] + $user['annual_bringForward']), 1) ?></td>
                            <td class="text-right"><?= number_format($user['annual_approved'], 1) ?></td>
                            <td class="text-right"><?= number_format($user['annual_balanceCurrent'], 1) ?></td>
                            <td class="br text-right"><?= number_format($user['annual_balanceYearEnd'], 1) ?></td>
                            <td class="bl text-right"><?= number_format($user['sick_entitlementYearEnd'], 1) ?></td>
                            <td class="text-right"><?= number_format($user['sick_approved'], 1) ?></td>
                            <td class="text-right"><?= number_format($user['sick_balanceCurrentCanApply'], 1) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>