<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use \yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\MasterIncomingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Active Records';
$this->params['breadcrumbs'][] = ['label' => 'Admin - Document Incoming'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="">
    <?= $this->render('__MINavBar', ['title' => $this->title, 'module' => 'mi_admin']) ?>
    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]);    ?>


    <div class="tab-content p-1">
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
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{grn}',
//                'template' => '{download} {view} {update} {delete}',
                    'buttons' => [
                        'grn' => function ($url, $model, $key) {
                            return Html::a(
                                            '<i class="fas fa-times fa-lg text-danger" aria-hidden="true"></i>',
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
                ],
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
                    'attribute' => 'project_code',
                    'format' => 'raw',
                    'value' => function($data) {
                        $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                        $str = '';
                        foreach ($projects as $key => $project) {
                            $str .= ($str == '' ? '' : ',<br/>') . $project->project_code;
                        }
                        return $str;
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
                ]
            ],
        ]);
        ?>
    </div>
</div>

<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!--<form method="post" action="/working/mi/insertgrn">-->


            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/mi/forceclose',
                        'method' => 'post',
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="workingModalLabel">Force Close</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0">
                    <tbody>
                        <tr>
                            <td>Index No</td>
                            <td> : <span class="bold" id="modal-idxno"></span></td>
                        </tr>
                        <tr>
                            <td>Doc Type</td>
                            <td> : <span class="bold" id="modal-doctype"></span></td>
                        </tr>
                        <tr>
                            <td>Project</td>
                            <td> : <span class="bold" id="modal-project"></span></td>
                        </tr>
                    </tbody>
                </table>


                <div class="form-group">
                    <input type="text" style="display:none" class="form-control" id="mi_id" name="mi_id">
                    <input type="text" style="display:none" class="form-control" id="currentstep" name="currentstep">
                </div>
                <div class="form-group">
                    <label for="remarks" class="col-form-label">Message: <span class="text-danger">*</span></label>
                    <?= yii\bootstrap4\Html::textarea("remarks", "", ['class' => 'form-control', 'id' => 'remarks', 'required' => 'required']) ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger">Force Close</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#workingModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal        
            var modal = $(this);
            modal.find('#modal-idxno').text(button.data('idxno'));
            modal.find('#modal-doctype').text(button.data('doctype'));
            modal.find('#modal-project').text(button.data('projcode') + " - " + button.data('projectname'));

//            modal.find('.modal-body input').val(recipient);
            modal.find('.modal-body #mi_id').val(button.data('id'));
            modal.find('.modal-body #currentstep').val(button.data('currentstep'));

        });
    });

</script>