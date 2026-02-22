<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model app\models\working\MasterIncomings */

$this->title = $model->index_no;
$this->params['breadcrumbs'][] = ['label' => 'Document Incoming', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container p-0">
    <div class=" justify-content-center">
        <div class="">
            <!--<h3><?= Html::encode($this->title) ?></h3>-->

            <p>
                <?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])  ?>
                <?php
//               echo Html::a('Delete', ['delete', 'id' => $model->id], [
//                    'class' => 'btn btn-danger',
//                    'data' => [
//                        'confirm' => 'Are you sure you want to delete this item?',
//                        'method' => 'post',
//                    ],
//                ])
                ?>
            </p>

            <?=
            DetailView::widget([
                'model' => $model,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
                'options' => ['class' => 'table table-striped table-bordered detail-view fix-width table-sm'],
                'attributes' => [
                    'index_no',
                    [
                        'attribute' => 'docType.doc_type_name',
                        'label' => "Doc Type"
                    ],
                    [
                        'attribute' => 'doc_due_date',
                        'value' => function($model) {
                            return $model->doc_due_date == "" ? "" : MyFormatter::asDate_Read($model->doc_due_date);
                        },
                        'format' => 'html',
                    ],
                    'reference_no',
                    'particular',
                    [
                        'label' => "Performa?",
                        'value' => function($model) {
                            if ($model->isPerforma == 1) {
                                return "Yes";
                            } else {
                                return "";
                            }
                        }
                    ],
                    [
                        'attribute' => 'fileType.file_type_name',
                        'label' => "File Type"
                    ],
                    'received_from',
                    [
                        'label' => "File",
                        'format' => 'raw',
                        'value' => function($modal) {
                            return Html::a("<i class='far fa-file-alt fa-lg' ></i>",
                                            "/working/mi/get-file?filename=" . urlencode($modal->filename),
                                            ['target' => "_blank"]);
                        }
                    ],
                    [
                        'attribute' => 'project_code',
                        'value' => function($data) {
                            $str = "";
                            foreach ($data->miProjects as $miProjects) {
                                $str .= ($str == "" ? $miProjects->project_code : ", " . $miProjects->project_code);
                            }
                            return $str;
                        }
                    ],
                    [
                        'attribute' => 'po_id',
                        'label' => 'Purchase Order',
                        'format' => 'raw',
                        'value' => function($data) {
                            $po = frontend\models\working\po\PurchaseOrderMaster::findOne($data->po_id);
                            return $po ? (Html::a($po->po_number . "<i class='far fa-file-alt fa-lg' ></i>", "/working/po/get-file?filename=" . urlencode($po->po_upload_file), ['target' => "_blank"])) : " - ";
                        }
                    ],
                    [
                        'attribute' => '',
                        'label' => 'Main Invoice',
                        'format' => 'raw',
                        'value' => function($data) {
                            
                            return $data->finalInvoice ? (Html::a($data->finalInvoice->index_no . " <i class='far fa-file-alt fa-lg' ></i>", "/working/mi/view?id=" . $data->final_invoice, ['target' => "_blank"])) : " - ";
                        }
                    ],
                    [
                        'attribute' => 'requestor.fullname',
                        'label' => 'Requestor',
                        'value' => function($data) {
                            $str = "";
                            foreach ($data->miProjects as $miProjects) {
                                $requestor = \common\models\User::findOne($miProjects->requestor);
                                $str .= ($str == "" ? $requestor->fullname : ", " . $requestor->fullname);
                            }
                            return $str;
                        }
                    ],
                    ['attribute' => 'amount',
                        'value' => function($data) {
                            $str = "";
                            foreach ($data->miProjects as $miProjects) {
                                $amt = $miProjects->amount == "" ? "(NOT SET)" : $miProjects->currency->currency_sign . " " . MyFormatter::asDecimal2($miProjects->amount);
                                $str .= ($str == "" ? $amt : ", " . $amt);
                            }
                            return $str;
                        }
                    ],
                    'grn_no',
                    [
                        "attribute" => "currentStepTask.task_description",
                        "label" => "Current Step"
                    ],
                    [
                        'attribute' => 'miStatus.status',
                        'label' => "Overall Status"
                    ],
                    [
                        'attribute' => 'remarks',
//                        'contentOptions' => ['class'=>'text-wrap']
                        'format' => 'raw',
                        'value' => function($model) {
                            return yii\bootstrap4\Html::tag('span', $model->remarks, ['class' => 'text-wrap']);
                        }
                    ],
                    [
                        'attribute' => 'Record Submitted at',
                        'value' => function($model) {
                            return MyFormatter::asDateTime_Read($model->created_at);
                        }
                    ],
                    [
                        "attribute" => "uploader.fullname",
                        "label" => "Uploaded By"
                    ],
                    [
//                        "attribute" => "uploader.fullname",
                        'label' => 'Audit Trail',
                        'format' => 'raw',
                        'value' => function($modal) {
                            $li = "";
                            $workList = $modal->miWorklists;
                            \yii\helpers\ArrayHelper::multisort($workList, ['created_at']);
                            foreach ($workList as $key => $work) {
                                $content = $work->step . '. ' . $work->task->response_name . ' : '
                                        . ($work->approved_flag == '1' ? 'Approved' : 'Rejected') . ' by ' . $work->responsedBy->fullname
                                        . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($work->created_at) .
                                        ($work->remarks == '' ? '' : '<br/>' . yii\bootstrap4\Html::tag('span', 'Remark: ' . $work->remarks, ['class' => 'text-danger text-wrap', 'style' => 'white-space: pre-wrap']) );
                                $li .= yii\bootstrap4\Html::tag('li', $content, ['class' => 'list-group-item']);
                            }

                            return '<ul class="list-group" >' . $li . '</ul>';
                        }
                    ]
                ],
            ])
            ?>
        </div>

    </div>   
</div>