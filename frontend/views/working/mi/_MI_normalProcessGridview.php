<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
//use \yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => ['class' => yii\bootstrap4\LinkPager::class],
    'headerRowOptions' => ['class' => 'my-thead'],
    'tableOptions' => ['class' => 'table-hover table table-striped table-bordered'],
//            'options'=>['class'=>'table-responsive grid-view'],
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
    'columns' => [
        $forceClose?[ /* force close*/
            'class' => 'yii\grid\ActionColumn',
            'template' => '{grn}',
            'buttons' => [
                'grn' => function ($url, $model, $key) {
                    return Html::a(
                                    '<i class="fas fa-times fa-lg" aria-hidden="true"></i>',
                                    "#",
                                    [
                                        'title' => 'Force Close',
//                                            'data-pjax' => '0',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#workingModel',
//                                            'data-target' => '#confirm-model',
                                        'data-id' => $model->id,
                                        'data-idxno' => $model->index_no,
                                        'data-doctype' => $model->doc_type_name,
                                        'data-projcode' => $model->project_code,
                                        'data-projectname' => $model->project_name,
                                        'data-currentstep' => $model->current_step
                                    ]
                    );
                },
            ],
        ]:'',
                        NULL,
        [
            'attribute' => 'index_no',
            'label' => "Index Number",
            'format' => 'raw',
            'value' => function($model) {
                $title = "Uploaded By: " . $model->uploader_fullname . "\n" . "At time: " . MyFormatter::asDateTime_Read($model->created_at);
                $title .= "\nRemarks: " . $model->remarks;
                return Html::a($model->index_no . ' <i class="fas fa-info-circle" aria-hidden="true"></i>',
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
            'attribute' => 'task_description',
            'label' => 'Current Job',
            'filter' => $taskList,
        ],
        [
            'attribute' => 'doc_type_name',
            'label' => 'Document Type',
            'filter' => $docTypeList,
        ],
//                [
//                    'attribute' => 'sub_doc_type_name',
//                    'label' => 'Sub Document Type',
//                    'filter' => $subDocTypeList,
//                ],
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
        ['attribute' => "amount", 'value' => function($data) {
                return MyFormatter::asDecimal2($data->amount);
            }],
        ['attribute' => "po_number"],
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
        'project_code',
        ['attribute' => 'requestor_fullname', 'label' => 'Requestor'],
    ],
]);
