<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="payroll-records-create">
    <?php
    $form = ActiveForm::begin([
                'id' => 'myForm',
                'method' => 'post',
    ]);
    ?>

    <div class="col-lg-12 col-md-12 col-sm-12 d-flex align-items-center mb-2">
        <h5 for="receiver" class="mb-0 pr-3 text-nowrap">Received By: </h5>
        <div class="w-100">
            <select name="receiver[id]" id="receiver" class="form-control form-control-sm <?= empty($receivers) ? 'is-invalid' : '' ?>">
                <?php if (!empty($receivers)): ?>
                    <?php foreach ($receivers as $key => $receiver): ?>
                        <option value="<?= $receiver['id'] ?>" <?= ($model->received_by == $receiver['id']) ? 'selected' : '' ?>>
                            <?= $receiver['fullname'] ?>
                        </option>                    
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <?php if (empty($receivers)): ?>
                <small class="invalid-feedback" style="font-size: 10pt">No staff available to select.</small>
            <?php endif; ?>
        </div>
    </div>
    <?php
    if (!empty($receivers)) {
        echo Html::submitButton(
                'Save',
                ['class' => 'btn btn-success px-3 float-right mt-2 mr-3']);
        ActiveForm::end();
    }
    ?>
</div>