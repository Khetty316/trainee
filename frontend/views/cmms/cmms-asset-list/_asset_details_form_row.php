<?php
use yii\helpers\Html;
use frontend\models\cmms\CmmsAssetFaults;
?>
<tr id="tr_<?= $key ?>" data-index="<?= $key ?> " name="currencyValue">
    <td class="text-center"><?= $key + 1 ?></td>
        <?= Html::activeHiddenInput($fault, "[$key]id"); ?>
        <?= Html::activeHiddenInput($fault, "[$key]is_deleted") ?>
        <?= Html::activeHiddenInput($fault, "[$key]updated_by") ?>
        <?= Html::activeHiddenInput($fault, "[$key]asset_id") ?>
        <?= Html::activeHiddenInput($fault, "[$key]active_sts") ?>
    <td>
        <?php 
            $defaultTypes = [
                'Electrical' => 'Electrical',
                'Mechanical' => 'Mechanical',
                'Operational' => 'Operational',
            ];
            $faultTypes = CmmsAssetFaults::getFaultTypes(); 
            $faultTypes = array_combine($faultTypes, $faultTypes);
            $faultTypes = array_replace($defaultTypes, $faultTypes);
        ?>
        <?= 
            $form->field($fault, "[$key]fault_type")->textInput([
                'list' => 'faultTypeList',
                'required' => true
                ])->label(false);
        ?>
        <datalist id="faultTypeList">
            <?php
            foreach ($faultTypes as $faultType) {
                echo "<option value='{$faultType}'>";
            }
            ?>
        </datalist>
    </td>
    <td>
        <?=
            $form->field($fault, "[$key]fault_primary_detail")->textInput([
            ])->label(false);
        ?>
    </td>
    <td>
        <?=
            $form->field($fault, "[$key]fault_secondary_detail")->textInput()->label(false);
        ?>
    </td>
    <td>
        <input type="hidden" name="[<?= $key ?>]toDelete" id="toDelete-<?= $key ?>" value="0">
            <?php if ($isUpdate && $fault->id !== null): ?>
                <a href="javascript:void(0);" class="btn btn-danger btn-sm" 
                   onclick="markDelete(<?= $fault->id ?>, <?= $key ?>)">
                    <i class="fas fa-minus-circle"></i>
                </a>
            <?php else: ?>
                <a href="javascript:void(0);" class="btn btn-danger btn-sm" 
                   onclick="removeRow(<?= $key ?>)">
                    <i class="fas fa-minus-circle"></i> 
                </a>
            <?php endif; ?>
    </td>
</tr>