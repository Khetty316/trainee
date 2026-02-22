<?php

use yii\helpers\Html;
?>
<!---->
<fieldset class="form-group border p-3">
    <div class="row">
        <div class="col-xl-8 order-md-1">
            <h5>Task List</h5>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center col-1" rowspan="2">Panel's Code</th>
                            <th class="text-center col-1 br" rowspan="2">Panel's Name</th>
                            <th class="text-center col-1" colspan="<?= count($refFabTask) ?>">Fabrication</th>
                            <th class="text-center col-1" colspan="<?= count($refElecTask) ?>">Electrical</th>
                            <th class="text-center col-1" rowspan="2">
                                <div class="task-container">
                                    <div class="task-name">
                                        Select All
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <!-- Fabrication Tasks -->
                            <?php foreach ($refFabTask as $refFabItem) { ?>
                                <th class="text-center col-1">
                                    <div class="task-container">
                                        <div class="task-name">
                                            <?= Html::encode($refFabItem->name) ?>
                                        </div>
                                    </div>
                                </th>
                            <?php } ?>
                            <!-- Electrical Tasks -->
                            <?php foreach ($refElecTask as $refElecItem) { ?>
                                <th class="text-center col-1">
                                    <div class="task-container">
                                        <div class="task-name">
                                            <?= Html::encode($refElecItem->name) ?>
                                        </div>
                                    </div>
                                </th>
                            <?php } ?>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        foreach ($panels as $panel) {
                            $taskAssignFabs = $panel->checkTaskAssignFab();
                            $taskAssignElecs = $panel->checkTaskAssignElec();
                            ?>
                            <tr class="text-center col-1 panel-code" data-panel-code="<?= Html::encode($panel->project_production_panel_code) ?>">
                                <td><?= Html::encode($panel->project_production_panel_code) ?></td>
                                <td class="br"><?= Html::encode($panel->panel_description) ?></td>
                                <?php foreach ($refFabTask as $refFabItem) { ?>
                                    <?php
                                    $checkBoxName = "tasks[{$panel->id}][fab][{$refFabItem->code}][id]";
                                    $checkboxOptions = [
                                        'value' => $panel->id,
                                        'class' => 'taskCheckBox fab-checkbox checkbox'
                                    ];
                                    $checked = false;
                                    $selectedFabTask = false;
                                    $assignedFabTask = false;
                                    foreach ($prodFabTasks as $prodFabTask) {
                                        if ($prodFabTask->fab_task_code === $refFabItem->code) {
                                            $checked = true;
                                        }
                                    }
                                    foreach ($taskAssignFabs as $key => $taskAssignFab) {
                                        foreach ($prodFabTasks as $prodFabTask) {
                                            if ($prodFabTask->fab_task_code === $refFabItem->code) {
                                                if ($taskAssignFab->prod_fab_task_id === $prodFabTask->id) {
                                                    $assignedFabTask = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    $sharedData->taskAssigned = $sharedData->taskAssigned ?: $assignedFabTask;
                                    ?>
                                    <td>
                                        <?= Html::hiddenInput("tasks[{$panel->id}][fab][{$refFabItem->code}][code]", $refFabItem->code) ?>
                                        <?= Html::hiddenInput("tasks[{$panel->id}][fab][{$refFabItem->code}][quantity]", $panel->quantity) ?>
                                        <?= Html::hiddenInput("tasks[{$panel->id}][fab][{$refFabItem->code}][id]", '') ?>
                                        <?php
                                        if ($assignedFabTask) {
                                            $checkboxOptions = [
                                                'value' => $panel->id,
                                                'class' => 'taskCheckBox fab-checkbox-checked checkbox',
                                                'disabled' => 'disabled'
                                            ];
                                            echo Html::hiddenInput($checkBoxName, $panel->id);
                                        }
                                        ?>
                                        <?= Html::checkbox($checkBoxName, $checked, $checkboxOptions) ?>
                                    </td>
                                <?php } ?>


                                <?php foreach ($refElecTask as $refElecItem) { ?>
                                    <?php
                                    $checkBoxName = "tasks[{$panel->id}][elec][{$refElecItem->code}][id]";
                                    $checkboxOptions = [
                                        'value' => $panel->id,
                                        'class' => 'taskCheckBox elec-checkbox checkbox'
                                    ];
                                    $checked = false;
                                    $selectedElecTask = false;
                                    $assignedElecTask = false;
                                    foreach ($prodElecTasks as $prodElecTask) {
                                        if (($prodElecTask->elec_task_code === $refElecItem->code)) {
                                            $checked = true;
                                        }
                                    }

                                    foreach ($taskAssignElecs as $key => $taskAssignElec) {
                                        foreach ($prodElecTasks as $prodElecTask) {
                                            if (($prodElecTask->elec_task_code === $refElecItem->code)) {

                                                if ($taskAssignElec->prod_elec_task_id === $prodElecTask->id) {
                                                    $assignedElecTask = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    $sharedData->taskAssigned = $sharedData->taskAssigned ?: $assignedElecTask;
                                    ?>
                                    <td>
                                        <?= Html::hiddenInput("tasks[{$panel->id}][elec][{$refElecItem->code}][code]", $refElecItem->code) ?>
                                        <?= Html::hiddenInput("tasks[{$panel->id}][elec][{$refElecItem->code}][quantity]", $panel->quantity) ?>
                                        <?= Html::hiddenInput("tasks[{$panel->id}][elec][{$refElecItem->code}][id]", '') ?>
                                        <?php
                                        if ($assignedElecTask) {
                                            $checkboxOptions = [
                                                'value' => $panel->id,
                                                'class' => 'taskCheckBox elec-checkbox-checked checkbox',
                                                'disabled' => 'disabled'
                                            ];
                                            echo Html::hiddenInput($checkBoxName, $panel->id);
                                        }
                                        ?>
                                        <?= Html::checkbox($checkBoxName, $checked, $checkboxOptions) ?>
                                    </td>

                                <?php } ?>
                                <td>
                                    <?= Html::checkbox("allTask[]", false, ['class' => 'allTaskCheckBox checkbox']) ?>
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-xl-4 order-md-2">
            <div class="table-responsive">
                <?php
                $fabPanelWeight = $fabPanelWeight->panel_type_weight ?? 0;
                $elecPanelWeight = $elecPanelWeight->panel_type_weight ?? 0;
                echo $this->render('_panelWeight', [
                    'model' => $model,
                    'panel' => $panel,
                    'fabPanelWeight' => $fabPanelWeight,
                    'elecPanelWeight' => $elecPanelWeight
                ]);
                ?>
            </div>
        </div>        
    </div>
</fieldset>
<script>
    $('.allTaskCheckBox').change(function () {
        var isChecked = $(this).is(":checked");
        var rowIndex = $(this).closest('tr').index();

        $('tbody tr').eq(rowIndex).find('.taskCheckBox.fab-checkbox, .taskCheckBox.elec-checkbox').prop('checked', isChecked);
    });
</script>
