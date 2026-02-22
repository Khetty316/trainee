<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\test\TestFormDimension;
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
    <h5 class="modal-title"><?= $title ?? "" ?></h5>
    <table class="table table-sm table-bordered text-center mt-2">
        <thead>
            <tr>
                <th>Measurement (mm)</th>
                <th>Error tolerance (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= TestFormDimension::MEASUREMENT_A_MIN ?> to <?= TestFormDimension::MEASUREMENT_A_MAX ?></td>
                <td><?= $form->field($model, 'treshold_a', ['options' => ['style' => 'margin: 0px; padding: 0px;']])->input('number', ['step' => 'any', 'class' => 'form-control text-center threshold m-0'])->label(false) ?></td>
            </tr>
            <tr>
                <td><?= TestFormDimension::MEASUREMENT_B_MIN ?> to <?= TestFormDimension::MEASUREMENT_B_MAX ?></td>
                <td><?= $form->field($model, 'treshold_b', ['options' => ['style' => 'margin: 0px; padding: 0px;']])->input('number', ['step' => 'any', 'class' => 'form-control text-center threshold'])->label(false) ?></td>
            </tr>
        </tbody>
    </table>
    <div class="text-success" style="font-size: 9pt">* For measurements from 0 to 999 mm, the default error tolerance is <?= TestFormDimension::THRESHOLD_A ?>&nbsp;%. *</div>
    <div class="text-success" style="font-size: 9pt">* For measurements from 1000 to 8000 mm, the default error tolerance is <?= TestFormDimension::THRESHOLD_B ?>&nbsp;%.*</div>
    <button type="button" class="btn btn-secondary float-right mt-3" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-success float-right submitButton mr-2 mt-3">Update <i class="fas fa-check"></i></button>
<?php ActiveForm::end(); ?>
</div>
