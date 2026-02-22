<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\project\ProjectMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Account To Issue Invoice (Project)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-master-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <span class="text-success"></span>
    <!--<p>-->
    <?php //= Html::a('Create Project Master', ['create'], ['class' => 'btn btn-success'])   ?>
    <!--</p>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

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
//            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            [
                'attribute' => 'project_id',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'value' => function($model) {
//                    return Html::a($model->project->proj_code, \yii\helpers\Url::to('view?id=' . $model->id));
                    return $model->project->proj_code;
                }
            ],
            [
                'attribute' => 'certified_reference',
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'certified_date',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'format' => 'raw',
                'value' => function($model) {
                    return common\models\myTools\MyFormatter::asDate_Read($model->certified_date);
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'certified_date',
                    'language' => 'en',
                    'dateFormat' => 'dd/MM/yyyy',
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            [
                'attribute' => 'certified_amount',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'raw',
                'value' => function($model) {
                    return common\models\myTools\MyFormatter::asDecimal2($model->certified_amount);
                }
            ],
            [
                'label' => 'Attachment',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model) {
                    if ($model->certified_file) {
                        return Html::a("<i class='far fa-file-alt fa-lg' ></i>", ['get-file-p-claim-main', 'id' => $model->id, 'type' => 'certified'], ['target' => '_blank']);
                    } else {
                        return "-";
                    }
                }
            ],
            [
                'label' => 'Attach Invoice',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model) {
                    return Html::a('<i class="fas fa-plus"></i>',
                                    "javascript:",
                                    [
                                        'class' => 'text-success m-0 p-0',
                                        'title' => 'Add Payment',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#workingModel',
                                        'data-pclaimid' => $model->id,
                                        'data-projcode' => $model->project->proj_code,
                                        'data-certifiedreference' => $model->certified_reference
                    ]);
                }
            ],
        ],
    ]);
    ?>


</div>


<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/project/account-issue-invoice',
                        'method' => 'post',
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
            ]);


            $modelInvoice = new frontend\models\working\project\ProjectProgressClaim();
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Attach Outgoing Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td>Project Code</td><td>:</td>
                            <td>
                                <span class="bold" id="modal-projCode"></span>
                                <input type='text' name='ProjectProgressClaim[id]' id="ProjectProgressClaim-id" value='' class='hidden'/> 
                            </td>
                        </tr>
                        <tr>
                            <td>Certification Reference</td><td>:</td>
                            <td>
                                <span class="bold" id="modal-certifiedReference"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="req">Attachment</td><td>:</td>
                            <td>               
                                <span id="currentCertifiedFile"></span>
                                <div class="custom-file">

                                    <!--<input type="file" class="custom-file-input" id="customFile2" name='ProjectProgressClaim[scannedFile]' required/>-->


                                    <?php
                                    echo $form->field($modelInvoice, 'scannedFile')
                                            ->fileInput(['class' => 'form-control-file is-invalid custom-file-input', 'id' => 'customFile2'])->label(false)
                                    ?>
                                                                        <label class="custom-file-label" for="customFile2" id="customFileLabel2">Choose file</label>

                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="req">Date</td><td>:</td>
                            <td>
                                <?=
                                DatePicker::widget([
                                    'model' => $searchModel,
                                    'name' => 'ProjectProgressClaim[invoice_date]',
                                    'language' => 'en',
                                    'dateFormat' => 'php:d/m/Y',
                                    'options' => ['class' => 'form-control', 'required' => true],
                                    'id' => 'payDate'
                                ])
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success submitBtn" id="submitBtn2">Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>

    $(function () {
        $('#workingModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#ProjectProgressClaim-id').val(button.data('pclaimid'));
            modal.find('#modal-projCode').html(button.data('projcode'));
            modal.find('#modal-certifiedReference').html(button.data('certifiedreference'));
        });

        $('#customFile2').on('change', function () {
            //get the file name
            var fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).parent().next('#customFileLabel2').html(fileName);
        });

    });


</script>