<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\attendance\MonthlyAttendance $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="monthly-attendance-form">

    <?php
    $form = ActiveForm::begin();
    if ($new) {
        ?>
        <div class="row">
            <div class="col-3">
                <?= $form->field($model, 'user_id')->dropDownList($userList, ['prompt' => 'Select staff']) ?>
            </div>
            <div class="col-3">
                <?=
                $form->field($model, 'year')->widget(\yii\widgets\MaskedInput::class, [
                    'mask' => '9999',
                    'options' => ['value' => $year, 'class' => 'form-control']
                ])
                ?>
            </div>
            <div class="col-3">
                <?= $form->field($model, 'month')->dropDownList($monthList, ['prompt' => 'Select month', 'value' => $month]) ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-3"><?= $form->field($model, 'year')->textInput(['readonly' => true]) ?></div>
            <div class="col-3"><?= $form->field($model, 'month')->textInput(['readonly' => true]) ?></div>
        </div>
    <?php }
    ?>

    <div class="row">
        <div class="col-3"><?= $form->field($model, 'perfect')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'total_days')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'total_present')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'workday_present')->textInput() ?></div>
    </div>
    <div class="row">
        <div class="col-3"><?= $form->field($model, 'unpaid_leave_present')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'rest_holiday_present')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'absent')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'leave_taken')->textInput() ?></div>
    </div>
    <div class="row">
        <div class="col-3"><?= $form->field($model, 'late_in')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'early_out')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'miss_punch')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'short')->textInput() ?></div>
    </div>
    <div class="row">
        <div class="col-3"><?= $form->field($model, 'workday')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'workday_ot')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'holiday')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'holiday_ot')->textInput() ?></div>
    </div>
    <div class="row">
        <div class="col-3"><?= $form->field($model, 'restday')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'restday_ot')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'unpaid_leave')->textInput() ?></div>
        <div class="col-3"><?= $form->field($model, 'unpaid_leave_ot')->textInput() ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
