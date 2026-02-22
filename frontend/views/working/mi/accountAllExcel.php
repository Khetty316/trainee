<?php

use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\MasterIncomingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$file = "Incoming Document.xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
?>
<style>
    td{
        border: 1px solid black;
    }

</style>
<!--
<script>
    $(document).ready(function () {
        $("#navMasterIncoming").addClass("active");
    });
</script>-->
<div class="master-incomings-index">

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{items}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            [
                'label' => "Index Number",
                'format' => 'raw',
                'value' => function($model) {
                    return $model->index_no;
                }
            ],
            [
                'label' => 'Document Type',
                'value' => function($model) {
                    return $model->doc_type_name;
                }
            ],
            [
                'label' => 'Doc. Date',
                'format' => 'html',
                'value' => function($model) {
                    return $model->doc_due_date == "" ? "" : Yii::$app->formatter->asDatetime($model->doc_due_date, 'php:d/m/Y');
                },
            ],
            [
                'label' => 'Invoice No.',
                'value' => function($model) {
                    return $model->reference_no;
                },
            ],
            [
                'label' => 'Particular',
                'value' => function($model) {
                    return $model->particular;
                },
            ],
            [
                'label' => 'Project Code',
                'format' => 'raw',
                'value' => function($data) {
                    $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                    $str = '';
                    foreach ($projects as $key => $project) {
                        $str .= ($str == '' ? '' : '<br/>') . $project->project_code;
                    }
                    return $str;
                }
            ],
            [
                'label' => 'Amount',
                'format' => 'raw',
                'value' => function($data) {
                    $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                    $str = '';
                    foreach ($projects as $key => $project) {
                        $amt = $project->amount > 0 ? $project->currency->currency_sign . " " . MyFormatter::asDecimal2($project->amount) : ' - ';
                        $str .= ($str == '' ? '' : '<br/>') . $amt;
                    }
                    return '<p class="text-right m-0">' . $str . '</p>';
                }
            ],
            [
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
            ],
            [
                'label' => 'Current Job',
                'value' => function($model) {
                    return $model->task_description;
                },
            ],
            [
                'label' => 'P.O. Number',
                'value' => function($model) {
                    return $model->po_number;
                },
            ],
            [
                'label' => 'Pro Forma?',
                'value' => function ($model) {
                    return $model->isPerforma ? 'Yes' : '';
                }
            ],
            [
                'label' => 'File Type',
                'value' => function ($model) {
                    return $model->file_type_name;
                }
            ],
            [
                'label' => 'Received From',
                'value' => function ($model) {
                    return $model->received_from;
                }
            ],
            [
                'label' => 'Status',
                'value' => function ($data) {
                    return $data->status;
                }
            ],
            [
                'label' => "Remarks",
                'format' => 'raw',
                'value' => function($model) {
                    return nl2br($model->remarks);
                }
            ],
        ],
    ]);
    ?>

</div>
