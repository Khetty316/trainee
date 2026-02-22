<?php

use yii\helpers\Html;
use \yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\working\MasterIncomings */

$this->title = 'Edit : ' . $model->index_no;
$this->params['breadcrumbs'][] = ['label' => 'Super User (Document Incoming)'];
$this->params['breadcrumbs'][] = ['label' => 'All', 'url' => ['/working/mi/super-mi-all']];
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
                        'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-12',
                            'offset' => 'col-sm-offset-4',
                            'wrapper' => 'col-sm-6',
                            'error' => '',
                            'hint' => '',
                        ],
                    ],
                    'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
        ]);

        if (Yii::$app->session->hasFlash("success")) {
            echo Yii::$app->session->getFlash("success");
        }
        ?>

        <?= $form->field($model, 'uploader_id')->hiddenInput(["value" => Yii::$app->user->id])->label(false) ?>
        <div class="form-row">
            <div class="col-md-12 col-lg-3">
                <div class="form-group row">
                    <label class="col-sm-12">Document Type</label>
                    <div class="col-sm-12 font-weight-bold">
                        <?= $model->docType->doc_type_name ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-3">    
                <?= $form->field($model, "file_type_id")->dropDownList($fileTypeList, ['prompt' => 'Select...'])->label("File Type (Original/Copy)") ?>
            </div>
            <div class="col-md-12 col-lg-4 pl-lg-3">
                <div class="form-group row">
                    <div class="form-group row">
                        <label class="col-sm-12">Scanned File</label>
                        <div class="col-sm-12 font-weight-bold">
                            <?php
                            echo Html::a(urlencode($model->filename),
                                    "javascript:",
                                    [
                                        'title' => "Click to view me",
                                        "value" => ("/working/mi/get-file?filename=" . urlencode($model->filename)),
                                        "class" => "modalButtonPdf m-2"
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>      
        </div>
        <?php if ($model['claimsMasters']) { ?>

            <div class="form-row" id='forClaimId'>
                <div class="col-md-12 col-lg-3">
                    <div class="form-group row">
                        <label class="col-sm-12">Claim ID</label>
                        <div class="col-sm-12 font-weight-bold">
                            <?= $model['claimsMasters'][0]['claims_id'] ?>
                            <input type="text" id="masterincomings-claimsid" value="<?= $model['claimsMasters'][0]['claims_master_id'] ?>" class="hidden"/>
                            <?php
                            echo Html::a("Claim Form <i class='far fa-file-pdf'></i>", "#", ['onclick' => 'getClaimForm()', 'class' => 'btn btn-primary']);
                            ?>
                        </div>
                    </div>           
                </div> 
            </div>
        <?php } ?>

        <div class="form-row">
            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, "reference_no")->textInput()->label("Inv. No / Rererence No.") ?>
            </div>
            <div class="col-md-12 col-lg-3">
                <?= $form->field($model, 'doc_due_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'yyyy-MM-dd']) ?>
            </div>

            <div class="col-md-12 col-lg-3 pt-md-4">
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
                    'options' => ['class' => 'form-control', 'value' => $model['po']['po_number']],
                ])->label("Purchase Order No.");
                ?>
                <input type="text" class="form-control" id='po_id' name="po_id" style="display:none"/>
            </div>

        </div>   


        <?php
        // Handling Projects 
        $projects = $model->miProjects;
        ?>
        <div class="form-row">
            <div class="col-md-12 col-lg-9">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Project Code</th>
                        <th>Requestor</th>
                        <th>Amount</th>
                    </tr>
                    <?php
                    foreach ($projects as $key => $miProject) {
                        ?>
                        <tr>
                            <td><?= $miProject->project_code ?></td>
                            <td><?= $miProject->requestor0->fullname ?></td>
                            <td><?= $miProject->currency->currency_sign . ' ' . $miProject->amount ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </div>
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
                    <?= Html::input('text', 'miId', $model->id, ['class' => 'hidden']) ?>
                    <?= Html::a('Submit', "javascript:", ['class' => 'btn btn-success', 'onclick' => "validateInputs()", 'id' => 'submitButton']) ?>
                    <?php //= Html::submitButton('Submit', ['class' => 'btn btn-primary'])            ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>

<script>
    $(function () {

        $("#masterincomings-claimsid").change(function (event, ui) {
            var opt = $(this).find('option:selected');

            $("#masterincomings-amount").val(opt.data("amount"));
            $("#main_requestor").val(opt.data("requestor_id"));
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