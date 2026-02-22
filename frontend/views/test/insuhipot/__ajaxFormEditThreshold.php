<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="edit-threshold">
    <?php
    $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}{input}{error}{hint}\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
                'options' => ['autocomplete' => 'off'],
    ]);
    ?>
    <div class="d-flex mb-0 pb-0">
        <div class="col-7 m-0 pt-2 pl-0 pr-0">
            <h5><?= $title ?? "" ?>&nbsp;(<?= $unit ?>)</h5>
        </div>
        <div class="col-5 m-0 pl-0">
            <?= $form->field($model, $type)->input('number', ['step' => 'any', 'class' => 'form-control border text-left threshold'])->label(false); ?> 
        </div>
    </div>
    <div class="text-success text-right" style="font-size: 9pt">* The default value is <?= $defaultValue ?>&nbsp;<?= $unit ?>. *</div>
    <button type="button" class="btn btn-secondary float-right mt-4" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-success float-right submitButton mr-2 mt-4">Update <i class="fas fa-check"></i></button>
    <?php ActiveForm::end(); ?>
</div>
