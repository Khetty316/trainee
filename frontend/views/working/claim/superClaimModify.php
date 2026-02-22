<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">


    <?php
//    $this->title = "HR Claim List";
//    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <?php
    echo $this->render('__ClaimNavBar', ['module' => 'super_claims', 'pageKey' => '2']);
    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <?php
    $i = 0;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'options' => ['class' => 'table-sm'],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'columns' => [
            [
                'attribute' => 'claims_detail_id',
                'label' => 'Item ID',
                'format' => 'raw',
                'value' => function($model) {
//                    $url = '/working/claim/modify-claim-detail-ajax?claimsDetailId=' . $data->claims_detail_id;
                    return $model->claims_detail_id . "&nbsp;&nbsp;&nbsp;" . Html::a('<i class="far fa-edit float-right"></i>',
                                    "javascript:",
                                    [
                                        'title' => 'Modify Claim Type',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#workingModel',
                                        'data-title' => 'Modify Claim Type',
                                        'data-claims_type' => $model->claim_type,
                                        'data-claims_detail_id' => $model->claims_detail_id,
                                    ]
                    );


//                    return $data->claims_detail_id . "&nbsp;&nbsp;&nbsp;" . Html::a("<i class='far fa-edit'></i>", "javascript:", ["value" => $url, "class" => "modalButton mr-3"]);
                }
            ],
            [
                'attribute' => 'claims_id',
                'format' => 'raw',
                'value' => function($data) {
                    $url = '/working/claim/viewonly?claimsMasterId=' . $data->claims_master_id;
                    return Html::a($data->claims_id, "#", ["value" => \yii\helpers\Url::to($url), "class" => "modalButton"]);
                }
            ],
            'claim_type_name',
            'claimant',
            [
                'attribute' => 'detail',
                'value' => function($data) {
                    return ($data->claim_type == 'med' ? '(Medical) - ' : '') . $data->detail;
                }
            ],
            [
                'attribute' => 'claims_status_name',
                'label' => 'Status',
            ],
            [
                'attribute' => 'amount',
                'label' => 'Amount',
                'format' => 'raw',
                'value' => function($data) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asCurrency($data->amount) . '</p>';
                }
            ],
            [
                'attribute' => 'invoice_date',
                'label' => 'Invoice Date',
                'format' => 'raw',
                'value' => function($data) {
                    return MyFormatter::asDate_Read($data->invoice_date);
                },
                'filter' => \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'invoice_date',
                    'language' => 'en',
                    'dateFormat' => 'yyyy-MM-dd',
//                    'dateFormat'=>'php:d/m/Y',
                    'options' => ['class' => 'form-control'],
                ]),
            ],
        ],
    ]);
    ?>

</div>


<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/claim/super-modify-claim-type',
                        'method' => 'post',
                        'id' => 'project-form',
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <label for="modal-claims_detail_id" class="col-form-label">Item ID:</label>
                <input type="text" class="form-control" id="modal-claims_detail_id" name="claims_detail_id" readonly=""/>
                <div class="form-group">
                    <label for="approval" class="col-form-label">Change Claim Type to:</label>
                    <?php
                    echo Html::dropDownList(
                            'claims_type',
                            '',
                            frontend\models\working\claim\RefClaimType::getDropDownListNoTravel(),
                            [
                                'prompt' => 'Select...',
                                'id' => 'modal-claims_type',
                                'class' => 'form-control'
                            ]
                    );
                    ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" >Submit</button>
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
            modal.find('#modal-claims_detail_id').val(button.data('claims_detail_id'));
            modal.find('#modal-claims_type').val(button.data('claims_type'));
            modal.find('#workingModalLabel').html(button.data('title'));
        });
    });
</script>