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
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="mb-1 font-weight-bold">
                Corrective Maintenance Work Order - <?= Html::encode($model->id ?? 'New') ?>
            </h6>
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
                'action' => ['/cmms/cmms-fault-list/bulk-update', 'moduleStatus' => $moduleStatus],
                'method' => 'post', 
            ]); ?>

            <table class="table table-bordered align-middle" id="item_table">
            <thead class="table-dark text-center">
                <tr>
                    <th class="text-center" width="15%">Fault ID</th>
                    <th class="text-center" width="15%">Fault Type</th>
                    <th class="text-center">Primary Description</th>
                    <th class="text-center">Secondary Description</th>
                    <!--<th width="15%">Machine Breakdown Type</th>-->
                    <th class="text-center" width="20%">Last Record</th>
                    <th class="text-center" width="15%">Frequency</th>
                    <th class="text-left" width="30%">Remedial Actions</th>
                    <th>Part List</th>
                    <th>Tool List</th>
                    <th>Safety Precautions</th>
                    <th>Status</th>
                    <th>Reviewed by</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <?php foreach($faultLists as $fault): ?>
                    <?= Html::activeHiddenInput(
                        $fault,
                        "[{$fault->id}]id"
                    ) ?>
                <tr>
                    <td>
                        <?= Html::encode($fault->id) ?>
                        <br>
                        <?= Html::a(
                            '<i class="bi bi-eye"></i>',
                            'javascript:void(0);',
                            [
                                'class' => 'modalButtonSingle btn btn-sm btn-success',
                                'id' => 'view-asset-btn',
                                'data-asset-id' => $fault->fault_asset_id,
                                'data-model-id' => $fault->id,
                                'data-url' => Url::to([
                                    'cmms/cmms-fault-list/view-asset-details'
                                    ]), 
                                'data-back-url' => Url::to(['cmms/cmms-fault-list/fault-form-modal', 'id' => $model->id ?? null]),
                                'aria-disabled' => 'true',
                            ]
                        ); ?>
                    </td>
                    <td>
                        <?php if ($moduleStatus === 'superior'): ?>
                            <?= 
                                $form->field($fault, "[{$fault->id}]fault_type")->dropDownList(
                                        CmmsAssetList::getFaultType_by_ID($fault->fault_asset_id),
                                        [
                                            'class' => 'type-dropdown',
                                            'prompt' => 'Edit Fault Type'
                                        ]
                                )->label(false);
                            ?>
                        <?php else: ?>
                            <?= $fault->fault_type; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($moduleStatus === 'superior'): ?>
                            <?= 
                                $form->field($fault, "[{$fault->id}]fault_primary_detail")->dropDownList(
                                    CmmsAssetList::getPrimaryFault_by_type($fault->fault_type),
                                        [
                                            'class' => 'primary-dropdown',
                                            'prompt' => 'Edit Primary Fault'
                                        ],
                                )->label(false); 
                            ?>
                        <?php else: ?>
                            <?= $fault->fault_primary_detail; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($moduleStatus === 'superior'): ?>
                            <?= 
                                $form->field($fault, "[{$fault->id}]fault_secondary_detail")->dropDownList(
                                    CmmsAssetList::getSecondaryFault($fault->fault_primary_detail),
                                        [
                                            'class' => 'secondary-dropdown',
                                            'prompt' => 'Edit Secondary Fault'
                                        ],
                                )->label(false); 
                            ?>
                        <?php else: ?>
                            <?= $fault->fault_secondary_detail; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $fault->last_record ?></td>
                    <td><?= $fault->frequency ?></td>
                    <td><?= $form->field($fault, "[{$fault->id}]remedial_actions")->textarea()->label(false); ?></td>
                    <td>
                        <?php if ($moduleStatus === 'superior'): ?>
                            <?= 
                                $form->field($fault, "[{$fault->id}]part_list_id")->dropDownList($partList ,
                                        ['prompt' => 'Select part'],
                                        )->label(false); 
                            ?>
                        <?php else: ?>
                            <?= $fault->part_list_id; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($moduleStatus === 'superior'): ?>
                            <?= 
                                $form->field($fault, "[{$fault->id}]tool_list_id")->dropDownList($toolList,
                                        ['prompt' => 'Select tool'],
                                        )->label(false);
                            ?>
                        <?php else: ?>
                            <?= $fault->tool_list_id; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $form->field($fault, "[{$fault->id}]safety_precautions")->textarea()->label(false); ?></td>
                    <td><?= $fault->status ?></td>
                    <td>
                        <?php if ($moduleStatus === 'superior'): ?>
                            <?php 
                                $users = User::find()
                                        ->select(['id', 'fullname'])
    //                                    ->where(['<>', 'id', Yii::$app->user->id])
                                        ->where(['<>', 'id', '<>'])
                                        ->asArray()
                                        ->all();
                            ?>
                            <?= $form->field($fault, "[{$fault->id}]reviewed_by_name")->textInput([
                                'value' => Yii::$app->user->identity->fullname,
                                'list' => 'users',
                                'class' => 'reviewed-by-name',
                                'data-row' => $fault->id,
                                'style' => 'width:100%; min-width:150px;'
                            ])->label(false); ?>

                            <?= $form->field($fault, "[{$fault->id}]reviewed_by")
                                ->hiddenInput([
                                    'class' => 'reviewed-by-id',
                                    'data-row' => $fault->id,
                                ])
                                ->label(false);
                            ?>
                            <datalist id="users">
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= Html::encode($user['fullname']) ?>"
                                            data-id="<?= $user['id'] ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <?php
                                $jsUsers = json_encode($users);
                                $this->registerJs("
                                    const users = $jsUsers;

                                    const nameInput = document.getElementById('reviewed-by-name');
                                    const idInput = document.getElementById('reviewed-by-id');

                                    function syncAssignedPIC() {
                                        const match = users.find(u => u.fullname === nameInput.value);
                                        idInput.value = match ? match.id : '';
                                    }

                                    nameInput.addEventListener('change', syncAssignedPIC);
                                    nameInput.addEventListener('blur', syncAssignedPIC);
                                ");
                            ?>
                        <?php else: ?>
                            <?= $fault->reviewed_by; ?>
                        <?php endif; ?>
<!--                        <php 
                            $user = User::findOne(['id' => $fault->reviewed_by]);
                        ?>
                        <? $user->fullname ?>-->
                    </td>
                </tr>
                <?php endforeach; ?>
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
    $(document).on('click', '#view-asset-btn', function (e) {
        e.preventDefault();

        const assetId = $(this).data('asset-id');
        const modelId = $(this).data('model-id');

        $('#fault-form-container').fadeOut(150, function () {
            $('#asset-details-container').fadeIn(150);
            $('#asset-details-content').html('<div class="text-muted">Loading...</div>');
        });

        $.get(
            '<?= Url::to(['cmms/cmms-fault-list/view-asset-details']) ?>',
            { asset_id: assetId, model_id: modelId },
            function (html) {
                $('#asset-details-content').html(html);
            }
        );
    });
    
    $(document).on('click', '#back-to-fault-list', function () {
        $('#asset-details-container').fadeOut(150, function () {
            $('#asset-details-content').empty();
            $('#fault-form-container').fadeIn(150);
        });
    });
    
    window.primaryUrl = '<?= $primaryUrl ?>';
    window.secondaryUrl = '<?= $secondaryUrl ?>';
    
    $(document).on('change', '.type-dropdown', function () {
        const type = $(this).val();
        const row = $(this).closest('tr');

        const primaryDropdown = row.find('.primary-dropdown');
        const secondaryDropdown = row.find('.secondary-dropdown');

        primaryDropdown.empty().append('<option value="">Loading...</option>');
        secondaryDropdown.empty().append('<option value="">Select Secondary Fault</option>');

        if (!type) {
            primaryDropdown.html('<option value="">Select Primary Fault</option>');
            return;
        }

        $.post(window.primaryUrl, { type: type }, function (html) {
            primaryDropdown.html(html);
        });
//        const type = $(this).val();
//            
//        const row = $(this).closest('tr');
//        row.find('.primary-dropdown')
//            .val('')
//            .trigger('change');
//            
//        row.find('.secondary-dropdown')
//            .val('')
//            .trigger('change');
//
////        const primaryDropdown = $('select[name="CmmsFaultListDetails[' + key + '][fault_primary_detail]"]');
//        const primaryDropdown = $('select[name="CmmsFaultList[fault_primary_detail]"]');
//
//        primaryDropdown.empty().append('<option value="">Loading...</option>');
//        
//        if (!type) {
//            primaryDropdown.html('<option value="">Select Primary Fault</option>');
//            return;
//        }
//
//        $.post(window.primaryUrl, { type: type }, function (html) {
//            primaryDropdown.html(html);
//        });
    });
    
    $(document).on('change', '.primary-dropdown', function () {
        const primary = $(this).val();
        const row = $(this).closest('tr');

        const secondaryDropdown = row.find('.secondary-dropdown');

        secondaryDropdown.empty().append('<option value="">Loading...</option>');

        if (!primary) {
            secondaryDropdown.html('<option value="">Select Secondary Fault</option>');
            return;
        }

        $.post(window.secondaryUrl, { primary: primary }, function (html) {
            secondaryDropdown.html(html);
        });
//        const primary = $(this).val();
//        const secondaryDropdown = $('select[name="CmmsFaultList[fault_secondary_detail]"]');
//
//        secondaryDropdown.empty().append('<option value="">Loading...</option>');
//        
//        if (!primary) {
//            secondaryDropdown.html('<option value="">Select Secondary Fault</option>');
//            return;
//        }
//
//        $.post(window.secondaryUrl, { primary: primary }, function (html) {
//            secondaryDropdown.html(html);
//        });
    });
</script>