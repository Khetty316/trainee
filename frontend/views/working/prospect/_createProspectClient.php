<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="prospect-client-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <div class="prospect-client-form">

        <?php
        $form = ActiveForm::begin([
                    'action' => '/working/prospect/create-client-ajax',
                    'method' => 'post',
                    'options' => ['autocomplete' => 'off'],
                    'id' => 'createProspectClientForm'
        ]);
        ?>
        <?= $form->field($model, 'prospect_master')->hiddenInput()->label(false) ?>
        <?= Html::hiddenInput('id2', $model->id) ?>
        <?= $form->field($model, 'client_id')->hiddenInput()->label(false) ?>


        <?php //= Html::hiddenInput('id2', $model->id) ?>

        <div class="form-row">
            <div class="col-xs-12 col-md-8">

                <?php
                echo $form->field($model, 'tempCompanyName')->widget(yii\jui\AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $clientList,
                        'minLength' => '1',
                        'autoFill' => true,
                        'delay' => 1,
                        'appendTo' => '#createProspectClientForm',
                        'select' => new \yii\web\JsExpression("function( event, ui ) {
                                     assignValueFromCompany(ui);
			     }"),
                        'search' => new \yii\web\JsExpression("function( event, ui ) { 
			        $('#prospectdetail-client_id').val('');
			     }"),
                        'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.value : ''));
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['client']['company_name'],($model->id?'readonly':'')=>'']
                ])->label("Client Name");
                ?>
            </div>
            <div class="col-xs-12 col-md-4">
                <?php
                echo $form->field($model, 'service')->widget(yii\jui\AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $serviceList,
                        'minLength' => '1',
                        'autoFill' => true,
                        'delay' => 1,
                        'appendTo' => '#createProspectClientForm'
                    ],
                    'options' => ['class' => 'form-control']
                ]);
                ?>
            </div>
        </div>

        <div class="form-row">
            <div class="col-xs-12 col-md-4">
                <?= $form->field($model, 'pic_name')->textInput()->label("Client Person In Charge") ?>
            </div>
            <div class="col-xs-12 col-md-4">
                <?= $form->field($model, 'pic_contact')->textInput()->label("Client P.I.C Contact No.") ?>
            </div>
            <div class="col-xs-12 col-md-4">
                <?= $form->field($model, 'pic_email')->textInput()->label("Client P.I.C Email") ?>
            </div>
        </div>



    </div>
</div>

<div class="form-group">
    <?= Html::button('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success', 'onclick' => 'submitProspectClient()']) ?>
</div>

<?php ActiveForm::end(); ?>

<script>
    function submitProspectClient() {
        var form = $("#createProspectClientForm");
        var data = form.serializeArray();
        var url = form.attr('action');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data
        }).done(function (response) {
            if (response.data.success === true) {
                $('#myModal').modal('toggle');
                reloadClientDiv();
            }
        }).fail(function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
        });
    }


    function assignValueFromCompany(ui) {
        $('#prospectdetail-pic_contact').val(ui.item.contact_number);
        $('#prospectdetail-client_id').val(ui.item.id);
        $('#prospectdetail-pic_name').val(ui.item.contact_person);
        $('#prospectdetail-pic_email').val(ui.item.email);

    }
</script>



