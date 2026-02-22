<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;

$this->title = "Confirm Task - ";
$this->params['breadcrumbs'][] = ['label' => 'Elecrication Task Assignment'];
$this->params['breadcrumbs'][] = ['label' => 'Project List', 'url' => ['index-elec-project-list']];
$this->params['breadcrumbs'][] = ['label' => $project->project_production_code, 'url' => ['index-elec-project-panels', 'id' => $project->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="work-assignment-master-form">
    <h4><?= Html::encode($this->title) ?></h4>
    <div class="work-assignment-master-form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'confirmAssignForm',
            'options' => ['autocomplete' => 'off'],
            'action' => 'confirm-task-assignment'
        ]);
        ?>

        <div class="form-row" style="height: 100%">
            <div class='col-md-7 col-sm-12'>
                <div class="form-row">
                    <div class="col-12">
                        <fieldset class="border p-1">
                            <legend class="w-auto px-2 m-0">Selected Staff(s):</legend>
                            <ol>
                                <?php
                                foreach ($staffNameList as $key => $name) {
                                    echo "<li>";
                                    echo Html::encode($name['fullname']);
                                    echo "</li>";
                                }
                                ?>
                            </ol>
                        </fieldset>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-12">
                        <fieldset class="border p-1">
                            <legend class="w-auto px-2 m-0">Task Assigning Detail:</legend>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="col-12 hidden">
                                        <?php
                                        echo $form->field($model, 'startDate')->label(false);
                                        echo $form->field($model, 'comments')->textarea()->label(false);
                                        echo $form->field($model, 'panelId')->label(false);
                                        echo $form->field($model, 'projectId')->label(false);
                                        echo $form->field($model, 'staffIdString')->label(false);
                                        ?>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Task</th>
                                                    <th>QTY</th>
                                                    <th>Comment</th>
                                                    <th>Start Date</th>
                                                    <th>Target Completion Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
//                                                $showButton = false;
//                                                foreach ($formList as $key => $taskAssign) {
//                                                    $staffInCharge = "";
//                                                    $stffInChargeIds = "";
//                                                    // To show who is in this tasks
//                                                    foreach ($staffNameList as $availableStaff) {
//                                                        if ($availableStaff['task_code'] == $taskAssign->taskCode) {
//                                                            $staffInCharge .= "<br/> &nbsp;&nbsp;- " . $availableStaff['fullname'];
//                                                            $stffInChargeIds .= (empty($stffInChargeIds) ? "" : ",") . $availableStaff['user_id'];
//                                                        }
//                                                    }
//                                                    $taskAssign->staffIdString = $stffInChargeIds;
//                                                    if (empty($taskAssign->quantity)) { // If can no longer assign
//                                                        echo "<tr><td style='text-decoration: line-through'>";
//                                                        echo Html::encode($taskAssign->tempTaskName);
//                                                        echo "</td><td style='text-decoration: line-through'>";
//                                                        echo MyFormatter::asDate_Read($taskAssign->start_date);
//                                                        echo "</td><td style='text-decoration: line-through'>";
//                                                        echo MyFormatter::asDate_Read($taskAssign->quantity);
//                                                        echo "</td><td style='text-decoration: line-through'>";
//                                                        echo Html::encode($taskAssign->comments);
//                                                        echo "</td></tr>";
//                                                    } else if (empty($staffInCharge)) {
//                                                        echo "<tr><td style='text-decoration: line-through'>";
//                                                        echo Html::encode($taskAssign->tempTaskName);
//
//                                                        echo "</td><td colspan='3' class='text-danger bold'>";
//                                                        echo "No Staff Selected";
//                                                        echo "</td></tr>";
//                                                    } else {
//                                                        echo "<tr><td>";
//                                                        echo "<div class='hidden'>";
//                                                        echo $form->field($taskAssign, "[$key]prod_elec_task_id")->label(false);
//                                                        if (empty($taskAssign->proj_prod_panel_id)) {
//                                                            $taskAssign->proj_prod_panel_id = $model->panelId;
//                                                        }
//                                                        echo $form->field($taskAssign, "[$key]proj_prod_panel_id")->label(false);
//                                                        echo $form->field($taskAssign, "[$key]staffIdString")->label(false);
//                                                        echo "</div>";
//                                                        echo "<b class='text-success'>" . Html::encode($taskAssign->panelCode) . "</b><br/> <b class='text-primary'>- " . Html::encode($taskAssign->tempTaskName) . "</b>";
//                                                        echo $staffInCharge;
//                                                        echo "</td><td>";
//                                                        $project_target_date = $taskAssign->projProdPanel->projProdMaster->current_target_date;
//                                                        echo $form->field($taskAssign, "[$key]start_date", [
//                                                            'options' => ['style' => 'margin: 0;']
//                                                        ])->widget(yii\jui\DatePicker::className(), [
//                                                            'options' => [
//                                                                'class' => 'form-control',
//                                                                'placeholder' => 'dd/mm/yyyy',
//                                                                'id' => 'start-date-' . $key
//                                                            ],
//                                                            'dateFormat' => 'dd/MM/yyyy',
//                                                            'clientOptions' => [
//                                                                'maxDate' => $project_target_date ? date('d/m/Y', strtotime($project_target_date)) : 0,
//                                                                'changeMonth' => true,
//                                                                'changeYear' => true,
//                                                                'onSelect' => new yii\web\JsExpression("
//                                                                    function(dateText) {
//                                                                        // Update minDate dynamically
//                                                                        $('#current-target-date-$key').datepicker('option', 'minDate', dateText);
//                                                                        $('#current-target-date-no-limit-$key').datepicker('option', 'minDate', dateText);
//
//                                                                        // Clear selected target date
//                                                                        $('#current-target-date-$key').val('');
//                                                                        $('#current-target-date-no-limit-$key').val('');
//                                                                    }
//                                                                "),
//                                                            ],
//                                                        ])->label(false);
//                                                        echo "</td><td>";
//                                                        echo $form->field($taskAssign, "[$key]quantity", ['options' => ['style' => 'margin: 0;']])
//                                                                ->dropdownList(range(0, $taskAssign->quantity), ['class' => 'form-control text-right'])
//                                                                ->label(false);
//                                                        echo "</td><td>";
//                                                        echo $form->field($taskAssign, "[$key]comments", ['options' => ['style' => 'margin: 0;']])
//                                                                ->textarea(['row' => 6])
//                                                                ->label(false);
//                                                        echo "</td><td>";
//                                                        if (!empty($project_target_date)) {
//                                                            echo $form->field($taskAssign, "[$key]current_target_date")->widget(yii\jui\DatePicker::className(), [
//                                                                'options' => [
//                                                                    'class' => 'form-control',
//                                                                    'required' => true,
//                                                                    'placeholder' => 'dd/mm/yyyy',
//                                                                    'id' => 'current-target-date-' . $key
//                                                                ],
//                                                                'dateFormat' => 'dd/MM/yyyy',
//                                                                'clientOptions' => [
//                                                                    'minDate' => $taskAssign->start_date ? date('d/m/Y', strtotime($taskAssign->start_date)) : 0,
//                                                                    'maxDate' => date('d/m/Y', strtotime($project_target_date)),
//                                                                    'onSelect' => new yii\web\JsExpression("
//                                                                        function(dateText) {
//                                                                            validateTargetDate(dateText, $key); 
//                                                                        }
//                                                                    "),
//                                                                ],
//                                                            ])->label(false);
//                                                        } else {
//                                                            echo $form->field($taskAssign, "[$key]current_target_date")->widget(yii\jui\DatePicker::className(), [
//                                                                'options' => [
//                                                                    'class' => 'form-control',
//                                                                    'required' => true,
//                                                                    'placeholder' => 'dd/mm/yyyy',
//                                                                    'id' => 'current-target-date-no-limit-' . $key
//                                                                ],
//                                                                'dateFormat' => 'dd/MM/yyyy',
//                                                                'clientOptions' => [
//                                                                    'minDate' => $taskAssign->start_date ? date('d/m/Y', strtotime($taskAssign->start_date)) : 0,
//                                                                    'changeYear' => true,
//                                                                    'changeMonth' => true,
//                                                                ]
//                                                            ])->label(false);
//                                                        }
//                                                        echo "</td></tr>";
//                                                        $showButton = true;
//                                                    }
//                                                }
                                                $showButton = false;

                                                // --- STEP 1: Group tasks and simultaneously categorize them as valid or invalid ---
                                                $groupedTasks = [];
                                                foreach ($formList as $key => $taskAssign) {
                                                    $taskName = $taskAssign->tempTaskName;

                                                    // Initialize the group if it's the first time we see this task name
                                                    if (!isset($groupedTasks[$taskName])) {
                                                        $groupedTasks[$taskName] = [
                                                            'validTasks' => [], // Tasks that can be assigned
                                                            'invalidTasks' => [], // Tasks that are disabled or have errors
                                                            'latestStartDate' => null,
                                                            'firstValidTaskKey' => null, // Key of the first task that is assignable
                                                        ];
                                                    }

                                                    // Determine the staff assigned to this specific task
                                                    $staffInCharge = "";
                                                    $stffInChargeIds = "";
                                                    foreach ($staffNameList as $availableStaff) {
                                                        if ($availableStaff['task_code'] == $taskAssign->taskCode) {
                                                            $staffInCharge .= "<br/> &nbsp;&nbsp;- " . Html::encode($availableStaff['fullname']);
                                                            $stffInChargeIds .= (empty($stffInChargeIds) ? "" : ",") . $availableStaff['user_id'];
                                                        }
                                                    }
                                                    $taskAssign->staffIdString = $stffInChargeIds;

                                                    // OLD CODE'S LOGIC: Check if the task is valid for assignment
                                                    if (empty($taskAssign->quantity)) {
                                                        // Condition 1: Cannot be assigned because quantity is 0
                                                        $groupedTasks[$taskName]['invalidTasks'][] = [
                                                            'task' => $taskAssign,
                                                            'reason' => 'no_quantity' // Store the reason for being invalid
                                                        ];
                                                    } else if (empty($staffInCharge)) {
                                                        // Condition 2: Cannot be assigned because no staff is selected
                                                        $groupedTasks[$taskName]['invalidTasks'][] = [
                                                            'task' => $taskAssign,
                                                            'reason' => 'no_staff' // Store the reason
                                                        ];
                                                    } else {
                                                        // This task is VALID. Add it to the 'validTasks' list.
                                                        $groupedTasks[$taskName]['validTasks'][] = [
                                                            'key' => $key,
                                                            'task' => $taskAssign,
                                                            'staffInCharge' => $staffInCharge,
                                                        ];

                                                        // If this is the first valid task in the group, store its key
                                                        if ($groupedTasks[$taskName]['firstValidTaskKey'] === null) {
                                                            $groupedTasks[$taskName]['firstValidTaskKey'] = $key;
                                                        }

                                                        // Update the latest start date for the group (used for date picker validation)
                                                        if (!empty($taskAssign->start_date)) {
                                                            $currentStartDate = strtotime($taskAssign->start_date);
                                                            if ($groupedTasks[$taskName]['latestStartDate'] === null || $currentStartDate > $groupedTasks[$taskName]['latestStartDate']) {
                                                                $groupedTasks[$taskName]['latestStartDate'] = $currentStartDate;
                                                            }
                                                        }
                                                    }
                                                }

                                                // --- STEP 2: Render each task group based on the categorized lists ---
                                                foreach ($groupedTasks as $taskName => $groupData) {
                                                    $validTasks = $groupData['validTasks'];
                                                    $invalidTasks = $groupData['invalidTasks'];
                                                    $firstValidTaskKey = $groupData['firstValidTaskKey'];
                                                    $taskNameId = preg_replace('/[^a-zA-Z0-9]/', '', $taskName);

                                                    // Only render the interactive header if there's at least one valid task to assign
                                                    if (!empty($validTasks)) {
                                                        $showButton = true; // A valid task exists, so the submit button should be shown
                                                        $firstTask = $formList[$firstValidTaskKey];
                                                        $project_target_date = $firstTask->projProdPanel->projProdMaster->current_target_date;

                                                        // Render the group header row with shared date pickers
                                                        echo "<tr>";
                                                        echo "<td colspan='3' class='text-center' style='background-color: #f8f9fa; font-weight: bold; font-size: 16px; padding: 10px; height: 60px; line-height: 40px;'>";
                                                        echo "<span class='text-primary'>" . Html::encode($taskName) . "</span>";
                                                        echo "</td>";

                                                        // Group Start Date
                                                        echo "<td class='text-center' style='background-color: #f8f9fa;'>";
                                                        echo $form->field($firstTask, "[$firstValidTaskKey]start_date")->widget(DatePicker::className(), [
                                                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'id' => 'group-start-date-' . $taskNameId],
                                                            'dateFormat' => 'dd/MM/yyyy',
                                                            'clientOptions' => [
                                                                'maxDate' => !empty($project_target_date) ? date('d/m/Y', strtotime($project_target_date)) : 0,
                                                                'changeMonth' => true,
                                                                'changeYear' => true,
                                                                'onSelect' => new yii\web\JsExpression("function(dateText) { updateTaskGroupDates('$taskNameId', dateText); }"),
                                                            ],
                                                        ])->label(false);
                                                        echo "</td>";

                                                        // Group Target Date
                                                        echo "<td colspan='2' class='text-center' style='background-color: #f8f9fa;'>";
                                                        echo $form->field($firstTask, "[$firstValidTaskKey]current_target_date")->widget(DatePicker::className(), [
                                                            'options' => ['class' => 'form-control', 'required' => true, 'placeholder' => 'dd/mm/yyyy', 'id' => 'target-date-' . $taskNameId],
                                                            'dateFormat' => 'dd/MM/yyyy',
                                                            'clientOptions' => [
                                                                'minDate' => !empty($firstTask->start_date) ? date('d/m/Y', strtotime($firstTask->start_date)) : 0,
                                                                'maxDate' => !empty($project_target_date) ? date('d/m/Y', strtotime($project_target_date)) : '',
                                                                'changeYear' => true,
                                                                'changeMonth' => true,
                                                                'onSelect' => new yii\web\JsExpression("function(dateText) { updateTaskNameTargetDate('$taskNameId', dateText); }"),
                                                            ],
                                                        ])->label(false);
                                                        echo "</td>";
                                                        echo "</tr>";

                                                        // Now, render the individual rows for each VALID task in the group
                                                        foreach ($validTasks as $taskData) {
                                                            $key = $taskData['key'];
                                                            $taskAssign = $taskData['task'];
                                                            $staffInCharge = $taskData['staffInCharge'];

                                                            echo "<tr><td style='padding-left: 20px;'>";
                                                            echo "<div class='hidden'>";
                                                            echo $form->field($taskAssign, "[$key]prod_elec_task_id")->label(false);
                                                            if (empty($taskAssign->proj_prod_panel_id)) {
                                                                $taskAssign->proj_prod_panel_id = $model->panelId;
                                                            }
                                                            echo $form->field($taskAssign, "[$key]proj_prod_panel_id")->label(false);
                                                            echo $form->field($taskAssign, "[$key]staffIdString")->label(false);
                                                            echo "</div>";
                                                            echo "<b class='text-success'>" . Html::encode($taskAssign->panelCode) . "</b>";
                                                            echo $staffInCharge; // Staff names are already calculated
                                                            echo "</td><td>";
                                                            echo $form->field($taskAssign, "[$key]quantity", ['options' => ['style' => 'margin: 0;']])->dropdownList(range(0, $taskAssign->quantity), ['class' => 'form-control text-right'])->label(false);
                                                            echo "</td>";
                                                            // Hidden start_date to be synced by JS
                                                            echo $form->field($taskAssign, "[$key]start_date")->hiddenInput(['class' => 'hidden-start-date-' . $taskNameId])->label(false);
                                                            echo "<td>";
                                                            echo $form->field($taskAssign, "[$key]comments", ['options' => ['style' => 'margin: 0;']])->textarea(['rows' => 2])->label(false);
                                                            echo "</td>";
                                                            // Hidden current_target_date to be synced by JS
                                                            echo $form->field($taskAssign, "[$key]current_target_date")->hiddenInput(['class' => 'hidden-target-date-' . $taskNameId])->label(false);
                                                            echo "</tr>";
                                                        }
                                                    }

                                                    // Finally, render the INVALID tasks for this group
                                                    if (!empty($invalidTasks)) {
                                                        // If there were no valid tasks, we still need a header for context
                                                        if (empty($validTasks)) {
                                                            echo "<tr><td colspan='3' class='text-center' style='background-color: #f8f9fa; font-weight: bold; font-size: 16px; padding: 10px;'>";
                                                            echo "<span class='text-primary'>" . Html::encode($taskName) . "</span>";
                                                            echo "</td></tr>";
                                                        }

                                                        foreach ($invalidTasks as $invalidTaskData) {
                                                            $taskAssign = $invalidTaskData['task'];

                                                            // Apply the correct display logic based on the reason for invalidity
                                                            if ($invalidTaskData['reason'] === 'no_quantity') {
                                                                echo "<tr><td style='text-decoration: line-through; padding-left: 20px;'>";
                                                                echo Html::encode($taskAssign->panelCode);
                                                                echo "</td><td style='text-decoration: line-through'>";
                                                                echo "N/A"; // Or MyFormatter::asDate_Read($taskAssign->start_date);
                                                                echo "</td><td style='text-decoration: line-through'>";
                                                                echo "0"; // Quantity is 0
                                                                echo "</td><td style='text-decoration: line-through' colspan='2'>";
                                                                echo Html::encode($taskAssign->comments);
                                                                echo "</td></tr>";
                                                            } else { // 'no_staff'
                                                                echo "<tr><td style='padding-left: 20px;'>";
                                                                echo Html::encode($taskAssign->panelCode);
                                                                echo "</td><td colspan='4' class='text-danger bold'>";
                                                                echo "No Staff Selected";
                                                                echo "</td></tr>";
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <?php
                        if ($showButton) {
                            echo Html::submitButton("Submit", ['class' => 'btn btn-success float-right m-2 submitButton', 'onclick' => 'return submitForm()']);
                        }
                        ?>
                    </div>

                </div>
            </div>
            <div class='col-md-5 col-sm-12'>
                <div class="col-sm-12">
                    <?php
                    echo $this->render("/projectproduction/main/_detailViewProjProdDetail", [
                        'projProdMaster' => $project,
//                        'panel' => $panel
                    ]);
                    ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
    function updateTaskGroupDates(taskNameId, dateText) {
        // Update the hidden start date fields for this group
        $('.hidden-start-date-' + taskNameId).val(dateText);

        // Update the minDate for the target date DatePicker
        // This ensures the target date cannot be before the start date.
        $('#target-date-' + taskNameId).datepicker('option', 'minDate', dateText);
    }

    function updateTaskNameTargetDate(taskNameId, dateText) {
        $('.hidden-target-date-' + taskNameId).val(dateText);
    }
</script>
<script>
    function submitForm() {
<?php if (!empty($project_target_date)): ?>
            // Check target date validation for all date inputs
            var allDatesValid = true;
            var hasEmptyDates = false;

            // Find all target date inputs
            $('[id^="current-target-date"]').each(function () {
                var dateInput = $(this);
                var dateValue = dateInput.val();
                var inputId = dateInput.attr('id');
                var errorId = inputId + '-error';

                // Clear previous errors
                $('#' + errorId).remove();
                dateInput.removeClass('is-invalid').closest('.form-group').removeClass('has-error');

                // Check if date field is empty
                if (dateValue === '' || dateValue === null || dateValue === undefined) {
                    dateInput.addClass('is-invalid').closest('.form-group').addClass('has-error');
                    dateInput.after(
                            '<div id="' + errorId + '" class="invalid-feedback d-block">Target Date cannot be blank.</div>'
                            );
                    hasEmptyDates = true;
                    allDatesValid = false;
                } else {
                    // Check if selected date is before project target date
                    var selectedDate = new Date(dateValue.split('/').reverse().join('-'));
                    var projectTargetDate = new Date('<?php echo date('Y-m-d', strtotime($project_target_date)); ?>');

                    if (selectedDate > projectTargetDate) {
                        dateInput.addClass('is-invalid').closest('.form-group').addClass('has-error');
                        dateInput.after(
                                '<div id="' + errorId + '" class="invalid-feedback d-block">Target completion date cannot exceed project target completion date (<?php echo date('d/m/Y', strtotime($project_target_date)); ?>).</div>'
                                );
                        allDatesValid = false;
                    }
                }
            });

            if (!allDatesValid) {
                if (hasEmptyDates) {
                    alert('Please select target completion dates for all tasks.');
                } else {
                    alert('Please correct the target completion dates before submitting.');
                }
                return false;
            }

<?php else: ?>
            // If no project target date is set, just check if target dates are provided
            var hasEmptyDates = false;

            $('[id^="current-target-date"]').each(function () {
                var dateInput = $(this);
                var dateValue = dateInput.val();
                var inputId = dateInput.attr('id');
                var errorId = inputId + '-error';

                // Clear previous errors
                $('#' + errorId).remove();
                dateInput.removeClass('is-invalid').closest('.form-group').removeClass('has-error');

                if (dateValue === '' || dateValue === null || dateValue === undefined) {
                    dateInput.addClass('is-invalid').closest('.form-group').addClass('has-error');
                    dateInput.after(
                            '<div id="' + errorId + '" class="invalid-feedback d-block">Target Date cannot be blank.</div>'
                            );
                    hasEmptyDates = true;
                }
            });

            if (hasEmptyDates) {
                alert('Please select target completion dates for all tasks.');
                return false;
            }
<?php endif; ?>

        // Trigger Yii2 validation
        var $form = $("#myForm");

        if ($form.data("yiiActiveForm")) {
            $form.yiiActiveForm("validate");

            setTimeout(function () {
                var hasBootstrap4Errors = $form.find(".is-invalid").length > 0;
                var hasBootstrap3Errors = $form.find(".has-error").length > 0;
                var hasYiiErrors = $form.find(".field-error").length > 0;

                if (!hasBootstrap4Errors && !hasBootstrap3Errors && !hasYiiErrors) {
                    $form.submit();
                } else {
                    console.log('Form has errors, not submitting');
                }
            }, 300);
            return false;
        } else {
            return true;
        }
    }

<?php if (!empty($project_target_date)): ?>
        function validateTargetDate(selectedDateText, rowKey) {
            console.log('validateTargetDate called with:', selectedDateText, 'for row:', rowKey);

            var selectedDate = new Date(selectedDateText.split('/').reverse().join('-'));
            var projectTargetDate = new Date('<?php echo date('Y-m-d', strtotime($project_target_date)); ?>');
            var inputId = 'current-target-date-' + rowKey;
            var errorId = inputId + '-error';

            // Clear previous errors
            $('#' + errorId).remove();
            $('#' + inputId).removeClass('is-invalid').closest('.form-group').removeClass('has-error');

            // Check if selected date is before project target date
            if (selectedDate > projectTargetDate) {
                $('#' + inputId).addClass('is-invalid').closest('.form-group').addClass('has-error');
                $('#' + inputId).after(
                        '<div id="' + errorId + '" class="invalid-feedback d-block">' +
                        'Target completion date cannot exceed project target completion date (<?php echo date('d/m/Y', strtotime($project_target_date)); ?>)' +
                        '</div>'
                        );
                $('#' + inputId).val('');
                return false;
            }

            return true;
        }
<?php endif; ?>

    // Document ready function for additional setup
    $(document).ready(function () {
        console.log('Document ready');
        console.log('Form found:', $('#myForm').length > 0);

        // Additional validation on date input blur for all date inputs
        $(document).on('blur', '[id^="current-target-date"]', function () {
            var dateInput = $(this);
            var dateValue = dateInput.val();
            var inputId = dateInput.attr('id');
            var errorId = inputId + '-error';

            console.log('Date input blur event, ID:', inputId, 'value:', dateValue);

            if (dateValue === '' || dateValue === null) {
                // Clear any existing errors first
                $('#' + errorId).remove();
                dateInput.removeClass('is-invalid').closest('.form-group').removeClass('has-error');

                // Add error styling and message
                dateInput.addClass('is-invalid').closest('.form-group').addClass('has-error');
                dateInput.after(
                        '<div id="' + errorId + '" class="invalid-feedback d-block">Target Date cannot be blank.</div>'
                        );
            } else {
                // Clear errors if date is provided
                $('#' + errorId).remove();
                dateInput.removeClass('is-invalid').closest('.form-group').removeClass('has-error');
            }
        });

        console.log('Target date inputs found:', $('[id^="current-target-date"]').length);
    });
</script>

<style>
    /* Ensure error messages are visible */
    .invalid-feedback.d-block {
        display: block !important;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Additional styling for form groups with errors */
    .has-error .form-control {
        border-color: #dc3545;
    }

    .has-error .control-label {
        color: #dc3545;
    }
</style>