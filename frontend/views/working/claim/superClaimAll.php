<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;
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
    echo $this->render('__ClaimNavBar', ['module' => 'super_claims', 'pageKey' => '1']);
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
                'attribute' => 'claims_id',
                'format' => 'raw',
                'value' => function($data) {
                    $url = '/working/claim/viewonly?claimsMasterId=' . $data->claims_master_id;

                    $viewDetail = Html::a($data->claims_id, "#", ["value" => \yii\helpers\Url::to($url), "class" => "modalButton"]);

                    if (!in_array($data->claims_status, array(1, 2, 3), true)) { // not allowed if it's paid, cancelled or rejected
                        $transfer = Html::a('<i class="fas fa-exchange-alt text-secondary"></i>', "javascript:alert('Cannot transfer, claim already " . $data['claimsStatus']['status_name'] . "')",
                                        [
                                            'class' => 'float-right',
                                        ]
                        );
                    } else {
                        $transfer = Html::a('<i class="fas fa-exchange-alt"></i>', "javascript:",
                                        [
                                            'class' => 'float-right',
                                            'title' => 'Transfer Claimant',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#workingModel',
                                            'data-claims_master_id' => $data->claims_master_id,
                                            'data-claims_id' => $data->claims_id,
                                            'data-claims_claimant' => $data['claimant']['fullname'],
                                            'data-claim_type' => $data['claimType']['claim_name'],
                                            'data-label' => 'Transfer Claimant'
                                        ]
                        );
                    }
                    return $viewDetail . "&nbsp;&nbsp;&nbsp;" . $transfer;
                }
            ],
            [
                'attribute' => 'claims_mi_id',
                'label' => 'Doc. Incoming idx no.',
                'format' => 'raw',
                'value' => function($data) {
                    $mi = $data['claimsMi'];
                    if ($mi) {
                        return Html::a($mi->index_no . ' ',
                                        "#",
                                        ["value" => \yii\helpers\Url::to('/working/mi/viewonly?id=' . $mi->id),
                                            "class" => "modalButton"])
                                . Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
                                        [
                                            'title' => "Click to view me",
                                            "value" => ("/working/mi/get-file?filename=" . urlencode($mi->filename)),
                                            "class" => "modalButtonPdf m-2"]);
                    }
                }
            ],
            [
                'attribute' => 'claim_type',
                'label' => 'Claim Type',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimType->claim_name;
                }
            ],
            [
                'attribute' => 'claimant_id',
                'label' => 'Claimant',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimant->fullname;
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Submission Date',
                'format' => 'raw',
                'value' => function($data) {
                    return MyFormatter::asDate_Read($data->created_at);
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'en',
                    'dateFormat' => 'yyyy-MM-dd',
//                    'dateFormat'=>'php:d/m/Y',
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            [
                'attribute' => 'claims_status',
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimsStatus->status_name;
                }
            ],
            [
                'attribute' => 'total_amount',
                'label' => 'Total Amount',
                'format' => 'raw',
                'value' => function($data) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asCurrency($data->total_amount) . '</p>';
                }
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
                        'action' => '/working/claim/super-transfer-claimant',
                        'method' => 'post',
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Process..</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-striped ">
                    <tbody>
                        <tr>
                            <td>Claim ID</td>
                            <td> : <span class="bold" id="modal-claims_id"></span></td>
                        </tr>
                        <tr>
                            <td>Claim Type</td>
                            <td> : <span class="bold" id="modal-claim_type"></span></td>
                        </tr>
                        <tr>
                            <td>Current Claimant</td>
                            <td> : <span class="bold" id="modal-claims_claimant"></span></td>
                        </tr>

                    </tbody>
                </table>

                <input type="hidden" class="form-control" id="modal-claims_master_id" name="claims_master_id" readonly=""/>


                <div class="form-group">
                    <label for="approval" class="col-form-label">Change Claimant to:</label>
                    <?php
                    echo Html::dropDownList(
                            'new_claimant',
                            '',
                            \common\models\User::getActiveDropDownList(),
                            [
                                'id' => 'modal-new_claimant',
                                'class' => 'form-control'
                            ]
                    );
                    ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" data-confirm="Are you sure to transfer the Claimant?">Submit</button>
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
            modal.find('#modal-claims_master_id').val(button.data('claims_master_id'));
            modal.find('#modal-claim_type').val(button.data('claim_type'));
            modal.find('#modal-claims_id').html(button.data('claims_id'));
            modal.find('#modal-claims_claimant').html(button.data('claims_claimant'));
            modal.find('#modal-claim_type').html(button.data('claim_type'));
            modal.find('#workingModalLabel').html(button.data('label'));
        });
    });
</script>