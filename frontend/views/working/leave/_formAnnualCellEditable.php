<?php

use yii\bootstrap4\ActiveForm;
?>

<div>
    <?php
    $form = ActiveForm::begin([
                'method' => 'post',
                'options' => ['autocomplete' => 'off'],
    ]);
    ?>
    <table border="0">
        <tbody>
            <tr>
                <td>Staff No.</td>
                <td> : <span class="bold"><?= $vEntitlement->staff_id ?></span>
                </td>
            </tr>
            <tr>
                <td>Name</td>
                <td> : <span class="bold"><?= $vEntitlement->fullname ?></span></td>
            </tr>
            <tr>
                <td>Year</td>
                <td> : <span class="bold"><?= $selectYear ?></span>
                </td>
            </tr>
        </tbody>
    </table>

    <div>
        <div class="form-row mt-2">
            <div class="col-sm-12 col-md-12 col-xl-12">
                <?= $form->field($leaveEntitle, 'annual_bring_forward_days')->textInput(['type' => 'number', 'required' => true, 'placeholder' => 'Days'])->label('Days:') ?>
            </div>
        </div>
    </div>

    <div class="modal-footer mt-5">
        <button type="submit" class="btn btn-success" data-confirm="Are you sure to save?" >Save</button>
    </div>
    <?php ActiveForm::end(); ?>

</div>
