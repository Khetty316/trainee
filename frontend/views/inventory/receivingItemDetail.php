<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

if ($moduleIndex === 'exec') {
    $pageName = 'Receiving - Executive';
} else if ($moduleIndex === 'assist') {
    $pageName = 'Receiving - Assistant';
}else if ($moduleIndex === 'maintenanceHead') {
    $pageName = 'Receiving - Head of Maintenance';
}
    
$url = 'receiving-history?type=' .$moduleIndex;

$this->title = 'Order Receive Detail';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = ['label' => 'History', 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => $batch->inventoryPo->po_no];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mb-5">

    <div class="alert alert-info border-left-primary mb-3">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <strong class="text-muted">PO No.:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= Html::encode($batch->inventoryPo->po_no) ?>
                        </span>                      
                    </div>
                    <div class="col-md-2">
                        <strong class="text-muted">PO Date:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= MyFormatter::asDate_Read($batch->inventoryPo->po_date) ?> 
                        </span>                      
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Received By:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= Html::encode($batch->receivedBy->fullname) ?>
                        </span>                      
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Received Date:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= MyFormatter::asDateTime_ReaddmYHi($batch->received_at) ?> 
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 col-md-12 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Received Items</legend>  
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Item No.</th>
                                <th>Description</th>
                                <th class="text-center">Received Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($items)):
                                foreach ($items as $index => $item):
                                    $detail = frontend\models\inventory\InventoryPurchaseOrderItem::findOne($item->inventory_po_item_id);

                                    $allocations = frontend\models\inventory\InventoryPoItemReceiveAllocation::find()
                                                    ->where(['inventory_po_item_receive_id' => $item['id']])->all();

                                    $hasAllocation = !empty($allocations);
                                    ?>
                                    <tr>
                                <input type="hidden" name="receive[<?= $index ?>][id]" value="<?= $item->id ?>" />
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= Html::encode($detail->inventoryDetail->code) ?></td>
                                <td>
                                    <?= Html::encode($detail->brand->name) ?>, 
                                    <?= Html::encode($detail->model_description) ?>
                                    <br>
                                    MODEL: <?= Html::encode($detail->model_type) ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="font-weight-bold text-success">
                                            <?= $detail->received_qty ?? 0 ?>
                                        </span>

                                        <?php if ($hasAllocation): ?>
                                            <button class="btn btn-sm btn-outline-primary mt-1"
                                                    type="button"
                                                    data-toggle="collapse"
                                                    data-target="#allocation-<?= $index ?>">
                                                View <i class="fa fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                </tr>
                                <!-- COLLAPSIBLE ALLOCATION SECTION -->
                                <tr>
                                    <td colspan="10" class="p-0">
                                        <div class="border-left border-right p-1">

                                            <?php if ($hasAllocation):
                                                ?>

                                                <div class="collapse" id="allocation-<?= $index ?>">

                                                    <div>

                                                        <table class="table table-sm table-bordered">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Reference Type</th>
                                                                    <th>Reference ID</th>
                                                                    <th>Requested By</th>
                                                                    <th class="text-center">Allocated</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($allocations as $rIndex => $allocation):
                                                                    $referenceType = ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === 'bom_detail' || $allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === 'bomstockoutbound') ? 'Project - Bill of Material' : '-';

                                                                    // Reference ID
                                                                    $referenceId = '-';
                                                                    if ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === 'bom_detail') {
                                                                        $ref = frontend\models\bom\BomDetails::findOne($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_id);
                                                                        $referenceId = $ref->bomMaster->productionPanel->project_production_panel_code ?? '-';
                                                                    } elseif ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === 'bomstockoutbound') {
                                                                        $ref = frontend\models\bom\StockOutboundDetails::findOne($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_id);
                                                                        $referenceId = $ref->bomDetail->bomMaster->productionPanel->project_production_panel_code ?? '-';
                                                                    } else if ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
                                                                        $referenceType = 'Corrective Maintenance';
                                                                        $referenceId = $allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_id;
                                                                    } else if ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
                                                                        $referenceType = 'Preventive Maintenance';
                                                                        $referenceId = $allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_id;
                                                                    }
                                                                    ?>

                                                                    <tr>
                                                                        <td><?= $rIndex + 1 ?></td>
                                                                        <td><?= Html::encode($referenceType) ?></td>
                                                                        <td><?= Html::encode($referenceId) ?></td> 
                                                                        <td><?= ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->requestedBy->fullname) . " @ " . common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->requested_at) ?></td>
                                                                        <td class="text-center"><?= $allocation->allocated_qty ?? 0 ?></td>
                                                                    </tr>

                                                                <?php endforeach; ?>

                                                            </tbody>

                        <!--                                                                <tfoot>
                                                                                            <tr>
                                                                                                <td colspan="6" class="text-right font-weight-bold">
                                                                                                    Balance to Stock:
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    <input type="number"
                                                                                                           name="receive[<?php //= $index   ?>][add_to_stock]"
                                                                                                           class="form-control text-center add-to-stock"
                                                                                                           data-index="<?php //= $index   ?>"
                                                                                                           value="<?php //= $receiveQty   ?>"
                                                                                                           readonly>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tfoot>-->
                                                        </table>

                                                    </div>

                                                </div>

                                            <?php else: ?>
                                                <div class="text-muted">
                                                    Quantity received and added to general stock inventory
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No items found</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>

        <div class="col-lg-5 col-md-12 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Attached Documents</legend>    
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>No.</th>
                                <th>Document Type</th>
                                <th>Document No.</th>
                                <th>File</th>
                                <th>Uploaded By</th>
                                <th>Uploaded At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($documents)): ?>
                                <?php foreach ($documents as $index => $doc): ?>
                                    <tr>
                                        <td class="text-center"><?= $index + 1 ?></td>
                                        <td>
                                            <?php
                                            $docTypes = \frontend\models\inventory\InventoryPurchaseOrderItemDoc::receivingDocType;
                                            echo Html::encode($docTypes[$doc->document_type] ?? 'Unknown');
                                            ?>
                                        </td>
                                        <td><?= Html::encode($doc->document_no) ?></td>
                                        <td class="text-center">
                                            <?php
                                            $filePath = Yii::getAlias('@frontend/uploads/inventory-po-attachments/') . $doc->filename;
                                            $fileExists = file_exists($filePath);

                                            if ($fileExists):
                                                ?>
                                                <?=
                                                Html::a(
                                                        'View <i class="fas fa-eye"></i>',
                                                        ['get-po-attachment', 'filename' => $doc->filename],
                                                        [
                                                            'class' => 'btn btn-sm btn-primary',
                                                            'target' => '_blank',
                                                            'title' => 'View Document',
                                                            'data-pjax' => '0'
                                                        ]
                                                )
                                                ?>

                                                <a href="<?= \yii\helpers\Url::to(['download-attachment', 'id' => $doc->id]) ?>" 
                                                   class="btn btn-sm btn-secondary m-1" 
                                                   title="Download"
                                                   download
                                                   data-pjax="0">
                                                    Download <i class="fas fa-download"></i>
                                                </a>

                                            <?php else: ?>
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> File not found
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= Html::encode($doc->uploadedBy->fullname ?? 'Unknown') ?>
                                        </td>
                                        <td>
                                            <?= MyFormatter::asDateTime_ReaddmYHi($doc->uploaded_at) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No documents attached
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    $(document).ready(function () {
                        $('[data-toggle="tooltip"]').tooltip();
                    });
                </script>
            </fieldset>
        </div>
    </div>
</div>
