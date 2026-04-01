<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$this->title = 'Corrective Maintenance - Selected Part/Tool List';
$this->params['breadcrumbs'][] = ['label' => 'Maintenance - Material Request Master List', 'url' => ['/cmms/cmms-wo-material-request/pending-material-request-master-list']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Work Order #' . $model->wo_id];
$receivers = \frontend\models\cmms\RefAssignedPic::find()->where(['corrective_work_order_master_id' => $model->wo_id])->all();
?>       
<?php
$form = ActiveForm::begin([]);
?>
<?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal, AuthItem::ROLE_CMMS_Superior]) && $moduleIndex === "inventory") { ?>
    <div class="row mt-3">
        <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center mb-2">
            <h5 for="receiver" class="mb-0 pr-3 text-nowrap">Received By: </h5>
            <div class="w-100">
                <select name="receiver[id]" id="receiver" class="form-control form-control-sm <?= empty($receivers) ? 'is-invalid' : '' ?>">
                    <?php if (!empty($receivers)): ?>
                        <?php foreach ($receivers as $receiver): ?>
                            <option value="<?= $receiver->staff_id ?>">
                                <?= Html::encode($receiver->name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (empty($receivers)): ?>
                    <small class="invalid-feedback" style="font-size: 10pt">No staff available to select.</small>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center mb-2">
            <h5 class="mb-0 pr-3 text-nowrap">Status: </h5>
            <div class="w-100">
                <select name="current_sts" id="current_sts" class="form-control form-control-sm">
                    <?php foreach (\frontend\models\bom\StockDispatchMaster::pending_status as $value => $label) : ?>
                        <option value="<?= $value ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?= Html::submitButton('Proceed', ['class' => 'btn btn-primary px-3 float-right proceed']) ?>
        </div>
    </div>
<?php } ?>
<?=
$this->render('_materialRequestDetailCm', [
    'model' => $model,
    'faults' => $faults,
    'partToolList' => $partToolList,
    'moduleIndex' => $moduleIndex,
    'wotype' => $wotype,
]);
?>
<?php ActiveForm::end(); ?>
<script>
    function fillQuantity(checkbox) {
        var row = checkbox.closest('tr');
        var qtyInput = row.querySelector('.qty-input');
        var maxQty = checkbox.getAttribute('data-max-qty');
        qtyInput.value = checkbox.checked ? maxQty : '';
    }

    function selectAll(faultCounter) {
        var masterCheckbox = document.getElementById('select_all_' + faultCounter);
        var checkboxes = document.querySelectorAll('.select-row[data-master="' + faultCounter + '"]');
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = masterCheckbox.checked;
            fillQuantity(checkbox);
        });
    }
</script>
