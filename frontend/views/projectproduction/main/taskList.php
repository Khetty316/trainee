<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = $model->project_production_code;
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view-production-main', 'id' => $model->id]];
$this->params['breadcrumbs'][] = "Task List";
?>
<style>
    .checkbox{
        transform: scale(1.3);
    }
</style>
<div class="task-list-view">
    <div class="row">
        <h4 class="col-12">
            <?= Html::a($model->project_production_code . ' <i class="fas fa-external-link-square-alt fa-sm"></i>', "javascript:void(0)", ['class' => 'modalButtonMedium', 'value' => '/production/production/ajax-view-project-detail?id=' . $model->id]) ?>
        </h4>
        <h6 class="col-12"><?= Html::encode($model->name) ?></h6>
    </div>
    <div class="row">
        <div class="col-12">
            <?php
            $panellist = $panels;
            $pushPanel = $finalize ? true : false;

            $sharedData = new \stdClass();
            $sharedData->taskAssigned = false;
            $formFinalize = ActiveForm::begin([
                        'options' => ['autocomplete' => 'off'],
                        'method' => 'post',
                        'action' => ['update-tasks', 'id' => $model->id, 'pushPanel' => $pushPanel],
                        'id' => 'myFinalizeForm'
            ]);

            if ($panellist) {
                array_multisort(array_column($panellist, "sort"), SORT_ASC, $panellist);
                if ($finalize) {
                    echo $this->render('_finalizedPanel', [
                        'model' => $model,
                        'panels' => $panels,
                        'prodFabTasks' => $prodFabTasks,
                        'prodElecTasks' => $prodElecTasks,
                        'refFabTask' => $refFabTask,
                        'refElecTask' => $refElecTask,
                        'finalize' => $finalize
                    ]);
                } else {
                    echo $this->render('_editFinalizedPanel', [
                        'model' => $model,
                        'panels' => $panels,
                        'prodFabTasks' => $prodFabTasks,
                        'prodElecTasks' => $prodElecTasks,
                        'refFabTask' => $refFabTask,
                        'refElecTask' => $refElecTask,
                        'fabPanelWeight' => $fabPanelWeight,
                        'elecPanelWeight' => $elecPanelWeight,
                        'finalize' => $finalize,
                        'sharedData' => $sharedData, // Pass the array by reference
                    ]);
                }
            } else {
                echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
            }
            ?>
        </div>
        <div class="col-12 order-md-1">

            <?=
            Html::a("Save", "javascript:void(0);", ["class" => "btn btn-success mb-2 mt-0 float-right px-3", "onclick" => "validateAndFinalize();"]);
            ?>

            <?php
            if (!$finalize) {
                if ($sharedData->taskAssigned) {
                    echo Html::a("Revert Finalize", "javascript:void(0);", ["class" => "btn btn-secondary mb-2 mt-0 mx-3 float-right px-3 disabled"]);
                } else {
                    echo Html::a("Revert Finalize", ["revert-finalize", 'panelId' => $panelId], ["class" => "btn btn-warning mb-2 mt-0 mx-3 float-right px-3 "]);
                }
            }
            ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
    function validateAndFinalize() {
        var totalPanelWeightErrors = $('.panel-error-message:contains("Total weight cannot exceed 100%")');
        if (totalPanelWeightErrors.length > 0) {
            return false;
        } else {
            if (confirm("Are you sure you want to proceed?")) {
                $("#myFinalizeForm").submit();
            }
        }
    }

    // Fabrication "Select All" checkboxes
    $(".select_all_fab").on('change', function () {
        var isChecked = $(this).is(":checked");
        var columnIndex = $(this).closest('th').index();

        $(this).closest('table').find("td:nth-child(" + (columnIndex + 2) + ") .taskCheckBox.fab-checkbox").prop("checked", isChecked);
    });

    // Electrical "Select All" checkboxes
    $(".select_all_elec").on('change', function () {
        var isChecked = $(this).is(":checked");
        var columnIndex = $(this).closest('th').index();

        $(this).closest('table').find("td:nth-child(" + (columnIndex + 2) + ") .taskCheckBox.elec-checkbox").prop("checked", isChecked);
    });
</script>


