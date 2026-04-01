<?php

use yii\widgets\DetailView;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

if(MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin])){
    $label = 'Leave Approval (Director)';
    $url = '/working/leavemgmt/director-compulsory-leave';
}else if(MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior, AuthItem::ROLE_SystemAdmin])){
    $label = 'HR - Leave Management';
    $url = '/working/leavemgmt/hr-compulsory-leave';
}

$this->params['breadcrumbs'][] = ['label' => $label];
$this->params['breadcrumbs'][] = ['label' => 'Compulsory Leave', 'url' => $url];
$this->params['breadcrumbs'][] = 'View Compulsory Leave';
?>

<style>
    .scrollable-table {
        max-height: 60vh;
        overflow-y: auto;
    }
</style>

<div class="leave-master-compulsory">
    <h2 class="m-3">Compulsory Leave Details</h2>
    <div class="row m-1">
        <div class="col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Leave Info</legend>
                <?=
                $this->render('/working/leave/__detailviewCompulsory', [
                    'model' => $model,
                    'all' => true,
                ])
                ?>
            </fieldset>
        </div>
        <div class="col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Staff</legend>
                <label for="nameFilter">Search by Name:</label>
                <div class="input-group mb-3">
                    <input class="form-control" id="nameFilter" type="text" placeholder="Search.."/>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="clearFilter" title="Clear Text">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="scrollable-table">
                    <table class="table table-striped table-bordered table-sm filter-table">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Staff ID</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
<!--                            <tr>
                                <th colspan="2">Production Staff</th>
                            </tr>-->
                            <?php //foreach ($prods as $prod): ?>
<!--                                <tr>
                                    <td><?php //= Html::encode($prod['staff_id']) ?></td>
                                    <td><?php //= Html::encode(ucwords(strtolower($prod['fullname']))) ?></td>
                                </tr>-->
                            <?php //endforeach; ?>
<!--                            <tr>
                                <th colspan="2">Executive Staff</th>
                            </tr>-->
                            <?php //foreach ($execs as $exec): ?>
<!--                                <tr>
                                    <td><?php //= Html::encode($exec['staff_id']) ?></td>
                                    <td><?php //= Html::encode(ucwords(strtolower($exec['fullname']))) ?></td>
                                </tr>-->
                            <?php //endforeach; ?>
<!--                            <tr>
                                <th colspan="2">Office Staff</th>
                            </tr>-->
                            <?php //foreach ($offices as $office): ?>
<!--                                <tr>
                                    <td><?php //= Html::encode($office['staff_id']) ?></td>
                                    <td><?php //= Html::encode(ucwords(strtolower($office['fullname']))) ?></td>
                                </tr>-->
                            <?php //endforeach; ?>
                                <?php foreach ($cDetails as $staff): 
                                $staff = common\models\User::findOne($staff['user_id']); ?>
                                <tr>
                                    <td><?= Html::encode($staff->staff_id) ?></td>
                                    <td><?= Html::encode(ucwords(strtolower($staff->fullname))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#nameFilter").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            searchName(value);
        });

        $("#clearFilter").click(function () {
            $("#nameFilter").val(""); // Clear the input value
            searchName("");
        });

        function searchName(value) {
            $(".filter-table tbody tr").filter(function () {
                var nameColumn = $(this).find("td:eq(1)"); // Assuming name is in the third column (index 2)
                var nameText = nameColumn.text().toLowerCase();
                $(this).toggle(nameText.indexOf(value) > -1);
            });
        }

    });
</script>