<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use frontend\models\cmms\CmmsAssetList;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsFaultList */
/* @var $form yii\widgets\ActiveForm */
?>
<!--<php
//    use yii\bootstrap4\Modal;
//
//    Modal::begin([
//        'id' => 'modalSingle',
//        'size' => Modal::SIZE_LARGE,
//    ]);
//    echo '<h5 id="modalSingleTitle"></h5>';
//    echo '<div id="modalSingleContent"></div>';
//    Modal::end();
?>-->
<!--<php $key = $model->id ?? 'new'; ?>-->
<?php
    $key = $model->id !== null
        ? $model->id
        : uniqid('new_', true);
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
<!--<div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-body">-->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="mb-1 font-weight-bold">
                Fault List - <?= Html::encode($model->id ?? 'New') ?>
            </h6>
            <small class="text-muted">
                Created on: <?= Yii::$app->formatter->asDate($model->reported_at, 'php:d M Y') ?>
            </small>
        </div>

        <div class="text-right mt-2 mt-md-0">
            <small class="text-muted d-block">Status</small>
            <span class="badge badge-info">
                <?= Html::encode($model->status ?? 'Draft') ?>
            </span>
        </div>
    </div>
    <div class="card-body p-2 table-responsive">
        <div class="cmms-fault-list-form table-responsive" id="fault-form-container">

            <?php $form = ActiveForm::begin([
                'id' => 'fault-form',
                'options' => [
                            'autocomplete' => 'off',
                            'enctype' => 'multipart/form-data'
                        ],
            ]); ?>

            <div class="alert alert-warning small">
                <i class="fas fa-solid fa-triangle-exclamation"></i>️ Newly selected photos will be lost if you navigate away before saving.
            </div>
            <table class="table table-bordered align-middle" id="item_table">
            <thead class="table-dark text-center">
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
                <?= Html::activeHiddenInput(
                    $model,
                    "id"
                ) ?>
                <tr>
                    <td>
                            <?php $assetIDs = ArrayHelper::map(
                                     CmmsAssetList::find()
                                            ->where(['active_sts' => 1])
                                            ->all(),
                                            'asset_id', 'asset_id'
                                    ); 
                            ?>
                            <?=
                                $form->field($model, "fault_asset_id")->dropDownList(
                                        $assetIDs,
                                        [
                                            'id' => 'id-dropdown',
                                            'prompt' => 'Select ID'
                                        ]
                                    )->label(false);
                            ?>
                            <?php $isDisabled = !$isUpdate; ?>
                        <?= yii\helpers\Html::a(
                                '<i class="bi bi-eye"></i>',
                                'javascript:void(0);',
                                [
                                    'class' => 'modalButtonSingle btn btn-sm btn-success' . ($isDisabled ? ' disabled' : ''),
                                    'id' => 'view-asset-btn',
                                    'data-url' => Url::to(['view-asset-details']), 
                                    'data-back-url' => Url::to(['fault-form-modal', 'id' => $model->id ?? null]),
                                    'aria-disabled' => 'true',
                                ]
                            ); ?>
                    </td>
                    <td>
                        <?php if ($moduleIndex === 'superior'): ?>
                            <?= Html::encode(
                                CmmsAssetList::getFaultType_by_ID($model->fault_asset_id)[$model->fault_type] ?? '-'
                            ) ?>
                            <?= Html::activeHiddenInput(
                                $model,
                                "fault_type"
                            ) ?>
                        <?php else: ?>
                        <?php if (!$isUpdate): ?>
                            <?=  
                                $form->field($model, "fault_type")->dropDownList(
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
                                $form->field($model, "fault_type")->dropDownList(
                                        CmmsAssetList::getFaultType_by_ID($model->fault_asset_id),
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
                                $form->field($model, "fault_primary_detail")->dropDownList(
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
                                    CmmsAssetList::getPrimaryFault_by_type($model->fault_type)[$model->fault_primary_detail] ?? '-'
                                ) ?>
                                <?= Html::activeHiddenInput(
                                    $model,
                                    "fault_primary_detail"
                                ) ?>
                            <?php else: ?>
                            <?=
                                $form->field($model, "fault_primary_detail")->dropDownList(
                                    CmmsAssetList::getPrimaryFault_by_type($model->fault_type),
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
                                $form->field($model, "fault_secondary_detail")->dropDownList(
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
                                    CmmsAssetList::getSecondaryFault($model->fault_primary_detail)[$model->fault_secondary_detail] ?? '-'
                                ) ?>
                                <?= Html::activeHiddenInput(
                                    $model,
                                    "fault_secondary_detail"
                                ) ?>
                            <?php else: ?>
                            <?=
                                $form->field($model, "fault_secondary_detail")->dropDownList(
                                      CmmsAssetList::getSecondaryFault($model->fault_primary_detail)  
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
                            $form->field($model, "machine_priority_id")->dropDownList(
                                $priorityList
                            )->label(false); 
                        ?>
                    </td>
                    <td>
                        <?php if ($moduleIndex === 'superior'): ?>

                        <?php if (!empty($model->machinePhotos)): ?>

                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($model->machinePhotos as $photo): ?>
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
                            <?php foreach ($model->cmmsMachinePhotos as $photo): ?>
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
                        <?= Html::encode($model->additional_remarks); ?>
                        <?= Html::activeHiddenInput(
                                    $model,
                                    "additional_remarks"
                                ) ?>
                        <?php else: ?>
                        <?=
                            $form->field($model, "additional_remarks")->textarea()->label(false);
                        ?>
                        <?php endif; ?>
                    </td>
        </tr>
            </tbody>
            </table>
                <?=
                    Html::submitButton('Save', [
                        'id' => 'save-btn',
                        'class' => 'float-right btn btn-success'
                    ])
                ?>
            <?php ActiveForm::end(); ?>
        </div>
        <!--Asset Details container-->
        <div id="asset-details-container" style="display: none">
            <div class="d-flex justify-content-between mb-3">
                <!--<h5>Asset Details</h5>-->
                <button class="btn btn-secondary btn-sm" id="back-to-fault-list">
                    Back to Fault List
                </button>
            </div>
            <div id="asset-details-content">
            </div>
        </div>
    </div>
</div>
<?php
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
    });
    JS);
?>

<?php 
    $primaryUrl = Url::to(['/cmms/cmms-fault-list/get-primary-fault-details']);
    $secondaryUrl = Url::to(['/cmms/cmms-fault-list/get-secondary-fault-details']);
?>
<script>
//    console.count('photo-upload change fired');
    
    $('#modalSingle').on('hidden.bs.modal', function () {
        selectedFilesMap = {};
        $('.new-photo-preview').empty();
    });
    
//    $('#modalSingle')
//    .off('change.photoUpload')
//    .on('change.photoUpload', '.photo-upload', function () {
//        var key = $(this).data('key');
//        selectedFilesMap[key] = Array.from(this.files);
//        renderNewPreviews(key);
//        rebuildFileInput(this, key);
//    });
//
//$('#modalSingle')
//    .off('click.removePhoto')
//    .on('click.removePhoto', '.remove-new-photo', function () {
//        var key = $(this).data('key');
//        var index = $(this).data('index');
//
//        selectedFilesMap[key].splice(index, 1);
//
//        var input = $('.photo-upload[data-key="' + key + '"]')[0];
//        rebuildFileInput(input, key);
//        renderNewPreviews(key);
//    });
        
    var modelId = "<?= $model->id; ?>";
    
    $(document).on('click', '#view-asset-btn', function (e) {
        e.preventDefault();

        if ($(this).hasClass('disabled')) return;

        const assetId = $('#id-dropdown').val();
        if (!assetId) return;

        $('#fault-form-container').fadeOut(150, function () {
            $('#asset-details-container').fadeIn(150);
            $('#asset-details-content').html('<div class="text-muted">Loading...</div>');
        });

        $.get(
            '<?= Url::to(['view-asset-details']) ?>',
            { asset_id: assetId },
            function (html) {
                $('#asset-details-content').html(html);
            }
        );
    });
    
    var currentKey = $('tr[id^="tr_"]').length;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    var selectedFilesMap = {}; // key => array of File objects

if (!window.photoUploadBound) {
    window.photoUploadBound = true;
//    $(document).on('change.photoUpload', '.photo-upload', function () {
    $(document).on('change', '.photo-upload', function () {
        var key = $(this).data('key');
        selectedFilesMap[key] = Array.from(this.files).map(file => ({
            id: crypto.randomUUID(), // unique ID per file
            file: file
        }));
        renderNewPreviews(key);
        rebuildFileInput(this, key);
    });
//    $('#modalSingle')
//        .off('change.photoUpload')
//        .on('change.photoUpload', '.photo-upload', function () {
//            var key = $(this).data('key');
//            selectedFilesMap[key] = Array.from(this.files);
//            renderNewPreviews(key);
//            rebuildFileInput(this, key);
//        });
//
//    $('#modalSingle')
//        .off('click.removePhoto')
//        .on('click.removePhoto', '.remove-new-photo', function () {
//            var key = $(this).data('key');
//            var index = $(this).data('index');
//
//            selectedFilesMap[key].splice(index, 1);
//
//            var input = $('.photo-upload[data-key="' + key + '"]')[0];
//            rebuildFileInput(input, key);
//            renderNewPreviews(key);
//        });

    }
    
    // --- Handle new photo uploads ---
//    $(document).on('change.photoUpload', '.photo-upload', function () {
//        var key = $(this).data('key');
//        selectedFilesMap[key] = Array.from(this.files);
//        renderNewPreviews(key);
//        rebuildFileInput(this, key);
//    });

    function renderNewPreviews(key) {
        var container = $('.new-photo-preview[data-key="' + key + '"]');
        container.empty();

//        (selectedFilesMap[key] || []).forEach(function (file, index) {
        var files = selectedFilesMap[key] || [];
        if (!files.length)  return;
        
        // Read all files first
        var readers = files.map(obj => {
            return new Promise(resolve => {
                var reader = new FileReader();
                reader.onload = function(e) {
                    resolve({id: obj.id, result: e.target.result});
                };
                reader.readAsDataURL(obj.file);
            });
        });
        
        Promise.all(readers).then(results => {
            results.forEach(obj => {
                container.append(
                    '<div class="photo-item d-inline-block me-2 mb-2">' +
                        '<img src="' + obj.result + '" class="img-thumbnail" style="width:80px">' +
                        '<button type="button" class="btn btn-sm btn-warning w-100 remove-new-photo" ' +
                            'data-key="' + key + '" data-id="' + obj.id + '">' +
                            'Remove' +
                        '</button>' +
                    '</div>'
                );
            });
        });
            
//            if (!file.type.match(/^image\//)) return;

//            var reader = new FileReader();
//            reader.onload = function (e) {
//                container.append(
//                    '<div class="photo-item d-inline-block me-2 mb-2">' +
//                        '<img src="' + e.target.result + '" class="img-thumbnail" style="width:80px">' +
//                        '<button type="button" class="btn btn-sm btn-warning w-100 remove-new-photo" ' +
//                            'data-key="' + key + '" data-id="' + id + '">' +
//                            'Remove' +
//                        '</button>' +
//                    '</div>'
                    // previous version (duplicates still occur)
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
    }

    function rebuildFileInput(input, key) {
        var dt = new DataTransfer();
//        (selectedFilesMap[key] || []).forEach(function (file) {
//            dt.items.add(file);
//        });
        (selectedFilesMap[key] || []).forEach(function(obj) {
            dt.items.add(obj.file);
        });
        input.files = dt.files;
    }
    
    $(document).on('click', '.remove-new-photo', function () {
        var key = $(this).data('key');
        var id = $(this).data('id');

        // Get the index based on position in container
//        var container = $('.new-photo-preview[data-key="' + key + '"]');
        selectedFilesMap[key] = selectedFilesMap[key].filter(obj => obj.id !== id);
//        var index = container.find('.photo-item').index($(this).closest('.photo-item'));
//
//        selectedFilesMap[key].splice(index, 1);

        var input = $('.photo-upload[data-key="' + key + '"]')[0];
        rebuildFileInput(input, key);
        renderNewPreviews(key);
    });

//    $(document).on('click', '.remove-new-photo', function () {
//        var key = $(this).data('key');
//        var index = $(this).data('index');
//
//        selectedFilesMap[key].splice(index, 1);
//
//        var input = $('.photo-upload[data-key="' + key + '"]')[0];
//        rebuildFileInput(input, key);
//        renderNewPreviews(key);
//    });

    // --- Remove existing photo ---
    $(document).on('click', '.remove-existing-photo', function () {
        var photoId = $(this).data('photo-id');

        $(this).closest('.photo-item').remove();

        $('.delete-photo-inputs').append(
            '<input type="hidden" name="DeletePhotos[]" value="' + photoId + '">'
        );
    });

    // --- Reset new uploads when navigating back ---
    $(document).on('click', '#back-to-fault-list', function () {
        $('#asset-details-container').fadeOut(150, function () {
            $('#asset-details-content').empty();
            $('#fault-form-container').fadeIn(150);
        });

        // clear new uploads
        selectedFilesMap = {};
        $('.new-photo-preview').empty();

        // clear file inputs
        $('.photo-upload').each(function() { this.value = ''; });
    });
    
    $(document).on('change', '#id-dropdown', function () {
        const selectedId = $(this).val();
        const button = $('#view-asset-btn');
        let typeDropdown = $('#type-dropdown');
        
        const row = $(this).closest('tr');
        row.find('#primary-dropdown')
            .val('')
            .trigger('change');
            
        row.find('#secondary-dropdown')
            .val('')
            .trigger('change');
        
        if (selectedId) {
            button
                .removeClass('disabled')
                .data('asset-id', selectedId);
        } else {
            button
                .addClass('disabled')
                .removeData('asset-id');
        }

        $.ajax({
            url: '<?= Url::to(['get-fault-type-by-asset']) ?>',
            data: { id: selectedId },
            dataType: 'json',
            success: function (res) {
                let options = '<option value="">Select Fault Type</option>';
                $.each(res.options, function (key, val) {
                    options += `<option value="${key}">${val}</option>`;
                });
                typeDropdown.html(options);
            }
        });
    });

//    const primaryUrl = '<? $primaryUrl ?>';
    window.primaryUrl = '<?= $primaryUrl ?>';
    window.secondaryUrl = '<?= $secondaryUrl ?>';
    
    $(document).on('change', '#type-dropdown', function () {
        const type = $(this).val();
            
        const row = $(this).closest('tr');
        row.find('#primary-dropdown')
            .val('')
            .trigger('change');
            
        row.find('#secondary-dropdown')
            .val('')
            .trigger('change');

//        const primaryDropdown = $('select[name="CmmsFaultListDetails[' + key + '][fault_primary_detail]"]');
        const primaryDropdown = $('select[name="CmmsFaultList[fault_primary_detail]"]');

        primaryDropdown.empty().append('<option value="">Loading...</option>');
        
        if (!type) {
            primaryDropdown.html('<option value="">Select Primary Fault</option>');
            return;
        }

        $.post(window.primaryUrl, { type: type }, function (html) {
            primaryDropdown.html(html);
        });
    });
    
    $(document).on('change', '#primary-dropdown', function () {
        const primary = $(this).val();
//        const key = $(this).data('key');
//            console.log(key);
            
//        const secondaryDropdown = $('select[name="CmmsFaultListDetails[' + key + '][fault_secondary_detail]"]');
        const secondaryDropdown = $('select[name="CmmsFaultList[fault_secondary_detail]"]');

        secondaryDropdown.empty().append('<option value="">Loading...</option>');
        
        if (!primary) {
            secondaryDropdown.html('<option value="">Select Secondary Fault</option>');
            return;
        }

        $.post(window.secondaryUrl, { primary: primary }, function (html) {
            secondaryDropdown.html(html);
        });
    });
    
</script>


