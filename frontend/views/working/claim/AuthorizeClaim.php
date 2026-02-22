<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">

    <?php
    $this->title = "Authorize Claim Item";
    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <h3><?= $this->title ?></h3>


    <?php
    $i = 0;
    foreach ($dataProviders as $claimsId => $dataProvider) {
        $model = $dataProvider->getModels()[0];
        ?>
        <div class="card mt-2 border-dark  bg-light mt-3">
            <div class="p-1 pl-2 m-0 card-header hoverItem border-dark " id="heading_<?= ++$i ?>" data-toggle="collapse" data-target="#collapse_<?= $i ?>" aria-expanded="false" aria-controls="collapse_<?= $i ?>">
                <h5 class="p-0 m-2"><span class='text-primary'><?= $model->claimant->fullname . " - " . $model->claimType->claim_name . " (" . $model->claimMaster->claims_id . ")" ?></span></h5>
            </div>
            <div id="collapse_<?= $i ?>" class="collapse show" aria-labelledby="heading_<?= $i ?>"  ><div class="card-body p-1">
                    <?php
                    $recordIds = array();
                    $travelType = "";
                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
//                        'filterModel' => $searchModel,
                        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
                        'headerRowOptions' => ['class' => 'my-thead'],
                        'tableOptions' => ['class' => 'table-sm table table-striped table-bordered', 'id' => 'table_' . $i],
                        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                        'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) use (&$recordIds) {
                                    array_push($recordIds, $model->claims_detail_id);
                                    return ['value' => $model->claims_detail_id, 'step' => '', 'style' => 'text-align:center'];
                                },
//                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['style' => 'width: 1%']
                            ],
                            [
                                'attribute' => 'date1',
                                'label' => "Date",
                                'format' => 'raw',
                                'value' => function($data) use (&$travelType) {
                                    $travelType = $data->claim_type;
                                    return MyFormatter::asDate_Read($data->date1) . ($data->date2 ? (" - " . MyFormatter::asDate_Read($data->date2)) : "");
                                },
                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['class' => 'd-none d-md-table-cell']
                            ],
                            [
                                'attribute' => 'date1',
                                'label' => "Days",
                                'format' => 'raw',
                                'value' => function($data) {
                                    return $data->date2 ? (MyCommonFunction::countDays($data->date1, $data->date2) + 1) : 0;
                                },
                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                                'visible' => $model->claim_type == "tra" ? true : false
                            ],
                            [
                                'attribute' => 'detail',
                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'company_name',
                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                                'visible' => $model->claim_type == "tra" ? false : true
                            ],
                            [
                                'attribute' => 'receipt_no',
                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                                'visible' => $model->claim_type == "tra" ? false : true
                            ],
                            [
                                'attribute' => 'project_account',
                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                                'format' => 'raw',
                            ],
                            [
                                'attribute' => 'amount',
//                                'label' => 'Document Type',
                                'value' => function($data) {
                                    return MyFormatter::asCurrency($data->amount);
                                },
                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['class' => 'd-none d-md-table-cell']
                            ],
                            [
                                'attribute' => 'filename',
                                'format' => 'raw',
                                'label' => 'File',
                                'value' => function ($data) {
                                    return $data->filename == "" ? "" : Html::a("<i class='far fa-file-alt'></i>", "/working/claim/get-file?filename=" . urlencode($data->filename), ['target' => '_blank']);
                                },
                                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                                'headerOptions' => ['class' => 'd-none d-md-table-cell']
                            ],
                            [
                                'attribute' => 'amount',
                                'label' => '',
                                'format' => 'raw',
                                'value' => function($data) {
                                    $date = MyFormatter::asDate_Read($data->date1) . ($data->date2 ? (" - " . MyFormatter::asDate_Read($data->date2)) : "");
                                    $days = ($data->date2 ? (" (" . (MyCommonFunction::countDays($data->date1, $data->date2) + 1) . " days)") : "") . "<br>";
                                    $detail = "Detail: " . $data->detail . "<br>";
                                    $comp = ($data->company_name ? "Company: " . $data->company_name . "<br>" : "");
                                    $recp = $data->receipt_no ? "Receipt No.: " . $data->receipt_no . "<br>" : "";
                                    $proj = $data->project_account ? "Proj/Acc: " . $data->project_account . "<br>" : "";
                                    $amt = "Amount: " . MyFormatter::asCurrency($data->amount) . "   ";
                                    $file = $data->filename ? Html::a("<i class='far fa-file-alt'></i>", "/working/claim/get-file?filename=" . urlencode($data->filename), ['target' => '_blank']) : "";

                                    return $date . $days . $detail . $comp . $recp . $proj . $amt . $file;
                                },
                                'contentOptions' => ['class' => 'd-md-none'],
                                'headerOptions' => ['class' => 'd-md-none']
                            ]
                        ],
                    ]);
                    ?>
                    <button class="btn btn-primary mb-2 " onclick="bulkAuthorize('<?= $i ?>', '<?= $model->claim_master_id ?>')">Authorize All <i class="fas fa-check"></i></button>
                    <button class="btn btn-danger mb-2 " onclick="bulkReject('<?= $i ?>', '<?= $model->claim_master_id ?>')">Reject Selected <i class="fas fa-times-circle"></i></button>
                    <?php echo Html::textInput("", implode(",", $recordIds), ['id' => 'recordIds_' . $i, 'class' => 'hidden']); ?>
                </div>
            </div>
        </div>
    <?php } ?>    
</div>



<div class="hidden">
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
                'id' => 'myForm',
                'action' => '/working/claim/authorize-claim',
                'method' => 'post'
    ]);
    echo '<input type="text" name="claims_detail_ids_authorize" id="claims_detail_ids_authorize"/> ';
    echo '<input type="text" name="claims_detail_ids_reject" id="claims_detail_ids_reject"/> ';
    echo '<input type="text" name="claim_master_id" id="claim_master_id"/>';
    \yii\bootstrap4\ActiveForm::end();
    ?>
</div>


<script>
    function bulkAuthorize(table_idx, claimMasterId) {

        $("#claims_detail_ids_authorize").val($("#recordIds_" + table_idx).val());
        $("#claims_detail_ids_reject").val('');
        $("#claim_master_id").val(claimMasterId);

        var ans = confirm("Authorize ALL claim item(s)?");
        if (ans) {
            $("#myForm").submit();
        }
    }

    function bulkReject(table_idx, claimMasterId) {
        var checkedList = [];
        $('#table_' + table_idx + ' tbody input:checked').each(function () {
            checkedList.push(this.value);
        });
//        $("#claims_detail_ids_authorize").val($("#recordIds_" + table_idx).val());
        $("#claims_detail_ids_reject").val(checkedList);

        if ($("#claims_detail_ids_reject").val() === "") {
            alert("No claim item is selected");
            return;
        }
        $("#claim_master_id").val(claimMasterId);
        var ans = confirm("Are you sure to REJECT the selected item(s)?");
        if (ans) {
            $("#myForm").submit();
        }
    }





</script>