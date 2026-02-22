<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\helpers\Url;
use yii\helpers\ArrayHelper;

use frontend\models\cmms\CmmsAssetList;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsFaultList */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
    use yii\bootstrap4\Modal;

    Modal::begin([
        'id' => 'modalSingle',
        'size' => Modal::SIZE_LARGE,
    ]);
    echo '<h5 id="modalSingleTitle"></h5>';
    echo '<div id="modalSingleContent"></div>';
    Modal::end();
?>
<?php $key = $faultListDetail->id ?? 'new'; ?>
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
<div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-body">
<div class="cmms-fault-list-form table-responsive">

    <?php $form = ActiveForm::begin([
        'id' => 'fault-form',
        'options' => [
                    'autocomplete' => 'off',
                    'enctype' => 'multipart/form-data'
                ],
    ]); ?>
    <?= $form->field($model, 'reported_by')->hiddenInput()->label(false) ?>
<!--    <? Html::hiddenInput('area', $assetArea) ?>
    <? Html::hiddenInput('section', $assetSection) ?>
    <? Html::hiddenInput('asset_code', $assetCode) ?>-->
<!--    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">Asset Details:</legend>
    <table class="table table-bordered mb-0 w-100" id="item_table">
        <thead class="table-dark">
            <tr>
                <th>Asset Code</th>
                <th>Asset Area</th>
                <th>Asset Section</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <php $assetCodes = CmmsAssetList::getAssetCodes(); ?>
                    <?
                        $form->field($faultListDetail, "fault_asset_code")->textInput([
                            'list' => 'idList',
                            'class' => 'fault-asset-code'
                        ])->label(false);
                    ?>
                    <datalist id="idList">
                        <php
                        foreach ($assetCodes as $code) {
                            echo "<option value='{$code}'>";
                        }
                        ?>
                    </datalist>
                </td>
                <td>
                    <?
                        $form->field($faultListDetail, "fault_area")->textInput([
                            'list' => 'assetAreaList',
                            'class' => 'fault-area'
                        ])->label(false);
                    ?>
                    <datalist id="assetAreaList"></datalist>
                </td>
                <td>
                    <? 
                        $form->field($faultListDetail, "fault_section")->textInput([
                            'list' => 'assetSectionList',
                            'class' => 'fault-section'
                        ])->label(false);
                    ?>
                    <datalist id="assetSectionList"></datalist>
                </td>
            </tr>
        </tbody>
    </table>
    </fieldset>-->
    <div class="alert alert-warning small">
        <i class="fas fa-solid fa-triangle-exclamation"></i>️ Newly selected photos will be lost if you navigate away before saving.
    </div>
    <!--<fieldset class="form-group border p-3">-->
        <!--<legend class="w-auto px-2 m-0">Fault Details:</legend>-->
<!--<? $form->field($model, 'status')->hiddenInput()->label(false) ?>-->
    <table class="table table-bordered mb-0" id="item_table">
    <thead class="table-dark">
        <tr>
            <th class="text-center" width="15%">Asset ID</th>
            <th class="text-center" width="15%">Fault Type</th>
            <th class="text-center">Primary Description</th>
            <th class="text-center">Secondary Description</th>
            <!--<th width="15%">Machine Breakdown Type</th>-->
            <th class="text-center" width="20%">Fault Priority</th>
            <th class="text-center" width="15%">Photos</th>
            <th class="text-left" width="30%">Remark</th>
        </tr>
    </thead>
    <tbody id="listTBody">
        <!--<td class="text-center"><? $key + 1 ?></td>-->
        <?= Html::activeHiddenInput(
            $faultListDetail,
            "id"
        ) ?>
        <tr>
            <td>
                <?php $assetIDs = ArrayHelper::map(
                         CmmsAssetList::find()
                                ->where(['active_sts' => 1])
                                ->all(),
                                'id', 'id'
                        ); 
                ?>
                <?=
                    $form->field($faultListDetail, "cmms_asset_list_id")->dropDownList(
                            $assetIDs,
                            [
                                'id' => 'id-dropdown',
                                'prompt' => 'Select ID'
                            ]
//                        ->textInput([
//                        'list' => 'idList',
//                        'class' => 'asset-id'
//                            ]
                        )->label(false);
                ?>
<!--                <? Html::a(
                    '<i class="bi bi-eye"></i>',
                    'javascript:void(0);',
                    [
                        'class' => 'modalButtonSingle btn btn-sm btn-success disabled',
                        'id' => 'view-asset-btn',
                        'data-url' => Url::to(['view-asset-details']),
                        'data-modaltitle' => 'Asset Details',
                        'aria-disabled' => 'true', // disabled until ID is selected
                    ]
                ); ?>-->
                <?= Html::a(
                    '<i class="bi bi-eye"></i>',
                    'javascript:void(0);',
                    [
                        'class' => 'modalButtonSingle btn btn-sm btn-success disabled',
                        'id' => 'view-asset-btn',
                        'data-url' => Url::to(['view-asset-details']), //'id' => $faultListDetail->cmms_asset_list_id]),
                        'data-back-url' => Url::to(['cmms-fault-list/fault-form-modal', 'id' => $model->id ?? null]),
                        'data-modaltitle' => 'Asset Details',
                        'area-disabled' => 'true',
                    ]
                ); ?>
<!--                <datalist id="idList">
                    <php
                    foreach ($assetIDs as $id) {
                        echo "<option value='{$id}'>";
                    }
                    ?>
                </datalist>-->
            </td>
            <td>
                <?php if ($moduleIndex === 'superior'): ?>
                    <?= Html::encode(
                        CmmsAssetList::getFaultType_by_ID($faultListDetail->cmms_asset_list_id)[$faultListDetail->fault_type] ?? '-'
                    ) ?>
                    <?= Html::activeHiddenInput(
                        $faultListDetail,
                        "fault_type"
                    ) ?>
                <?php else: ?>
                <?php if (!$isUpdate): ?>
                    <?=  
                        $form->field($faultListDetail, "fault_type")->dropDownList(
                                [],
                                [
                                    'id' => 'type-dropdown',
            //                        'data-key' => $key,
                                    'prompt' => 'Select Fault Type'
                                ]
                        )->label(false);
                    ?>
                <?php else: ?>
                    <?=  
                        $form->field($faultListDetail, "fault_type")->dropDownList(
                                CmmsAssetList::getFaultType_by_ID($faultListDetail->cmms_asset_list_id),
                                [
                                    'id' => 'type-dropdown',
            //                        'data-key' => $key,
                                    'prompt' => 'Select Fault Type'
                                ]
                        )->label(false);
                    ?>
                <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!$isUpdate): ?>
                    <?=
                        $form->field($faultListDetail, "fault_primary_detail")->dropDownList(
                                [],
                                [
                                    'id' => 'primary-dropdown',
        //                            'data-key' => $key,
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
                            "fault_primary_detail"
                        ) ?>
                    <?php else: ?>
                    <?=
                        $form->field($faultListDetail, "fault_primary_detail")->dropDownList(
                            CmmsAssetList::getPrimaryFault_by_type($faultListDetail->fault_type),
                                [
                                    'id' => 'primary-dropdown',
        //                            'data-key' => $key,
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
                        $form->field($faultListDetail, "fault_secondary_detail")->dropDownList(
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
                            "fault_secondary_detail"
                        ) ?>
                    <?php else: ?>
                    <?=
                        $form->field($faultListDetail, "fault_secondary_detail")->dropDownList(
                              CmmsAssetList::getSecondaryFault($faultListDetail->fault_primary_detail)  
                        )->label(false);
                    ?>
                <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <?php 
                    $priorityList = \frontend\models\cmms\RefMachinePriority::getActiveDropdownlist_by_id();
                ?>
                <?= 
                    $form->field($faultListDetail, "machine_priority_id")->dropDownList(
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
                    <!--<div class="existing-photos">-->
                    <?php foreach ($faultListDetail->cmmsMachinePhotos as $photo): ?>
                        <?php if (!$photo->is_deleted): ?>
                            <div class="photo-item d-inline-block me-2 mb-2" data-photo-id="<?= $photo->id ?>">
                                <!--<a href="<? Yii::getAlias('@web') . '/uploads/cmms-fault-list/' . $photo->file_name ?>" target="_blank">-->
                                <img src="<?= Yii::getAlias('@web') . '/uploads/cmms-fault-list/' . $photo->file_name ?>"
                                     class="img-thumbnail"
                                     style="width:80px">
                                <!--</a>-->
                                <button type="button" class="btn btn-sm btn-danger remove-existing-photo mt-1 w-100"
                                        data-photo-id="<?= $photo->id ?>" 
                                        data-key="<?= $key ?>">
                                    Remove
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <!--preview container for new uploads-->
                <div class="new-photo-preview mt-2" data-key="<?= $key ?>"></div>
                <!--<div class="new-photo-preview mt-2"></div>-->
                <div class="delete-photo-inputs" data-key="<?= $key ?>"></div>
                <!--<div class="delete-photo-inputs"></div>-->
            </td>
            <?php endif; ?>
            <td>
                <?php if ($moduleIndex === 'superior'): ?>
                <?= Html::encode($faultListDetail->additional_remarks); ?>
                <?= Html::activeHiddenInput(
                            $faultListDetail,
                            "additional_remarks"
                        ) ?>
                <?php else: ?>
                <?=
                    $form->field($faultListDetail, "additional_remarks")->textarea()->label(false);
                ?>
                <?php endif; ?>
            </td>
<!--    <php if ($moduleIndex === 'personal'): ?>
    <td>
        <input type="hidden" name="[<? $key ?>]toDelete" id="toDelete-<? $key ?>" value="0">
            <php if ($isUpdate && $faultListDetail->id !== null): ?>
                <a href="javascript:void(0);" class="btn btn-danger btn-sm" 
                   onclick="markDelete(<? $faultListDetail->id ?>, <? $key ?>)">
                    <i class="fas fa-minus-circle"></i>
                </a>
            <php else: ?>
                <a href="javascript:void(0);" class="btn btn-danger btn-sm" 
                   onclick="removeRow(<? $key ?>)">
                    <i class="fas fa-minus-circle"></i> 
                </a>
            <php endif; ?>
    </td>
    <php endif; ?>-->
</tr>
<!--        <php 
            if (!is_array($vModel)) {
                $vModel = [$vModel];
            }
        $vModelMap = [];
            foreach ($vModel as $v) {
            $vModelMap[$v->fault_list_detail_id] = $v;
            }
        ?>
        <php
            $faultListDetails = array_filter($faultListDetails, function ($md) use ($vModelMap) {
                $row = $vModelMap[$md->id] ?? $md;
                return (int)($row->is_deleted ?? 0) === 0;
            });
        ?>-->
        
<!--        <php $renderIndex = 0; ?>
        <php foreach ($faultListDetails as $mD => $faultListDetail): ?>
            <?
            $this->render('_form_row', [
//                'key' => $mD,
                'key' => $renderIndex,
                'form' => $form,
//                'model' => $vmodelMap[$item->id] ?? $item,
                'faultListDetail' => $vmodelMap[$faultListDetail->id] ?? $faultListDetail,
//                'machineBreakdownTypeList' => frontend\models\cmms\RefMachineBreakdownType::getActiveDropdownlist_by_id(),
                'priorityList' => \frontend\models\cmms\RefMachinePriority::getActiveDropdownlist_by_id(),
//                'faultAreaList' => \frontend\models\cmms\CmmsAssetList::getActiveDropdownlist(),
//                'faultSectionList' => \frontend\models\cmms\CmmsAssetList::getSections(),
                'isUpdate' => $isUpdate,
//                'assetCode' => $assetCode,
                'moduleIndex' => $moduleIndex
            ])
            ?>
        <php $renderIndex++; ?>
        <php endforeach; ?>-->
    </tbody>
<!--    <tfoot>
        <tr>
            <td colspan="12">      
                <php if ($moduleIndex === 'personal'): ?>
                    <a class='btn btn-primary' href='javascript:addRow()'> 
                        <i class="fas fa-plus-circle"></i></a>
                <php endif; ?>
                <?
                Html::submitButton('Save', [
                    'id' => 'save-btn',
                    'class' => 'float-right btn btn-success'
                ])
                ?>
            </td>
        </tr>
    </tfoot>-->
    </table>
    <!--</fieldset>-->
        <?=
            Html::submitButton('Save', [
                'id' => 'save-btn',
                'class' => 'float-right btn btn-success'
            ])
        ?>
    <?php ActiveForm::end(); ?>

</div>
        </div>
    </div>
    </div>
<!--<php
$this->registerJs(<<<JS
$(document).on('click', 'a.modalButtonSingle', function (e) {
    e.preventDefault();

    const button = $(this);
    const url = button.attr('data-url');
    const title = button.data('modaltitle');

    if (!url || !url.includes('id=')) {
        return false;
    }

    $('#modalSingle').modal('show')
        .find('#modalSingleContent')
        .load(url);

    $('#modalSingleTitle').html(title);
});
JS);
?>-->

<?php 
    $primaryUrl = Url::to(['/cmms/cmms-fault-list/get-primary-fault-details']);
    $secondaryUrl = Url::to(['/cmms/cmms-fault-list/get-secondary-fault-details']);
?>
<!--<php if (!empty($saved)): ?>
    <div class="alert alert-success">
        Fault details saved successfully.
    </div>
<php endif; ?>-->
<script>
//    let faultFormHtml = null;
//    $(document).on('click', 'a.modalButtonSingle', function (e) {
//        e.preventDefault();
//
//        const button = $(this);
//        const url = button.attr('data-url');
//        const title = button.data('modaltitle');
//
//        if (!url || !url.includes('id=')) {
//            return false;
//        }
//        const modal = $('#modalSingle');
//        const content = modal.find('#modalSingleContent');
//        const modalTitle = modal.find('#modalSingleTitle');
//        
//        // save only once
//        if (!faultFormHtml) {
//            faultFormHtml = content.children().detach();
//        }
//        // Show loading indicator
//        content.html('<div class="text-center p-3">Loading...</div>');
//        // Open modal
//        modal.modal('show');
//        // Load content
//        content.load($(this).data('url'));
//    });
    
//    $(document).on('submit', '#fault-form', function (e) {
//        e.preventDefault();
//
//        const form = this;
//        const formData = new FormData(form);
//
//        $.ajax({
//            url: form.action,
//            type: 'POST',
//            data: formData,
//            processData: false,
//            contentType: false,
//            success: function (html) {
//                $('#modalSingleContent').html(html);
//            },
//            error: function (xhr) {
//                // Often still saved — show response for debugging
//                $('#modalSingleContent').html(xhr.responseText || 
//                    '<div class="alert alert-danger">Unexpected error occurred</div>');
//            }
//        });
//    });
    
    var modelId = "<?= $model->id; ?>";
    var currentKey = $('tr[id^="tr_"]').length;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    var selectedFilesMap = {};

$(document).on('change', '.photo-upload', function () {
    var key = $(this).data('key');
    selectedFilesMap[key] = Array.from(this.files);
    renderNewPreviews(key);
    rebuildFileInput(this, key);
});

function renderNewPreviews(key) {
    var container = $('.new-photo-preview[data-key="' + key + '"]');
    container.empty();

    (selectedFilesMap[key] || []).forEach(function (file, index) {
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

function rebuildFileInput(input, key) {
    var dt = new DataTransfer();
    (selectedFilesMap[key] || []).forEach(function (file) {
        dt.items.add(file);
    });
    input.files = dt.files;
}

$(document).on('click', '.remove-new-photo', function () {
    var key = $(this).data('key');
    var index = $(this).data('index');

    selectedFilesMap[key].splice(index, 1);

    var input = $('.photo-upload[data-key="' + key + '"]')[0];
    rebuildFileInput(input, key);
    renderNewPreviews(key);
});

$(document).on('click', '.remove-existing-photo', function () {
    var photoId = $(this).data('photo-id');

    $(this).closest('.photo-item').remove();

    $('.delete-photo-inputs').append(
        '<input type="hidden" name="DeletePhotos[]" value="' + photoId + '">'
    );
});

$(document).on('change', '#id-dropdown', function () {
    let selectedId = $(this).val();
    let button = $('#view-asset-btn');
    let baseUrl = button.attr('data-url').split('?')[0];
    let backUrl = '<?= Url::to(['fault-form-modal', 'id' => $model->id ?? null]) ?>';

    if (selectedId) {
        button
            .removeClass('disabled')
            .attr('aria-disabled', 'false')
            .attr('data-url', baseUrl + '?id=' + selectedId + '&backUrl=' + encodeURIComponent(backUrl));
    } else {
        button
            .addClass('disabled')
            .attr('aria-disabled', 'true')
            .attr('data-url', baseUrl);
    }
//});

//$(document).on('change', '#id-dropdown', function () {
//    let assetId = $(this).val();
    let typeDropdown = $('#type-dropdown');

    if (!selectedId) {
        typeDropdown.html('<option value="">Select Fault Type</option>');
        return;
    }
    $.ajax({
        url: '<?= Url::to(['get-fault-type-by-asset']) ?>',
        data: { id: selectedId },
        dataType: 'json',
        success: function(res) {
            let options = '<option value="">Select Fault Type</option>';
            $.each(res.options, function(key, val) {
                options += `<option value="${key}">${val}</option>`;
            });
            typeDropdown.html(options);
        },
        error: function() {
            typeDropdown.html('<option value="">Select Fault Type</option>');
        }
    });
});

//    var selectedFiles = [];
//        const primaryUrl = '$primaryUrl';
//        const secondaryUrl = '$secondaryUrl';

//    $(document).on('change', '.photo-upload', function () {
//        var key = $(this).data('key');
//        selectedFiles[key] = Array.from(this.files);
//        renderNewPreviews(key);
//    });
//
//    function renderNewPreviews(key) {
//        var container = $('.new-photo-preview[data-key="' + key + '"]');
//        container.empty();
//
//        if (!selectedFiles[key]) return;
//
//        selectedFiles[key].forEach(function (file, index) {
//            if (!file.type.match(/^image\//)) return;
//
//            var reader = new FileReader();
//            reader.onload = function (e) {
//                container.append(
//                    '<div class="photo-item d-inline-block me-2 mb-2">' +
//                        '<img src="' + e.target.result + '" class="img-thumbnail" style="width:80px">' +
//                        '<button type="button" class="btn btn-sm btn-warning w-100 remove-new-photo" ' +
//                            'data-key="' + key + '" data-index="' + index + '">' +
//                            'Remove' +
//                        '</button>' +
//                    '</div>'
//                );
//            };
//            reader.readAsDataURL(file);
//        });
//    }
//
//    $(document).on('click', '.remove-new-photo', function () {
//        var key = $(this).data('key');
//        var index = $(this).data('index');
//
//        selectedFiles[key].splice(index, 1);
//        rebuildFileInput(key);
//        renderNewPreviews(key);
//    });
//
//    function rebuildFileInput(key) {
//        var input = $('.photo-upload[data-key="' + key + '"]')[0];
//        var dt = new DataTransfer();
//
//        (selectedFiles[key] || []).forEach(function (file) {
//            dt.items.add(file);
//        });
//
//        input.files = dt.files;
//    }
//
//    $(document).on('click', '.remove-existing-photo', function () {
//        var key = $(this).data('key');
//        var photoId = $(this).data('photo-id');
//
//        $(this).closest('.photo-item').remove();
//
//        $('.delete-photo-inputs[data-key="' + key + '"]').append(
//            '<input type="hidden" name="DeletePhotos[' + key + '][]" value="' + photoId + '">'
//        );
//    });

    const primaryUrl = '<?= $primaryUrl ?>';
    const secondaryUrl = '<?= $secondaryUrl ?>';
    
    $(document).on('change', '#type-dropdown', function () {
        const type = $(this).val();
//        const key = $(this).data('key');
            
        const row = $(this).closest('tr');
        row.find('#primary-dropdown')
            .val('')
            .trigger('change');
            
        row.find('#secondary-dropdown')
            .val('')
            .trigger('change');

//        const primaryDropdown = $('select[name="CmmsFaultListDetails[' + key + '][fault_primary_detail]"]');
        const primaryDropdown = $('select[name="CmmsFaultListDetails[fault_primary_detail]"]');

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
//        const key = $(this).data('key');
//            console.log(key);
            
//        const secondaryDropdown = $('select[name="CmmsFaultListDetails[' + key + '][fault_secondary_detail]"]');
        const secondaryDropdown = $('select[name="CmmsFaultListDetails[fault_secondary_detail]"]');

        secondaryDropdown.empty().append('<option value="">Loading...</option>');
        
        if (!primary) {
            secondaryDropdown.html('<option value="">Select Secondary Fault</option>');
            return;
        }

        $.post(secondaryUrl, { primary: primary }, function (html) {
            secondaryDropdown.html(html);
        });
    });
    
    $(document).on('input', '.fault-asset-code', function () {
        const assetCode = $(this).val();
        const row = $(this).closest('tr');
        
        const areaInput = row.find('.fault-area');
        const sectionInput = row.find('.fault-section');
        
        areaInput.val('');
        sectionInput.val('');
        $('#assetAreaList').empty();
        $('#assetSectionList').empty();
        
        if (!assetCode) return;
        
        $.get(
            '<?= \yii\helpers\Url::to(['get-asset-areas']) ?>',
            { assetCode },
            function (areas) {
                areas.forEach(area => {
                    $('#assetAreaList').append(`<option value="${area}">`);
                });
            }
        );
    });
    
    $(document).on('input', '.fault-area', function () {
        const row = $(this).closest('tr');

        const assetCode = row.find('.fault-asset-code').val();
        const area = $(this).val();
        const sectionInput = row.find('.fault-section');

        sectionInput.val('');
        $('#assetSectionList').empty();

        if (!assetCode || !area) return;

        $.get(
            '<?= \yii\helpers\Url::to(['get-sections']) ?>',
            { 
                assetCode: assetCode, 
                assetArea: area 
            },
            function (sections) {
                sections.forEach(section => {
                    $('#assetSectionList').append(`<option value="${section}">`);
                });
            }
        );
    });

    
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
                isUpdate: '<?= $isUpdate; ?>',
//                asset_code: '<? $assetCode ?>',
//                area: '<? $assetArea ?>',
//                section: '<? $assetSection ?>',
                moduleIndex: '<?= $moduleIndex ?>'
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


