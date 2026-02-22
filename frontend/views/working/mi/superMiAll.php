<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\MasterIncomingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


//$this->title = 'All Incoming Document';
//$this->params['breadcrumbs'][] = ['label' => 'Account - Document Incoming'];
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="master-incomings-index">
    <?php
    echo $this->render('__MINavBar', ['module' => 'mi_super', 'pageKey' => '1']);
    $this->params['breadcrumbs'][] = $this->title;
    ?>
    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>

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
                'attribute' => 'index_no',
                'label' => "Index Number",
                'format' => 'raw',
                'value' => function($model) {
                    $editLink = Html::a("<i class='far fa-edit'></i>", ['/working/mi/super-mi-edit', 'miId' => $model->id], ['class' => 'text-success', 'title' => "Edit"]);
                    $title = "Uploaded By: " . $model->uploader_fullname . "\n" . "At time: " . MyFormatter::asDateTime_Read($model->created_at);
                    $title .= "\nRemarks: " . $model->remarks;
                    return $editLink . " " . Html::a($model->index_no . ' <i class="fas fa-info-circle"></i>',
                                    "#",
                                    [
                                        "title" => $title,
                                        "value" => \yii\helpers\Url::to('viewonly?id=' . $model->id),
                                        "class" => "modalButton"])
                            . Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
                                    [
                                        'title' => "Click to view me",
                                        "value" => ("/working/mi/get-file?filename=" . urlencode($model->filename)),
                                        "class" => "modalButtonPdf m-2"
                                        ]);
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
            [
                'attribute' => 'reference_no',
                'label' => 'Invoice No.'
            ],
            'particular',
//            ['attribute' => "amount", 'value' => function($data) {
//                    return MyFormatter::asDecimal2($data->amount);
//                }],
//            ['attribute' => "grn_no"],
            [
                'attribute' => 'project_code',
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
            ],
            [
                'attribute' => 'task_description',
                'label' => 'Current Job',
                'filter' => $taskList,
            ],
            ['attribute' => "po_number"],
            [
                'attribute' => 'isPerforma',
                'value' => function ($model) {
                    return $model->isPerforma ? 'Yes' : '';
                },
                'filter' => ['1' => "Yes", "0" => "No"],
                'label' => 'Pro Forma?'
            ],
            [
                'attribute' => 'file_type_name',
                'label' => 'File Type',
                'filter' => $fileTypeList,
            ],
            'received_from',
            [
                'attribute' => 'mi_status',
                'label' => 'Status',
                'filter' => $statusList,
                'value' => function ($data) {
                    return $data->status;
                }
            ],
        ],
    ]);
    ?>

</div>
<script>
    function exportToExcel() {
        window.open('/working/mi/accountalldoc-excel' + window.location.search, '_blank');
    }
</script>