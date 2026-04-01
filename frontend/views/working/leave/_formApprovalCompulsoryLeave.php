<?php

use yii\widgets\DetailView;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

if(MyCommonFunction::checkRoles([AuthItem::ROLE_Director])){
    $label = 'Leave Approval (Director)';
    $url = '/working/leavemgmt/director-compulsory-leave';
}else if(MyCommonFunction::checkRoles([AuthItem::ROLE_SystemAdmin])){
    $label = 'HR - Leave Management';
    $url = '/working/leavemgmt/hr-compulsory-leave';
}

$this->params['breadcrumbs'][] = ['label' => $label];
$this->params['breadcrumbs'][] = ['label' => 'Compulsory Leave', 'url' => $url];
$this->params['breadcrumbs'][] = 'Leave Approval';
?>

<style>
    .scrollable-table {
        max-height: 60vh;
        overflow-y: auto;
    }
</style>

<div class="leave-master-compulsory">
    <h2 class="m-3">Compulsory Leave Approval</h2>
    <div class="row m-1">
        <div class="col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Leave Info</legend>
                <?=
                $this->render('/working/leave/__detailviewCompulsory', [
                    'model' => $model,
                    'all' => false,
                ])
                ?>
                <div>
                    <?php
                    $form = ActiveForm::begin([
                                'id' => 'approval-form',
                    ]);

                    echo $form->field($model, 'status')->dropDownList([
                        '4' => 'Approve',
                        '7' => 'Reject',
                    ]);

                    echo $form->field($model, 'approval_remark')->textarea(['rows' => 4]);

echo Html::submitButton('Submit', ['class' => 'btn btn-success float-right', 'id' => 'submit-btn']);

                    ActiveForm::end();
                    ?> 
                </div>
            </fieldset>
        </div>
        <div class="col-sm-12 col-md-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0">Staff</legend>
                <div class="scrollable-table">
                    <table class="table table-striped table-bordered table-sm">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Staff ID</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
<!--                            <tr>
                                <th colspan="2">Production Staff</th>
                            </tr>
                            <?php// foreach ($prods as $prod): ?>
                                <tr>
                                    <td><?php //= Html::encode($prod['staff_id']) ?></td>
                                    <td><?php //= Html::encode(ucwords(strtolower($prod['fullname']))) ?></td>
                                </tr>
                            <?php //endforeach; ?>
                            <tr>
                                <th colspan="2">Executive Staff</th>
                            </tr>-->
                            <?php //foreach ($execs as $exec): ?>
<!--                                <tr>
                                    <td><?php //= Html::encode($exec['staff_id']) ?></td>
                                    <td><?php //= Html::encode(ucwords(strtolower($exec['fullname']))) ?></td>
                                </tr>
                            <?php // endforeach; ?>
                            <tr>
                                <th colspan="2">Office Staff</th>
                            </tr>-->
                            <?php foreach ($cDetails as $staff): 
                                $staff = common\models\User::findOne($staff['user_id']); ?>
                                <tr>
                                    <td><?= Html::encode($staff->staff_id) ?></td>
                                    <td><?= Html::encode(ucwords(strtolower($staff->fullname))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('#submit-btn').on('click', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    var status = $('#leavecompulsorymaster-status').val();

    if (status == '4') {
        if (!confirm('Are you sure you want to APPROVE this application?')) {
            return false;
        }
    } else if (status == '7') {
        if (!confirm('Are you sure you want to REJECT this application?')) {
            return false;
        }
    }

    $('#approval-form').yiiActiveForm('submitForm');
});
JS;

$this->registerJs($js);
?>