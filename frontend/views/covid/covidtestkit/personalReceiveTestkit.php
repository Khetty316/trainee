<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\covid\testkit\CovidTestkitInventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test-Kit Record of ' . Yii::$app->user->identity->fullname;

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="covid-testkit-inventory-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Test-Kit under transfer</legend>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'headerRowOptions' => ['class' => 'my-thead'],
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
//        'filterModel' => $searchModel,
            'columns' => [
                'brand',
                [
                    'attribute' => 'record_date',
                    'format' => 'raw',
                    'value' => function($model) {
                        return \common\models\myTools\MyFormatter::asDate_Read($model->record_date);
                    }
                ],
                [
                    'attribute' => 'total_movement',
                    'label' => 'Total',
                    'format' => 'raw',
                    'value' => function($model) {
                        return "<p class='text-right m-0'>" . ( $model->total_movement * -1) . "</p>";
                    }
                ],
                [
                    'attribute' => 'confirm_status',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->confirm_status ? "Yes" : "<span class='text-red'>No</span>";
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{Receive}',
                    'buttons' => [
                        'Receive' => function ($url, $model, $key) {
                            $url = '/covidtestkit/receive-testkit?invId=' . $model->id;
                            return $model->confirm_status ? "" : (Html::a('<i class="fas fa-download"></i>', \yii\helpers\Url::to($url), ['data-method' => 'POST', 'data-confirm' => 'Receive the test-kit?']));
                        },
                    ],
                ],
            ],
        ]);
        ?>
    </fieldset>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Received Test-kit</legend>
        <p class="text-red font-weight-bolder" >You should update your own test result in <?= Html::a('>>HEALTH DECLARATION FORM<<', '/covidform/create-covidform-personal' ) ?> !</p>
        <span class="text-red font-weight-lighter" >If the test-kit is for someone other than yourself, please update their test result here</span>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider2,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'headerRowOptions' => ['class' => 'my-thead'],
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'filterModel' => $searchModel2,
            'columns' => [
                'brand',
                [
                    'attribute' => 'record_date',
                    'label' => 'Date Receive',
                    'format' => 'raw',
                    'value' => function($model) {
                        return MyFormatter::asDate_Read($model->inventory->record_date);
                    }
                ],
                [
                    'attribute' => 'complete_status',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->complete_status ? "Yes" : "<span class='text-red'>No</span>";
                    }
                ],
                [
                    'attribute' => 'result_attachment',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($model) {
//                            return ;
                        if ($model->result_attachment) {
                            return Html::a(" <i class='far fa-file-alt m-1' ></i>", "/covidform/get-file?filename=" .
                                            urlencode($model->result_attachment), ['target' => "_blank", 'class' => 'mr-2']);
                        }
                    }
                ],
                [
                    'attribute' => 'remark',
                    'format' => 'raw',
                    'value' => function($model) {
                        return nl2br(Html::encode($model->remark)); //           yii\i18n\Formatter::asNtext($model->remark);
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{Update}',
                    'buttons' => [
                        'Update' => function ($url, $model, $key) {
                            if ($model->complete_status) {
                                return '';
                            } else {
                                return Html::a(
                                                '<i class="fas fa-pencil-alt fa-lg text-success"></i>',
                                                "#",
                                                [
                                                    'title' => 'Update',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#workingModal',
                                                    'data-receivedate' => MyFormatter::asDate_Read($model->inventory->record_date),
                                                    'data-brand' => $model->brand,
                                                    'data-testkitid' => $model->id,
                                                ]
                                );
                            }
                        },
                    ],
                ],
            ],
        ]);
        ?>
    </fieldset>

</div>


<div class="modal fade" id="workingModal" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $record = new frontend\models\covid\testkit\CovidTestkitRecord();
            $form = ActiveForm::begin([
                        'action' => '/covidtestkit/personal-update',
                        'method' => 'post',
//                        'id' => 'project-form',
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Update Test-kit Result</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0">
                    <tbody>
                        <tr>
                            <td>Brand</td>
                            <td> : <span class="bold" id="modal-brand"></span></td>
                        </tr>
                        <tr>
                            <td>Date Received</td>
                            <td> : <span class="bold" id="modal-receivedate"></span></td>
                        </tr>

                    </tbody>
                </table>


                <div class="form-group">
                    <input type="text" style="display:none" class="form-control" id="modal-testkitid" name="testkitid">
                </div>
                <div class="form-group">
                    <?php
//                    echo Html::fileInput('scannedFile', '');
                    echo $form->field($record, 'scannedFile')->fileInput()->label(false);
                    ?>
                </div>
                <div class="form-group">
                    <label for="remarks" class="col-form-label">Remarks:</label>
                    <?= Html::textarea("remark", "", ['class' => 'form-control', 'required' => 'required', 'id' => 'remarks', 'rows' => 6]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" >Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    $(function () {

        $('#workingModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#modal-receivedate').text(button.data('receivedate'));
            modal.find('#modal-brand').text(button.data('brand'));
            modal.find('#modal-testkitid').val(button.data('testkitid'));


            // Commented on 20210629, some of the invoice doesn't have P.O.
//            var docTypeId = button.data('doctypeid');
//            if (docTypeId == 2 || docTypeId == 4) {
//                $('#poAutocomplete').prop('required', true);
//                $('#label_po').addClass('req');
//            } else {
//                $('#poAutocomplete').prop('required', false);
//                $('#label_po').removeClass('req');
//            }
        });

    });

</script>