<?php
use yii\helpers\Html;
use frontend\models\cmms\CmmsAssetFaults;
?>
<tr id="tr_<?= $key ?>" data-index="<?= $key ?>">
    <td class="text-center"><?= $key + 1 ?></td>

    <td>
        <!--<php if ($moduleIndex === 'superior'): ?>-->
            <?php $categories = \frontend\models\cmms\RefPmCategory::getActiveDropdownlist_by_id(); ?>
            <?= $form->field($detail, "[{$key}]maintenance_category_id")
                ->dropDownList($categories, [
                    'prompt' => 'Select Maintenance Category',
                    'class' => 'form-control maintenance-category-dropdown',
                    'data-row' => $key,
                    'disabled' => $moduleIndex === 'assigned_tasks',
                    ])
                ->label(false) 
            ?>
        <?php if ($moduleIndex === 'assigned_tasks'): ?>
                <?= Html::activeHiddenInput($detail, "[{$key}]maintenance_category_id") ?>
            <?php endif; ?>
    </td>

    <td>
        <div class="instruction-wrapper" data-detail-key="<?= $key ?>">
            <?php foreach ($pmCategoryDescs as $p => $desc): ?>
                <div class="instruction-row mb-2 border rounded p-2" data-detail-key="<?= $key ?>" data-instruction-key="<?= $p ?>">
                    <?= Html::activeHiddenInput($desc, "[{$key}][{$p}]id") ?>

                    <?= $form->field($desc, "[{$key}][{$p}]instruction")
                        ->textArea([
                            'class' => 'form-control instruction-name mb-2',
                            'placeholder' => 'Add Instruction',
                            'readonly' => $moduleIndex === 'assigned_tasks'
                        ])->label(false) 
                    ?>
                    
                    <div class="conditional-block yes-no-block mb-2">
                        <p>Status?</p>
                        <label class="mr-3">
                            <?= Html::radio(
                                "CmmsPmCategoryDesc[{$key}][{$p}][yes_no]",
                                (string)$desc->yes_no === '1',
                                [
                                    'value' => 1,
                                    'class' => 'yes-no-radio'
                                ]
                            ) ?>
                            Yes
                        </label>

                        <label>
                            <?= Html::radio(
                                "CmmsPmCategoryDesc[{$key}][{$p}][yes_no]",
                                (string)$desc->yes_no === '0',
                                [
                                    'value' => 0,
                                    'class' => 'yes-no-radio'
                                ]
                            ) ?>
                            No
                        </label>
                    </div>
                    
                    <div class="conditional-block status-block mb-2" style="display:none;">
                        <?= Html::dropDownList(
                                "CmmsPmCategoryDesc[{$key}][{$p}][check_status]",
                                $desc->check_status ?? null,
                                [
                                    'Good' => 'Good',
                                    'Fair' => 'Fair',
                                    'Poor' => 'Poor',
                                ],
                                [
                                    'prompt' => 'Select condition',
                                    'class' => 'form-control'
                                ]
                        )
                        ?>
                    </div>
                    
                    <div class="conditional-block observation-block mb-2" style="display:none;">
                        <?= Html::textArea(
                                "CmmsPmCategoryDesc[{$key}][{$p}][observation_reading]",
                                $desc->observation_reading ?? '',
                                [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter observations/readings'
                                ]
                        )
                        ?>
                    </div>
                    
                    <div class="conditional-block pass-fail-block mb-2" style="display:none;">
                        <p>Status?</p>
                        <label class="mr-3">
                            <?= Html::radio(
                                "CmmsPmCategoryDesc[{$key}][{$p}][pass_fail]",
                                (string)$desc->pass_fail === '1',
                                [
                                    'value' => 1,
                                    'class' => 'pass-fail-radio'
                                ]
                            ) ?>
                            Pass
                        </label>

                        <label>
                            <?= Html::radio(
                                "CmmsPmCategoryDesc[{$key}][{$p}][pass_fail]",
                                (string)$desc->pass_fail === '0',
                                [
                                    'value' => 0,
                                    'class' => 'pass-fail-radio'
                                ]
                            ) ?>
                            Fail
                        </label>
                    </div>
                    <?php if ($moduleIndex === 'superior'): ?>
                        <button type="button" class="btn btn-danger btn-sm remove-instruction">x</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($moduleIndex === 'superior'): ?>
            <button type="button"
                    class="btn btn-sm btn-primary add-instruction"
                    data-detail-key="<?= $key ?>">
                + Add Instruction
            </button>
        <?php endif; ?>
    </td>
    <td></td>

    <td>
        <?= $form->field($detail, "[{$key}]remarks")->textArea()->label(false) ?>
    </td>
    <?php if ($moduleIndex === 'superior'): ?>
        <td>
            <input type="hidden" name="toDelete[<?= $key ?>]" id="toDelete-<?= $key ?>" value="0">

            <?php if ($detail->id): ?>
                <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                   onclick="markDelete(<?= $detail->id ?>, <?= $key ?>)">
                    <i class="fas fa-minus-circle"></i>
                </a>
            <?php else: ?>
                <a href="javascript:void(0);" class="btn btn-danger btn-sm"
                   onclick="removeRow(<?= $key ?>)">
                    <i class="fas fa-minus-circle"></i>
                </a>
            <?php endif; ?>
        </td>
    <?php endif; ?>
</tr>