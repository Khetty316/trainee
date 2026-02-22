<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Leave Approval (Superior)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">

    <h3><?= Html::encode($this->title) ?></h3>


    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <?php
    echo $this->render('__gridviewLeave', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'Tick' => true,]);
    ?>


</div>

<?php
$form = ActiveForm::begin([
    'action' => '/working/leavemgmt/resp-superior-leave-approval',
    'method' => 'post',
    'options' => ['autocomplete' => 'off']
        ]);
$modalFooter = yii\bootstrap4\Html::button('Close', ['data-dismiss' => 'modal', 'class' => 'btn btn-secondary'])
        . yii\bootstrap4\Html::submitButton('Submit', ['class' => 'btn btn-success']);

Modal::begin([
    'id' => 'workingModel',
    'title' => 'Response to leave..',
    'centerVertical' => true,
    'footer' => $modalFooter
]);
?>
<table border="0">
    <tbody>
        <tr><td>Leave Code</td><td> : <span class="bold" id="modal-display5"></span></td></tr>
        <tr><td>Requestor</td><td> : <span class="bold" id="modal-display1"></span></td></tr>
        <tr><td>Leave Type</td><td> : <span class="bold" id="modal-display2"></span></td></tr>
        <tr><td>From</td><td> : <span class="bold" id="modal-display3"></span></td></tr>
        <tr><td>To</td><td> : <span class="bold" id="modal-display4"></span></td></tr>
    </tbody>
</table>
<div class="form-group"><?= Html::hiddenInput('leaveId', '', ['id' => 'leaveId']) ?> </div>
<div class="form-group">
    <?= Html::label('Approval:', 'approval', ['class' => 'col-form-label']) ?>
    <?php
//    if (Yii::$app->user->identity->superior) {
//        echo Html::dropDownList("approval", "1", ["1" => "Approve", "0" => "Reject", "2" => "Delegate to " . Yii::$app->user->identity->superior->fullname], ["class" => "form-control"]);
//    } else {
    echo Html::dropDownList("approval", "1", ["1" => "Approve", "0" => "Reject"], ["class" => "form-control"]);
//    }
    ?>
</div>
<div class="form-group">
    <?= Html::label('Message:', 'remarks', ['class' => 'col-form-label']) ?>
    <?= Html::textarea("remarks", "", ['class' => 'form-control', 'id' => 'remarks']) ?>
</div>

<?php
Modal::end();
ActiveForm::end();
?>
<script>
    $(function () {
        $('#workingModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#modal-display1').text(button.data('requestor'));
            modal.find('#modal-display2').text(button.data('leavetype'));
            modal.find('#modal-display3').text(button.data('from'));
            modal.find('#modal-display4').text(button.data('to'));
            modal.find('#modal-display5').text(button.data('leave_code'));

            modal.find('#leaveId').val(button.data('id'));

        });



    });

</script>