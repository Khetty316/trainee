<?php
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use yii\helpers\Html;

//$this->title = 'Leave Entitlement';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$shortMonthName = date("M", mktime(0, 0, 0, $selectMonth, 10))
?>

<div class="leave-master-index">
    <?php //= $this->render('__hrLeaveNavBar', ['title' => $this->title]) ?>
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '7']) ?>


<!--<p class="font-weight-lighter text-success">Staffs' leave summary.</p>-->


    <?php
    $form2 = ActiveForm::begin([
                'method' => 'post',
                'options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data'],
                'id' => 'myForm',
                'action' => '/working/leavemgmt/hr-batch-update-leave-entitlement'
    ]);
    ?>
    <div id='divCanUpdate' class='hidden'>
        <?= Html::submitButton('Update Changes', ['class' => 'btn btn-success btn-sm', 'id' => 'submitBtn']) ?>
    </div>
    <div id='divCannotUpdate' class='hidden'>
        <h5 class="font-weight-bolder p-0 m-2 text-danger"><?= Html::button('Update Changes', ['class' => 'btn btn-success btn-sm', 'disabled' => true]) ?> NO CHANGES</h5>
    </div>
    <h5 class="font-weight-bolder p-0 m-2 text-danger">Year: <?= $excelResult[0] ? $excelResult[0]['year'] : "" ?></h5>
    <div class="view">
        <div class="wrapper">
            <table class="table table-sm table-bordered table-striped ">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th class="text-center align-middle">Staff ID</th>
                        <th class="text-center align-middle br">Name</th>
                        <th class="text-center align-middle bl">Brought Forward<br/>(From Last Year)</th>
                        <th class="text-center align-middle">Annual Leave<br/>Yearly Entitlement</th>
                        <th class="text-center align-middle bl">Sick Leave<br/>Yearly Entitlement</th>
                        <th class="text-center align-middle bl">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $idx = 0;
                    $canSubmit = false;
                    foreach ($excelResult as $key => $LeaveEntitlementExcelModel) {
                        $haveEdit = false;
                        $originalEntitle = $LeaveEntitlementExcelModel->leaveEntitlementBean;
                        ?>
                        <tr>
                            <td><?= ++$idx ?></td>
                            <td><?= $LeaveEntitlementExcelModel->userBean->staff_id ?></td>    
                            <td><?= $LeaveEntitlementExcelModel->userBean->fullname ?></td>
                            <td class="text-right">
                                <div class="">
                                    <?php
                                    if ($LeaveEntitlementExcelModel->daysBroughtForward == "") {
                                        $originalEntitle['annual_bring_forward_days'] = 0;
                                    }

                                    if ($originalEntitle['annual_bring_forward_days'] != $LeaveEntitlementExcelModel->daysBroughtForward) {
                                        echo $originalEntitle['annual_bring_forward_days']
                                        . " => "
                                        . Html::textInput("newDaysBroughtForward[$idx]", $LeaveEntitlementExcelModel->daysBroughtForward, ['size' => 1, 'class' => 'text-right']);
                                        $haveEdit = true;
                                    } else {
                                        echo $originalEntitle['annual_bring_forward_days'];
                                        echo Html::hiddenInput("newDaysBroughtForward[$idx]");
                                    }
                                    ?> 
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="">
                                    <?php
                                    if ($LeaveEntitlementExcelModel->daysAnnual == "") {
                                        $originalEntitle['annual_entitled'] = 0;
                                    }
                                    if ($originalEntitle['annual_entitled'] != $LeaveEntitlementExcelModel->daysAnnual) {
                                        echo $originalEntitle['annual_entitled']
                                        . " => "
                                        . Html::textInput("newDaysAnnual[$idx]", $LeaveEntitlementExcelModel->daysAnnual, ['size' => 1, 'class' => 'text-right']);
                                        $haveEdit = true;
                                    } else {
                                        echo $originalEntitle['annual_entitled'] ?? null;
                                        echo Html::hiddenInput("newDaysAnnual[$idx]");
                                    }
                                    ?> 
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="">
                                    <?php
                                    if ($LeaveEntitlementExcelModel->daysSick == "") {
                                        $originalEntitle['sick_entitled'] = 0;
                                    }
                                    if ($originalEntitle['sick_entitled'] != $LeaveEntitlementExcelModel->daysSick) {
                                        echo $originalEntitle['sick_entitled']
                                        . " => "
                                        . Html::textInput("newDaysSick[$idx]", $LeaveEntitlementExcelModel->daysSick, ['size' => 1, 'class' => 'text-right']);
                                        $haveEdit = true;
                                    } else {
                                        echo $originalEntitle['sick_entitled'] ?? null;
                                        echo Html::hiddenInput("newDaysSick[$idx]");
                                    }
                                    ?> 
                                </div>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($haveEdit) {
                                    echo Html::hiddenInput("action[$idx]", $LeaveEntitlementExcelModel->processMethod);
                                    echo Html::hiddenInput("leaveEntitleId[$idx]", $originalEntitle['id']);
                                    echo Html::hiddenInput("userId[$idx]", $LeaveEntitlementExcelModel->userBean->id);
                                    echo Html::hiddenInput("year[$idx]", $LeaveEntitlementExcelModel['year']);
                                    echo ucfirst($LeaveEntitlementExcelModel->processMethod);
                                } else {
                                    echo "No Changes";
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        if ($haveEdit) {
                            $canSubmit = true;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>

    $(function () {
<?php
if ($canSubmit) {
    ?>
            $("#divCannotUpdate").remove();
            $("#divCanUpdate").show();
    <?php
} else {
    ?>
            $("#divCanUpdate").remove();
            $("#divCannotUpdate").show();
    <?php
}
?>
    });


</script>
