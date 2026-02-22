<?php
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;

//$this->title = 'Leave Entitlement';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$shortMonthName = date("M", mktime(0, 0, 0, $selectMonth, 10))
?>

<link rel="stylesheet" href="/css/jquery.dataTables.min.css">
<script src="/js/jquery.dataTables.min.js"></script>
<style>
    .view .my-2 fieldset{
        overflow: auto;
    }

</style>

<div class="leave-master-index">
    <?php //= $this->render('__hrLeaveNavBar', ['title' => $this->title]) ?>
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '7']) ?>
    <div class="form-row">
        <?php
        $form = ActiveForm::begin([
                    'method' => 'get',
                    'options' => ['autocomplete' => 'off'],
                    'id' => 'myCurrentForm',
                    'action' => '/working/leavemgmt/hr-leave-entitlement'
        ]);
        ?> 
        <div class="form-row mx-1">
            <div class='form-group'>
                <?= MyCommonFunction::myDropDownNoEmpty($yearsList, 'selectYear', 'form-control', 'selectYear', $selectYear) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?> 
        <div >
            <?php
            if ($selectYear <= date("Y") && $noEntitlementNextYear) {
                echo Html::a('Generate ' . ($selectYear + 1) . ' Leave Entitlement',
                        "bulk-entitlement-generation?nextYear=" . ($selectYear + 1), ['class' => 'btn btn-success btn-md',
                    'title' => "Bring forward entitlement to next year.",
                    'data-confirm' => "Are you sure to generate " . ($selectYear + 1) . "'s Leave Entitlement?"]);
            }
            ?>
        </div>
    </div>
    <div class="view">
        <div class="wrapper">
            <?php if (!empty($noEntitlementUsers)) { ?>
                <div class = "my-2">
                    <fieldset class = "form-group border-dark border p-3" style = "position:relative;">
                        <legend class = "w-auto px-2 m-0 text-uppercase">Staff without entitlement</legend>
                        <div class="mb-2">
                            <?=
                            Html::a('Generate ' . ($selectYear) . ' Leave Entitlement from ' . ($selectYear - 1),
                                    "bulk-entitlement-generation?nextYear=" . ($selectYear) . "&statusChange=true", ['class' => 'btn btn-success btn-md',
                                'title' => "Bring forward " . ($selectYear - 1) . " entitlement to $selectYear. for these users",
                                'data-confirm' => "Are you sure to generate " . ($selectYear) . "'s Leave Entitlement?"]);
                            ?>
                        </div>
                        <table class = "table table-sm table-bordered table-striped table-hover" id = "searchTable1">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Fullname</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($noEntitlementUsers as $key => $value) {
                                    ?>
                                    <tr>
                                        <td> <?= $value["staff_id"] ?>  </td>
                                        <td><?= ucwords(strtolower($value["fullname"])) ?> </td>
                                        <td class='text-center'>
                                            <?=
                                            Html::button('No Record. Click here to add ',
                                                    ['value' => Url::to(['add-entitlement-to-user', 'id' => $value["id"], 'selectYear' => $selectYear]),
                                                        'class' => 'btn btn-success btn-sm modalButton',
                                                        'title' => "Add new record for leave entitlement",
                                                        'data-modaltitle' => "Add Leave Entitlement"
                                                    ])
                                            ?>
                                        </td>
                                    </tr>
                                <?php }
                                ?>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
            <?php }
            ?>
            <div class="my-2">
                <fieldset class="form-group border-dark border p-3" style="position:relative;">
                    <legend class="w-auto px-2 m-0 text-uppercase">Leave Entitlement <?= $selectYear ?></legend>
                    <table class="table table-sm table-bordered table-striped table-hover" id="searchTable">
                        <thead class="thead-light">
                            <tr>
                                <?php
                                foreach ($headers as $header) {
                                    print ($header == "" ? "" : "<th>" . $header ?? null . "</th>");
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($contentDatas as $contentData) {
                                ?><tr>
                                    <td class="align-middle"><?= $contentData["staff_id"] ?></td>
                                    <td class="align-middle"><?= ucwords(strtolower($contentData["fullname"])) ?></td>
                                    <td class="align-middle text-center" style ='width:20%;'><?=
                                        Html::button(number_format($contentData["annual_rollover"], 1),
                                                ['value' => Url::to(['edit-annual-rollover', 'id' => $contentData["user_id"], 'selectYear' => $selectYear]),
                                                    'class' => 'btn btn-sm btn-link modalButtonSmall',
                                                    'title' => "Click to update value.",
                                                    'data-modaltitle' => "Edit Annual Rollover"
                                                ])
                                        ?></td>
                                    <?php
                                    foreach ($contentData['leaveTypes'] as $type) {
                                        ?>
                                        <td class="align-middle text-center" style ='width:20%;'>
                                            <?php
                                            if (is_numeric($type["leaveDays"])) {
                                                echo Html::button(number_format($type["leaveDays"], 1),
                                                        ['value' => Url::to(['edit-entitlement-cell',
                                                                'id' => $type['detailId'],
                                                                'entitleId' => $contentData["entitle_id"],
                                                                'leaveTypeCode' => $type['leaveType'],
                                                                'year' => $selectYear]),
                                                            'class' => 'btn btn-sm btn-link modalButtonSmall',
                                                            'title' => "Click to update value.",
                                                            'data-modaltitle' => "Edit Entitlement Details"
                                                ]);
                                            } else {
                                                echo Html::a($type["leaveDays"], ['entitlement-detail-adjustment', 'id' => $type['detailId']], [
                                                    'class' => 'btn btn-sm btn-link text-center',
                                                    'title' => "Click to update value."
                                                ]);
                                            }
                                            ?>
                                        </td>
                                        <?php
                                    }
                                    print "<td class='align-middle text-initial' style ='width:13%;'><span>";

                                    print Html::button('Edit <i class="far fa-edit"></i>',
                                                    ['value' => Url::to(['edit-user-entitlement', 'id' => $contentData['entitle_id']]),
                                                        'class' => 'btn btn-success btn-sm modalButton mb-1',
                                                        'title' => "Edit record for leave entitlement",
                                                        'data-modaltitle' => "Edit Leave Entitlement"
                                    ]);
                                    print "</span>  <span>";
                                    if ($contentData['annual_bring_next_year_days'] == null && $contentData['staff_active_status'] == User::STATUS_ACTIVE) {
                                        print Html::button('Forward <i class="fas fa-arrow-right"></i>',
                                                        ['value' => Url::to(['add-entitlement-to-user', 'id' => $contentData["user_id"], 'selectYear' => $selectYear + 1]),
                                                            'class' => 'btn btn-success btn-sm modalButton mb-1',
                                                            'title' => "Add new record for leave entitlement",
                                                            'data-modaltitle' => "Bring Forward"
                                        ]);
                                    } else {
                                        if ($contentData['staff_active_status'] != User::STATUS_ACTIVE) {
                                            print "Inactive User";
                                        } else if ($contentData['annual_bring_next_year_days'] != null) {
                                            print "Brought Forward";
                                        }
                                    }
                                    print "</span></td>";
                                    ?>

                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>
</div>



<script>

    $(function () {

        $("#selectYear").change(function () {
            $(".view").hide();
            $("#myCurrentForm").submit();
        });

        $('#searchTable').DataTable({
            searching: false,
            info: false,
            paging: false
        });
        initiateFilterTable($("#searchTable"));

        $('#searchTable1').DataTable({
            searching: false,
            info: false,
            paging: false
        });
        initiateFilterTable($("#searchTable1"));
    });

    window.onbeforeunload = function () {
        var currentYOffset = window.pageYOffset;  // save current page postion.
        setCookie('jumpToScrollPostion', currentYOffset, 2);
    };

    $(function () {
        $(document).on('beforeSubmit', 'form', function (event) {
            $(".submitBtn").attr('disabled', true).addClass('disabled');
            $(".submitBtn").html('Submitting...');
            var currentYOffset = window.pageYOffset;  // save current page postion.
            setCookie('jumpToScrollPostion', currentYOffset, 2);
        });


        // check if we should jump to postion.
        var jumpTo = getCookie('jumpToScrollPostion');
        if (jumpTo !== "undefined" && jumpTo !== null) {
            window.scrollTo(0, jumpTo);
            eraseCookie('jumpToScrollPostion');  // and delete cookie so we don't jump again.
        }

    });

</script>