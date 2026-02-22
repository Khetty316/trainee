<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Monthly Summary';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
//
?>
<style>
    .view {
        margin: auto;
    }
    .wrapper{
        position: relative;
        overflow: auto;
        white-space: nowrap;
    }
    table{
        overflow: hidden;
    }
    td, th{
        font-weight: normal ;
        position: relative;
    }
    .table thead{
        background-color: #e9ecef;
        color: #495057;
    }
    .table thead th{
        padding:3px !important;
        text-align: center;
        font-weight: bold;
    }
    .verticaltext span{
        padding-bottom: 0px ;
        max-width: 30px;
        writing-mode: vertical-rl;
        transform: rotate(180deg);
    }
    .table td{
        vertical-align: middle;
        text-align: center;
    }
    table .num0, table .num4, table .num8, table .num12,table .num16,
    table .num20, table .num24, table .num28, table .num32, table .num36,
    table .num40, table .num44{
        vertical-align: middle;
        border-left-width: 10px;
        border-left-color: #e9ecef;
    }
    tbody tr:hover td, tbody tr:hover th {
        background-color: rgba(0,162,226,0.5) !important;
    }
    tbody td:hover::after {
        content: '';
        height: 700vh;
        left: 0;
        position: absolute;
        top: -350vh;
        width: 100%;
        z-index: -1;
    }
    td:hover::after{
        background-color: rgba(0,162,226,0.5) !important;

    }

</style>

<div class="leave-master-index">
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '4']) ?>

    <p class="font-weight-lighter text-success">Staffs' leave summary. For user Active account with Staff No, Normal Staff (Role)</p>

    <?php
//    $yearList/
    $year = Yii::$app->request->get('year');
    $formShow = ActiveForm::begin([
                'method' => 'get',
    ]);
    echo '<div class="form-group row">';
    echo MyCommonFunction::myDropDownNoEmpty($yearList, 'year', 'form-control m-0 ml-3 col-sm-2', '', $year);
    echo Html::submitButton('Show', ['class' => 'btn btn-primary ml-3 ']);
    echo '</div>';
    Activeform::end();
    ?>
</div>

<div class="view">
    <div class="wrapper">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <td class="text-center align-bottom font-weight-bold" rowspan="2">Staff ID</td>
                    <td class="text-center align-bottom font-weight-bold" rowspan="2">Staff Name</td>
                    <?php
                    $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                    foreach ($months as $month) {
                        ?>
                        <th colspan="4"><?= $month ?></th>
                    <?php }
                    ?>
                </tr>
                <tr><?php
                    $count = 0;
                    foreach ($months as $month) {
                        ?>
                        <th class="verticaltext"><span>Annual</span></th>
                        <th class="verticaltext"><span>Unpaid</span></th>
                        <th class="verticaltext"><span>Sick</span></th>
                        <th class="verticaltext"><span>Others</span></th>
                    <?php } ?>

                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($leaveSummarys as $leaveSummary) {
                    $count = 0;
                    ?><tr>
                        <th><?= $leaveSummary["staffid"] ?></th>
                        <th><?= $leaveSummary["fullname"] ?></th>
                        <?php
                        foreach ($intMonth as $month) {

                            foreach ($leaveTypes as $leaveType) {
                                ?><td class="num<?= $count ?>"><?= $leaveSummary[$month][$leaveType] ?></td><?php
                                $count++;
                            }
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>

            </tbody>
        </table>
    </div>
</div>

</div>