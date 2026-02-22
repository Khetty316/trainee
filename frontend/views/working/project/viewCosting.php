<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
?>
<style>
    .borderTable th{
        border: 1px solid black!important;
    }

    #myModalContent, #workingModelContent{
        height:85vh;
        overflow-y: scroll;
    }
</style>
<div class="project-master-view">
    <?= $this->render('__ProjectNavBar', ['pageKey' => '5', 'id' => $model->id, 'projectCode' => $model->proj_code, 'model' => $model]); ?>

    <fieldset class="border border-dark pl-3 pr-3 pb-3">
        <legend class="w-auto pl-2 pr-2 mb-0 text-primary">Invoices</legend>
        <?php
        if ($invoiceCostingTotal) {
            ?>
            <table class='table-sm table-borderless'>
                <tr></tr>
                <?php
                foreach ($invoiceCostingTotal as $key => $invoiceCost) {
                    echo '<tr><td style="width:300px">' . $invoiceCost['doc_type_name']
                    . '</td><td class="text-right" style="width:200px">' . $invoiceCost['currency_sign'] . " " . MyFormatter::asDecimal2($invoiceCost['total']) . '</td><td>';
                    echo Html::a('View Detail',
                            "javascript:",
                            [
                                'class' => 'text-primary ml-2 mr-2',
                                'title' => 'Edit',
                                'data-toggle' => 'modal',
                                'data-target' => '#myModal',
                                'data-content' => 'invoice',
                                'data-doc_type_id' => $invoiceCost['doc_type_id']
                            ]
                    );
                    echo '</td></tr>';
                }
                ?>
            </table>
            <?php
        } else {
            echo Html::tag('p', '-- No Records --', ['class' => 'text-center w-100']);
        }
        ?>
    </fieldset>
    <fieldset class="border border-dark pl-3 pr-3 pb-3">
        <legend class="w-auto pl-2 pr-2 mb-0 text-primary">Claims</legend>
        <?php
        if ($claimCostingTotal) {
            ?>
            <table class='table-sm table-borderless'>
                <tr></tr>
                <?php
                foreach ($claimCostingTotal as $key => $claimCost) {
                    echo '<tr><td style="width:300px">' . $claimCost['claim_name']
                    . '</td><td class="text-right" style="width:200px">RM ' . MyFormatter::asDecimal2($claimCost['total']) . '</td><td>';
                    echo Html::a('View Detail',
                            "javascript:",
                            [
                                'class' => 'text-primary ml-2 mr-2',
                                'title' => 'Edit',
                                'data-toggle' => 'modal',
                                'data-target' => '#myModal',
                                'data-content' => 'claim',
                                'data-claim_type' => $claimCost['claim_type']
                            ]
                    );
                    echo '</td></tr>';
                }
                ?>
            </table>
            <?php
        } else {
            echo Html::tag('p', '-- No Records --', ['class' => 'text-center w-100']);
        }
        ?>
    </fieldset>
    <fieldset class="border border-dark pl-3 pr-3 pb-3">
        <legend class="w-auto pl-2 pr-2 mb-0 text-primary">Sub Cons</legend>
        <?php
        if ($subconClaimTotal) {
            ?>
            <table class='table-sm table-borderless'>
                <tr></tr>
                <?php
                foreach ($subconClaimTotal as $key => $subconCost) {
                    echo '<tr><td style="width:300px">' . $subconCost['company_name'] . ' <b>' . $subconCost['description'].'</b>'
                    . '</td><td class="text-right" style="width:200px">RM ' . MyFormatter::asDecimal2($subconCost['total']) . '</td><td>';
                    echo Html::a('View Detail',
                            "javascript:",
                            [
                                'class' => 'text-primary ml-2 mr-2',
                                'title' => 'Edit',
                                'data-toggle' => 'modal',
                                'data-target' => '#myModal',
                                'data-content' => 'claim',
                                'data-claim_type' => $claimCost['claim_type']
                            ]
                    );
                    echo '</td></tr>';
                }
                ?>
            </table>
            <?php
        } else {
            echo Html::tag('p', '-- No Records --', ['class' => 'text-center w-100']);
        }
        ?>
    </fieldset>
</div>


<!--Modal for editing invoice (sub vs main)-->
<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background-color:#fefefe" id="workingModelContent">
            <!--<form method="post" action="/working/mi/insertgrn">-->

            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/project/update-mi-po',
                        'method' => 'post',
                        'id' => 'project-form',
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Process..</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0">
                    <tbody>
                        <tr>
                            <td>Index No</td>
                            <td> : <span class="bold" id="modal-idxno"></span></td>
                        </tr>
                        <tr>
                            <td>Particular</td>
                            <td> : <span class="bold" id="particular"></span></td>
                        </tr>
                        <tr>
                            <td>Reference No.</td>
                            <td> : <span class="bold" id="reference_no"></span></td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td> : <span class="bold" id="amount"></span></td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-group">
                    <input type="text" class="form-control hidden" id="mi_id" name="miId">
                    <input type="text" class="form-control hidden" name="projId" value="<?= $model->id ?>">
                </div>
                <div class="form-group">
                    <label for="grn" class="col-form-label">P.O.:</label>
                    <?php
                    $data = \frontend\models\working\po\PurchaseOrderMaster::find()
                            ->select(['po_number as value', 'po_number as label', 'po_id as id'])
                            ->asArray()
                            ->all();


                    echo \yii\jui\AutoComplete::widget([
                        'clientOptions' => [
                            'appendTo' => '#project-form',
                            'source' => $data,
                            'minLength' => '1',
                            'autoFill' => true,
                            'select' => new \yii\web\JsExpression("function( event, ui ) { 
			        $('#po_id').val(ui.item.id);                            getRelatedInvoices(ui.item.id);

			     }"),
                            'change' => new \yii\web\JsExpression("function( event, ui ) { 
                                if(ui.item){
                                    $(this).val(ui.item.value);
                                }else{
                                    $(this).val('');
                                    $('#po_id').val('');
                                    getRelatedInvoices('');
                                }

			     }"),
                            'delay' => 50
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'id' => 'poAutocomplete'
                        ]
                    ]);
                    ?>

                    <input type="text" class="form-control hidden" id="po_id" name="MasterIncomings[po_id]" />

                </div>
                <div class="form-group">
                    <label for="remarks" class="col-form-label">Related Invoices:</label>
                    <div id="div_po_related_inv"></div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" >Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


<script>

    $(function () {

        $('#myModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            var content = button.data('content');
            if (content === "claim") {
                getClaimsDetail(<?= $model->id ?>, button.data('claim_type'));
            } else if (content === "invoice") {
                getInvoiceDetail('<?= $model->proj_code ?>', button.data('doc_type_id'));
            }
        });

        // open Modal for edit Invoice (sub / main invoice)
        $('#workingModel').on('show.bs.modal', function (event) {
            $('#myModal').modal('hide');
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#modal-idxno').text(button.data('idxno'));
            modal.find('.modal-body #poAutocomplete').val(button.data('po_number'));
            modal.find('.modal-body #po_id').val(button.data('po_id'));
            modal.find('.modal-body #mi_id').val(button.data('mi_id'));
            modal.find('.modal-body #particular').text(button.data('particular'));
            modal.find('.modal-body #reference_no').text(button.data('reference_no'));
            modal.find('.modal-body #amount').text(button.data('currency_sign') + " " + button.data('amount'));
            getRelatedInvoices(button.data('po_id'));
        });

        $('#workingModel').on('hide.bs.modal', function (event) {
            $('#myModal').modal('show');

        });

    });

    function getRelatedInvoices(poId) {
        if (poId !== undefined && poId !== '') {
            var miId = $("#mi_id").val();
            $("#div_po_related_inv").html('<p class="text-center">Loading....</p>');
            $("#div_po_related_inv").load('<?= \yii\helpers\Url::to('/working/mi/get-po-proj-related-inv?poId=') ?>' + poId + '&miId=' + miId + '&projCode=<?= $model->proj_code ?>');

        } else {
            $("#div_po_related_inv").html('<p class="text-center">-- (No Record) --</p>');

        }
    }

    function getClaimsDetail(projId, claimType) {
        if (projId !== undefined && projId !== '' && claimType !== undefined && claimType !== '') {
            $("#myModalContent").html('<p class="text-center">Loading....</p>');
            $("#myModalContent").load('<?= \yii\helpers\Url::to('/working/project/view-costing-claim-detail?projId=') ?>' + projId + '&claimType=' + claimType);
        } else {
            $("#myModalContent").html('<p class="text-center">-- (No Record) --</p>');
        }
    }

    function getInvoiceDetail(projCode, docTypeId) {
        if (projCode !== undefined && projCode !== '' && docTypeId !== undefined && docTypeId !== '') {
            $("#myModalContent").html('<p class="text-center">Loading....</p>');
            $("#myModalContent").load('<?= \yii\helpers\Url::to('/working/project/view-costing-invoice-detail?projCode=') ?>' + projCode + '&docTypeId=' + docTypeId);
        } else {
            $("#myModalContent").html('<p class="text-center">-- (No Record) --</p>');
        }
    }
</script>