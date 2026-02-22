<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use \yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\MasterIncomingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Review';
$this->params['breadcrumbs'][] = ['label' => 'Director - Document Incoming'];
$this->params['breadcrumbs'][] = $this->title;
?>
<!--
<script>
    $(document).ready(function () {
        $("#navMasterIncoming").addClass("active");
    });
</script>-->
<div class="">
    <?= $this->render('__MINavBar', ['title' => $this->title, 'module' => 'mi_director']) ?>
    <p class="d-none d-md-block">
        <?php
        echo Html::a(
                'Approve <i class="fas fa-check"></i>', "#",
                ['class' => 'btn btn-success', 'onclick' => 'bulkProcess("1")']
        )
        ?>
        <?php
        echo Html::a(
                'Reject <i class="fas fa-times"></i>', "#",
                ['class' => 'btn btn-danger', 'onclick' => 'bulkProcess("0")']
        )
        ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]);    ?>


    <div class="d-none d-md-block">
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
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) {
                        return ['value' => $model->id, 'step' => $model->current_step];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{grn}',
                    'buttons' => [
                        'grn' => function ($url, $model, $key) {
                            return Html::a(
                                            '<i class="fas fa-pencil-alt fa-lg text-success"></i>',
                                            "#",
                                            [
                                                'title' => 'Process',
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
                'particular',
                [
                    'attribute' => 'isPerforma',
                    'value' => function ($model) {
                        return $model->isPerforma ? 'Yes' : '';
                    },
                    'label' => 'Pro Forma?'
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
                    'attribute' => 'project_code',
                    'format' => 'raw',
                    'value' => function($data) {
                        $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                        $str = '';
                        foreach ($projects as $key => $project) {
                            $str .= ($str == '' ? '' : ',<br/>') . $project->project_code . " - " . $project->projectCode['project_name'];
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
//                'attribute' => 'requestor_fullname',
                ],
            ],
        ]);
        ?>
    </div>
    <div  class="d-md-none">
        <?php
        echo \yii\bootstrap4\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
        ]);
        ?>
        <table class="table table-striped table-bordered">
            <tbody>
                <?php
                $models = $dataProvider->getModels();
                $i = 0;
                foreach ($models as $key => $model) {
                    $title = "Uploaded By: " . $model->uploader_fullname . "\n" . "At time: " . MyFormatter::asDateTime_Read($model->created_at);
                    $title .= "\nRemarks: " . $model->remarks;



                    $docTypeName = $model->doc_type_name;
                    $amount = 'RM ' . MyFormatter::asDecimal2($model->amount);
                    $project = 'Project: ' . $model->project_code . " - " . $model->project_name;
                    $recDate = "Doc Time: " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);

                    $responseButton = Html::a(
                                    '<i class="fas fa-pencil-alt fa-lg fa-pull-right text-success"></i>',
                                    "#",
                                    [
                                        'title' => 'Process',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#workingModel',
                                        'data-id' => $model->id,
                                        'data-idxno' => $model->index_no,
                                        'data-doctype' => $model->doc_type_name,
                                        'data-projcode' => $model->project_code,
                                        'data-projectname' => $model->project_name,
                                        'data-currentstep' => $model->current_step
                                    ]
                    );

                    echo '<tr><td><b>' . '#' . ++$i . '</b> ' .
                    Html::a($model->index_no . ' <i class="fas fa-info-circle" aria-hidden="true"></i>',
                            "#",
                            [
                                "title" => $title,
                                "value" => \yii\helpers\Url::to('viewonly?id=' . $model->id),
                                "class" => "modalButton"])
                    . Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/working/mi/get-file?filename=" . urlencode($model->filename),
                            [
                                "value" => ("/working/mi/get-file?filename=" . urlencode($model->filename)),
                                "class" => "m-2", 'target' => "_blank"]) . $responseButton . '<br/>'
                    . Html::tag('p', $docTypeName, ['class' => 'p-0 m-0'])
                    . Html::tag('p', $amount, ['class' => 'p-0 m-0'])
                    . Html::tag('p', $project, ['class' => 'p-0 m-0', 'style' => 'word-wrap: break-word'])
                    . Html::tag('p', $recDate, ['class' => 'p-0 m-0'])
                    .
                    '</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <?php
        echo \yii\bootstrap4\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
        ]);
        ?>
    </div>

</div>

<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/mi/directorapprove',
                        'method' => 'post',
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Process..</h5>
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
                    <label for="approval" class="col-form-label">Approval:</label>
                    <?= yii\bootstrap4\Html::dropDownList("approval", "1", ["1" => "APPROVE", "0" => "REJECT"], ["class" => "form-control"]) ?>
                </div>
                <div class="form-group">
                    <label for="remarks" class="col-form-label">Message:</label>
                    <?= yii\bootstrap4\Html::textarea("remarks", "", ['class' => 'form-control', 'id' => 'remarks']) ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>





<?php echo $this->render('_checkboxModal', ['action' => '/working/mi/accreceiveddoc', 'title' => 'Receive Document?']); ?>

<script>


    function bulkProcess(approval) {
        if (approval === "0") {
            $("#checkboxForm").attr('action', '/working/mi/directorbulkreject');
            $("#checkboxWorkingModelLabel").html('<span class="text-danger">Reject Document?</span>');
        } else {
            $("#checkboxForm").attr('action', '/working/mi/directorbulkapprove');
            $("#checkboxWorkingModelLabel").html('Approve Document?');
        }
        checkCheckBoxes();
    }
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