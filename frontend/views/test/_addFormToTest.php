<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
?>

<div class="add-form-to-test">
    <?php
    if (empty($choices)) {
        echo "All Form Added!";
    } else {
        $form = ActiveForm::begin();
        foreach ($choices as $choice) {
            echo '<div class="vmiddle mb-2">';
            echo '<label class="big-checkbox mb-0">';
            echo Html::checkbox('TestMaster[testPlan][]', false, ['value' => $choice['formclass'] . '|' . $choice['code']]);
            echo '</label>';
            echo '<span class="pl-2 vmiddle text-center">' . $choice['formname'] . '</span>';
            echo '</div>';
        }
        echo '<div class="form-group col text-right pr-0">';
        echo Html::submitButton('Save', ['class' => 'btn btn-success']);
        echo '</div>';
        ActiveForm::end();
    }
    ?>



</div>