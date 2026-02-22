<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use frontend\models\bom\BomMaster;

/* @var $this yii\web\View */
/* @var $bomMaster BomMaster */
/* @var $validationResults array */
/* @var $hasErrors boolean */
/* @var $itemsToUpdate array */
/* @var $productionPanelId int */

$production = $bomMaster->productionPanel->projProdMaster;
$this->title = 'Inventory Validation Check';

$this->params['breadcrumbs'][] = ['label' => 'Stock Outbound', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['view-panels', 'id' => $production->id]];
$this->params['breadcrumbs'][] = $this->title;

// Count status
$errorCount = 0;
$warningCount = 0;
$successCount = 0;

foreach ($validationResults as $result) {
    if ($result['status'] === 'error')
        $errorCount++;
    elseif ($result['status'] === 'warning')
        $warningCount++;
    else
        $successCount++;
}
?>

<h4><?= Html::encode($this->title) ?></h4>

<!-- Summary Alert -->
<?php if ($hasErrors): ?>
    <div class="alert alert-danger">
        <h5>
            <i class="fas fa-exclamation-triangle text-danger"></i> 
            <strong>Cannot Proceed - Validation Failed</strong>
        </h5>
        <p>The following issues must be resolved before initiating outbound:</p>
        <ul>
            <?php foreach ($validationResults as $result): ?>
                <?php if ($result['status'] === 'error'): ?>
                    <li>
                        <strong><?= Html::encode($result['description'] ?: $result['model_type']) ?>:</strong> 
                        <?= ($result['message']) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <p class="mb-0">
            <i class="fas fa-info-circle text-info"></i> 
            <small>Please update the BOM details or contact procurement to add missing items to inventory.</small>
        </p>
    </div>
<?php elseif ($warningCount > 0): ?>
    <div class="alert alert-warning">
        <h5>
            <i class="fas fa-exclamation-triangle text-warning"></i> 
            <strong>Warnings Detected</strong>
        </h5>
        <p>Some items have warnings but can still proceed:</p>
        <ul>
            <?php foreach ($validationResults as $result): ?>
                <?php if ($result['status'] === 'warning'): ?>
                    <li>
                        <strong><?= Html::encode($result['description'] ?: $result['model_type']) ?>:</strong> 
                        <?= ($result['message']) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <div class="alert alert-success">
        <h5>
            <i class="fas fa-check-circle text-success"></i> 
            <strong>All Items Validated Successfully</strong>
        </h5>
        <p class="mb-0">All <?= count($validationResults) ?> items are ready for outbound initiation.</p>
    </div>
<?php endif; ?>

<!-- Validation Table -->
<?php if (!empty($itemsToUpdate)): ?>
    <?php
    $form = ActiveForm::begin([
        'action' => ['update-and-initiate', 'productionPanelId' => $productionPanelId],
        'method' => 'post',
        'id' => 'update-form'
    ]);
    ?>
<?php else: ?>
    <?php
    $form = ActiveForm::begin([
        'action' => ['initiate-outbound-master', 'productionPanelId' => $productionPanelId],
        'method' => 'post',
        'id' => 'initiate-form'
    ]);
    ?>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-bordered" id="validation-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 20%;">Model/Type</th>
                <th style="width: 15%;">Brand</th>
                <th style="width: 25%;">Description</th>
                <th style="width: 8%;">Qty</th>
                <th style="width: 24%;">Validation Result</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($validationResults as $index => $result): ?>
                <tr data-index="<?= $index ?>">
                    <td class="text-right px-2 pt-1">
                        <?= $index + 1 ?>
                    </td>
                    <td class="p-1">
                        <div class="d-flex align-items-center">
                            <?= Html::encode($result['model_type']) ?>
                            <?php if (isset($result['found_model_name'])): ?>
                                <span class="badge badge-info ml-2 px-2 py-1">
                                    <i class="fas fa-arrow-right"></i> <?= Html::encode($result['found_model_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="p-1">
                        <div class="d-flex align-items-center">
                            <?= Html::encode($result['brand']) ?>
                            <?php if (isset($result['found_brand_name'])): ?>
                                <span class="badge badge-info ml-2 px-2 py-1">
                                    <i class="fas fa-arrow-right"></i> <?= Html::encode($result['found_brand_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="p-1">
                        <?= Html::encode($result['description']) ?>
                    </td>
                    <td class="p-1 text-right">
                        <?= $result['qty'] ?>
                        <?php if (isset($result['stock_available'])): ?>
                            <br>
                            <small class="<?= $result['qty'] > $result['stock_available'] ? 'text-danger' : 'text-success' ?>">
                                Stock: <?= $result['stock_available'] ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    <td class="p-1">
                        <div class="validation-message">
                            <?= nl2br(($result['message'])) ?>

                            <?php if (isset($result['found_model_id']) && isset($result['found_brand_id'])): ?>
                                <input type="hidden" 
                                       name="updateItems[<?= $index ?>][itemId]" 
                                       value="<?= $result['item']->id ?>">
                                <input type="hidden" 
                                       name="updateItems[<?= $index ?>][modelId]" 
                                       value="<?= $result['found_model_id'] ?>">
                                <input type="hidden" 
                                       name="updateItems[<?= $index ?>][brandId]" 
                                       value="<?= $result['found_brand_id'] ?>">
                                   <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="form-group mt-3">
    <?php if ($hasErrors): ?>
        <div class="alert alert-danger">
            <i class="fas fa-ban"></i> Cannot proceed until all errors are resolved.
        </div>
        <?=
        Html::a('Back to Panel',
                ['view-panels', 'id' => $production->id],
                ['class' => 'btn btn-secondary']
        )
        ?>

    <?php else: ?>
        <div class="row">
            <div class="col-md-12">
                <?=
                Html::a('Cancel',
                        ['view-panels', 'id' => $production->id],
                        ['class' => 'btn btn-danger float-right ml-2']
                )
                ?>
                <?php if (!empty($itemsToUpdate)): ?>
                    <?=
                    Html::submitButton(
                            'Proceed',
                            [
                                'class' => 'btn btn-success float-right',
                                'data' => [
                                    'confirm' => 'Initiate outbound for ' . $bomMaster->productionPanel->quantity . ' panels. Continue?'
                                ]
                            ]
                    )
                    ?>
                <?php else: ?>
                    <?=
                    Html::submitButton(
                            '<i class="fas fa-play-circle"></i> Proceed',
                            [
                                'class' => 'btn btn-success btn-block',
                                'data' => [
                                    'confirm' => 'Initiate outbound for ' . $bomMaster->productionPanel->quantity . ' panels. Continue?'
                                ]
                            ]
                    )
                    ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>

<style>
    .table-danger {
        background-color: #f8d7da !important;
    }
    .table-warning {
        background-color: #fff3cd !important;
    }
    .validation-message {
        font-size: 0.9em;
        line-height: 1.4;
    }
    .badge {
        font-size: 0.8em;
        padding: 0.25em 0.5em;
    }
    .btn-xs {
        padding: 0.1rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
</style>

<script>
    $(document).ready(function () {
        // Highlight rows with errors
        $('tr.table-danger').each(function () {
            $(this).find('.validation-message').addClass('text-danger font-weight-bold');
        });

        // Highlight rows with warnings
        $('tr.table-warning').each(function () {
            $(this).find('.validation-message').addClass('text-warning');
        });

        // Form confirmation
        $('#update-form, #initiate-form').on('submit', function (e) {
            var $form = $(this);
            var hasErrors = <?= $hasErrors ? 'true' : 'false' ?>;

            if (hasErrors) {
                e.preventDefault();
                alert('Cannot proceed. Please fix all errors first.');
                return false;
            }

            // Default confirmation handled by data-confirm attribute
            return true;
        });

        // Auto-scroll to first error if exists
<?php if ($errorCount > 0): ?>
            var $firstError = $('tr.table-danger').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 500);
            }
<?php endif; ?>
    });
</script>