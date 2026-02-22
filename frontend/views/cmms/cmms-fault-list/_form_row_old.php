<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\cmms\CmmsAssetList;
?>
<?php if ($isUpdate): ?>
    <?php $assetCode = $faultListDetail->fault_asset_code; ?>
<?php endif; ?>
<tr id="tr_<?= $key ?>" data-index="<?= $key ?> " name="currencyValue">
    <td class="text-center"><?= $key + 1 ?></td>
        <?= Html::activeHiddenInput(
            $faultListDetail,
            "[$key]id"
        ) ?>
        <?= Html::activeHiddenInput($faultListDetail, "[$key]fault_asset_code") ?>
        <?= Html::activeHiddenInput($faultListDetail, "[$key]fault_area") ?>
        <?= Html::activeHiddenInput($faultListDetail, "[$key]fault_section") ?>
<!--    <td>
        <?
            $form->field($faultListDetail, "[$key]fault_area")->dropDownList(
                    $faultAreaList,
                    [
                        'class' => 'area-dropdown',
                        'data-key' => $key,
                        'prompt' => 'Select area'
                    ]
            )->label(false);
        ?>
    </td>-->
<!--    <td>
        <php if (!$isUpdate): ?>
            <?
                $form->field($faultListDetail, "[$key]fault_section")->dropDownList(
                       [],
                       ['prompt' => 'Select section'],
                )->label(false);
            ?>
        <php else: ?>
            <? 
                $form->field($faultListDetail, "[$key]fault_section")->dropDownList(
                        \frontend\models\cmms\CmmsAssetList::getSections_by_Area($faultListDetail->fault_area)
                )->label(false);
            ?>
        <php endif; ?>
    </td>-->
<!--    <td>
        <? 
            $form->field($faultListDetail, "[$key]machine_breakdown_type_id")->dropDownList(
                $machineBreakdownTypeList
                )->label(false); 
        ?>
    </td>-->
    <td>
        <?php if ($moduleIndex === 'superior'): ?>
            <?= Html::encode(
                CmmsAssetList::getFaultType_by_code($assetCode)[$faultListDetail->fault_type] ?? '-'
            ) ?>
            <?= Html::activeHiddenInput(
                $faultListDetail,
                "[$key]fault_type"
            ) ?>
        <?php else: ?>
        <?=  
            $form->field($faultListDetail, "[$key]fault_type")->dropDownList(
                    CmmsAssetList::getFaultType_by_code($assetCode),
                    [
                        'id' => 'type-dropdown',
                        'data-key' => $key,
                        'prompt' => 'Select Fault Type'
                    ]
            )->label(false);
        ?>
        <?php endif; ?>
    </td>
    <td>
        <?php if (!$isUpdate): ?>
            <?=
                $form->field($faultListDetail, "[$key]fault_primary_detail")->dropDownList(
                        [],
                        [
                            'id' => 'primary-dropdown',
                            'data-key' => $key,
                            'prompt' => 'Select Primary Fault'
                        ],
                )->label(false);
            ?>
        <?php else: ?>
            <?php if ($moduleIndex === 'superior'): ?>
                <?= Html::encode(
                    CmmsAssetList::getPrimaryFault_by_type($faultListDetail->fault_type)[$faultListDetail->fault_primary_detail] ?? '-'
                ) ?>
                <?= Html::activeHiddenInput(
                    $faultListDetail,
                    "[$key]fault_primary_detail"
                ) ?>
            <?php else: ?>
            <?=
                $form->field($faultListDetail, "[$key]fault_primary_detail")->dropDownList(
                    CmmsAssetList::getPrimaryFault_by_type($faultListDetail->fault_type),
                        [
                            'id' => 'primary-dropdown',
                            'data-key' => $key,
                            'prompt' => 'Select Primary Fault'
                        ],
                )->label(false);
            ?>
        <?php endif; ?>
        <?php endif; ?>
    </td>
    <td>
        <?php if (!$isUpdate): ?>
            <?= 
                $form->field($faultListDetail, "[$key]fault_secondary_detail")->dropDownList(
                        [],
                        [
                            'id' => 'secondary-dropdown',
                            'prompt' => 'Select Secondary Fault'
                        ]
                )->label(false);
            ?>
        <?php else: ?>
        <?php if ($moduleIndex === 'superior'): ?>
                <?= Html::encode(
                    CmmsAssetList::getSecondaryFault($faultListDetail->fault_primary_detail)[$faultListDetail->fault_secondary_detail] ?? '-'
                ) ?>
                <?= Html::activeHiddenInput(
                    $faultListDetail,
                    "[$key]fault_secondary_detail"
                ) ?>
            <?php else: ?>
            <?=
                $form->field($faultListDetail, "[$key]fault_secondary_detail")->dropDownList(
                      CmmsAssetList::getSecondaryFault($faultListDetail->fault_primary_detail)  
                )->label(false);
            ?>
        <?php endif; ?>
        <?php endif; ?>
    </td>
    <td>
        <?= 
            $form->field($faultListDetail, "[$key]machine_priority_id")->dropDownList(
                $priorityList
            )->label(false); 
        ?>
    </td>
    <td>
        <?php if ($moduleIndex === 'superior'): ?>

        <?php if (!empty($faultListDetail->machinePhotos)): ?>

            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($faultListDetail->machinePhotos as $photo): ?>
                    <a href="<?= $photo->getUrl() ?>" target="_blank">
                        <img src="<?= $photo->getUrl() ?>"
                             style="width:60px; height:60px; object-fit:cover;"
                             class="img-thumbnail">
                    </a>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <span class="text-muted">No photos</span>
        <?php endif; ?>
            <?php else: ?>
        <?= 
        Html::fileInput(
                "CmmsMachinePhotos[$key][]",
                null,
                [
                    'multiple' => true,
                    'accept' => 'image/*',
                    'class' => 'photo-upload',
                    'data-key' => $key,
                ]
        )
        ?>
            
        <div class="existing-photos" data-key="<?= $key ?>">
            <?php foreach ($faultListDetail->cmmsMachinePhotos as $photo): ?>
                <?php if (!$photo->is_deleted): ?>
                    <div class="photo-item d-inline-block me-2 mb-2" data-photo-id="<?= $photo->id ?>">
                        <!--<a href="<? Yii::getAlias('@web') . '/uploads/cmms-fault-list/' . $photo->file_name ?>" target="_blank">-->
                        <img src="<?= Yii::getAlias('@web') . '/uploads/cmms-fault-list/' . $photo->file_name ?>"
                             class="img-thumbnail"
                             style="width:80px">
                        <!--</a>-->
                        <button type="button" class="btn btn-sm btn-danger remove-existing-photo mt-1 w-100"
                                data-photo-id="<?= $photo->id ?>" data-key="<?= $key ?>">
                            Remove
                        </button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <!--preview container for new uploads-->
        <div class="new-photo-preview mt-2" data-key="<?= $key ?>"></div>
        <div class="delete-photo-inputs" data-key="<?= $key ?>"></div>
    </td>
    <?php endif; ?>
    <td>
        <?php if ($moduleIndex === 'superior'): ?>
        <?= Html::encode($faultListDetail->additional_remarks); ?>
        <?= Html::activeHiddenInput(
                    $faultListDetail,
                    "[$key]additional_remarks"
                ) ?>
        <?php else: ?>
        <?=
            $form->field($faultListDetail, "[$key]additional_remarks")->textarea()->label(false);
        ?>
        <?php endif; ?>
    </td>
    <?php if ($moduleIndex === 'personal'): ?>
    <td>
        <input type="hidden" name="[<?= $key ?>]toDelete" id="toDelete-<?= $key ?>" value="0">
            <?php if ($isUpdate && $faultListDetail->id !== null): ?>
                <a href="javascript:void(0);" class="btn btn-danger btn-sm" 
                   onclick="markDelete(<?= $faultListDetail->id ?>, <?= $key ?>)">
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
<?php
    $primaryUrl = Url::to(['/cmms/cmms-fault-list/get-primary-fault-details']);
    $secondaryUrl = Url::to(['/cmms/cmms-fault-list/get-secondary-fault-details']);
    $this->registerJs(<<<JS
        var selectedFiles = {};
        const primaryUrl = '$primaryUrl';
        const secondaryUrl = '$secondaryUrl';

    $(document).on('change', '.photo-upload', function () {
        var key = $(this).data('key');
        selectedFiles[key] = Array.from(this.files);
        renderNewPreviews(key);
    });

    function renderNewPreviews(key) {
        var container = $('.new-photo-preview[data-key="' + key + '"]');
        container.empty();

        if (!selectedFiles[key]) return;

        selectedFiles[key].forEach(function (file, index) {
            if (!file.type.match(/^image\//)) return;

            var reader = new FileReader();
            reader.onload = function (e) {
                container.append(
                    '<div class="photo-item d-inline-block me-2 mb-2">' +
                        '<img src="' + e.target.result + '" class="img-thumbnail" style="width:80px">' +
                        '<button type="button" class="btn btn-sm btn-warning w-100 remove-new-photo" ' +
                            'data-key="' + key + '" data-index="' + index + '">' +
                            'Remove' +
                        '</button>' +
                    '</div>'
                );
            };
            reader.readAsDataURL(file);
        });
    }

    $(document).on('click', '.remove-new-photo', function () {
        var key = $(this).data('key');
        var index = $(this).data('index');

        selectedFiles[key].splice(index, 1);
        rebuildFileInput(key);
        renderNewPreviews(key);
    });

    function rebuildFileInput(key) {
        var input = $('.photo-upload[data-key="' + key + '"]')[0];
        var dt = new DataTransfer();

        (selectedFiles[key] || []).forEach(function (file) {
            dt.items.add(file);
        });

        input.files = dt.files;
    }

    $(document).on('click', '.remove-existing-photo', function () {
        var key = $(this).data('key');
        var photoId = $(this).data('photo-id');

        $(this).closest('.photo-item').remove();

        $('.delete-photo-inputs[data-key="' + key + '"]').append(
            '<input type="hidden" name="DeletePhotos[' + key + '][]" value="' + photoId + '">'
        );
    });
    
    $(document).on('change', '#type-dropdown', function () {
        const type = $(this).val();
        console.log(type);
        const key = $(this).data('key');
            
        const row = $(this).closest('tr');
        row.find('#primary-dropdown')
            .val('')
            .trigger('change');
            
        row.find('#secondary-dropdown')
            .val('')
            .trigger('change');

        const primaryDropdown = $('select[name="CmmsFaultListDetails[' + key + '][fault_primary_detail]"]');

        primaryDropdown.empty().append('<option value="">Loading...</option>');
        
        if (!type) {
            primaryDropdown.html('<option value="">Select Primary Fault</option>');
            return;
        }

        $.post(primaryUrl, { type: type }, function (html) {
            primaryDropdown.html(html);
        });
    });
    
    $(document).on('change', '#primary-dropdown', function () {
        const primary = $(this).val();
        const key = $(this).data('key');
            console.log(key);
            
        const secondaryDropdown = $('select[name="CmmsFaultListDetails[' + key + '][fault_secondary_detail]"]');

        secondaryDropdown.empty().append('<option value="">Loading...</option>');
        
        if (!primary) {
            secondaryDropdown.html('<option value="">Select Secondary Fault</option>');
            return;
        }

        $.post(secondaryUrl, { primary: primary }, function (html) {
            secondaryDropdown.html(html);
        });
    });
    JS);
?>