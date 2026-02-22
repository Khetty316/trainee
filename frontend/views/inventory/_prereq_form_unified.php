<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
<style>
    .duplicate-row {
        background-color: #fff1f1 !important;
    }
    .model-cell .duplicate-warning {
        margin-top: 4px;
        font-size: 12px;
        color: red;
        font-weight: bold;
    }
    #save-btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
    }
    .deleted-row {
        text-decoration: line-through;
        opacity: 0.6;
        color: #6c757d;
    }
</style>

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'validateOnSubmit' => true,
    'id' => $isView ? 'view-form' : 'edit-form',
        ]);

if (!is_array($items)) {
    $items = [$items];
}

// Ensure vmodel is an array
if (!is_array($vmodel)) {
    $vmodel = [$vmodel];
}

// Filter out any null items
$items = array_filter($items, function ($item) {
    return is_object($item);
});

// Create map for easy lookup
$vmodelMap = [];
foreach ($vmodel as $v) {
    if (is_object($v) && property_exists($v, 'item_id') && $v->item_id !== null) {
        $vmodelMap[$v->item_id] = $v;
    }
}
?>

<div class="container-fluid">
    <div class="row mb-3">
        <?php if (!$isView): ?>
            <div class="col-12 col-md-6 col-lg-4 offset-md-6 offset-lg-8">
                <?=
                        $form->field($master, 'date_of_material_required')
                        ->input('date', [
                            'class' => 'form-control',
                            'required' => true,
                            'readonly' => $isView
                        ])
                        ->label('Date Of Material Required:')
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered mb-0" id="item_table">
        <thead class="table-dark">
            <?=
            $this->render('_prereq_table_header', [
                'isView' => $isView,
                'hasSuperiorUpdate' => $hasSuperiorUpdate,
                'moduleIndex' => $moduleIndex,
            ])
            ?>
        </thead>
        <tbody id="listTBody">
            <?php
            // Convert vmodel to array if needed
            if (!is_array($vmodel)) {
                $vmodel = [$vmodel];
            }

            // Create map for easy lookup
            $vmodelMap = [];
            foreach ($vmodel as $v) {
                $vmodelMap[$v->item_id] = $v;
            }
            ?>

            <?php foreach ($items as $i => $item): ?>
                <?php
                // Safety check - ensure item is an object
                if (!is_object($item)) {
                    continue;
                }

//                $itemModel = $vmodelMap[$item->item_id] ?? $item;
                $itemModel = isset($vmodelMap[$item->item_id]) ? $vmodelMap[$item->item_id] : $item;
                ?>


                <?php if ($isView): ?>
                    <!-- VIEW MODE -->
                    <?=
                    $this->render('_prereq_view_cells', [
                        'model' => $itemModel,
                        'index' => $i,
                        'hasSuperiorUpdate' => $hasSuperiorUpdate,
                        'worklist' => $worklists[$i] ?? null,
                        'departmentList' => $departmentList,
                        'currencyList' => $currencyList,
                    ])
                    ?>
                <?php else: ?>
                    <!-- EDIT/CREATE MODE -->
                    <?=
                    $this->render('_prereq_edit_cells', [
                        'form' => $form,
                        'model' => $itemModel,
                        'index' => $i,
                        'moduleIndex' => $moduleIndex,
                        'hasSuperiorUpdate' => $hasSuperiorUpdate,
                        'worklist' => $worklists[$i] ?? null,
                        'departmentList' => $departmentList,
                        'supplierList' => $supplierList,
                        'brandList' => $brandList,
                        'currencyList' => $currencyList,
                    ])
                    ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <?php if (!$isView || $moduleIndex === 'superior'): ?>
                <!-- This row will contain the Add Row and Save buttons -->
                <tr>
                    <td colspan="<?= $hasSuperiorUpdate && $isView ? '20' : '15' ?>">
                        <div class="row">
                            <?php if (($moduleIndex === 'personal' || $moduleIndex === 'inventory' || $moduleIndex === 'projcoor') && !$isView): ?>
                                <div class="col-2 col-sm-1 col-md-1 col-lg-1">
                                    <button type="button" class="btn btn-primary btn-block" onclick="PrereqForm.addRow()">
                                        <i class="fas fa-plus-circle"></i> Add Row
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>

        </tfoot>

    </table> 
</div>
<div class="row mt-3">
    <?php if (!$isView): ?>
        <div class="col-12 text-right">
            <?=
            Html::submitButton('Save', [
                'id' => 'save-btn',
                'class' => 'btn btn-success'
            ])
            ?>
        </div>
    <?php endif; ?>
</div>

<?php ActiveForm::end(); ?>

<?=
$this->render('_prereq_javascript', [
    'master' => $master,
    'items' => $items,
    'isView' => $isView,
    'isUpdate' => $isUpdate,
    'moduleIndex' => $moduleIndex,
    'hasSuperiorUpdate' => $hasSuperiorUpdate,
    'supplierList' => $supplierList,
    'brandList' => $brandList,
])
?>
<script>
    $(document).ready(function () {
        // Validate brands on form submission
        $('#<?= $isView ? 'view-form' : 'edit-form' ?>').on('beforeSubmit', function (e) {
            var hasInvalidBrand = false;
            var invalidBrands = [];

            // Check all brand dropdowns
            $('.brand-select').each(function () {
                var $select = $(this);
                var selectedValue = $select.val();
                var originalValue = $select.data('original-value');

                // If no brand is selected but there was an original value not in list
                if (!selectedValue && originalValue && $select.data('brand-exists') === 'false') {
                    hasInvalidBrand = true;
                    invalidBrands.push(originalValue);
                    $select.closest('td').addClass('table-danger');
                }
            });

            if (hasInvalidBrand) {
                e.preventDefault();
                alert('Cannot submit: The following brands are not in the inventory system:\n\n' +
                        invalidBrands.join('\n') +
                        '\n\nPlease select valid brands from the dropdown or contact inventory to add these brands.');
                return false;
            }

            return true;
        });

        // Remove error styling when user selects a valid brand
        $('.brand-select').on('change', function () {
            $(this).closest('td').removeClass('table-danger');
        });
    });
</script>