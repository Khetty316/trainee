<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Leave Relief Request';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>
    <span class="text-success">These staff request a relief on their leave day.</span>


    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{process}',
                'buttons' => [
                    'process' => function ($url, $model, $key) {
                        return Html::a(
                                '<i class="fas fa-check fa-lg text-success"></i>',
                                "#",
                                [
                                    'title' => 'Process',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#workingModel',
                                    'data-id' => $model->id,
                                    'data-requestor' => $model->requestor,
                                    'data-leavetype' => $model->leave_type_name,
                                    'data-from' => MyFormatter::asDate_Read($model->start_date) . ' (' . MyFormatter::asDay_Read($model->start_date) . ') ' . $model->start_sec_name,
                                    'data-to' => MyFormatter::asDate_Read($model->end_date) . ' (' . MyFormatter::asDay_Read($model->end_date) . ') ' . $model->end_sec_name,
                                ]
                        );
                    },
                ],
            ],
            'id',
            'leave_code',
            'requestor',
//            ['attribute' => 'leave_type_name', 'label' => 'Leave Type'],
            'reason:ntext',
            [
                'attribute' => 'start_date',
                'label' => 'Date',
                'format' => 'raw',
                'value' => function ($data) {
                    $str = 'From: ' . MyFormatter::asDate_Read($data->start_date) . $data->start_sec_name . ' (' . MyFormatter::asDay_Read($data->start_date) . ') '
                            . '<br/>'
                            . 'To: ' . MyFormatter::asDate_Read($data->end_date) . $data->end_sec_name . ' (' . MyFormatter::asDay_Read($data->end_date) . ') ';
                    return$str;
                }
            ],
            'total_days',
//            [
//                'attribute' => 'support_doc',
//                'label' => 'Attachment',
//                'format' => 'raw',
//                'value' => function ($data) {
//                    return $data->support_doc == '' ? '' : Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
//                            [
//                                'title' => "Click to view me",
//                                "value" => ("/working/leavemgmt/get-file?filename=" . urlencode($data->support_doc)),
//                                "class" => "modalButtonPdf m-2"]);
//                }
//            ],
//            [
//                'label' => 'Superior\'s Response',
//                'format' => 'raw',
//                'value' => function ($data) {
//                    if ($data->superior_id) {
//                        if ($data->sup_response_by) {
//                            $text = ($data->sup_response ? 'Approved by :' : '<span class="text-danger">Rejected</span> by :') . $data->sup_response_by . '.<br/>@ ' . MyFormatter::asDateTime_ReaddmYHi($data->sup_response_at);
//                            $text .= "<br/>Remarks: <p class='text-wrap'>" . $data->sup_remarks . '</p>';
//                            return $text;
//                        } else {
//                            return ' (Pending) ';
//                        }
//                    } else {
//                        return '(No Superior)';
//                    }
//                }
//            ],
//            [
//                'label' => 'HR Dept\'s Response',
//                'format' => 'raw',
//                'value' => function ($data) {
//                    if ($data->hr_response_by) {
//                        $text = ($data->hr_response ? 'Approved by :' : '<span class="text-danger">Rejected</span> by :') . $data->hr_response_by . '.<br/>@ ' . MyFormatter::asDateTime_ReaddmYHi($data->hr_response_at);
//                        $text .= $data->hr_remarks == "" ? "" : "<br/>Remarks: <p class='text-wrap'>" . $data['hr_remarks'] . '</p>';
//                        return $text;
//                    } else {
//                        return ' (Pending) ';
//                    }
//                }
//            ],
        ],
    ]);
    ?>


</div>

<?php
$form = ActiveForm::begin([
    'action' => '/working/leavemgmt/resp-relief-leave-approval',
    'method' => 'post',
//                        'id' => ''
    'options' => ['autocomplete' => 'off']
        ]);
$modalFooter = yii\bootstrap4\Html::button('Close', ['data-dismiss' => 'modal', 'class' => 'btn btn-secondary'])
        . yii\bootstrap4\Html::submitButton('Submit', ['class' => 'btn btn-success']);

Modal::begin([
    'id' => 'workingModel',
    'title' => 'Response to relief request..',
    'centerVertical' => true,
    'footer' => $modalFooter
]);
?>
<table border="0">
    <tbody>
        <tr><td>Requestor</td><td> : <span class="bold" id="modal-display1"></span></td></tr>
        <!--<tr><td>Leave Type</td><td> : <span class="bold" id="modal-display2"></span></td></tr>-->
        <tr><td>From</td><td> : <span class="bold" id="modal-display3"></span></td></tr>
        <tr><td>To</td><td> : <span class="bold" id="modal-display4"></span></td></tr>
    </tbody>
</table>
<?php
echo '<div class="form-group">' . yii\bootstrap4\Html::hiddenInput('leaveId', '', ['id' => 'leaveId']) . '</div>';
//echo '<div class="form-group">' . yii\bootstrap4\Html::input('text', 'leaveId', '',['id' => 'leaveId']) . '</div>';
echo '<div class="form-group">'
 . yii\bootstrap4\Html::label('Consent:', 'approval', ['class' => 'col-form-label'])
 . yii\bootstrap4\Html::dropDownList("approval", "1", ["1" => "ACCEPT", "8" => "DECLINE"], ["class" => "form-control"])
 . '</div>';

echo '<div class="form-group">'
 . yii\bootstrap4\Html::label('Message:', 'remarks', ['class' => 'col-form-label'])
 . yii\bootstrap4\Html::textarea("remarks", "", ['class' => 'form-control', 'id' => 'remarks'])
 . '</div>';
?>

<?php
Modal::end();
ActiveForm::end();
?>
<script>
    $(function () {
        $('#workingModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal        
            var modal = $(this);
            modal.find('#modal-display1').text(button.data('requestor'));
            modal.find('#modal-display2').text(button.data('leavetype'));
            modal.find('#modal-display3').text(button.data('from'));
            modal.find('#modal-display4').text(button.data('to'));

            modal.find('#leaveId').val(button.data('id'));

        });



    });

</script>