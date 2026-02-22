<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\RefProjectItemUnit;
use frontend\models\ProjectProduction\ProjectProductionPanelFabBqItems;

//$this->registerJsFile('/js/vue.js', ['position' => $this::POS_HEAD]);

$inputClass = 'form-control';
$unitList = RefProjectItemUnit::getDropDownList();
echo yii\jui\AutoComplete::widget(['options' => ['class' => 'hidden']]);
?>

<div id="myFormDiv">
    <?php
    if (!$model->isNewRecord) {
        echo "<h3> BQ No.: " . Html::encode($model->bq_no) . "</h3>";
    }
    ?>
    <?php
    $form = ActiveForm::begin([
                'id' => 'myForm',
                'options' => [
                    'autocomplete' => 'off'
                ]
    ]);
    ?>
    <div class="col-md-12 col-lg-8">
        <div class="hidden">
            <?= $form->field($model, 'proj_prod_panel_id')->textInput() ?>
            <?= $form->field($model, 'isSubmission')->textInput(['id' => 'isSubmission']) ?>
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
                <tbody id='divItems'>
                    <?php
                    $items = $model->projectProductionPanelFabBqItems;
                    if (!empty($items)) {
                        foreach ($items as $item) {
                            echo $this->render('__ajaxInsertBqItems.php', [
                                'item' => $item,
                                'unitList' => $unitList
                            ]);
                        }
                    } else {
                        echo $this->render('__ajaxInsertBqItems.php', [
                            'item' => new ProjectProductionPanelFabBqItems(),
                            'unitList' => $unitList
                        ]);
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="form-row">
            <div class='col'>
                <a class='btn btn-success' onclick="addRow()" >Add <i class="fas fa-plus"></i></a>
            </div>
            <div class="col text-right">
                <?= Html::a("Save Only", "javascript:saveForm(0)", ['class' => 'btn btn-primary']) ?>
                <?= Html::a("Submit", "javascript:saveForm(1)", ['class' => 'btn btn-success']) ?>
            </div>
        </div>

    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-insert-bq-items']) ?>',
            dataType: 'html'
        }).done(function (response) {
            $("#divItems").append(response);
            $("#divItems").append(response);
            initialAutocomplete();
        });
    }

    function saveForm(isSubmit) {
        $("#isSubmission").val(isSubmit);
        if (isSubmit == 1 && !confirm("Submit B.Q.?")) {
            return false;
        }

        $("#myForm").submit();
    }

    function initialAutocomplete() {
        $(".isItemDesc").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "ajax-get-bq-item-history",
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

    $(function () {
        initialAutocomplete();

        $("#myForm").submit(function (e) {
            let hasError = false;
            let hasItem = false;
            $('#divItems tr').each(function (idx, elem) {
                let desc = $(elem).find(".isItemDesc").val();
                let qty = $(elem).find(".isQty").val();

                if (desc.length > 0) {
                    hasItem = true;
                    if (qty.length == 0) {
                        $(elem).find(".qtyError").show();
                        hasError = true;
                    } else {
                        $(elem).find(".qtyError").hide();
                    }
                }


            });
            if (hasError) {
                e.preventDefault();
            } else if (!hasItem && $("#isSubmission").val() == 1) {
                myAlert("The B.Q. list is blank!");
                e.preventDefault();
            }
        });
    });

</script>