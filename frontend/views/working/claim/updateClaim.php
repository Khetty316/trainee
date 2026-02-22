<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\claim\ClaimsDetail */

$this->title = 'Update Claims Detail';
$this->params['breadcrumbs'][] = ['label' => 'Personal Claims', 'url' => ['/working/claim/personal-claim']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claims-detail-create">
    <h3><?= Html::encode($this->title) ?></h3>
    <?php
    $form = ActiveForm::begin([
                'id' => 'new_claim_form',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
//                'action' => '/project/newquotation',
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
    ]);
    ?>
    <?= $form->field($model, 'claimant_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'claims_detail_id')->hiddenInput()->label(false) ?>


    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'claim_type')->dropdownList($claimTypeList, ['prompt' => 'Select...', 'id' => 'claimTypeDropdown']) ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'scannedFile')->fileInput()->label('Replace Reference') ?>
            <?= $model->filename == "" ? "" : Html::a('<i class="far fa-file-alt" ></i>', '/working/claim/get-file?filename=' . urlencode($model->filename), ['target' => '_blank', 'title' => "Attached Reference"]) ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-2">
            <?=
                    $form->field($model, 'date1')
                    ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy', 'id' => 'dateFrom'])
                    ->label("Date")
            ?>
        </div>
        <div class="col-sm-12 col-md-2 forTravel">
            <?=
                    $form->field($model, 'date2', ['options' => ['class' => 'required form-group row'], 'errorOptions' => ['class' => 'invalid-feedback-show']])
                    ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy', 'id' => 'dateTo'])
                    ->label("Date To")
            ?>
        </div>
        <div class="col-sm-12 col-md-2 forTravel">
            <?=
                    $form->field($model, 'amtDay', ['options' => ['class' => 'required form-group row'], 'errorOptions' => ['class' => 'invalid-feedback-show']])
                    ->textInput(['type' => 'number', 'style' => 'text-align:right', 'step' => ".01"])
                    ->label("Amount/Day (RM)")
            ?>
        </div>
    </div>

    <div class="form-row notForTravel">
        <div class="col-sm-12 col-md-2 ">
            <?php
            $data1 = \frontend\models\working\claim\ClaimsDetail::find()
                    ->select(['company_name', 'company_name as id', 'company_name as label'])
                    ->where("claimant_id = " . Yii::$app->user->identity->id)
                    ->distinct()
                    ->orderBy(['company_name' => SORT_ASC])
                    ->asArray()
                    ->all();

            echo $form->field($model, "company_name")->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => $data1,
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 500,
                ],
                'options' => ['class' => 'form-control']
            ]);
            ?>
        </div>
        <div class="col-sm-12 col-md-2 ">
            <?= $form->field($model, 'receipt_no')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-row">
        <?php
        foreach ($model['claimsDetailSubs'] as $key => $detailSub) {
            if ($key == 0) {
                ?>
                <div class="col-sm-12 col-md-2">
                    <?php
                    $data = frontend\models\working\project\MasterProjects::find()
                            ->select(['project_code as value', 'project_code as id', 'CONCAT(project_code," - ",project_name) as label'])
                            ->asArray()
                            ->all();
                    echo $form->field($model, "project_account")->widget(yii\jui\AutoComplete::className(), [
                        'clientOptions' => [
                            'source' => $data,
                            'minLength' => '1',
                            'autoFill' => true,
                            'delay' => 500,
                            'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.id : ''));
			     }"),
                        ],
                        'options' => ['class' => 'form-control'],
                    ]);
                    ?>
                </div>
                <div class="col-sm-12 col-md-2">
                    <?php
                    $detailList = \frontend\models\working\claim\ClaimsDetail::find()
                            ->select(['claims_detail_sub.detail', 'claims_detail_sub.detail as id', 'claims_detail_sub.detail as label'])
                            ->join("INNER JOIN", "claims_detail_sub", "claims_detail_sub.claims_detail_id=claims_detail.claims_detail_id")
                            ->where("claimant_id = " . Yii::$app->user->identity->id)
                            ->distinct()
                            ->orderBy(['detail' => SORT_ASC])
                            ->asArray()
                            ->all();

                    echo $form->field($model, "detail")->widget(yii\jui\AutoComplete::className(), [
                        'clientOptions' => [
                            'source' => $detailList,
                            'minLength' => '1',
                            'autoFill' => true,
                            'delay' => 500,
                        ],
                        'options' => ['class' => 'form-control']
                    ]);
                    ?>
                </div>
                <div class="col-sm-12 col-md-2">
                    <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'style' => 'text-align:right', 'step' => ".01", 'value' => $detailSub->amount])->label('Amount (RM)') ?>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="container1">
        <?php
        foreach ($model['claimsDetailSubs'] as $key => $detailSub) {
            if ($key > 0) {
                ?>
                <div class='form-row pb-2'>
                    <div class='col-sm-12 col-md-2'>
                        <input type='text' name='extraProj[]' class='form-control extraProj compulsory' style='display:inline' placeholder='(Extra Project Code)' value='<?= $detailSub->project_account ?>'/>
                        <div class='invalid-feedback'>Project cannot be blank</div></div>
                    <div class='col-sm-12 col-md-2'>
                        <input type='text' name='extraDetail[]' class='form-control extraDetail compulsory' style='display:inline' placeholder='(Extra Detail)' value='<?= $detailSub->detail ?>'/>
                        <div class='invalid-feedback'>Detail cannot be blank</div></div>
                    <div class='col-sm-12 col-md-2'>
                        <input type='number' name='extraAmount[]' class='form-control isAmount compulsory' style='display:inline;text-align:right'  placeholder='(Extra Amount)' value='<?= $detailSub->amount ?>'/>
                        <div class='invalid-feedback'>Amount cannot be blank</div>
                    </div>
                    <i class='far fa-trash-alt delete text-danger pt-2' title='Delete' aria-hidden='true'></i>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="form-row" id="totalAmountDiv" style="display:<?= sizeof($model['claimsDetailSubs']) > 1 ? "" : "none" ?>">
        <div class="col-sm-12 col-md-6">
            <p class="text-right">Total Amount:RM <span id="totalAmount">0.00</span></p>
        </div>
    </div>
    <a class="add_form_field pl-3" href="#"><i class="fas fa-plus-circle text-primary fa-lg" title="Add New Row" aria-hidden="true"></i></a>
    <div class="form-row">
        <div class="col-sm-12 col-md-2">
            <?php
            $data = common\models\User::find()
                    ->select(['fullname as value', 'fullname as label', 'id as id'])
                    ->where("status=10")
                    ->asArray()
                    ->all();
            echo $form->field($model, "tempAuthorizeName")->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => $data,
                    'minLength' => '1',
                    'autoFill' => true,
                    'select' => new \yii\web\JsExpression("function( event, ui ) {
                             $('#claimsdetail-authorized_by').val(ui.item.id); 
			     }"),
                    'search' => new \yii\web\JsExpression("function( event, ui ) { 
			        $('#claimsdetail-authorized_by').val('');
			     }"),
                    'delay' => 1
                ],
                'options' => ['class' => 'form-control', 'value' => $model['authorizedBy']['fullname']]
            ])->label("Authorized By");
            ?>
            <?= $form->field($model, 'authorized_by', ['options' => ['class' => 'hidden']])->textInput(['class' => 'hidden'])->label(false) ?>
        </div>
    </div>


    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, "receipt_lost")->checkbox()->label("Receipt Lost?") ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-8">
            <?= Html::button('Save', ['class' => 'btn btn-success', 'onclick' => 'save()']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>

    $(function () {
        $("#claimTypeDropdown").change(function (e) {
            controlTravelField();
        });

        controlTravelField();

        $("#claimsdetail-date1").change(function (e) {
            countTravelAmount();
        });

        $("#claimsdetail-date2").change(function (e) {
            countTravelAmount();
        });
        $("#claimsdetail-amtday").change(function (e) {
            countTravelAmount();
        });

        $('#claimsdetail-tempauthorizename').blur(function (event, ui) {
            if ($('#claimsdetail-authorized_by').val() == '') {
                $('#claimsdetail-tempauthorizename').val('');
            }
        });


        // functions for multiple projects
        var add_button = $(".add_form_field");

        var max_fields = 10;
        var wrapper = $(".container1");
        var add_button = $(".add_form_field");


        var template = "<div class='form-row pb-2'>"
                + " <div class='col-sm-12 col-md-2'><input type='text' name='extraProj[]' class='form-control extraProj compulsory' style='display:inline' placeholder='(Extra Project Code)'/>"
                + "<div class='invalid-feedback'>Project cannot be blank.</div></div>"
                + "<div class='col-sm-12 col-md-2'><input type='text' name='extraDetail[]' class='form-control extraDetail compulsory' style='display:inline' placeholder='(Extra Detail)'/>"
                + "<div class='invalid-feedback'>Detail cannot be blank.</div></div>"
                + "<div class='col-sm-12 col-md-2'><input type='number' name='extraAmount[]' class='form-control isAmount compulsory' style='display:inline;text-align:right'  placeholder='(Extra Amount)'/>"
                + "<div class='invalid-feedback'>Amount cannot be blank.</div></div>"
                + "<i class='far fa-trash-alt delete text-danger pt-2' title='Delete' aria-hidden='true'></i></div>";


        var x = 1;
        $(add_button).click(function (e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                $(wrapper).append(template); //add input box
            } else {
                alert('You Reached the limits');
            }
            initiateAutocomplete();
            showTotalAmount(x);
        });

        $(wrapper).on("click", ".delete", function (e) {
            e.preventDefault();
            $(this).parent('div').remove();
            x--;
            showTotalAmount(x);
        });

        $(wrapper).on("change", ".isAmount", function (e) {
            calculateTotalAmount();
        });

        $("#claimsdetail-amount").on("change", function (e) {
            calculateTotalAmount();
        });
        calculateTotalAmount();
        initiateAutocomplete();
    });


    function countTravelAmount() {
        var days = checkDate();

        var amtDay = $("#claimsdetail-amtday").val();
        var total = (days * amtDay).toFixed(2);
        if (total == 0) {
            total = "";
        }
        $("#claimsdetail-amount").val(total);

    }

    function controlTravelField() {
        if ($("#claimTypeDropdown").val() === 'tra') {
            $(".forTravel").show();
            $(".notForTravel").hide();
        } else {
            $(".forTravel").hide();
            $(".notForTravel").show();
        }
    }

    function checkDate() {
        var date1 = $("#claimsdetail-date1").val();
        var date2 = $("#claimsdetail-date2").val();

        if (date1 === '' || date2 === '') {
            return 0;
        } else if (date1 > date2) {
            alert("Date to is earlier than date from.");
            return 0;
        }
        var days = countReadDateDays(date1, date2) + 1;
        return days;
    }

    function save(andNext) {
        var allowSubmit = true;
        if ($("#claimTypeDropdown").val() === 'tra') {
            if ($("#claimsdetail-date2").val() === "") {
                alert("Date to cannot be empty");
                $("#claimsdetail-date2").focus();
                return;
            }
            $('#claimsdetail-receipt_no').val('');
            $('#claimsdetail-company_name').val('');
        } else {
            $('#claimsdetail-date2').val('');
            $('#claimsdetail-amtDay').val('');
        }


        $('.compulsory').each(function (idx, elem) {
            if ($(elem).val() === "") {
                $(elem).addClass("is-invalid");
//                $(elem).next(".invalid-feedback").show();
                allowSubmit = false;

            } else {
                $(elem).removeClass("is-invalid");
            }
        });

        if (!allowSubmit) {
            return;
        }

        $('#claimsdetail-saveandnext').val(andNext);
        $('#new_claim_form').submit();

        // Disable submit button after clicked
        $(document).on('beforeSubmit', 'form', function (event) {
            $(".btn-success").attr('disabled', true).addClass('disabled');
        });

    }

    function initiateAutocomplete() {
        $(".extraProj").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "/list/getprojectlist",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            change: function (event, ui) {
                $(this).val((ui.item ? ui.item.id : ""));
            },
            delay: 500

        });

        $(".extraDetail").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "/list/get-claim-detail-list",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            minLength: 1,
            delay: 500

        });
    }

    function showTotalAmount(x) {
        if (x > 1) {
            $("#totalAmountDiv").show();
        } else {
            $("#totalAmountDiv").hide();
        }
    }

    function calculateTotalAmount() {
        var total = 0;
        $('input[name^=extraAmount]').each(function (idx, elem) {
            if ($(elem).val() != "") {
                total += parseFloat($(elem).val());
            }
        });
        total += parseFloat($("#claimsdetail-amount").val() === "" ? 0 : $("#claimsdetail-amount").val());
        $("#totalAmount").html(total.toFixed(2));
    }

</script>
<?php
$this->registerJs(<<<JS



JS
);
?>