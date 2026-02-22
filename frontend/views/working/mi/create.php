<?php

use yii\helpers\Html;
use \yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\working\MasterIncomings */

$this->title = 'Document Incoming Registration';
$this->params['breadcrumbs'][] = ['label' => 'Document Incoming', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$formCss4 = '{label} <div class="col-sm-12">{input}{error}{hint}</div>';
$formCssCheckbox = '{label} {input}';
?>
<link href="/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="/js/bootstrap4-toggle.min.js"></script>
<style>
    .help-block-error {
        color: red;
    }
</style>
<div class="master-incomings-create">
    <div class="col-11">
        <h3><?= Html::encode($this->title) ?></h3>

        <?php
        $form = ActiveForm::begin([
                    'id' => 'myForm',
                    'layout' => 'horizontal',
                    'fieldConfig' => [
//                    'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>\n\n",
                        'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
//                    'template' => '{label} <div class="col-sm-8">{input}</div>',
//                    'labelOptions' => ['class' => 'col-lg-2 control-label']
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

        if (Yii::$app->session->hasFlash("success")) {
            echo Yii::$app->session->getFlash("success");
        }
        ?>

        <?= $form->field($model, 'uploader_id')->hiddenInput(["value" => Yii::$app->user->id])->label(false) ?>


        <div class="form-row">
            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, 'doc_type_id', ['inputOptions' => ['autofocus' => 'autofocus']])->dropDownList($docTypeList, ['prompt' => 'Select...'])->label("Document Type") ?>
            </div>
            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, "file_type_id")->dropDownList($fileTypeList, ['prompt' => 'Select...'])->label("File Type (Original/Copy)") ?>
            </div>
            <div class="col-md-12 col-lg-4 pl-lg-3">
                <?= $form->field($model, 'scannedFile')->fileInput() ?>
            </div>      
        </div>

        <div class="form-row" id='forClaimId' style='display: none'>
            <div class="col-md-12 col-lg-3">
                <?php
                $claimList = \frontend\models\working\claim\ClaimsMaster::getPendingClaimList_obj();


//                echo "<pre>";
//                \yii\helpers\VarDumper::dump($claimList);
//                echo "</pre>";
//                echo $form->field($model, 'claimsId', ['options' => ['class' => 'form-group row field-masterincomings-claimsid required']])
//                        ->dropdownList(\yii\helpers\ArrayHelper::map($claimList, "claims_master_id", "claims_id"), ['prompt' => 'Select...', 'data-requestor' => 'thank'])->label("Claim ID");
                ?>
                <div class="form-group row field-masterincomings-claimsid required field-masterincomings-claimsid">
                    <label class="col-sm-12" for="masterincomings-claimsid">Claim ID</label> <div class="col-sm-12">
                        <select id="masterincomings-claimsid" class="form-control" name="MasterIncomings[claimsId]" data-requestor="thank">
                            <option value="">Select...</option>
                            <?php
                            foreach ($claimList as $theList) {
                                echo "<option value='$theList->claims_master_id' data-requestor_id='$theList->claimant_id' data-amount='"
                                . common\models\myTools\MyFormatter::asDecimal2NoSeparator($theList->total_amount)
                                . "'>"
                                . $theList->claims_id
                                . "</option>";
                            }
                            ?>
                        </select><div class="invalid-feedback "></div></div>

                </div>
            </div> 
            <div class="col-md-12 col-lg-3">
                <label class="col-md-12 d-none d-md-block" for="">&nbsp;</label>
                <?php
                echo Html::a("Claim Form <i class='far fa-file-pdf'></i>", "#", ['onclick' => 'getClaimForm()', 'class' => 'btn btn-primary']);
                ?>

            </div> 

        </div>

        <div class="form-row">
            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, "reference_no")->textInput()->label("Inv. No / Rererence No.") ?>
            </div>
            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, 'doc_due_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'yyyy-MM-dd']) ?>
            </div>

            <div class="col-md-12 col-lg-3 pt-md-4">

                <!--<label class="custom-control-label" for="masterincomings-isperforma">Is Pro Forma</label>-->
                <?= $form->field($model, "isPerforma")->checkbox() ?>
            </div>
        </div>   

        <div class="form-row">
            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, "received_from")->textInput() ?>
            </div>
            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, "particular")->textInput() ?>
            </div>
            <div class="col-md-12 col-lg-3">
                <?php
                $data = \frontend\models\working\po\PurchaseOrderMaster::find()
                        ->select(['po_number as value', 'po_number as label', 'po_id as id'])
                        ->asArray()
                        ->all();

                echo $form->field($model, "po_id")->widget(yii\jui\AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $data,
                        'minLength' => '1',
                        'autoFill' => true,
                        'select' => new \yii\web\JsExpression("function( event, ui ) { $('#po_id').val(ui.item.id); }"),
                        'search' => new \yii\web\JsExpression("function( event, ui ) { $('#po_id').val(''); }"),
                        'change' => new \yii\web\JsExpression("function( event, ui ) { $(this).val((ui.item ? ui.item.value : '')); }"),
                        'delay' => 50
                    ],
                    'options' => ['class' => 'form-control']
                ])->label("Purchase Order No.");


  
                ?>
                <input type="text" class="form-control" id='po_id' name="po_id" style="display:none"/>
            </div>

        </div>   



        <div class="form-row">
            <div class="col-md-12 col-lg-3">
                <?php
                echo $form->field($model, 'project_code')->widget(\yii\jui\AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => \yii\helpers\Url::to(['/list/getprojectlist']),
                        'minLength' => '1',
                        'autoFill' => true,
                        'select' => new \yii\web\JsExpression("function( event, ui ) { 
			        $('#projCodeTesting').val(ui.item.id);
                                selectRequestor(false,ui.item.pic);
			     }"),
                        'search' => new \yii\web\JsExpression("function( event, ui ) { 
			        $('#projCodeTesting').val('');
			     }"),
                        'delay' => 1
                    ],
                    'options' => [
                        'class' => 'form-control',
                    ]
                ])->label("Project Code " . Html::a('<i class="fas fa-plus" aria-hidden="true"></i>',
                                "#",
                                [
                                    "title" => 'Create New Project',
                                    "value" => \yii\helpers\Url::to('/working/projects/create?getAjax=true'),
                                    "class" => "modalButton"]) . "&nbsp;&nbsp;&nbsp;" .
                        \yii\bootstrap4\Html::input('checkbox', 'hasMultipleProj', null,
                                ['id' => 'hasMultipleProj', 'data-toggle' => 'toggle',
                                    'data-onstyle' => 'success',
                                    'data-on' => 'Multiple',
                                    'data-off' => 'Single',
                                    'data-offstyle' => 'secondary',
                                    'data-size' => 'xs',
                        ])

//                        .'<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-on="Ready" data-off="Not Ready" data-offstyle="secondary">'
                );

                echo yii\bootstrap4\Html::input('text', '', '', ['id' => 'projCodeTesting', 'style' => 'display:none']);
                ?>
            </div>

            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, "requestor_id")->dropDownList($userList, ['prompt' => 'Select...', 'id' => 'main_requestor'])->label('Requestor') ?>
            </div>
            <div class="col-md-12 col-lg-3">

                <?php
                $currencyInput = $form->field($model, "currency")->dropdownList(frontend\models\common\RefCurrencies::getCurrencyActiveDDropdownlist())->label(false);

                echo $form->field($model, 'amount', [
                    'inputTemplate' => '<div class="input-group"><span class="input-group-addon"></span>' . $currencyInput . '{input}</div>',
                ])->textInput(['style' => 'text-align:right', 'type' => 'number']);
                ?>
            </div>


        </div>

        <div class="form-row" id="multiProjDiv" >

            <div class="container1 w-100">

                <div>
                    <input type="text" class="form-control col-3  extraProj" style="display:inline" placeholder="(Extra Project Code)"/>
                    <input type="text" class="form-control col-4 m-1 extraPIC" style="display:inline" placeholder="(Extra Requestor)"/>
                    <input type="number" name="myamount[]" class="form-control col-2 m-1" style="display:inline;text-align:right"  placeholder="(Extra Amount)"/>
                    <div style="display:none"><input tyle="text" name="extraProj[]"/><input tyle="text" name="extraPIC[]"/></div>
                </div>

            </div>
            <!--<button class="add_form_field">Add New Field &nbsp; <span style="font-size:16px; font-weight:bold;">+ </span></button>-->

            <a class="add_form_field pl-3" href="#"><i class="fas fa-plus-circle text-primary fa-lg" title="Add New Row" aria-hidden="true"></i></a>

        </div>

        <div class="form-row">
            <div class="col-sm-12 col-md-8">
                <?= $form->field($model, 'remarks')->textarea(['rows' => '6']) ?>
            </div>
        </div>


        <div class="form-row">
            <div class="col-8">
                <div class="form-group">
                    <div class="pull-right">

                        <?= Html::a('Submit', "javascript:", ['class' => 'btn btn-success', 'onclick' => "validateInputs()", 'id' => 'submitButton']) ?>
                        <?php //= Html::submitButton('Submit', ['class' => 'btn btn-primary'])     ?>
                    </div>
                </div>
            </div>
        </div>



        <?php ActiveForm::end(); ?>

    </div>
</div>

<script>
    $(function () {

        $('#multiProjDiv').hide();

        $("#masterincomings-claimsid").change(function (event, ui) {
            var opt = $(this).find('option:selected');

            $("#masterincomings-amount").val(opt.data("amount"));
            $("#main_requestor").val(opt.data("requestor_id"));
        });

        $('#hasMultipleProj').change(function (event, ui) {
            if ($(this).is(":checked")) {
//                displayMultipleProj();
                $("#multiProjDiv").show("fast", "linear");
            } else {
                $("#multiProjDiv").hide("fast", "linear");
            }
        });


        $('#masterincomings-project_code').blur(function (event, ui) {
            if ($('#projCodeTesting').val() == '') {
                $('#masterincomings-project_code').val('');
            }
        });


        $("#masterincomings-doc_type_id").change(function (event, ui) {
            if ($(this).val() == 1) {
                $("#forClaimId").show();
            } else {
                $("#forClaimId").hide();

            }
        });



        var max_fields = 10;
        var wrapper = $(".container1");
        var add_button = $(".add_form_field");

        var x = 1;
        $(add_button).click(function (e) {
            e.preventDefault();
            if (x < max_fields) {
                x++;
                $(wrapper).append('<div><input type="text" class="form-control col-3 extraProj" style="display:inline" placeholder="(Extra Project Code)"/>'
                        + '<input type="text" class="form-control col-4 m-2 extraPIC" style="display:inline" placeholder="(Extra Requestor)"/>'
                        + '<input type="number" name="myamount[]" class="form-control col-2 m-2" style="display:inline;text-align:right"  placeholder="(Extra Amount)"/><i class="far fa-trash-alt delete text-danger" title="Delete" aria-hidden="true"></i>'
                        + '<div style="display:none"><input tyle="text" name="extraProj[]"/><input tyle="text" name="extraPIC[]"/></div></div>'); //add input box
            } else {
                alert('You Reached the limits');
            }
            initiateAutocomplete();

        });

        $(wrapper).on("click", ".delete", function (e) {
            e.preventDefault();
            $(this).parent('div').remove();
            x--;
        });

    });


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
            select: function (event, ui) {
                $(this).parent().find('input[name ="extraProj[]"]').val(ui.item.id);
                $(this).parent().find('input[name ="extraPIC[]"]').val(ui.item.pic);
                $(this).parent().find('.extraPIC').val(ui.item.pic_fullname);

            },
            search: function (event, ui) {
                $(this).parent().find('input[name ="extraProj[]"]').val('');
            },
            delay: 1

        });

        $(".extraPIC").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "/list/get-user-list",
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
            select: function (event, ui) {
                $(this).parent().find('input[name ="extraPIC[]"]').val(ui.item.id);
            },
            search: function (event, ui) {
                $(this).parent().find('input[name ="extraPIC[]"]').val('');
            },
            delay: 1

        });
        $('.extraProj').blur(function (event, ui) {
            if ($(this).parent().find('input[name ="extraProj[]"]').val() == '') {
                $(this).val('');
            }
        });
        $('.extraPIC').blur(function (event, ui) {
            if ($(this).parent().find('input[name ="extraPIC[]"]').val() == '') {
                $(this).val('');
            }
        });
    }

    function validateInputs() {

        if ($("#masterincomings-doc_type_id").val() == 1 && $("#masterincomings-claimsid").val() == "") {
            $("#masterincomings-claimsid").next(".invalid-feedback").html("Claim ID cannot be blank if it doc type is Claim");
            $("#masterincomings-claimsid").next(".invalid-feedback").show();
            return;
        }



        var err = false;
        if ($("#hasMultipleProj").is(":checked")) {

            $('.extraProj').each(function () {
                if ($(this).val() == "") {
                    err = true;
                    $(this).focus();
                    return false;
                }
            });
            if (!err) {
                $('.extraPIC').each(function () {
                    if ($(this).val() == "") {
                        err = true;
                        $(this).focus();
                        return false;
                    }
                });
            }
            if (err) {
                alert("Extra Project or Requestor cannot be blank");
                return false;
            }
        }
        $("#myForm").submit();
    }


    function selectRequestor(isExtra, requestorId) {
        if (isExtra) {

        } else {
            if ($("#masterincomings-doc_type_id").val() != 1) {
                $("#main_requestor").val(requestorId);
            }
        }
    }

    function getClaimForm() {
        var claimMasterId = $("#masterincomings-claimsid").val();
        if (claimMasterId === "") {
            alert("No claim is selected");
            return;
        }
        window.open("/working/claim/print-claim-form?claimsMasterId=" + claimMasterId, "_blank", "");
    }


</script>

<?php
$this->registerJs(<<<JS
            initiateAutocomplete();
JS
);
?>