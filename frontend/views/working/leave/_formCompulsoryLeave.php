<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;

$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
$this->params['breadcrumbs'][] = ['label' => 'Compulsory Leave', 'url' => '/working/leavemgmt/hr-compulsory-leave'];
$this->params['breadcrumbs'][] = 'Schedule Compulsory Leave';
?>

<style>
    .table-sm tbody tr td,
    .table-sm tbody tr th,
    .table-sm thead tr th {
        padding: 0.25rem;
        font-size: 0.85rem;
    }

    .scrollable-table {
        max-height: 900px;
        overflow-y: auto;
    }

    .thead-fixed {
        position: sticky;
        top: 0;
        background-color: #fff;
        z-index: 1;
    }

</style>

<div class="leave-master-compulsory">
    <h3 class="m-4 p-1">Schedule Compulsory Leave</h3>
    <div class="row m-3">
        <div class="float-right col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Staff</legend>
                <?php $form = ActiveForm::begin(['id' => 'userSelectionForm', 'method' => 'post']); ?>
                <label for="nameFilter">Search by Name:</label>
                <div class="row mb-2">
                    <div class="col-sm-12 col-md-10">
                        <div class="input-group">
                            <input class="form-control" id="nameFilter" type="text" placeholder="Search.."/>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="clearFilter" title="Clear Text">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-2">
                        <button type="button" class="btn btn-success col-12" id="check-all-button" title="Check/Uncheck All"><i class="fas fa-check"></i> All</i></button>
                    </div>
                </div>
                    <div class="scrollable-table">
                        <table class="table table-striped table-bordered table-sm" name="myList">
                            <thead class="thead-fixed">
                                <tr>
                                    <th style="width: 20%;">Staff ID</th>
                                    <th style="width: 75%;">Name</th>
                                    <th class="text-center" style="width: 5%;"><input type="checkbox" class="check-all"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($staffList as $staff): ?>
                                    <tr>
                                        <td><?= Html::encode($staff['staff_id']) ?></td>
                                        <td><?= Html::encode(ucwords(strtolower($staff['fullname']))) ?></td>
                                        <td class="text-center">
                                            <?=
                                            Html::checkbox("selectedUsers[]", in_array($staff['id'], array_column($cDetails, 'user_id')),
                                                    ['value' => $staff['id'],])
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

            </fieldset>
        </div>
        <div class="col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Leave Info</legend>
                <div class="row mb-3">
                    <div class="col-12">
                        <?= $form->field($model, 'requestor_remark')->textarea(['rows' => 4]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <?=
                        MyCommonFunction::activeFormDateInput($form, $model, 'start_date', 'Start Date');
                        ?>
                    </div>
                    <div class="col-6">
                        <?=
                        MyCommonFunction::activeFormDateInput($form, $model, 'end_date', 'End Date');
                        ?>
                    </div>
                </div>


                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-success float-right', 'name' => 'submit-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
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
            $("#nameFilter").val("");
            searchName("");
        });

        function searchName(value) {
            $(".scrollable-table tbody tr").filter(function () {
                var nameColumn = $(this).find("td:eq(1)");
                var nameText = nameColumn.text().toLowerCase();
                $(this).toggle(nameText.indexOf(value) > -1);
            });
        }

        $(".check-all").change(function () {
            var isChecked = $(this).prop("checked");
            $(this).closest("table").find("tbody input[type='checkbox']").prop("checked", isChecked);
        });

        let toggleState = false;
        function toggleCheckboxes() {
            const checkboxes = $(".scrollable-table tbody input[type='checkbox']");
            checkboxes.prop("checked", toggleState);
        }

        $("#check-all-button").click(function () {
            toggleState = !toggleState;
            toggleCheckboxes();
        });

    });
</script>