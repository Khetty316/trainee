<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

if ($moduleIndex === 'execPendingPurchasing') {
    $pageName = 'Purchasing - Executive';
} else if ($moduleIndex === 'execAllPurchasing') {
    $pageName = 'Purchasing - Executive';
} else if ($moduleIndex === 'assistPendingPurchasing') {
    $pageName = 'Purchasing - Assistant';
} else if ($moduleIndex === 'assistAllPurchasing') {
    $pageName = 'Purchasing - Assistant';
} else if ($moduleIndex === 'maintenanceHeadPendingPurchasing') {
    $pageName = 'Purchasing - Head of Maintenance';
} else if ($moduleIndex === 'maintenanceHeadAllPurchasing') {
    $pageName = 'Purchasing - Head of Maintenance';
} else if ($moduleIndex === 'execPendingReceiving') {
    $pageName = 'Receiving - Executive';
} else if ($moduleIndex === 'execAllReceiving') {
    $pageName = 'Receiving - Executive';
} else if ($moduleIndex === 'assistPendingReceiving') {
    $pageName = 'Receiving - Assistant';
} else if ($moduleIndex === 'assistAllReceiving') {
    $pageName = 'Receiving - Assistant';
}else if ($moduleIndex === 'maintenanceHeadPendingReceiving') {
    $pageName = 'Receiving - Head of Maintenance';
} else if ($moduleIndex === 'maintenanceHeadAllReceiving') {
    $pageName = 'Receiving - Head of Maintenance';
}

$url = 'po?type=' . $moduleIndex;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName, 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => 'Purchase Order'];
$this->params['breadcrumbs'][] = ['label' => $po->po_no];

$form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{input}",
    ],
    'options' => [
        'autocomplete' => 'off',
        'enctype' => 'multipart/form-data',
    ],
        ]);
?>
<div class="po-create">
    <div class="col-6 pl-0">
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0">QUOTATION DETAILS</legend>
            <div class="row">
                <div class="col-md-12">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 20%; padding: 5px;">QUOTATION NO.</td>
                            <td style="width: 5%; padding: 5px;">:</td>
                            <td style="padding: 5px;">
                                <?=
                                $form->field($po, 'quotation_no', [
                                    'template' => '{input}{error}'
                                ])->textInput([
                                    'maxlength' => true,
                                    'class' => 'form-control',
                                ])
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-12">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 20%; padding: 5px;">QUOTATION DATE</td>
                            <td style="width: 5%; padding: 5px;">:</td>
                            <td style="padding: 5px;">
                                <?php
                                if (empty($po->quotation_date)) {
                                    $po->quotation_date = date('d/m/Y');
                                }
                                ?>

                                <?=
                                $form->field($po, 'quotation_date')->widget(
                                        yii\jui\DatePicker::className(),
                                        [
                                            'options' => ['class' => 'form-control', 'style' => 'height: auto;'],
                                            'dateFormat' => 'dd/MM/yyyy'
                                        ]
                                )
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-12">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 20%; padding: 5px; vertical-align: middle;">UPLOAD QUOTATION</td>
                            <td style="width: 5%; padding: 5px; vertical-align: middle;">:</td>
                            <td style="padding: 5px;">
                                <?= $form->field($po, 'quotation_file')->fileInput() ?>

                                <?php if (!empty($po->quotation_filename)): ?>
                                    <div class="alert alert-info">
                                        Current file: <strong><?= $po->quotation_filename ?></strong>
                                        <?=
                                        Html::a('View <i class="fas fa-file-alt fa-lg"></i>',
                                                ['get-quotation', 'filename' => $po->quotation_filename],
                                                ['class' => 'btn btn-xs btn-primary', 'target' => '_blank', 'title' => 'View Quotation']);
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Formats: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="row">
        <div class="col-12 pl-0">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">PURCHASE ORDER DETAIL</legend>
                <?=
                $this->render('_poForm', [
                    'po' => $po,
                    'currencies' => $currencies,
                    'currencyList' => $currencyList,
                    'companyGroupList' => $companyGroupList,
                    'form' => $form,
                    'purchaseOrderItems' => $purchaseOrderItems,
                    'moduleIndex' => $moduleIndex,
//                    'page' => $page
                ])
                ?>
            </fieldset>
        </div>
    </div>

    <div class="form-row mt-3 justify-content-end">
        <div class="form-group">
            <?php if ($po->status !== frontend\models\RefInventoryStatus::STATUS_FullyReceived) { ?>
                <?= Html::submitButton('Generate PO', ['class' => 'btn btn-success submitButton']) ?>
            <?php } else { ?>
                <?=
                Html::button('Generate PO', [
                    'class' => 'btn btn-secondary',
                    'disabled' => true
                ])
                ?>
            <?php } ?>
            <?php if ($po->status === frontend\models\RefInventoryStatus::STATUS_PoCreated || $po->status === frontend\models\RefInventoryStatus::STATUS_AwaitingDelivery || $po->status === frontend\models\RefInventoryStatus::STATUS_PartiallyReceived) { ?>
                <?=
                Html::a('Deactivate PO',
                        ['deactivate-po', 'id' => $po->id, 'moduleIndex' => $moduleIndex],
                        ['class' => 'btn btn-warning', 'title' => 'Deactivate PO']); //add prompt confirmation alert before submit
                ?>
            <?php } else { ?>
                <?=
                Html::button('Deactivate PO', [
                    'class' => 'btn btn-secondary',
                    'disabled' => true
                ])
                ?>
            <?php } ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function () {
        $('.submitButton').prop('disabled', false).removeClass('disabled');

        $('form').on('submit', function (e) {
            const itemCount = $('#items-tbody tr.item-row').length;

            if (itemCount === 0) {
                e.preventDefault();
                e.stopImmediatePropagation(); // Stop other handlers
                alert('Please add at least one item to the purchase order before generating.');
                return false;
            }
        });

        $('form').on('beforeSubmit', function (e) {
            const itemCount = $('#items-tbody tr.item-row').length;

            if (itemCount === 0) {
                alert('Please add at least one item to the purchase order before generating.');
                return false;
            }

            return true;
        });
    });
</script>