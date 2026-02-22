<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">
    <style>
        .table-bordered > thead > tr > th,
        .table-bordered > tbody > tr > th,
        .table-bordered > tfoot > tr > th,
        .table-bordered > thead > tr > td,
        .table-bordered > tbody > tr > td,
        .table-bordered > tfoot > tr > td {
            border: 1px solid #00a65a; 
        }

    </style>
    <?= $this->render('__ClaimNavBar', ['module' => 'personal_claims', 'pageKey' => '1']) ?>
    <?php $this->params['breadcrumbs'][] = $this->title; ?>
    <p>
        <?= Html::a('Add Claim <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div id="accordion">

        <?php
        $i = 0;
        foreach ($dataProviders as $groupName => $dataProvider) {
            $dataProvider->sort->sortParam = false;
            if ($dataProvider->totalCount > 0) {
                $okToSubmit = true;
                $recordIds = array();
                $claimFamily = "";
                $total = 0;
                $i++;
                echo '<div class="card mt-2 border-dark  bg-light">'
                . '<div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" id="heading_' . $i . '" data-toggle="collapse" data-target="#collapse_' . $i . '" aria-expanded="true" aria-controls="collapse_' . $i . '">';
                echo '<span class="p-0 m-0 accordionHeader">' . $groupName . '</span>';
                echo '</div><div id="collapse_' . $i . '" class="collapse show " aria-labelledby="heading_' . $i . '"  ><div class="card-body p-1" style="background-color:white">';

                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}{pager}',
                    'options' => ['class' => 'table-sm'],
                    'pager' => ['class' => yii\bootstrap4\LinkPager::class],
                    'formatter' => [
                        'class' => 'yii\i18n\Formatter',
                        'nullDisplay' => '',
                    ],
                    'columns' => [
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete} {File} {lost} {repeat}',
                            'headerOptions' => ['style' => 'width:100px'],
                            'buttons' => [
                                'update' => function ($url, $model, $key) {
                                    $returnStr = Html::a("<i class='far fa-edit text-success'></i>", '/working/claim/update-claim?id=' . $model->claims_detail_id, ['class' => 'm-1', 'title' => "Update", 'data-method' => 'post']);
                                    return "$returnStr";
                                },
                                'delete' => function($url, $model, $key) {
                                    return Html::a('<i class="far fa-trash-alt text-danger"></i>', ['/working/claim/delete', 'id' => $model->claims_detail_id], ['title' => 'Delete', 'data-confirm' => "Are you sure you want to delete this item?", 'data-method' => "post"]);
                                },
                                'File' => function ($url, $model, $key) use (&$recordIds, &$claimFamily) {
                                    array_push($recordIds, $model->claims_detail_id);
                                    $claimFamily = $model->claimType->claim_family;
                                    $returnStr = ($model->filename == "" ? "" : Html::a("<i class='far fa-file-alt' ></i>", "/working/claim/get-file?filename=" .
                                                    urlencode($model->filename), ['target' => "_blank", 'class' => 'm-1', 'title' => "Click to view"]));
                                    return $returnStr;
                                },
                                'lost' => function ($url, $model, $key) {
                                    $lost = ($model->receipt_lost == 1 ? ' <i class="fas fa-exclamation-triangle fa-sm text-danger" title="Receipt Lost"></i>' : '');
                                    return $lost;
                                },
                                'repeat' => function($url, $model, $key) {
                                    $repeat = ($model->claim_type == "tra") ? \frontend\models\working\claim\ClaimsDetail::checkTravelRepeatedAll($model->claims_detail_id) : '';
                                    return $repeat ? ' <br/><small class="text-danger">Date conflict with an item in: ' . $repeat . '</small>' : '';
//                                    return $repeat ? ' <i class="fas fa-exclamation-circle fa-sm text-danger" title="Date conflict with in item in: ' . $repeat . '"></i>' : '';
                                }
                            ],
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'date1',
                            'label' => 'Date',
                            'format' => 'raw',
                            'value' => function($data) {
                                if ($data->claim_type == "tra") {
                                    return MyFormatter::asDate_Read($data->date1) . " - " . MyFormatter::asDate_Read($data->date2);
                                } else {
                                    return MyFormatter::asDate_Read($data->date1);
                                }
                            },
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'detail',
                            'format' => 'raw',
                            'value' => function($data) {
                                return $data->showDetail();
                            },
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'company_name',
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'receipt_no',
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'project_account',
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'authorized_by',
                            'value' => function($data) {
                                return $data->authorizedBy['username'];
                            },
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'amount',
                            'format' => 'raw',
                            'value' => function($data)use(&$total) {
                                $total += $data->amount;
                                return '<p class="p-0 m-0 text-right">' . MyFormatter::asDecimal2($data->amount) . '</p>';
                            },
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'amount',
                            'format' => 'raw',
                            'value' => function($model) use(&$okToSubmit) {
                                $updateLink = Html::a("<i class='far fa-edit'></i>", '/working/claim/update-claim?id=' . $model->claims_detail_id, ['class' => 'm-1', 'title' => "Update", 'data-method' => 'post']);
                                $deleteLink = Html::a("<i class='far fa-trash-alt text-danger'></i>", '/working/claim/delete?id=' . $model->claims_detail_id, ['class' => 'm-1', 'data-confirm' => "Are you sure you want to delete this item?", 'data-method' => 'post']);
                                $lostSign = ($model->receipt_lost == 1 ? ' <i class="fas fa-exclamation-triangle fa-sm text-danger"> Receipt Lost</i>' : '');
                                $date = "Date: <b>" . MyFormatter::asDate_Read($model->date1) . ($model->date2 ? " - " . MyFormatter::asDate_Read($model->date2) . " (" . (\common\models\myTools\MyCommonFunction::countDays($model->date1, $model->date2) + 1) . " days)" : "") . "</b>";
                                $file = $model->filename ? Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/working/claim/get-file?filename=" . urlencode($model->filename), ["class" => "m-2", 'target' => "_blank"]) : "";
                                $detail = "<p class='m-0 p-0'>Detail: <b>" . $model->detail . "</b></p>";
                                $comp = $model->company_name ? ("<p class='m-0 p-0'>Company: <b>" . $model->company_name . "</b></p>") : "";
                                $recp = $model->receipt_no ? ("<p class='m-0 p-0'>Receipt No.: <b>" . $model->receipt_no . "</b></p>") : "";
                                $proj = "<p class='m-0 p-0'>Proj/Acc: <b>" . $model->project_account . "</b></p>";
                                $auth = "<p class='m-0 p-0'>Auth By: <b>" . $model->authorizedBy['username'] . "</b></p>";
                                $amt = "<p class='m-0 p-0'>Amt: <b>RM " . MyFormatter::asDecimal2($model->amount) . "</b></p>";


                                $repeat = ($model->claim_type == "tra") ? \frontend\models\working\claim\ClaimsDetail::checkTravelRepeatedAll($model->claims_detail_id) : '';
                                $repeatFlag = '';
                                if ($repeat != '') {
                                    $repeatFlag = '<small class="text-danger"><b>Date conflict with an item in: ' . $repeat . '</b></small>';
                                    $okToSubmit = false;
                                }

                                return '<p class="m-0 p-0">' .
                                        $updateLink . $deleteLink . $file . $lostSign . '</p>'
                                        . '<p>' . $repeatFlag . '</p>'
                                        . $date . '<br/>'
                                        . $detail . $comp . $recp . $proj . $auth . $amt;
                            },
                            'contentOptions' => ['class' => 'd-md-none'],
                            'headerOptions' => ['class' => 'd-none']
                        ],
                    ],
                ]);
                echo "<p class='text-right mr-1 mb-1'><b> Total: " . MyFormatter::asCurrency($total) . "</b></p>";
                echo Html::textInput("", implode(",", $recordIds), ['id' => 'recordIds_' . $i, 'class' => 'hidden']);
                if ($okToSubmit) {
                    echo '<button class="btn btn-primary mb-2 float-right" onclick="submitClaim(\'' . $groupName . '\',\'recordIds_' . $i . '\',\'' . $claimFamily . '\')">Submit ' . $groupName . ' <i class="fas fa-check"></i></button>';
                } else {
                    echo '<button class="btn btn-secondary mb-2 float-right disabled">Unable to submit ' . $groupName . ' <i class="fas fa-check"></i></button>';
                }
                echo '</div></div></div>';
            }
        }
        ?>



        <?php
        foreach ($dataProviderOthers as $key => $dataProvider) {
//        **************************** FOR EXPIRED RECORDS
            if ($dataProvider->totalCount > 0) {
                $i++;
                $title = "";
                $titleClass = "";
                if ($key == "outdated") {
                    $title = "Expired";
                    $titleClass = "bg-warning";
                } else {
                    $title = "Request Rejected";
                    $titleClass = "bg-danger text-white";
                }


                echo '<div class="card mt-2 border-dark">'
                . '<div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark ' . $titleClass . ' btn-header-link" id="heading_' . $i . '" data-toggle="collapse" data-target="#collapse_' . $i . '" aria-expanded="true" aria-controls="collapse_' . $i . '">';
                echo '<span class="p-0 m-0" style="font-weight: bold">' . $title . '</span>';
                echo '</div><div id="collapse_' . $i . '" class="collapse show" aria-labelledby="heading_' . $i . '"  ><div class="card-body p-1">';
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}{pager}',
                    'options' => ['class' => 'table-sm'],
                    'pager' => ['class' => yii\bootstrap4\LinkPager::class],
                    'formatter' => [
                        'class' => 'yii\i18n\Formatter',
                        'nullDisplay' => '',
                    ],
                    'columns' => [
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete} {request} {File} {lost}',
                            'headerOptions' => ['style' => 'width:120px'],
                            'buttons' => [
                                'update' => function ($url, $model, $key) {

                                    $returnStr = $model->special_approved > 0 ? "" : Html::a("<i class='far fa-edit'></i>", '/working/claim/update-claim?id=' . $model->claims_detail_id, ['class' => 'm-1', 'title' => "Update", 'data-method' => 'post']);
                                    return "$returnStr";
                                },
                                'request' => function ($url, $model, $key) {
                                    $returnStr = "";
                                    if ($model->special_approved > 0) {
                                        $returnStr = Html::tag("i", "", ['class' => 'fas fa-star m-1 text-warning', 'title' => "Request Sent"]);
                                    } else {
                                        $returnStr = Html::a("<i class='far fa-star text-warning'></i>", "/working/claim/get-special-approval?id=$key", ['class' => 'm-1', 'title' => "Request for Special approval", 'data-method' => 'post']);
                                    }
                                    return $returnStr;
                                },
                                'File' => function ($url, $model, $key) {
                                    $returnStr = ($model->filename == "" ? "" : Html::a("<i class='far fa-file-alt' ></i>", "/working/claim/get-file?filename=" .
                                                    urlencode($model->filename), ['target' => "_blank", 'class' => 'm-1', 'title' => "Click to view"]));
                                    return $returnStr;
                                },
                                'lost' => function ($url, $model, $key) {
                                    $lost = ($model->receipt_lost == 1 ? '<i class="fas fa-exclamation-triangle fa-lg text-warning fa-sm text-danger" title="Receipt Lost"></i>' : '');
                                    return $lost;
                                },
                                'delete' => function($url, $model, $key) {
                                    return Html::a("<i class='far fa-trash-alt text-danger'></i>", "/working/claim/delete?id=$key",
                                                    ['class' => 'm-1 text-danger',
                                                        'title' => "Delete", 'data-method' => 'post',
                                                        'data-confirm' => "Are you sure you want to delete this item?",
                                    ]);
                                }
                            ],
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'date1',
                            'label' => 'Date',
                            'format' => 'raw',
                            'value' => function($data) {
                                if ($data->claim_type == "tra") {
                                    return MyFormatter::asDate_Read($data->date1) . " - " . MyFormatter::asDate_Read($data->date2);
                                } else {
                                    return MyFormatter::asDate_Read($data->date1);
                                }
                            },
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'detail',
                            'format' => 'raw',
                            'value' => function($data) {
                                return ($data->claim_type == "med" ? "(Medical) - " : "") . $data->detail;
                            },
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'company_name',
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'receipt_no',
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'project_account',
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'authorized_by',
                            'value' => function($data) {
                                return $data->authorizedBy['fullname'];
                            },
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'attribute' => 'amount',
                            'format' => 'raw',
                            'value' => function($data)use(&$total) {
                                $total += $data->amount;
                                return '<p class="p-0 m-0 text-right">' . MyFormatter::asDecimal2($data->amount) . '</p>';
                            },
                            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                            'headerOptions' => ['class' => 'd-none d-md-table-cell']
                        ],
                        [
                            'format' => 'raw',
                            'value' => function($model) {
                                $request = "";
                                $updateLink = "";
                                if ($model->special_approved > 0) {
                                    $request = Html::tag("i", "", ['class' => 'fas fa-star m-1 text-primary']) . Html::tag('span', 'Request Sent', ['style' => 'font-size: small', 'class' => 'text-primary']);
                                } else {
                                    $request = Html::a("<i class='far fa-star'></i>", "/working/claim/get-special-approval?id=$model->claims_detail_id", ['class' => 'm-1', 'title' => "Request for Special approval", 'data-method' => 'post']);
                                    $updateLink = Html::a("<i class='far fa-edit'></i>", '/working/claim/update-claim?id=' . $model->claims_detail_id, ['class' => 'm-1', 'title' => "Update", 'data-method' => 'post']);
                                }
                                $deleteLink = Html::a("<i class='far fa-trash-alt text-danger'></i>", '/working/claim/delete?id=' . $model->claims_detail_id, ['class' => 'm-1', 'data-confirm' => "Are you sure you want to delete this item?", 'data-method' => 'post']);
                                $lostSign = ($model->receipt_lost == 1 ? ' <i class="fas fa-exclamation-triangle fa-sm text-danger"> Receipt Lost</i>' : '');
                                $date = "Date: <b>" . MyFormatter::asDate_Read($model->date1) . ($model->date2 ? " - " . MyFormatter::asDate_Read($model->date2) . " (" . (\common\models\myTools\MyCommonFunction::countDays($model->date1, $model->date2) + 1) . " days)" : "") . "</b>";
                                $file = $model->filename ? Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/working/claim/get-file?filename=" . urlencode($model->filename), ["class" => "m-2", 'target' => "_blank"]) : "";
                                $detail = "<p class='m-0 p-0'>Detail: <b>" . $model->detail . "</b></p>";
                                $comp = $model->company_name ? ("<p class='m-0 p-0'>Company: <b>" . $model->company_name . "</b></p>") : "";
                                $recp = $model->receipt_no ? ("<p class='m-0 p-0'>Receipt No.: <b>" . $model->receipt_no . "</b></p>") : "";
                                $proj = "<p class='m-0 p-0'>Proj/Acc: <b>" . $model->project_account . "</b></p>";
                                $auth = "<p class='m-0 p-0'>Auth By: <b>" . $model->authorizedBy['username'] . "</b></p>";
                                $amt = "<p class='m-0 p-0'>Amt: <b>RM " . MyFormatter::asDecimal2($model->amount) . "</b></p>";
                                return '<p class="m-0 p-0">' .
                                        $updateLink . $deleteLink . $request . $file . $lostSign . '</p>'
                                        . $date . '<br/>'
                                        . $detail . $comp . $recp . $proj . $auth . $amt;
                            },
                            'contentOptions' => ['class' => 'd-md-none'],
                            'headerOptions' => ['class' => 'd-none']
                        ],
//                    'created_at',
                    ],
                ]);
                echo '</div></div></div>';
            }
        }
        ?>

    </div>
</div>

<div class="hidden">
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
                'id' => 'myForm',
                'action' => '/working/claim/submit-claim',
                'method' => 'post'
    ]);
    echo '<input type="text" name="claimIds" id="claimIds"/> ';
    echo '<input type="text" name="claimFamily" id="claimFamily"/>';
    \yii\bootstrap4\ActiveForm::end();
    ?>
</div>
<script>

    function submitClaim(claimName, recordIds, claimFamily) {

        var Ids = $("#" + recordIds).val();

        var answer = confirm("Submit your " + claimName + "?");
        if (answer) {
            $("#claimIds").val(Ids);
            $("#claimFamily").val(claimFamily);
            $("#myForm").submit();
        }


    }

</script>