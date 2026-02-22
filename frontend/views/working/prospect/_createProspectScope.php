<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="prospect-scope-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <div class="prospect-scope-form">

        <?php
        $form = ActiveForm::begin([
                    'action' => '/working/prospect/create-scope-ajax',
                    'method' => 'post',
                    'options' => ['autocomplete' => 'off'],
                    'id' => 'createProspectScopeForm'
        ]);
        ?>
        <?= $form->field($model, 'master_prospect')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= Html::hiddenInput('id2', $model->id) ?>

        <div class="form-row">
            <div class="col-12">
                <?php //= $form->field($model, 'scope')->textInput() ?>
                <?php
                echo $form->field($model, 'scope')->widget(yii\jui\AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $scopeList,
                        'minLength' => '1',
                        'autoFill' => true,
                        'delay' => 1,
                        'appendTo' => '#createProspectScopeForm'
                    ],
                    'options' => ['class' => 'form-control', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off','id'=>'scope']
                ]);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-12">
                <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => .01, 'id' => 'amt']) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-12">
                <?= $form->field($model, 'scannedFile')->fileInput() ?>
            </div>
        </div>

    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success', 'onclick' => 'submit()','id'=>'submitButton']) ?>
</div>

<?php ActiveForm::end(); ?>

<script>

    $(function () {

        $('form').on('beforeSubmit', function (e) {
            $('#submitButton').attr('disabled', true).addClass('disabled');
            $('#submitButton').html('Submitting...');
            return true;
        });

    });
    function submit() {
        var form = $("#createProspectScopeForm");
        var url = form.attr('action');

        var formData = new FormData(document.getElementById('createProspectScopeForm'));

       if($("#scope").val()=="" || $("#amt").val()==""){
           alert("Please complete the entry.");
           return false;
       }
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.data.success === true) {
                    $('#myModalSmall').modal('toggle');
                    reloadScopesDiv();
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });

    }
</script>



