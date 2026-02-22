<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>

<div class="task-assignment-form">
    <?php
    $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]);
    ?>
    <div class="row" style="height: 100%">
        <div class="col-12">
            <div class="form-row">
                <div class="col-12">
                    <?= $form->field($model, 'revert_comment', ['options' => ['class' => 'form-group required']])->label("Comment")->textarea(['rows' => 4, 'required' => true]) ?>
                </div>
            </div>
            <?php// if ($limit != 0) { ?>
<!--                <div class="form-row">
                    <div class="col-0">
                        <label for="revertComplete-counter">Revert Completed Amount: <span class="text-success"><?php//= $limit ?? 0 ?></span>/<?php//= $model->quantity ?></label>
                    </div>
                </div>-->
<!--                <div class="form-row" id="addComplete-counter">
                    <div class="col-6">
                        <?php //= $form->field($model, 'addComplete')->label(false)->textInput(['type' => 'number', 'id' => 'counter-input', 'value' => $limit, 'min' => 1, 'max' => $limit]) ?>
                    </div>
                    <div class="col-0">
                        <button type="button" class="btn btn-warning" onmousedown="startDecrement()" onmouseup="stopChanging()" onmouseleave="stopChanging()" ontouchstart="startDecrement()" ontouchend="stopChanging()">-1</button>
                    </div>
                    <div class="col-0">
                        <button type="button" class="btn btn-warning" onmousedown="startIncrement()" onmouseup="stopChanging()" onmouseleave="stopChanging()" ontouchstart="startIncrement()" ontouchend="stopChanging()">+1</button>
                    </div>
                </div>-->
            <?php// } ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton("Revert Completed Panel", ['class' => 'btn btn-success float-right m-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!--<script>
    var counterInput = document.getElementById('counter-input');
    var limit = <?php//= $limit ?>; // Set the upper limit here
    var interval;

    function startDecrement() {
        interval = setInterval(function () {
            var currentValue = parseInt(counterInput.value) || 0;
            if (currentValue > 0) {
                counterInput.value = currentValue - 1;
            }
        }, 70);
    }

    function startIncrement() {
        interval = setInterval(function () {
            var currentValue = parseInt(counterInput.value) || 0;
            if (currentValue < limit) {
                counterInput.value = currentValue + 1;
            }
        }, 70);
    }

    function stopChanging() {
        clearInterval(interval);
    }

</script>-->

