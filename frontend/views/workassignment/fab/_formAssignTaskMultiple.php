<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<style>
    .staff-row {
        opacity: 1;
        transform: translateY(0);
        transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .staff-row.animate-in {
        opacity: 0;
        transform: translateY(-10px);
    }

    .staff-row.animate-out {
        opacity: 0;
        transform: translateY(10px);
    }

    .accordion-icon {
        transition: transform 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
    }

    .accordion-icon.rotated {
        transform: rotate(180deg);
    }

    .task-accordion-header {
        position: relative;
        overflow: hidden;
        transition: background-color 0.2s ease;
    }

    .task-accordion-header:hover {
        background-color: #17a2b8 !important;
    }

    .task-accordion-header::after {
        content: '';
        position: absolute;
        top: 50%;
        left: -100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s ease;
    }

    .task-accordion-header.active::after {
        left: 100%;
    }

    /* Add smooth border transition */
    .staff-row {
        border-top: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1), border-color 0.2s ease;
    }

    .staff-row:first-of-type {
        border-top: 1px solid #dee2e6;
    }
</style>
<div class="work-assignment-master-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'assignForm',
        'options' => ['autocomplete' => 'off']
    ]);
    ?>

    <div class="form-row" style="height: 100%">
        <div class='col-md-7 col-sm-12'>
            <div class="form-row">
                <div class="col-sm-12">
                    <?php
                    echo $this->render("/projectproduction/main/_detailViewProjProdDetail", [
                        'projProdMaster' => $project,
                        'panel' => $panel
                    ]);
                    ?>
                </div>
            </div>
            <div class="form-row">
                <div class="col-12">
                    <fieldset class="border p-1">
                        <legend class="w-auto px-2 m-0">Task Assigning Detail:</legend>
                        <div class="row">
                            <div class="col-md-5 col-sm-12">
                                <div class="col-12">
                                    <?php
//                                    echo $form->field($model, 'tempTaskName')->textInput(['disabled' => true])->label("Task");
                                    ?>
                                </div>
                                <div class="col-12">
                                    <?php
                                    ?>
                                </div>
                                <div class="col-12">
                                    <?php
                                    echo $form->field($model, 'startDate')->widget(yii\jui\DatePicker::className(),
                                            ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy']
                                                , 'dateFormat' => 'dd/MM/yyyy'])
                                    ?>
                                </div>
                                <?php // if (!$model->isNewRecord) {  ?>
                                <div class="col-12">
                                    <?php
//                                        echo $form->field($model, 'complete_date')->widget(yii\jui\DatePicker::className(),
//                                                ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy']
//                                                    , 'dateFormat' => 'dd/MM/yyyy'])
                                    ?>
                                </div>
                                <?php // }  ?>
                                <div class="col-12">
                                    <?= $form->field($model, 'comments')->textarea(['rows' => 8]) ?>
                                </div>
                            </div>
                            <div class="col-md-7 col-sm-12">
                                <table class="table table-sm table-stripped table-bordered" id="taskList">
                                    <thead>
                                        <tr>
                                            <th>Task name:</th>
                                            <th class="tdnowrap" >
                                                <?= Html::checkbox("", false, ['class' => 'form-control form-control-sm', 'id' => 'taskCheckBoxAll', 'style' => 'width:20px']) ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ((array) $taskList as $key => $task) {
                                            ?>
                                            <tr><td class="p-2">
                                                    <?= Html::encode($task->taskName) ?>
                                                </td>  
                                                <td class="text-center">
                                                    <?php
                                                    if ($task->isVacantTask) {
                                                        echo Html::checkbox('TaskAssignment[taskCode][]', null, ['value' => $task->fab_task_code, 'class' => 'form-control form-control-sm taskCheckBox']);
                                                    } else {
                                                        echo Html::checkbox(null, null, ['class' => 'form-control form-control-sm', 'disabled' => true]);
                                                    }
                                                    ?>
                                                </td></tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <div class='col-md-5 col-sm-12'>
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Staff:</legend>
                <div class="input-group mb-2">
                    <input class="form-control mr-2" id="nameFilter" type="text" placeholder="Search.."/>
                </div>
                <div  style="max-height:73vh;;  overflow:auto" >
                    <table class="table table-sm table-stripped table-bordered" id="myList">
                        <thead>
                            <tr>
                                <th class="col-10">Staff name:</th>
                                <th class="col-0 text-center">Task on Hand</th>
                                <th class="tdnowrap col-0">
                                    <?= Html::checkbox("", false, ['class' => 'form-control form-control-sm', 'id' => 'staffCheckBoxAll', 'style' => 'width:20px']) ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
//                            $assignedStaffs = array_column(\yii\helpers\ArrayHelper::toArray($model->taskAssignFabStaff), 'user_id');
//                            foreach ((array) $staffList as $key => $staff) {
                            ?>
<!--                                <tr>
                                    <td class="p-2">
                            <?php //= Html::encode($staff['fullname']) ?>
                                    </td>  
                                    <td class="text-right px-2">
                            <?php
//                                        if (!empty($staff['totalTaskOnHand']) && $staff['totalTaskOnHand']> 0) {
//                                            echo Html::a(\common\models\myTools\MyFormatter::asDecimal2($staff['totalTaskOnHand']), "javascript:void(0)", [
//                                                "value" => yii\helpers\Url::to('/production/task-assign-ongoing-summary/view-user-ongoing-task?userId=' . $staff['id']),
//                                                "class" => "modalButton m-2"]);
//                                        } else {
//                                            echo "<span class='m-2'>0.00</span>";
//                                        }
                            ?>
                                    </td>  
                                    <td class="text-center col-0">
                            <?php
//                                        echo Html::checkbox('TaskAssignment[staffIds][]', null, ['value' => $staff['id'], 'class' => 'form-control form-control-sm']);
                            ?>
                                    </td>
                                </tr>-->
                            <?php
//                            }
                            ?>
                            <?php
                            $refTaskFab = frontend\models\projectproduction\fabrication\RefProjProdTaskFab::find()
                                    ->orderBy(['sort' => SORT_ASC])
                                    ->all();
                            $staffListWithTaskType = \frontend\models\projectproduction\task\WorkerTaskCategories::find()
                                    ->where(['task_type' => frontend\models\projectproduction\task\TaskAssignment::taskTypeFabrication])
                                    ->all();

                            $staffByTaskCode = [];
                            foreach ($staffListWithTaskType as $taskTypeStaff) {
                                $taskCode = $taskTypeStaff->task_code;
                                $userId = $taskTypeStaff->user_id;

                                if (!isset($staffByTaskCode[$taskCode])) {
                                    $staffByTaskCode[$taskCode] = [];
                                }
                                $staffByTaskCode[$taskCode][] = $userId;
                            }

                            $orderedTaskCodes = [];
                            foreach ($refTaskFab as $taskFab) {
                                if ($taskFab->active_sts == 1) { // Only active tasks
                                    $orderedTaskCodes[] = [
                                        'code' => $taskFab->code,
                                        'name' => $taskFab->name,
                                        'sort' => $taskFab->sort,
                                        'weight' => $taskFab->weight
                                    ];
                                }
                            }

                            // Group staff by ordered task codes
                            foreach ($orderedTaskCodes as $taskInfo) {
                                $taskCode = $taskInfo['code'];
                                $taskName = $taskInfo['name'];
                                $taskSort = $taskInfo['sort'];

                                if (!isset($staffByTaskCode[$taskCode])) {
                                    continue; // Skip if no staff assigned to this task code
                                }

                                // Find staff members assigned to this task code
                                $staffForThisTask = [];
                                foreach ((array) $staffList as $staff) {
                                    if (in_array($staff['id'], $staffByTaskCode[$taskCode])) {
                                        $staffForThisTask[] = $staff;
                                    }
                                }

                                if (empty($staffForThisTask)) {
                                    continue; // Skip if no staff found
                                }
                                ?>
                                <tr class="task-type-header accordion-header" data-task-code="<?= $taskCode ?>">
                                    <td colspan="3" class="bg-info text-white p-2" style="cursor: pointer;">
                                        <i class="fas fa-chevron-down accordion-icon mr-2"></i>
                                        <strong><?= Html::encode($taskName) ?></strong>
                                        <span class="badge badge-warning ml-2"><?= count($staffForThisTask) ?> staff</span>
                                    </td>
                                </tr>
                                <?php foreach ($staffForThisTask as $staff) { ?>
                                    <tr class="staff-row" data-task-code="<?= $taskCode ?>">
                                        <td class="p-2">
                                            <?= Html::encode($staff['fullname']) ?>
                                            <?php if ($staff['status'] == common\models\User::STATUS_INACTIVE) { ?>
                                                <small class="text-danger">(Inactive)</small>
                                            <?php } ?>
                                        </td>  
                                        <td class="text-right px-2">
                                            <?php
                                            if (!empty($staff['totalTaskOnHand']) && $staff['totalTaskOnHand'] > 0) {
                                                echo Html::a(\common\models\myTools\MyFormatter::asDecimal2($staff['totalTaskOnHand']), "javascript:void(0)", [
                                                    "value" => yii\helpers\Url::to('/production/task-assign-ongoing-summary/view-user-ongoing-task?userId=' . $staff['id']),
                                                    "class" => "modalButton m-2"]);
                                            } else {
                                                echo "<span class='m-2'>0.00</span>";
                                            }
                                            ?>
                                        </td>  
                                        <td class="text-center col-0">
                                            <?php
                                            if ($staff['status'] == common\models\User::STATUS_ACTIVE) {
                                                echo Html::checkbox("TaskAssignment[staffIds][$taskCode][]", false, [
                                                    'value' => $staff['id'],
                                                    'class' => 'form-control form-control-sm staffCheckBox'
                                                ]);
                                            }
//                                            echo Html::checkbox('TaskAssignment[staffIds][]', null, ['value' => $staff['id'], 'class' => 'form-control form-control-sm staffCheckBox']);
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
            <?= Html::a("Proceed", "javascript:submitForm()", ['class' => 'btn btn-success float-right mx-2']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(function () {
//        $("#nameFilter").on("keyup", function () {
//            var value = $(this).val().toLowerCase();
//            $("#myList tbody tr").filter(function () {
//                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
//            });
//        });

        $("#nameFilter").on("keyup", function () {
            var value = $(this).val().trim().toLowerCase();

            if (value === "") {
                $(".staff-row").show();
                return;
            }

            $('.accordion-header').each(function () {
                var taskCode = $(this).data('task-code');
                var $staffRows = $('tr.staff-row[data-task-code="' + taskCode + '"]');
                var $icon = $(this).find('.accordion-icon');
                var matchingCount = 0;

                $staffRows.each(function () {
                    // Get only the staff name (first td), remove "(Inactive)" text if present
                    var nameText = $(this).find('td:first').text().trim();
                    nameText = nameText.replace(/\s*\(Inactive\)\s*$/i, '').toLowerCase(); // Remove "(Inactive)" and convert to lowercase

                    // Exact match - only show if the name contains exactly what was typed
                    if (nameText.includes(value.toLowerCase())) {
                        $(this).show();
                        matchingCount++;
                    } else {
                        $(this).hide();
                    }
                });

                // Auto-expand accordion if there are matches
                if (matchingCount > 0) {
                    $staffRows.filter(':visible').show();
                    $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                }

                // Update the staff count badge
                $(this).find('.badge').text(matchingCount + ' staff');
            });
        });

        $("#taskCheckBoxAll").change(function () {
            var isChecked = $(this).prop("checked");
            $(".taskCheckBox").prop("checked", isChecked);
        });
    });

    $(document).ready(function () {
        // Check all functionality
        $("#staffCheckBoxAll").on('change', function () {
            var isChecked = $(this).prop("checked");
            $(".staffCheckBox").prop("checked", isChecked);

            // Trigger change event for any other functionality that depends on individual checkbox changes
            $(".staffCheckBox").trigger('change');
        });

        // Update "check all" status when individual checkboxes change
        $(document).on('change', '.staffCheckBox', function () {
            updateCheckAllStatus();
        });

        function updateCheckAllStatus() {
            var $staffCheckboxes = $(".staffCheckBox");
            var totalCheckboxes = $staffCheckboxes.length;
            var checkedCheckboxes = $staffCheckboxes.filter(':checked').length;

            var $checkAll = $("#staffCheckBoxAll");

            if (checkedCheckboxes === 0) {
                $checkAll.prop("checked", false);
                $checkAll.prop("indeterminate", false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                $checkAll.prop("checked", true);
                $checkAll.prop("indeterminate", false);
            } else {
                $checkAll.prop("checked", false);
                $checkAll.prop("indeterminate", true);
            }
        }

        // Initialize the check all status
        updateCheckAllStatus();
    });

    $(document).ready(function () {
        var isAnimating = false;
        var currentOpenAccordion = null;

        // Initialize - all collapsed
        $('.staff-row').hide();

        // Optional: Open first accordion by default
        $('.accordion-header:first').trigger('click');

        $('.accordion-header').click(function (e) {
            e.preventDefault();

            if (isAnimating)
                return;

            var $header = $(this);
            var taskCode = $header.data('task-code');
            var $staffRows = $('tr.staff-row[data-task-code="' + taskCode + '"]');
            var $icon = $header.find('.accordion-icon');
            var isVisible = $staffRows.first().is(':visible');

            isAnimating = true;

            // Add active class for shine effect
            $header.addClass('active');
            setTimeout(() => $header.removeClass('active'), 500);

            if (currentOpenAccordion && currentOpenAccordion !== taskCode) {
                // close current open first
                var $currentRows = $('tr.staff-row[data-task-code="' + currentOpenAccordion + '"]');
                var $currentIcon = $('.accordion-header[data-task-code="' + currentOpenAccordion + '"] .accordion-icon');

                $currentRows.each(function (index) {
                    var $row = $(this);
                    setTimeout(() => {
                        $row.addClass('animate-out');
                        setTimeout(() => {
                            $row.hide().removeClass('animate-out');
                        }, 150);
                    }, index * 30);
                });

                $currentIcon.removeClass('rotated');

                var closeDelay = $currentRows.length * 30 + 150;
                setTimeout(() => {
                    openAccordion($staffRows, $icon, taskCode);
                }, closeDelay);
            } else if (isVisible) {
                closeAccordion($staffRows, $icon);
                currentOpenAccordion = null;
            } else {
                openAccordion($staffRows, $icon, taskCode);
            }
        });

        function openAccordion($staffRows, $icon, taskCode) {
            $staffRows.each(function (index) {
                var $row = $(this);
                setTimeout(() => {
                    $row.addClass('animate-in').show();
                    setTimeout(() => {
                        $row.removeClass('animate-in');
                        if (index === $staffRows.length - 1)
                            isAnimating = false;
                    }, 100);
                }, index * 50);
            });

            $icon.addClass('rotated');
            currentOpenAccordion = taskCode;
        }

        function closeAccordion($staffRows, $icon) {
            $staffRows.each(function (index) {
                var $row = $(this);
                setTimeout(() => {
                    $row.addClass('animate-out');
                    setTimeout(() => {
                        $row.hide().removeClass('animate-out');
                        if (index === $staffRows.length - 1)
                            isAnimating = false;
                    }, 150);
                }, index * 40);
            });

            $icon.removeClass('rotated');
        }
    });

//    function submitForm() {
//        if (typeof $("input[name='TaskAssignment[staffIds][]']:checked").val() === "undefined") {
//            myAlert('Please select a staff');
//            return false;
//        } else if (typeof $("input[name='TaskAssignment[taskCode][]']:checked").val() === "undefined") {
//            myAlert('Please select a task');
//            return false;
//        } else if ($("#taskassignment-startdate").val() === "") {
//            myAlert('Please insert Date');
//            return false;
//        }
//        $("#assignForm").submit();
//    }
    function submitForm() {
        // Check at least one task selected
        if ($("input[name='TaskAssignment[taskCode][]']:checked").length === 0) {
            myAlert('Please select a task');
            return false;
        }

        // Check at least one staff selected
        if ($("input[name^='TaskAssignment[staffIds]']:checked").length === 0) {
            myAlert('Please select a staff');
            return false;
        }

        // Check date
        if ($("#taskassignment-startdate").val() === "") {
            myAlert('Please insert Date');
            return false;
        }

        $("#assignForm").submit();
    }
</script>