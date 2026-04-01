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
<div class="card-body p-2 table-responsive">
    <div class="cmms-fault-list-form table-responsive" id="fault-form-container">

        <?php $form = ActiveForm::begin([
            'id' => 'fault-form',
            'options' => [
                        'autocomplete' => 'off',
                        'enctype' => 'multipart/form-data'
                    ],
            'action' => ['/cmms/cmms-fault-list/bulk-update', 'moduleStatus' => $moduleIndex, 'module' => 'Preventive'],
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
                <!--<th>Status</th>-->
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
<!--                        <br>
                    <? Html::a(
                        '<i class="bi bi-eye"></i>',
                        'javascript:void(0);',
                        [
                            'class' => 'modalButtonSingle btn btn-sm btn-success',
                            'id' => 'view-asset-btn',
                            'data-asset-id' => $assetCode,
                            'data-model-id' => $fault->id,
                            'data-url' => Url::to([
                                'cmms/cmms-fault-list/view-asset-details',
                                'asset_id' => $assetCode
                                ]), 
//                                'data-back-url' => Url::to(['cmms/cmms-fault-list/fault-form-modal', 'id' => $model->id ?? null]),
//                                'aria-disabled' => 'true',
                        ]
                    ); ?>-->
                </td>
                <td>
                    <?php if ($moduleIndex === 'superior'): ?>
                        <?= 
                            $form->field($fault, "[{$fault->id}]fault_type")->dropDownList(
                                    CmmsAssetList::getFaultType_by_ID($assetCode),
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
                    <?php if ($moduleIndex === 'superior'): ?>
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
                    <?php if ($moduleIndex === 'superior'): ?>
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
                    <?php $partList = $fault->partList ?? new \frontend\models\cmms\CmmsPartList(); ?>
                    <?php if ($moduleIndex === 'superior'): ?>
                        <?php 
                            $parts = \frontend\models\cmms\CmmsPartList::find()
                                    ->select(['id', 'name'])
                                    ->where(['<>', 'id', '<>'])
                                    ->asArray()
                                    ->all();
                        ?>
                        <?php 
                            $placeholder = \frontend\models\cmms\CmmsPartList::find()
                                    ->select('name')
                                    ->where(['id' => $fault->part_list_id])
                                    ->scalar();
                        ?>
                        <?= $form->field($fault, "[{$fault->id}]part_name")->textInput([
                                'value' => $placeholder ?? '-',
                                'list' => 'parts',
                                'class' => 'part-name',
                                'data-row' => $fault->id,
                                'style' => 'width:100%; min-width:150px;'
                            ])->label(false); 
                        ?>

                        <?= $form->field($fault, "[{$fault->id}]part_list_id")
                            ->hiddenInput([
                                'class' => 'part-list-id',
                                'data-row' => $fault->id,
                            ])
                            ->label(false);
                        ?>
                        <datalist id="parts">
                            <?php foreach ($parts as $part): ?>
                                <option value="<?= Html::encode($part['name']) ?>"
                                        data-id="<?= $part['id'] ?>">
                            <?php endforeach; ?>
                        </datalist>
<!--                            <? 
                            $form->field($fault, "[{$fault->id}]part_list_id")->dropDownList($partList,
                                    ['prompt' => 'Select part'],
                                    )->label(false); 
                        ?>-->
                    <?php else: ?>
                        <?php 
                            $partListDetails = \frontend\models\cmms\CmmsPartList::find()
                                    ->where(['id' => $fault->part_list_id])
                                    ->one();
//                                $partListDetails = frontend\models\cmms\VInventoryModel::find()
//                                    ->select('brand_model')
//                                    ->where([
//                                        'departments' => 'mecha',
//                                        'id' => $fault->part_list_id
//                                    ])
//                                    ->scalar();
                        ?>
                        <?= Html::encode($partListDetails->name) ?>
                    <?php endif; ?>
                    <?php $qtyInputId = "qty-{$fault->id}"; ?>
                    <div class="d-flex align-items-center mt-1" style="gap:6px;">
                        <button type="button"
                                class="btn btn-outline-secondary btn-sm qty-btn"
                                data-target="#<?= $qtyInputId ?>"
                                data-step="-1">−</button>

                        <?= 
                            $form->field($partList, "[{$fault->id}]qty")
//                                $form->field($fault->partList, "[{$fault->partList->id}]qty")
                            ->textInput([
                                'id' => $qtyInputId,
                                'class' => 'form-control form-control-sm qty-input',
                                'style' => 'width:70px; text-align:center;',
                                'inputmode' => 'numeric',
                                'value' => $fault->part_qty ?: 1,
//                                    'value' => $fault->partList->qty ?: 1,
                            ])
                            ->label(false); ?>

                        <button type="button"
                                class="btn btn-outline-secondary btn-sm qty-btn"
                                data-target="#<?= $qtyInputId ?>"
                                data-step="1">+</button>
                    </div>
                </td>
                <td>
                    <?php $toolList = $fault->toolList ?? new \frontend\models\cmms\CmmsToolList(); ?>
                    <?php if ($moduleIndex === 'superior'): ?>
                        <?php 
                            $tools = \frontend\models\cmms\CmmsToolList::find()
                                    ->select(['id', 'name'])
                                    ->where(['<>', 'id', '<>'])
                                    ->asArray()
                                    ->all();
                        ?>
                        <?php 
                            $placeholder = \frontend\models\cmms\CmmsToolList::find()
                                    ->select('name')
                                    ->where(['id' => $fault->tool_list_id])
                                    ->scalar();
                        ?>
                        <?= $form->field($fault, "[{$fault->id}]tool_name")->textInput([
                                'value' => $placeholder ?? '-',
                                'list' => 'tools',
                                'class' => 'tool-name',
                                'data-row' => $fault->id,
                                'style' => 'width:100%; min-width:150px;'
                            ])->label(false); 
                        ?>

                        <?= $form->field($fault, "[{$fault->id}]tool_list_id")
                            ->hiddenInput([
                                'class' => 'tool-list-id',
                                'data-row' => $fault->id,
                            ])
                            ->label(false);
                        ?>
                        <datalist id="tools">
                            <?php foreach ($tools as $tool): ?>
                                <option value="<?= Html::encode($tool['name']) ?>"
                                        data-id="<?= $tool['id'] ?>">
                            <?php endforeach; ?>
                        </datalist>
<!--                            <? 
                            $form->field($fault, "[{$fault->id}]tool_list_id")->dropDownList($toolList,
                                    ['prompt' => 'Select tool'],
                                    )->label(false);
                        ?>-->
                    <?php else: ?>
                        <?php 
                            $toolListDetails = \frontend\models\cmms\CmmsToolList::find()
                                    ->where(['id' => $fault->tool_list_id])
                                    ->one();
//                                $toolListDetails = frontend\models\cmms\VInventoryModel::find()
//                                    ->select('brand_model')
//                                    ->where([
//                                        'departments' => 'mecha',
//                                        'id' => $fault->tool_list_id
//                                    ])
//                                    ->scalar();
                        ?>
                        <?= Html::encode($toolListDetails->name) ?>
                    <?php endif; ?>
                    <?php $toolQtyInputId = "tool-qty-{$fault->id}"; ?>
                    <div class="d-flex align-items-center mt-1" style="gap:6px;">
                        <button type="button"
                                class="btn btn-outline-secondary btn-sm tool-qty-btn"
                                data-target="#<?= $toolQtyInputId ?>"
                                data-step="-1">−</button>
                        <?= 
                            $form->field($toolList, "[{$fault->id}]qty")
//                                $form->field($fault->toolList, "[{$fault->toolList->id}]qty")
                            ->textInput([
                                'id' => $toolQtyInputId,
                                'class' => 'form-control form-control-sm tool-qty-input',
                                'style' => 'width:70px; text-align:center;',
                                'inputmode' => 'numeric',
                                'value' => $fault->tool_qty ?: 1,
//                                    'value' => $fault->toolList->qty ?: 1,
                            ])
                            ->label(false); ?>

                        <button type="button"
                                class="btn btn-outline-secondary btn-sm tool-qty-btn"
                                data-target="#<?= $toolQtyInputId ?>"
                                data-step="1">+</button>
                    </div>
                </td>
                <td><?= $form->field($fault, "[{$fault->id}]safety_precautions")->textarea()->label(false); ?></td>
                <!--<td><? $fault->status ?></td>-->
                <td>
                    <?php if ($moduleIndex === 'superior'): ?>
                        <?php 
                            $users = User::find()
                                    ->select(['id', 'fullname'])
//                                    ->where(['<>', 'id', Yii::$app->user->id])
                                    ->where(['<>', 'id', '<>'])
                                    ->asArray()
                                    ->all();
                        ?>
                        <?php 
                            $placeholder = User::find()
                                    ->select('fullname')
                                    ->where(['id' => $fault->reviewed_by])
                                    ->scalar();
                        ?>
                        <?= $form->field($fault, "[{$fault->id}]reviewed_by_name")->textInput([
                            'value' => $placeholder ?? Yii::$app->user->identity->fullname,
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
                            $this->registerJs(<<<JS
                                (function () {
                                  const users = $jsUsers;

                                  // Build lookup map: fullname -> id
                                  const nameToId = new Map();
                                  users.forEach(u => {
                                    if (u.fullname != null) nameToId.set(String(u.fullname), String(u.id));
                                  });

                                  function syncForRow(rowId) {
                                    const nameInput = document.querySelector('.reviewed-by-name[data-row="' + rowId + '"]');
                                    const idInput   = document.querySelector('.reviewed-by-id[data-row="' + rowId + '"]');
                                    if (!nameInput || !idInput) return;

                                    const id = nameToId.get(nameInput.value) || '';
                                    idInput.value = id;
                                  }

                                  // Sync on change/blur for ANY row (event delegation)
                                  document.addEventListener('change', function(e) {
                                    if (!e.target.classList.contains('reviewed-by-name')) return;
                                    const rowId = e.target.dataset.row;
                                    if (rowId) syncForRow(rowId);
                                  });

                                  document.addEventListener('blur', function(e) {
                                    if (!e.target.classList.contains('reviewed-by-name')) return;
                                    const rowId = e.target.dataset.row;
                                    if (rowId) syncForRow(rowId);
                                  }, true);

                                  // IMPORTANT: initial sync on page load (because you set default fullname)
                                  document.querySelectorAll('.reviewed-by-name[data-row]').forEach(input => {
                                    syncForRow(input.dataset.row);
                                  });
                                })();
                                JS);
                        ?>
                    <?php else: ?>
                        <?php $reviewer = User::findOne(['id' => $fault->reviewed_by]); ?>
                        <?= Html::encode($reviewer->fullname) ?>
                    <?php endif; ?>
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
    $this->registerJs(<<<JS
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.qty-btn');
      if (!btn) return;

      const input = document.querySelector(btn.dataset.target);
      if (!input) return;

      const step = parseInt(btn.dataset.step || '0', 10);
      const current = parseInt(input.value || '1', 10);
      let next = isNaN(current) ? 1 : current + step;

      if (next < 1) next = 1; // min 1
      input.value = next;

      // trigger change for Yii/validators or any listeners
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    document.addEventListener('input', function(e) {
      if (!e.target.classList.contains('qty-input')) return;
      // keep only digits
      e.target.value = e.target.value.replace(/[^0-9]/g, '');
      if (e.target.value === '' || parseInt(e.target.value, 10) < 1) {
        e.target.value = '1';
      }
    });
            
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.tool-qty-btn');
      if (!btn) return;

      const input = document.querySelector(btn.dataset.target);
      if (!input) return;

      const step = parseInt(btn.dataset.step || '0', 10);
      const current = parseInt(input.value || '1', 10);
      let next = isNaN(current) ? 1 : current + step;

      if (next < 1) next = 1; // min 1
      input.value = next;

      // trigger change for Yii/validators or any listeners
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    document.addEventListener('input', function(e) {
      if (!e.target.classList.contains('tool-qty-input')) return;
      // keep only digits
      e.target.value = e.target.value.replace(/[^0-9]/g, '');
      if (e.target.value === '' || parseInt(e.target.value, 10) < 1) {
        e.target.value = '1';
      }
    });
    JS);
?>
<?php 
    $primaryUrl = Url::to(['/cmms/cmms-fault-list/get-primary-fault-details']);
    $secondaryUrl = Url::to(['/cmms/cmms-fault-list/get-secondary-fault-details']);
?>
<script>
    
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
    });
    
    // --- Map datalist selection -> hidden id (PART) ---
    document.addEventListener('input', function(e) {
      if (!e.target.classList.contains('part-name')) return;

      const rowId = e.target.dataset.row;              // fault id
      const typedValue = e.target.value;

      const listId = e.target.getAttribute('list');    // should be "parts"
      const list = document.getElementById(listId);
      if (!list) return;

      const option = Array.from(list.options).find(o => o.value === typedValue);

      const hidden = document.querySelector(`.part-list-id[data-row="${rowId}"]`);
      if (hidden) hidden.value = option ? option.dataset.id : '';
    });

    // --- Map datalist selection -> hidden id (TOOL) ---
    document.addEventListener('input', function(e) {
      if (!e.target.classList.contains('tool-name')) return;

      const rowId = e.target.dataset.row;
      const typedValue = e.target.value;

      const listId = e.target.getAttribute('list');    // should be "tools"
      const list = document.getElementById(listId);
      if (!list) return;

      const option = Array.from(list.options).find(o => o.value === typedValue);

      const hidden = document.querySelector(`.tool-list-id[data-row="${rowId}"]`);
      if (hidden) hidden.value = option ? option.dataset.id : '';
    });
</script>