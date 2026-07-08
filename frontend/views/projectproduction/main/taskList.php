<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;

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
    <div class="row mb-3">
        <div class="col-md-8">
            <h4>
                <?=
                Html::a($model->project_production_code . ' <i class="fas fa-external-link-square-alt fa-sm"></i>', "javascript:void(0)",
                        [
                            'class' => 'modalButtonMedium',
                            'value' => '/production/production/ajax-view-project-detail?id=' . $model->id
                        ]
                )
                ?>
            </h4>
            <h6><?= Html::encode($model->name) ?></h6>
        </div>
        <div class="col-md-4 text-right align-self-start">
<?= Html::a('Update All Panel Weights', 'javascript:void(0)', ['class' => 'btn btn-primary', 'id' => 'btn-update-panel-weight',]) ?>       
        </div>
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
            <?= Html::a("Save", "javascript:void(0);", ["class" => "btn btn-success mb-2 mt-0 float-right px-3", "onclick" => "validateAndFinalize();"]); ?>
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
        <?php
        Modal::begin(['id' => 'updatePanelWeightModal', 'title' => '<h5>Update Panel Weight</h5>',]);
        ?>
        <div class="mb-3">
            <strong id="panelWeightLabel"></strong>
        </div>
        <div class="form-group">
            <label>Fabrication (%)</label>
            <input type="number"
                   class="form-control"
                   id="modalFabWeight"
                   min="0"
                   max="100"
                   step="0.01">
        </div>
        <div class="form-group">
            <label>Electrical (%)</label>
            <input type="number"
                   class="form-control"
                   id="modalElecWeight"
                   min="0"
                   max="100"
                   step="0.01">
        </div>
        <div class="form-group">
            <label>Total (%)</label>
            <input type="text"
                   class="form-control"
                   id="modalTotalWeight"
                   readonly>
        </div>
        <div class="form-group mb-3">
            <span id="modalPanelWeightMessage"></span>
        </div>
        <div class="text-right">
            <button
                type="button"
                class="btn btn-primary"
                id="btnApplyPanelWeight">
                Update
            </button>
        </div>
        <?php Modal::end(); ?>
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
<script>
    function updatePanelWeightButton() {
        let selected = $('.panel-checkbox:checked').length;
        if (selected > 0) {
            $('#btn-update-panel-weight').text(
                    'Update Selected Panel Weight (' + selected + ')'
                    );
        } else {
            $('#btn-update-panel-weight').text(
                    'Update All Panel Weights'
                    );
        }
    }
    $(document).on('change', '.panel-checkbox', function () {
        updatePanelWeightButton();
    });
    $(function () {
        updatePanelWeightButton();
    });
    $('#btn-update-panel-weight').on('click', function () {
        let selected = $('.panel-checkbox:checked').length;
        if (selected > 0) {
            $('#panelWeightLabel').html(
                    'Selected Panels : <strong>' + selected + '</strong>'
                    );
        } else {
            $('#panelWeightLabel').html(
                    'No panel selected. <strong>All panel weights will be updated.</strong>'
                    );
        }
        $('#modalFabWeight').val('');
        $('#modalElecWeight').val('');
        $('#modalTotalWeight').val('');
        $('#updatePanelWeightModal').modal('show');
    });
    $('#modalFabWeight, #modalElecWeight').on('input', function () {
        let fab = parseFloat($('#modalFabWeight').val()) || 0;
        let elec = parseFloat($('#modalElecWeight').val()) || 0;
        let total = fab + elec;
        $('#modalTotalWeight').val(total.toFixed(2));
        let message = $('#modalPanelWeightMessage');
        message.removeClass('text-danger text-warning');
        message.html('');
        if (total > 100) {
            message
                    .addClass('text-danger')
                    .html('Total weight cannot exceed 100%.');
        } else if (total < 100) {
            message
                    .addClass('text-warning')
                    .html('Total weight is less than 100%.');
        }
    });
    $('#btnApplyPanelWeight').on('click', function () {
        let fab = parseFloat($('#modalFabWeight').val()) || 0;
        let elec = parseFloat($('#modalElecWeight').val()) || 0;
        if ($('.panel-checkbox:checked').length > 0) {
            $('.panel-checkbox:checked').each(function () {
                let panelId = $(this).val();
                $('#tr_' + panelId + ' .fabPanel').val(fab);
                $('#tr_' + panelId + ' .elecPanel').val(elec);
                let type = $('#tr_' + panelId + ' .fabPanel').data('project-code');
                validatePanelWeight(panelId, type);
            });
        } else {
            $('.fabPanel').val(fab);
            $('.elecPanel').val(elec);
            $('tr[id^="tr_"]').each(function () {
                let panelId = $(this).attr('id').replace('tr_', '');
                let type = $(this).find('.fabPanel').data('project-code');
                validatePanelWeight(panelId, type);
            });
        }
        $('#updatePanelWeightModal').modal('hide');
    });
</script>
