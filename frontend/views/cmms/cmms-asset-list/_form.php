<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use frontend\models\cmms\CmmsAssetList;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsAssetList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cmms-asset-list-form">

    <?php $form = ActiveForm::begin(); ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">Asset Details:</legend>
        <table class="table table-bordered mb-0" id="asset_table">
        <thead class="table-dark">
            <tr>
                <th class="text-center" width="10%">Area</th>
                <th class="text-center" width="10%">Section</th>
                <th class="text-center" width="10%">Asset ID</th>
                <th class="text-center" width="10%">Asset Name</th>
                <th class="text-center" width="10%">Manufacturer</th>
                <th class="text-center" width="10%">Serial No.</th>
                <th class="text-center" width="10%">Date of Purchase</th>
                <th class="text-center" width="10%">Date of Installation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?php $areas = CmmsAssetList::getAreas(); ?>
                    <?= $form->field($model, 'area')->textInput([
                        'maxlength' => true, 
                        'list' => 'areaList',
                        ])->label(false) ?>
                    <datalist id="areaList">
                        <?php
                        foreach ($areas as $area) {
                            echo "<option value='{$area}'>";
                        }
                        ?>
                    </datalist>
                </td>

                <td>
                    <?php $sections = CmmsAssetList::getSections(); ?>
                    <?= $form->field($model, 'section')->textInput([
                        'maxlength' => true,
                        'list' => 'sectionList',
                        ])->label(false) ?>
                    <datalist id="sectionList">
                        <?php
                        foreach ($sections as $section) {
                            echo "<option value='{$section}'>";
                        }
                        ?>
                    </datalist>
                </td>

                <td>
                    <?php $assetCodes = CmmsAssetList::getAssetCodes(); ?>
                    <?= $form->field($model, 'asset_id')->textInput([
                        'maxlength' => true,
                        'list' => 'assetCodeList',
                        ])->label(false) ?>
                    <datalist id="assetCodeList">
                        <?php
                        foreach ($assetCodes as $assetCode) {
                            echo "<option value='{$assetCode}'>";
                        }
                        ?>
                    </datalist>
                </td>

                <td>
                    <?php $assetNames = CmmsAssetList::getAssetNames(); ?>
                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true,
                        'list' => 'assetNameList'
                        ])->label(false) ?>
                    <datalist id="assetNameList">
                        <?php
                        foreach ($assetNames as $assetName) {
                            echo "<option value='{$assetName}'>";
                        }
                        ?>
                    </datalist>
                </td>

                <td><?= $form->field($model, 'manufacturer')->textInput(['maxlength' => true])->label(false) ?></td>

                <!--<? $form->field($model, 'part_description')->textInput(['maxlength' => true]) ?>-->

                <td><?= $form->field($model, 'serial_no')->textInput(['maxlength' => true])->label(false) ?></td>

                <td><?= $form->field($model, 'date_of_purchase')->input('date', [
                    'class' => 'form-control',
                ])->label(false); ?></td>

                <td><?= $form->field($model, 'date_of_installation')->input('date', [
                    'class' => 'form-control',
                ])->label(false); ?></td>
            </tr>
        </tbody>
        </table>
    </fieldset>
        <br><br>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">Asset Fault Information:</legend>
        <table class="table table-bordered mb-0" id="fault_table">
        <thead class="table-dark">
            <tr>
                <th class="text-center" width="5%">No.</th>
                <th class="text-center">Fault Type</th>
                <th class="text-center">Primary Fault</th>
                <th class="text-center">Secondary Fault</th>
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
                $faults = array_filter($faults, function ($md) use ($vModelMap) {
                    $row = $vModelMap[$md->id] ?? $md;
                    return (int)($row->is_deleted ?? 0) === 0;
                });
            ?>
            <?php $renderIndex = 0; ?>
            <?php foreach ($faults as $mD => $fault): ?>
                <?=
                    $this->render('_asset_details_form_row', [
                    'key' => $renderIndex,
                    'form' => $form,
                    'fault' => $vmodelMap[$fault->id] ?? $fault,
                    'isUpdate' => $isUpdate,
                ])
                ?>
            <?php $renderIndex++; ?>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="12">      
                        <a class='btn btn-primary' href='javascript:addRow()'> 
                            <i class="fas fa-plus-circle"></i></a>
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
    </fieldset>
    <?php ActiveForm::end(); ?>

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
            url: '<?= \yii\helpers\Url::to(['ajax-add-form-item']) ?>',
            type: 'POST',
            dataType: 'html',
            data: {
                modelId: modelId,
                key: currentKey++,
                isUpdate: '<?= $isUpdate; ?>'
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
                url: '<?= \yii\helpers\Url::to(['ajax-delete-item']) ?>?id=' + itemID,
                method: 'POST',
                data: {id: itemID},
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
</script>