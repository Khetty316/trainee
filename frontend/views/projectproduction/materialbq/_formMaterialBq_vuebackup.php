<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\RefProjectItemUnit;
use frontend\models\ProjectProduction\ProjectProductionPanelFabBqItems;

$this->registerJsFile('/js/vue.js', ['position' => $this::POS_HEAD]);

$inputClass = 'form-control';
$unitList = RefProjectItemUnit::getDropDownList();
?>

<div id="myFormDiv">
    <?php
    if (!$model->isNewRecord) {
        echo "<h4> BQ No.: " . Html::encode($model->bq_no) . "</h4>";
    }
    ?>
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    "@submit" => "checkForm",
                    "novalidate" => "true",
                    "test" => "ddd",
                ],
    ]);
    ?>
    <div class="col-md-12 col-lg-8">
        <div class="">
            <?= $form->field($model, 'proj_prod_panel_id')->textInput() ?>
            <?= $form->field($model, 'isSubmission')->textInput(['v-model' => 'isSubmission']) ?>
            <?= Html::textInput('formSubmitCheck', '', ['v-model' => 'formSubmitCheck', 'id' => 'formSubmitCheck']) ?>
        </div>
        <div class="form-row">
            <table class="table table-sm table-borderless">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="col-2">Qty</th>
                        <th class="col-2">Unit</th>
                    </tr>
                </thead>
                <tbody v-for="(value, index) in itemList">
                    <tr>
                        <td>
                            <?php //= Html::textInput('itemId[]', "", ['class' => 'form-control hidden', 'v-model' => "value.id"])    ?>
                            <input type="text" name="itemId[]" class="form-control hidden" v-model="value.id" v-model="testt" />
                            <?= Html::textInput('itemDescription[]', "", ['class' => $inputClass, 'v-model' => "value.item_description"]) ?>
                            <?php //= Html::tag("v-text-field", $content, $options)  ?>
                            <span class="text-danger">{{value.error}}</span>
                        </td>
                        <td><?= Html::textInput('quantity[]', "", ['class' => $inputClass . ' text-right', 'type' => 'number', 'step' => '0.01', 'v-model' => "value.quantity"]) ?></td>
                        <td><?= Html::dropDownList('unitCode[]', "", $unitList, ['class' => $inputClass, 'v-model' => "value.unit_code"]) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <a class='btn btn-success' @click="addList()" >Add <i class="fas fa-plus"></i></a>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Save Only', ['class' => 'btn btn-primary', '@click' => "saveForm"]) ?>
            <?= Html::submitButton('Submit', ['class' => 'btn btn-success', '@click' => "submitForm"]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    var app = new Vue({
        'el': '#myFormDiv',
        'data': {
            checkedNames: [],
            isSubmission: 0,
            formSubmitCheck: 0,
            itemList: [
<?php
$items = $model->projectProductionPanelFabBqItems;

foreach ($items as $item) {
    echo " { id: " . $item->id . ", quantity:" . $item->quantity . ", unit_code:'" . $item->unit_code . "', item_description:'" . $item->item_description . "', error:'' },";
}
?>
            ]
        },
        'methods': {
            addList() {
                this.itemList.push({unit_code: 'pc', quantity: "", item_description: "", error: ""});
                this.itemList.push({unit_code: 'pc', quantity: "", item_description: "", error: ""});
            },
            submitForm() {
                this.isSubmission = 1;
            },
            saveForm() {
                this.isSubmission = 0;
            },
            checkForm: function (e) {
                this.formSubmitCheck = 1;
                this.itemList.forEach((value, index) => {
                    if (value.item_description !== "") {
                        if (value.quantity == 0) {
                            value.error = "Please insert QTY";
                        } else {
                            value.error = "";
                        }
                    }

                    if (value.error.length > 0) {
                        this.formSubmitCheck = 0;
                    }
                });
            }
        }
    });


    $(function () {

        $("form").submit(function (e) {
            alert("HELLO?");
            if ($("#isSubmission").val() == 1) {
                if (confirm("Submit?")) {
                    $("#formSubmitCheck").val('1');
                } else {
                    $("#formSubmitCheck").val('0');
                }
            } else {
                alert("Wrong value");
            }

            e.preventDefault;
            if ($("#formSubmitCheck").val() !== '1') {
                e.preventDefault();
            }
        });
    });
</script>