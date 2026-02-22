<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use common\models\myTools\MyFormatter;
use frontend\models\projectproduction\task\TaskAssignment;
use common\modules\auth\models\AuthItem;

$this->title = $model->project_production_code;
$this->params['breadcrumbs'][] = ['label' => 'Fabrication Task Assignment'];
$this->params['breadcrumbs'][] = ['label' => 'Project List', 'url' => ['index-fab-project-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-view">
    <div class="row">
        <h4 class="col-12">
            <?= Html::a($model->project_production_code . ' <i class="fas fa-external-link-square-alt fa-sm"></i>', "javascript:void(0)", ['class' => 'modalButtonMedium', 'value' => '/production/production/ajax-view-project-detail?id=' . $model->id]) ?>
        </h4>
        <h6 class="col-12"><?= Html::encode($model->name) ?></h6>
    </div>
    <div class="row">
        <div class="col-12 order-md-1">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Panel List:</legend>
                <div class="table-responsive">
                    <?php
                    $formFinalize = ActiveForm::begin([
                        'options' => ['autocomplete' => 'off'],
                        'method' => 'post',
                        'action' => ['assign-task-multiple-panels', 'projectId' => $model->id],
                        'id' => 'myForm'
                    ]);
                    ?>

                    <?php
                    $panels = $model->projectProductionPanels;
                    if ($panels) {
                        array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                        ?> 
                        <table class="table table-sm table-striped table-bordered">
                            <thead class="thead-light">
                                <tr class=" ">
                                    <th class="tdnowrap text-center align-top">
                                        <?php
                                        echo Html::checkbox("panelCheckAll", false, ['id' => 'panelCheckAll', 'class' => 'big-checkbox']);
                                        ?>
                                    </th>
                                    <th class="tdnowrap text-center align-top">#</th>
                                    <th class="align-top">Panel's Code</th>
                                    <th class="align-top">Panel's Name</th>
                                    <th class="align-top">Task Weight</th>
                                    <th class="align-top col-1">Reference</th>
                                    <th class="tdnowrap align-top">Status</th>
                                    <th class="tdnowrap align-top">Complete %</th>
                                    <th class="tdnowrap align-top">Assigned %</th>
                                    <?php
                                    $toDos = RefProjProdTaskFab::getAllActiveSorted();
                                    foreach ((array) $toDos as $key => $toDo) {
                                        echo "<th class='text-right col-1'> $toDo->name </th>";
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="itemDisplayTable">
                                <?php
                                foreach ($panels as $key => $panel) {
//                                $panel->checkAndGetFabTask();
                                    $progress = $panel->getFabTaskProgressStatus(); // Pull record from task assigned
                                    $taskAssignFabs = $panel->checkTaskAssignFab();
                                    $isFinalized = !empty($panel->finalized_at . $panel->design_completed_at);
                                    $taskWeightIsValid = $panel->checkTotalFabTaskWeight();
                                    ?>

                                    <tr>
                                        <td>
                                            <?php
                                            if (!$taskWeightIsValid) {
                                                echo Html::checkbox("panelId[]", false, ['value' => $panel->id, 'class' => 'panelCheckBox big-checkbox', 'disabled' => true]);
                                            } else if (!empty($progress)) {
                                                echo Html::checkbox("panelId[]", false, ['value' => $panel->id, 'class' => 'panelCheckBox big-checkbox']);
                                            }
                                            ?>
                                        </td>
                                        <td class="text-right px-2"><?= $key + 1 ?></td>
                                        <td class="" style="width:12%;">
                                            <?php
                                            if (empty($progress) || !$taskWeightIsValid) {
                                                echo Html::encode($panel->project_production_panel_code);
                                            } else {
                                                echo Html::a($panel->project_production_panel_code,
                                                        ['assign-task-multiple', 'panelId' => $panel->id],
                                                        ['title' => 'Assign', 'class' => 'mx-1 text-primary']);
                                            }
                                            ?>
                                        </td>
                                        <td class="" style="width:20%;">
                                            <?php
                                            echo Html::encode($panel->panel_description);
                                            ?>
                                        </td>
                                        <td class="text-center" style="width:10%;">
                                            <?php
                                            if (!$taskWeightIsValid) {
                                                echo Html::a("<i class='far fa-edit'></i>", "javascript:", [
                                                    'title' => "Edit Task Weight",
                                                    "value" => yii\helpers\Url::to(['update-task-weight', 'id' => $panel->id]),
                                                    "class" => "modalButton",
                                                    'data-modaltitle' => "Edit Task Weight"
                                                ]);
                                                echo "<div class='text-danger text-left'>Total task weight is more than 100%</div>";
                                            } else if (!empty($progress)) {
                                                echo Html::a("<i class='far fa-edit'></i>", "javascript:", [
                                                    'title' => "Edit Task Weight",
                                                    "value" => yii\helpers\Url::to(['update-task-weight', 'id' => $panel->id]),
                                                    "class" => "modalButton",
                                                    'data-modaltitle' => "Edit Task Weight"
                                                ]);
                                            }
                                            ?>
                                        </td>
                                        <td class="">
                                            <?php
//                                        echo $panel->project_production_panel_code;
                                            if (!empty($progress)) {
                                                if (!empty($panel->filename)) {
                                                    echo Html::a('<i class="fas fa-file-alt fa-lg"></i>',
                                                            ['/production/production/get-panel-file-by-panel-id', 'panelId' => $panel->id], ['class' => 'text-warning m-2', 'target' => '_blank']);
                                                }
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </td>
                                        <td class="text-right tdnowrap"> 
                                            <?= $panel->fabWorkStatus->status_name ?? null ?>
                                        </td>
                                        <td class="text-right tdnowrap"> 
                                            <?= MyFormatter::asDecimal2_emptyZero($panel->fab_complete_percent) ?> %
                                        </td>
                                        <td class="text-right tdnowrap"> 
                                            <?= MyFormatter::asDecimal2_emptyZero($panel->fab_assign_percent) ?> %
                                        </td>
                                        <?php
                                        if (!empty($progress)) {
                                            foreach ((array) $toDos as $key => $toDo) {
                                                $code = $toDo->code;
                                                $hideDeleteIcon = false;
                                                echo "<td class='text-right col-1'>";
                                                if (!empty($progress[$code])) {

                                                    foreach ($taskAssignFabs as $key => $taskAssignFab) {
                                                        if ($taskAssignFab->prod_fab_task_id === $progress[$code]['id']) {
                                                            $hideDeleteIcon = true;
                                                        }
                                                    }

                                                    if (!$progress[$code]['allAssigned'] && Yii::$app->user->can(AuthItem::ROLE_PrdnFab_Executive) && $taskWeightIsValid) {
                                                        echo Html::a($progress[$code]['progress'], ['assign-task', 'id' => $progress[$code]['id']]);
                                                    } else {
                                                        echo $progress[$code]['progress'];
                                                    }

                                                    if (!$taskWeightIsValid) {
                                                        if ($progress[$code]['hasRecord'] || $hideDeleteIcon) {
                                                            echo '<i class="fas fa-list ml-2 text-secondary"></i>';
                                                            echo '<i class="fas fa-trash ml-2 text-secondary"></i>';
                                                        } else {
                                                            echo '<i class="fas fa-list ml-2 text-secondary"></i>';
                                                            echo Html::a('<i class="fas fa-trash ml-2" style="color: red;"></i>', ['delete-fab-task', 'taskId' => $progress[$code]['id'], 'id' => $model->id], [
                                                                'class' => 'delete-task-link',
                                                                'data' => [
                                                                    'confirm' => 'Delete this task?',
                                                                    'method' => 'post',
                                                                ],
                                                            ]);
                                                        }
                                                    } else {
                                                        if ($progress[$code]['hasRecord'] || $hideDeleteIcon) {
                                                            echo Html::a('<i class="fas fa-list ml-2"></i>', ['view-assigned-task', 'taskId' => $progress[$code]['id']]);
                                                            echo '<i class="fas fa-trash ml-2 text-secondary"></i>';
                                                        } else {
                                                            echo '<i class="fas fa-list ml-2 text-secondary"></i>';
                                                            echo Html::a('<i class="fas fa-trash ml-2" style="color: red;"></i>', ['delete-fab-task', 'taskId' => $progress[$code]['id'], 'id' => $model->id], [
                                                                'class' => 'delete-task-link',
                                                                'data' => [
                                                                    'confirm' => 'Delete this task?',
                                                                    'method' => 'post',
                                                                ],
                                                            ]);
                                                        }
                                                    }
                                                } else {
                                                    echo "-";
                                                }
//                                                echo Html::a('<i class="fas fa-list"></i>', 'javascript:void(0)', ['value' => 'ajax-view-assigned-task?taskId=' . $progress[$code]['id'],'class' => 'ml-2 modalButton']);
                                                echo "</td>";
                                            }
                                        } else if ($isFinalized) {
                                            echo "<td colspan='" . sizeof($toDos) . "' class='text-center'> -- No Task Yet -- </td>";
                                        } else {
                                            echo "<td colspan='" . sizeof($toDos) . "' class='text-center'> -- Not Confirm Yet -- </td>";
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                }
                                ?>        
                            </tbody>
                        </table>
                        <?php
                    } else {
                        echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
                    }
                    ?>
                    <?= Html::a("Assign Task", 'javascript:submitForm("assign")', ['id' => 'submitButton', 'class' => 'btn btn-success']) ?>
                    <?= Html::a("Update Task Weight", 'javascript:submitForm("update-weight")', ['id' => 'submitButton', 'class' => 'btn btn-success']) ?>
                    <?php
                    ActiveForm::end();
                    ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<script>
    $(function () {
        $(document).on('beforeSubmit', 'form', function (event) {
            setPositionCookie();
        });

        // check if we should jump to postion.
        getPositionCookie();

        $("#panelCheckAll").on('change', function () {
            if ($(this).is(":checked")) {
                $(".panelCheckBox").prop("checked", true);
            } else {
                $(".panelCheckBox").prop("checked", false);
            }
        });
    });


    function getGridview() {
        var url = '/working/prospect/delete-client-ajax';
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                id: id
            }
        }).done(function (response) {
            if (response.data.success === true) {
                reloadClientDiv();
            }
        }).fail(function (xhr, textStatus, errorThrown) {
//            alert(xhr.responseText);
        });
    }

//    function submitForm() {
//        if ($(".panelCheckBox").is(":checked")) {
//            $("#myForm").submit();
//        } else {
//            alert("Please select panel(s)");
//        }
//    }

    function submitForm(action) {
        if ($(".panelCheckBox").is(":checked")) {
            var form = $("#myForm");
            if (action === "assign") {
                form.attr('action', '<?= \yii\helpers\Url::to(['assign-task-multiple-panels', 'projectId' => $model->id]) ?>');
            } else if (action === "update-weight") {
                form.attr('action', '<?= \yii\helpers\Url::to(['update-task-weight-multiple-panels', 'projectId' => $model->id]) ?>');
            }
            form.submit();
        } else {
            alert("Please select panel(s)");
        }
    }

</script>