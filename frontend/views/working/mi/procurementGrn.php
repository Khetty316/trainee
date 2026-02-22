<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use \yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;
use common\models\myTools\MyFormatter;
use \yii\jui\AutoComplete;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\MasterIncomingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Provide GRN';
$this->params['breadcrumbs'][] = ['label' => 'Procurement - Document Incoming'];
$this->params['breadcrumbs'][] = $this->title;
?>
<!--
<script>
    $(document).ready(function () {
        $("#navMasterIncoming").addClass("active");
    });
</script>-->
<div class="master-incomings-index">
    <?= $this->render('__MINavBar', ['title' => $this->title, 'module' => 'mi_procurement']) ?>

    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>
    <?php
    // echo $this->render('_search', ['model' => $searchModel]);   
    ?>



    <div class="tab-content p-1">


        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'headerRowOptions' => ['class' => 'my-thead'],
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'columns' => [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{grn}',
                    'buttons' => [
                        'grn' => function ($url, $model, $key) {
                            return Html::a(
                                            '<i class="fas fa-pencil-alt fa-lg text-success"></i>',
                                            "#",
                                            ['onclick' => 'getPriorGRN(' . $model->id . ')',
                                                'title' => 'Process',
//                                            'data-pjax' => '0',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#workingModel',
//                                            'data-target' => '#confirm-model',
                                                'data-id' => $model->id,
                                                'data-idxno' => $model->index_no,
                                                'data-doctype' => $model->doc_type_name,
                                                'data-doctypeid' => $model->doc_type_id,
                                                'data-projcode' => $model->project_code,
                                                'data-projectname' => $model->project_name,
                                                'data-currentstep' => $model->current_step,
                                                'data-po_number' => $model->po_number,
                                                'data-po_id' => $model->po_id,
                                            ]
                            );
                        },
                    ],
                ],
                [
                    'attribute' => 'index_no',
                    'label' => "Index Number",
                    'format' => 'raw',
                    'value' => function($model) {
                        $title = "Uploaded By: " . $model->uploader_fullname . "\n" . "At time: " . MyFormatter::asDateTime_Read($model->created_at);
                        $title .= "\nRemarks: " . $model->remarks;
                        return Html::a($model->index_no . ' <i class="fas fa-info-circle"></i>',
                                        "#",
                                        [
                                            "title" => $title,
                                            "value" => \yii\helpers\Url::to('viewonly?id=' . $model->id),
                                            "class" => "modalButton"])
                                . Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
                                        [
                                            'title' => "Click to view me",
                                            "value" => ("/working/mi/get-file?filename=" . urlencode($model->filename)),
                                            "class" => "modalButtonPdf m-2"]);
                    }
                ],
                [
                    'attribute' => 'doc_type_name',
                    'label' => 'Document Type',
                    'filter' => $docTypeList,
                ],
                [
                    'attribute' => 'doc_due_date',
                    'value' => function($model) {
                        return $model->doc_due_date == "" ? "" : Yii::$app->formatter->asDatetime($model->doc_due_date, 'php:d/m/Y');
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'doc_due_date',
                        'language' => 'en',
                        'dateFormat' => 'dd-MM-yyyy',
//                    'dateFormat'=>'php:d/m/Y',
                        'options' => ['class' => 'form-control'],
                    ]),
                    'format' => 'html',
                ],
                'reference_no',
                'particular',
                [
                    'attribute' => 'isPerforma',
                    'value' => function ($model) {
                        return $model->isPerforma ? 'Yes' : '';
                    },
                    'label' => 'Pro Forma?'
                ],
                [
                    'attribute' => 'file_type_name',
                    'label' => 'File Type',
                    'filter' => $fileTypeList,
                ],
                'received_from',
                [
                    'attribute' => "amount",
                    'format' => 'raw',
                    'value' => function($data) {
                        $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                        $str = '';
                        foreach ($projects as $key => $project) {
//                            if($project->amount>0)
                            $amt = $project->amount > 0 ? $project->currency->currency_sign . " " . MyFormatter::asDecimal2($project->amount) : ' - ';
                            $str .= ($str == '' ? '' : ',<br/>') . $amt;
                        }
                        return '<p class="text-right m-0">' . $str . '</p>';
                    }
                ],
                [
                    'attribute' => 'project_code',
                    'format' => 'raw',
                    'value' => function($data) {
                        $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                        $str = '';
                        foreach ($projects as $key => $project) {
                            $str .= ($str == '' ? '' : ',<br/>') . $project->project_code;
                        }
                        return $str;
                    }
                ],
                [
                    'attribute' => 'requestor_fullname',
                    'format' => 'raw',
                    'label' => 'Requestor',
                    'value' => function($data) {
                        $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                        $str = '';
                        foreach ($projects as $key => $project) {
                            $str .= ($str == '' ? '' : ',<br/>') . $project->requestor0->fullname;
                        }
                        return $str;
                    }
//                'attribute' => 'requestor_fullname',
                ],
            ],
        ]);
        ?>
    </div>
</div>

<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!--<form method="post" action="/working/mi/insertgrn">-->

            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/mi/procinsertgrn',
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
                            <td>Doc Type</td>
                            <td> : <span class="bold" id="modal-doctype"></span></td>
                        </tr>
                        <tr>
                            <td>Project</td>
                            <td> : <span class="bold" id="modal-project"></span></td>
                        </tr>

                    </tbody>
                </table>


                <div class="form-group">
                    <input type="text" style="display:none" class="form-control" id="mi_id" name="mi_id">
                    <input type="text" style="display:none" class="form-control" id="currentstep" name="currentstep">
                </div>
                <div class="form-group">
                    <label for="approval" class="col-form-label">Approval:</label>
                    <?= yii\bootstrap4\Html::dropDownList("approval", "1", ["1" => "APPROVE", "0" => "REJECT"], ["class" => "form-control"]) ?>
                </div>
                <div class="form-group">
                    <label for="grn" class="col-form-label req">GRN:</label>
                    <input type="text" class="form-control" id="grn" name="grn" required/>
                </div>
                <div class="form-group">
                    <label for="po" class="col-form-label" id='label_po'>P.O.:</label>
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
                            'delay' => 1
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'id' => 'poAutocomplete'
                        ]
                    ]);
                    ?>

                    <input type="text" class="form-control hidden" id="po_id" name="po_id" />

                </div>
                <div class="form-group">
                    <label for="remarks" class="col-form-label">Related Invoices:</label>
                    <div id="div_po_related_inv"></div>
                </div>
                <div class="form-group">
                    <label for="remarks" class="col-form-label">Message:</label>
                    <?= yii\bootstrap4\Html::textarea("remarks", "", ['class' => 'form-control', 'id' => 'remarks']) ?>
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
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#modal-idxno').text(button.data('idxno'));
            modal.find('#modal-doctype').text(button.data('doctype'));
            modal.find('#modal-project').text(button.data('projcode') + " - " + button.data('projectname'));
            modal.find('.modal-body #poAutocomplete').val(button.data('po_number'));
            modal.find('.modal-body #po_id').val(button.data('po_id'));
            modal.find('.modal-body #mi_id').val(button.data('id'));
            modal.find('.modal-body #currentstep').val(button.data('currentstep'));
            
            // Commented on 20210629, some of the invoice doesn't have P.O.
//            var docTypeId = button.data('doctypeid');
//            if (docTypeId == 2 || docTypeId == 4) {
//                $('#poAutocomplete').prop('required', true);
//                $('#label_po').addClass('req');
//            } else {
//                $('#poAutocomplete').prop('required', false);
//                $('#label_po').removeClass('req');
//            }
            getRelatedInvoices(button.data('po_id'));
        });

    });


    function getPriorGRN(miId) {
        $.ajax({
            type: "POST",
            url: "/working/mi/get-prior-grn?mi_id=" + miId,
            // The key needs to match your method's input parameter (case-sensitive).
            data: {'mi_id': miId},
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data) {
                $("#grn").val(data);
            },
            error: function (errMsg) {
                $("#grn").val('');
            }
        });
    }

    function getRelatedInvoices(poId) {

        if (poId !== undefined && poId !== '') {
            var miId = $("#mi_id").val();
            $("#div_po_related_inv").html('<p class="text-center">Loading....</p>');
            $("#div_po_related_inv").load('<?= \yii\helpers\Url::to('/working/mi/get-po-related-inv?poId=') ?>' + poId + '&miId=' + miId);
        } else {
            $("#div_po_related_inv").html('<p class="text-center">-- (No Record) --</p>');

        }
    }


</script>
