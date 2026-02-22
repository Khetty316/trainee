<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->params['breadcrumbs'][] = ['label' => 'Fabrication Task Assignment'];
$this->params['breadcrumbs'][] = ['label' => 'Project List', 'url' => ['index-fab-project-list']];
$this->params['breadcrumbs'][] = ['label' => $project->project_production_code, 'url' => ['index-fab-project-panels', 'id' => $project->id]];
$this->params['breadcrumbs'][] = 'Update Panel Task Weight';
?>

<?php
$form = ActiveForm::begin([
    'id' => 'task-weight-form',
    'action' => ['save-task-weight-multiple-panels', 'projectId' => $project->id],
        ]);
?>
<div class="row">
    <h4 class="col-12">
        <?= Html::a($project->project_production_code . ' <i class="fas fa-external-link-square-alt fa-sm"></i>', "javascript:void(0)", ['class' => 'modalButtonMedium', 'value' => '/production/production/ajax-view-project-detail?id=' . $project->id]) ?>
    </h4>
    <h6 class="col-12"><?= Html::encode($project->name) ?></h6>
</div>
<fieldset class="form-group border p-3">
    <legend class="w-auto px-2  m-0 ">Task Weight Assignment</legend>
    <div class="row">
        <div class="col-xl-12 order-md-1">
            <strong><h5>Selected Panels:</h5></strong>
            <ul>
                <?php
                foreach ($prodFabTasks as $key => $prodFabTask):
                    $panel = $prodFabTask->projProdPanel;
                    if ($panel):
                        ?>
                        <li><?= Html::encode($panel->project_production_panel_code . ' - ' . $panel->panel_description) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" colspan="<?= count($refFabTask) ?>">Fabrication Tasks (Weight %)</th>
                            <th class="text-center" rowspan="2" style="width:15%;">Total (%)</th>
                        </tr>
                        <tr>
                            <?php foreach ($refFabTask as $refFabItem) { ?>
                                <th class="text-center" title="<?= Html::encode($refFabItem->code) ?>">
                                    <?= Html::encode($refFabItem->name) ?>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="panel-row text-center">
                            <?php foreach ($refFabTask as $refFabItem): ?>
                                <td>
                                    <?=
                                    Html::input('number', "TaskWeight[{$refFabItem->code}]", number_format($refFabItem->weight, 2), [
                                        'class' => 'form-control form-control-sm text-right task-weight-input',
                                        'data-task-code' => $refFabItem->code,
                                        'step' => '0.01',
                                        'min' => '0',
                                        'max' => '100',
                                        'placeholder' => '0.00'
                                    ])
                                    ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-center">
                                <div>
                                    <span class="total-weight font-weight-bold">0.00</span>%
                                </div>
                                <span class="panel-error-message"></span>
                            </td>
                        </tr>
                        <?php foreach ($panelIds as $panelId): ?>
                            <?= Html::hiddenInput('SelectedPanels[]', $panelId) ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</fieldset>

<div class="form-group mt-3 float-right">
    <?= Html::submitButton('Save Task Weights', ['class' => 'btn btn-success']) ?>
    <?= Html::button('Cancel', ['class' => 'btn btn-secondary', 'onclick' => 'window.history.back()']) ?>
</div>

<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        // Calculate total weight across all task inputs
        function calculateTotal() {
            let total = 0;
            $('.task-weight-input').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $('.total-weight').text(total.toFixed(2));
            return total;
        }

        // Validate the total weight
        function validateTotal() {
            const total = calculateTotal();
            const $msg = $('.panel-error-message');
            $msg.text('').removeClass('text-danger text-black');

            if (total > 100) {
                $msg.addClass('text-danger').text('Total weight cannot exceed 100%');
            } else if (total < 100 && total > 0) {
                $msg.addClass('text-black').text('Total weight is less than 100%');
            }
        }

        // Validate on input
        $('.task-weight-input').on('input', validateTotal);

        // Validate before form submit
        $('#task-weight-form').on('beforeSubmit', function (e) {
            e.preventDefault();
            if (calculateTotal() > 100)
                return false; // stop submission
            this.submit();
        });

        // Initialize
        validateTotal();
    });
</script>

