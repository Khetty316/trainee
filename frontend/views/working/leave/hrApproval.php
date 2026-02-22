<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Approval';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '1']) ?>

    <p class="font-weight-lighter text-success">Leave requests which are pending for HR's approval.</p>

    <?php
    echo $this->render('__gridviewLeave', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'HrRes' => false,
        'Super' => false,
        'Direct' => false,
        'Tick' => true,]);
    ?>
</div>

<?php
$form = ActiveForm::begin([
            'action' => '/working/leavemgmt/resp-hr-leave-approval',
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
        <tr><td>Requestor</td><td> : <span class="bold" id="modal-display1"></span></td></tr>
        <tr><td>Leave Type</td><td> : <span class="bold" id="modal-display2"></span></td></tr>
        <tr><td>From</td><td> : <span class="bold" id="modal-display3"></span></td></tr>
        <tr><td>To</td><td> : <span class="bold" id="modal-display4"></span></td></tr>
    </tbody>
</table>
<?php
echo '<div class="form-group">' . yii\bootstrap4\Html::hiddenInput('leaveId', '', ['id' => 'leaveId']) . '</div>';
echo '<div class="form-group">'
 . yii\bootstrap4\Html::label('Approval:', 'approval', ['class' => 'col-form-label'])
 . yii\bootstrap4\Html::dropDownList("approval", "1", ["1" => "APPROVE", "0" => "REJECT"], ["class" => "form-control"])
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
<script                   >
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