<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use common\models\User;
use frontend\models\cmms\CmmsFaultList;
use frontend\models\cmms\CmmsAssetList;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsFaultList */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    #item_table input,
    #item_table select,
    #item_table textarea {
        width: 100%;
        min-width: 0;
    }

    fieldset.form-group {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }
    
    a.disabled {
    pointer-events: none;
    opacity: 0.65;
}
</style>
<div class="card">
    <div class="card-body p-2 table-responsive">
        <div class="cmms-fault-list-form table-responsive" id="fault-form-container">

            <?php $form = ActiveForm::begin([
                'id' => 'fault-form',
                'options' => [
                            'autocomplete' => 'off',
                            'enctype' => 'multipart/form-data'
                        ],
//                'action' => ['/cmms/cmms-fault-list/bulk-update', 'moduleIndex' => $moduleIndex],
//                'method' => 'post', 
            ]); ?>

            <table class="table table-bordered align-middle" id="item_table">
            <thead class="table-dark text-center">
                <tr>
                    <th class="text-center">No.</th>
                    <th>Category Name</th>
                    <th class="text-center" width="35%">Instruction(s)</th>
                    <!--<th class="text-center">Results</th>-->
                    <th class="text-center">Standard</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <?php
                    if (!is_array($vModel)) {
                        $vModel = [$vModel];
                    }
                    $vModelMap = [];
                    foreach ($vModel as $v) {
                        $vModelMap[$v->id] = $v;
                    }
                ?>
                <?php
                    $details = array_filter($details, function ($md) use ($vModelMap) {
                        $row = $vModelMap[$md->id] ?? $md;
                        return (int)($row->active_sts ?? 1) === 1;
                    });
                ?>
                <!--<php $renderIndex = 0; ?>-->
                <?php foreach ($details as $key => $detail): ?>
                    <?= $this->render('_maintenance_details_rows', [
                        'detail' => $detail,
                        'key' => $key,
                        'form' => $form,
                        'moduleIndex' => $moduleIndex,
                        'pmCategoryDescs' => $detail->cmmsPmCategoryDescs ?: [new \frontend\models\cmms\CmmsPmCategoryDesc()],
                    ]) ?>
                <!--<php $renderIndex++; ?>-->
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12">   
                        <?php if ($moduleIndex === 'superior'): ?>
                            <a class='btn btn-primary' href='javascript:addRow()'> 
                                <i class="fas fa-plus-circle"></i></a>
                        <?php endif; ?>
                        <?=
                        Html::submitButton('Save', [
                            'id' => 'save-btn',
                            'class' => 'float-right btn btn-success'
                        ])
                        ?>
                    </td>
                </tr>
            </tfoot>
            </table>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    var modelId = "<?= $model->id; ?>";
    var currentKey = $('tr[id^="tr_"]').length;
    function updateRowIndices() {
        $('#listTBody tr:visible').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    }
    
    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-form-item', 'moduleIndex' => $moduleIndex]) ?>',
            type: 'POST',
            dataType: 'html',
            data: {
                modelId: modelId,
                key: currentKey++,
//                isUpdate: '<? $isUpdate; ?>',
            }
        }).done(function (response) {
            const $newRow = $(response);
            $("#listTBody").append($newRow);
            updateRowIndices(); 
        });
    }
    
    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $("#tr_" + rowNum).remove();
            $(".functionality-input").removeAttr("required");
            $("#toDelete-" + rowNum).val("1");
            $("#tr_" + rowNum + " .functionality-input").removeAttr("required");
            updateRowIndices();
        }
    }
    
    function markDelete(itemID, rowKey) {
        console.log(itemID);
        if (!itemID || itemID === 'null') {
            if (rowKey !== 0) {
                removeRow(rowKey);
            }
            return;
        }

        let ans = confirm('Are you sure you want to delete this item?');
        if (ans) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['ajax-delete-item']) ?>?id=' + itemID + '&moduleIndex=' + '<?= $moduleIndex ?>',
                method: 'POST',
                data: {
                    id: itemID,
                    moduleIndex: '<?= $moduleIndex ?>'
                },
//            headers: {
//                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
//            },
                success: function (response) {
                    if (response.success) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            $("#tr_" + rowKey).remove();
                            updateRowIndices();
                        }
                    } else {
                        alert("Failed to delete item: " + (response.error || "Unknown error"));
                    }
                },
                error: function () {
                    alert("Server error while deleting item.");
                }
            });
        }
    }
    
    function toggleInstructionBlocksForRow(detailKey, selectedValue) {
        const wrapper = $('.instruction-wrapper[data-detail-key="' + detailKey + '"]');
        
        wrapper.find('.instruction-row').each(function () {
            $(this).find('.yes-no-block, .status-block, .observation-block, .pass-fail-block').hide();
            
//            if (selectedText === 'General Condition') {
            if (selectedValue === '1') {
                $(this).find('.status-block').show();
            } else if (selectedValue === '2' ||
                       selectedValue === '3' ||
                       selectedValue === '4' ||
                       selectedValue === '5') {
//            else if (selectedText === 'Main Functioning System' || 
//                        selectedText === 'Motion System & Mechanical' ||
//                        selectedText === 'Electrical Inspection' ||
//                        selectedText === 'Cooling & Pneumatic System') {
                $(this).find('.observation-block').show();
            } else if (selectedValue === '6') {
//            else if (selectedText === 'Functional Tests') {
                $(this).find('.pass-fail-block').show();
            } else if (selectedValue) {
//            else if (selectedText && selectedText !== 'Select Maintenance Category') {
                $(this).find('.yes-no-block').show();
            }
        });
    }
    
    $(document).off('change', '.maintenance-category-dropdown').on('change', '.maintenance-category-dropdown', function () {
        const detailKey = $(this).data('row');
        const selectedValue = $(this).val();
//        const selectedText = $(this).find('option:selected').text().trim();
        toggleInstructionBlocksForRow(detailKey, selectedValue);
    });
    
    $(document).off('click', '.add-instruction').on('click', '.add-instruction', function () {
        const detailKey = $(this).data('detail-key');
        const wrapper = $('.instruction-wrapper[data-detail-key="' + detailKey + '"]');
//        const wrapper = $(this).siblings('.instruction-wrapper');
        const nextIndex = wrapper.find('.instruction-row').length;

        wrapper.append(`
            <div class="instruction-row mb-2 border rounded p-2" data-detail-key="${detailKey}" data-instruction-key="${nextIndex}">
                <input type="hidden" name="CmmsPmCategoryDesc[${detailKey}][${nextIndex}][id]" value="">
                <input type="text"
                       name="CmmsPmCategoryDesc[${detailKey}][${nextIndex}][instruction]"
                       class="form-control instruction-name mb-2"
                       placeholder="Instruction">
                
                <div class="conditional-block yes-no-block mb-2">
                    <p>Status?</p>
                    <label class="mr-3">
                            <input type="radio"
                                   name="CmmsPmCategoryDesc[${detailKey}][${nextIndex}][yes_no]"
                                   value="1"
                                   class="yes-no-radio">
                            Yes
                    </label>
                    
                    <label>
                        <input type="radio"
                               name="CmmsPmCategoryDesc[${detailKey}][${nextIndex}][yes_no]"
                               value="0"
                               class="yes-no-radio">
                        No
                    </label>
                </div>
                
                <div class="conditional-block status-block mb-2" style="display:none;">
                        <select name="CmmsPmCategoryDesc[${detailKey}][${nextIndex}][check_status]" class="form-control">
                            <option value="">Select condition</option> 
                            <option value="Good">Good</option> 
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                </div>
                <div class="conditional-block observation-block mb-2" style="display:none;">
                        <input type="text"
                            name="CmmsPmCategoryDesc[${detailKey}][${nextIndex}][observation_reading]"
                            class="form-control"
                            placeholder="Enter observations/readings">
                </div>
                
                <div class="conditional-block pass-fail-block mb-2">
                    <label class="mr-3">
                        <input type="radio"
                           name="CmmsPmCategoryDesc[${detailKey}][${nextIndex}][pass_fail]"
                           value="1"
                           class="pass-fail-radio">
                        Pass
                    </label>
                    
                    <label>
                        <input type="radio"
                           name="CmmsPmCategoryDesc[${detailKey}][${nextIndex}][pass_fail]"
                           value="0"
                           class="pass-fail-radio">
                        Fail
                    </label>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-instruction">x</button>
            </div>
        `);
         const selectedValue = $('.maintenance-category-dropdown[data-row="' + detailKey + '"]').val();
//         const selectedText = $('.maintenance-category-dropdown[data-row="' + detailKey + '"] option:selected').text().trim();
         toggleInstructionBlocksForRow(detailKey, selectedValue);
    });

    $(document).off('click', '.remove-instruction').on('click', '.remove-instruction', function () {
        $(this).closest('.instruction-row').remove();
    });
    
    $(document).ready(function () {
       $('.maintenance-category-dropdown').each(function () {
           const detailKey = $(this).data('row');
           const selectedValue = $(this).val();
//           const selectedText = $(this).find('option:selected').text().trim();
           toggleInstructionBlocksForRow(detailKey, selectedValue);
       }); 
    });
</script>