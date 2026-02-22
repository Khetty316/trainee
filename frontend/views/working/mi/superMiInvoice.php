<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\MasterIncomingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Document Incoming';
//$this->params['breadcrumbs'][] = $this->title;
?>


<div class="mi-index">    
    <?php
    echo $this->render('__MINavBar', ['module' => 'mi_super', 'pageKey' => '2']);
    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <div>
        <div class='form-group'>
            <div class='form-inline'>
                <span class='col-md-2'>Project Code : </span>
                <?php
                echo \yii\jui\AutoComplete::widget([
                    'clientOptions' => [
                        'appendTo' => '#searchForm',
                        'source' => \yii\helpers\Url::to(['/list/getprojectlist']),
                        'minLength' => '1',
                        'autoFill' => true,
                        'select' => new \yii\web\JsExpression("function( event, ui ) { $('#workplace').html(''); }"),
                        'change' => new \yii\web\JsExpression("function( event, ui ) { 
                                if(ui.item){
                                    $(this).val(ui.item.value);
                                }else{
                                    $(this).val('');
                                }
			     }"),
                        'delay' => 50,
                    ],
                    'options' => [
                        'class' => 'form-control  col-md-4',
                        'id' => 'project_code_search',
//                'autocomplete' => 'on'
                    ],
                    'value' => $projCodeSearch
                ]);
                ?>          
            </div>
        </div>

        <div class='form-group'>
            <div class='form-inline'>
                <span class='col-md-2'>Invoice Type : </span>
                <?php
                echo Html::dropDownList("#", $invTypeSearch, $docTypeList, ['prompt' => 'All', 'class' => 'form-control col-md-4', 'id' => 'invoice_type_search']);
                ?>
                <a href="javascript:getInvoiceDetail()" class="btn btn-primary ml-3">Search <i class="fas fa-search"></i></a>
            </div>
        </div>

    </div>
    <div id="workplace">

    </div>

</div>

<!--Modal for editing invoice (sub vs main)-->
<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background-color:#fefefe" id="workingModelContent">
            <!--<form method="post" action="/working/mi/insertgrn">-->

            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/mi/update-mi-po',
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
                    <input type="text" class="form-control hidden" id="projCodeSearch" name="projCodeSearch">
                    <input type="text" class="form-control hidden" id="invTypeSearch" name="invTypeSearch">
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
            modal.find('#projCodeSearch').val($("#project_code_search").val());
            modal.find('#invTypeSearch').val($("#invoice_type_search").val());
            getRelatedInvoices(button.data('po_id'));
        });

        getInvoiceDetail();
    });


    function getInvoiceDetail() {
        var projCode = $("#project_code_search").val();
        var docTypeId = $("#invoice_type_search").val();
        if (projCode !== '') {
            $("#workplace").html('<p class="text-center">Loading....</p>');
            $("#workplace").load('<?= \yii\helpers\Url::to('/working/mi/super-view-invoice-detail?projCode=') ?>' + projCode + '&docTypeId=' + docTypeId);
        } else {
            $("#workplace").html('<p class="text-center">-- (No Record) --</p>');
        }
    }


    function getRelatedInvoices(poId) {
        if (poId !== undefined && poId !== '') {
            var miId = $("#mi_id").val();
            var projCode = $("#project_code_search").val();
            $("#div_po_related_inv").html('<p class="text-center">Loading....</p>');
            $("#div_po_related_inv").load('<?= \yii\helpers\Url::to('/working/mi/get-po-proj-related-inv?poId=') ?>' + poId + '&miId=' + miId + '&projCode=' + projCode);
        } else {
            $("#div_po_related_inv").html('<p class="text-center">-- (No Record) --</p>');

        }
    }



</script>