<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

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
    $this->title = "Special Approval";
    $this->params['breadcrumbs'][] = ['label' => 'Director - Claim'];
    $this->params['breadcrumbs'][] = $this->title;
    ?>
    <h3><?= Html::encode($this->title) ?></h3>


    <p class="d-none d-md-block">
        <?php
        echo Html::a(
                'Approve <i class="fas fa-check"></i>', "#",
                ['class' => 'btn btn-success', 'onclick' => 'bulkAction("1")']
        )
        ?>
        <?php
        echo Html::a(
                'Reject <i class="fas fa-times"></i>', "#",
                ['class' => 'btn btn-danger', 'onclick' => 'bulkAction("0")']
        )
        ?>
    </p>
    <div class="d-none d-md-block">
        <?php
        $dataProvider->sort->sortParam = false;

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
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) {
                        return ['value' => $model->claims_detail_id, 'style' => 'text-align:center'];
                    }
                ],
                [
                    'attribute' => 'claimant_id',
                    'value' => function($data) {
                        return $data['claimant']['fullname'];
                    }
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
                    }
                ],
                [
                    'attribute' => 'detail',
                    'format' => 'raw',
                    'value' => function($data) {
                        return ($data->claim_type == "med" ? "(Medical) - " : "") . $data->detail;
                    }
                ],
                [
                    'attribute' => 'company_name',
                ],
                [
                    'attribute' => 'receipt_no',
                ],
                [
                    'attribute' => 'project_account',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'authorized_by',
                    'value' => function($data) {
                        return $data->authorizedBy['fullname'];
                    },
                ],
                [
                    'attribute' => 'amount',
                    'format' => 'raw',
                    'value' => function($data)use(&$total) {
                        return '<p class="p-0 m-0 text-right">' . MyFormatter::asDecimal2($data->amount) . '</p>';
                    }
                ],
                [
                    'attribute' => 'special_request_remark',
                    'format' => 'raw',
                    'value' => function($data) {
                        return '<p class="text-wrap">' . $data->special_request_remark . '</p>';
                    }
                ]
            ],
        ]);
        ?>
    </div>
    <div  class="d-md-none">
        <?php
        echo \yii\bootstrap4\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
        ]);
        echo '<table class="table table-striped table-bordered table-sm"><tbody>';
        $models = $dataProvider->getModels();

        foreach ($models as $key => $model) {

            $approveBtn = Html::a('<i class="fas fa-check fa-lg fa-pull-right p-2" aria-hidden="true"></i>',
                            'javascript:takeAction("' . $model->claims_detail_id . '", 1) ',
                            ['data-confirm' => 'Approve Request?', 'class' => '']
            );
            $rejectBtn = Html::a('<i class="fas fa-times fa-lg fa-pull-right text-danger p-2" aria-hidden="true"></i>',
                            'javascript:takeAction("' . $model->claims_detail_id . '", 0) ',
                            ['data-confirm' => 'Reject Request?']
            );




            $date = "Date: <b>" . MyFormatter::asDate_Read($model->date1) . ($model->date2 ? " - " . MyFormatter::asDate_Read($model->date2) . " (" . (\common\models\myTools\MyCommonFunction::countDays($model->date1, $model->date2) + 1) . " days)" : "") . "</b>";
            $file = $model->filename ? Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/working/claim/get-file?filename=" . urlencode($model->filename), ["class" => "m-2", 'target' => "_blank"]) : "";
            $claimamt = $model['claimant']['fullname'];
            $detail = "Detail: <b>" . $model->detail . "</b></br>";
            $proj = "Proj/Acc: <b>" . $model->project_account . "</b></br>";
            $auth = $model->authorizedBy['username'] ? "Auth By: <b>" . $model->authorizedBy['username'] . "</b></br>" : "";
            $amt = "Amt: <b>RM " . MyFormatter::asDecimal2($model->amount) . "</b></br>";
            echo '<tr><td><b>' . $claimamt . '</b>' . $file . $rejectBtn . ' ' . $approveBtn . '<br/>'
            . $date . '<br/>' . $detail . $proj . $auth . $amt . "Reason: <pre style='white-space:pre'><b>" . $model->special_request_remark . '</b></pre></td></tr>';
        }
        echo '</tbody></table>';
        echo \yii\bootstrap4\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
        ]);
        ?>
    </div>
</div>


<div class="hidden">
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
                'id' => 'myForm',
                'action' => '/working/claim/director-special-approval',
                'method' => 'post'
    ]);
    echo '<input type="text" name="claimIds" id="claimIds"/> ';
    echo '<input type="text" name="approval" id="approval"/> ';
    \yii\bootstrap4\ActiveForm::end();
    ?>
</div>
<script>

    function bulkAction(approval) {
        var checkedList = [];
        $('tbody input:checked').each(function () {
            checkedList.push(this.value);
        });
        if (checkedList.length === 0) {
            alert("No record selected");
            return;
        }
        $("#claimIds").val(checkedList);
        $("#approval").val(approval);
        var ans = "";
        if (approval === '1') {
            ans = confirm("Approve request?");
        } else {
            ans = confirm("Reject request?");
        }

        if (ans) {
            $("#myForm").submit();
        }
    }


    function takeAction(claimId, approval) {


        $("#claimIds").val(claimId);
        $("#approval").val(approval);

        $("#myForm").submit();
    }

</script>