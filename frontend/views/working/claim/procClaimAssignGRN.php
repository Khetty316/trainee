<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">


    <?php
    $this->title = "Assign GRN";
    $this->params['breadcrumbs'][] = ['label' => 'Procurement - Claim'];
    $this->params['breadcrumbs'][] = ['label' => 'Pre-GRN', 'url' => '/working/claim/proc-claim-grn'];
    $this->params['breadcrumbs'][] = $this->title;

    $dataProvider->sort->sortParam = false;


//            $claimMaster = ClaimsMaster::findOne(1);
    ?>
    <h4 class='mb-3'><?= $claimMaster->claimant->fullname . " - " . $claimMaster->claimType->claim_name . " (" . $claimMaster->claims_id . ")" ?>
        <?= ($claimMaster->claims_status == 4 ? "<span class='text-danger'> (PAID)</span>" : "") ?>
    </h4>

    <?php
    $i = 0;
    $total = 0;
    echo GridView::widget([
        'dataProvider' => $dataProvider,
//                'rowOptions' => function($model) use (&$okToSubmit) {
//                    if ($model->isExpired()) {
//                        $okToSubmit = false;
//                        return ['class' => 'table-danger'];
//                    }
//                },
        'layout' => '{items}{pager}',
        'options' => ['class' => 'table-sm'],
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'columns' => [
            [
                'attribute' => 'date1',
                'label' => 'Date',
                'format' => 'raw',
                'value' => function($data) {
                    return MyFormatter::asDate_Read($data->date1);
                }
            ],
            [
                'attribute' => 'detail',
                'format' => 'raw',
                'value' => function($data) {
                    return ($data->claim_type == "med" ? "(Medical) - " : "") . $data->detail;
                }
            ],
            'company_name',
            'receipt_no',
            [
                'attribute' => 'project_account',
                'format' => 'raw',
            ],
            [
                'attribute' => 'authorized_by',
                'value' => function($data) {
                    return $data->authorizedBy['username'];
                },
            ],
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'label' => 'Amount (RM)',
                'value' => function($data) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asDecimal2($data->amount) . '</p>';
                }
            ],
            [
                'attribute' => 'filename',
                'label' => "File",
                'format' => 'raw',
                'value' => function($data) {
                    if ($data->filename) {
                        return Html::a("<i class='far fa-file-alt'></i>", "/working/claim/get-file?filename=" . urlencode($data->filename), ['target' => '_blank']);
                    }
                }
            ],
            [
                'attribute' => 'grn_no',
                'label' => 'GRN',
                'format' => 'raw',
                'value' => function($data) {
                    $str = $data['grn_no'] . " " . Html::a("<i class='fas fa-pencil-alt fa-lg'></i>", "#",
                                    [
                                        'title' => 'Process',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#workingModel',
                                        'data-id' => $data->claims_detail_id,
                                        'data-detail' => $data->detail,
                                        'data-company' => $data->company_name,
                                        'data-receipt' => $data->receipt_no,
                                        'data-projcode' => $data->project_account,
                                        'data-amount' => $data->amount
                                    ]
                    );
                    return $str;
                }
            ]
        ],
    ]);
    ?>

</div>



<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!--<form method="post" action="/working/mi/insertgrn">-->


            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/claim/proc-claim-assign-grn',
                        'method' => 'post',
                        'id' => 'modal-form'
//                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Record GRN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0"  class="table table-sm table-striped">
                    <tbody>
                        <tr>
                            <td class="align-top">Detail</td>
                            <td class="align-top"> : </td><td><span class="bold" id="modal-detail"></span></td>
                        </tr>
                        <tr>
                            <td class="align-top">Company Name</td>
                            <td class="align-top"> : </td><td><span class="bold" id="modal-company"></span></td>
                        </tr>
                        <tr>
                            <td class="align-top">Receipt No.</td>
                            <td class="align-top"> : </td><td><span class="bold" id="modal-receipt"></span></td>
                        </tr>
                        <tr>
                            <td class="align-top">Project</td>
                            <td class="align-top"> : </td><td><span class="bold" id="modal-projcode" style="word-wrap: break-word"></span></td>
                        </tr>
                        <tr>
                            <td class="align-top">Amount</td>
                            <td> : </td><td><span class="bold" id="modal-amount"></span></td>
                        </tr>

                    </tbody>
                </table>

                <div class="form-group">
                    <label for="grn" class="col-form-label">GRN:</label>
                    <input type="text" class="form-control" id="grn" name="grn"/>
                    <input type="text" class="form-control" id="claimsDetailId" name="claimsDetailId" style="display: none"/>
                    <input type="text" class="form-control" id="claimsMasterId" name="claimsMasterId" style="display: none" value="<?= $claimMaster->claims_master_id ?>"/>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="insertGrn()">Record</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


<script>
    $(function () {
        $('#workingModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal        
            var modal = $(this);
            modal.find('#claimsDetailId').val(button.data('id'));
            modal.find('#modal-detail').html(button.data('detail'));
            modal.find('#modal-company').text(button.data('company'));
            modal.find('#modal-receipt').text(button.data('receipt'));
            modal.find('#modal-projcode').html(button.data('projcode'));
            modal.find('#modal-amount').text(button.data('amount'));

            modal.find('.modal-body #mi_id').val(button.data('id'));



        });



    });


    function insertGrn() {
        if ($("#grn").val() === "") {
            var ans = confirm("NO GRN?");
            if (!ans) {
                return;
            }
        }
        $("#modal-form").submit();
    }

</script>