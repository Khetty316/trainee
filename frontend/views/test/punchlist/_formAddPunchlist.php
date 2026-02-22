<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\test\TestMaster;
use frontend\models\projectproduction\RefProjProdTaskErrors;
?>

<div class="add-punchlist">
    <?php
    $form = ActiveForm::begin([
                'options' => ['id' => 'punchlistForm'],
    ]);
    ?>

    <div>
        <?= $form->field($detail, 'test_form_code')->dropdownList($formcodeList); ?>
    </div>
    <div>
        <?= $form->field($detail, 'error_id')->dropdownList(RefProjProdTaskErrors::getDropDownListAll()); ?>
    </div>
    <div>
        <?= $form->field($detail, 'remark')->textarea(['rows' => 3]) ?>
    </div>
    <div>
        <?=
                $form->field($detail, 'rectify_date', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                ->widget(\yii\jui\DatePicker::className(), [
                    'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                    'clientOptions' => [
                        'dateFormat' => 'dd/mm/yy',
                        'showButtonPanel' => true,
                        'closeText' => 'Close',
                        'beforeShow' => new \yii\web\JsExpression('function (input, instance) {
                                                    $(input).datepicker("option", "dateFormat", "dd/mm/yy");
                                                    }'),
                    ],
        ]);
        ?>
    </div>
    <div>
        <?= $form->field($detail, 'verify_by')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="form-group float-right">
        <?php
        if (!$detail->isNewRecord) {
            echo Html::a('Delete &nbsp;<i class="fas fa-trash"></i>', ["delete", 'id' => $detail->id], ['class' => 'btn revert btn-danger mr-2', 'data-confirm' => 'Delete this Punchlist?']);
        }
        $otherForm = $otherForm ?? null;
        if ($otherForm) {
            echo Html::submitButton('Save &nbsp;<i class="fas fa-save"></i>', ['class' => 'btn btn-success mt-3', 'onclick' => 'savePunchlist(); return false;']);
        } else {
            echo Html::submitButton('Save &nbsp;<i class="fas fa-save"></i>', ['class' => 'btn btn-success']);
        }
        ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    function savePunchlist() {
        var masterId = <?php echo $master->id; ?>;
        var formData = new FormData($('#punchlistForm')[0]);
        formData.append('masterId', masterId);

        $.ajax({
            url: '/test/punchlist/save',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                alert('Punchlist saved successfully.');
                $('#myModal').modal('hide');
            },
            error: function () {
                alert('An error occurred while saving punchlist.');
            }
        });
    }
</script>