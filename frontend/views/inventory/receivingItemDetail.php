<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

$this->title = 'Order Receive Detail';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Receiving'];
$this->params['breadcrumbs'][] = ['label' => 'History', 'url' => ['executive-receiving-history']];
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
                        <strong class="text-muted">Received At:</strong>
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
                                <td class="text-center"><?= $detail->received_qty ?? 0 ?></td>
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
                                                        '<i class="fas fa-eye"></i> View',
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
                                                   class="btn btn-sm btn-secondary ml-1" 
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
