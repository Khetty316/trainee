<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\MasterIncomingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Document Incoming';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--
<script>
    $(document).ready(function () {
        $("#navMasterIncoming").addClass("active");
    });
</script>-->
<div class="master-incomings-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('New Doc <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>&nbsp;
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

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
                    $title = "Uploaded By: " . $model->uploader_fullname . "\n" . "At time: " . MyFormatter::asDateTime_Read($model->created_at);
                    $title .= "\nRemarks: " . $model->remarks;
                    return Html::a($model->index_no . ' <i class="fas fa-info-circle" aria-hidden="true"></i>', "view?id=$model->id", ["title" => $title])
                            . Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/working/mi/get-file?filename=" .
                                    urlencode($model->filename), ['target' => "_blank", 'class' => 'm-2', 'title' => "Click to view me"]);
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
                        $str .= ($str == '' ? '' : ',<br/>') . $project['requestor0']['fullname'];
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
            [
                'attribute' => 'remarks',
//                        'contentOptions' => ['class'=>'text-wrap']
                'format' => 'raw',
                'value' => function($model) {
//                    return yii\bootstrap4\Html::tag('span', $model->remarks, ['class' => 'text-wrap']);
                    return  substr($model->remarks, 0,20).(strlen($model->remarks)>20?"...":"");
                }
            ],
        ],
    ]);
    ?>

</div>
