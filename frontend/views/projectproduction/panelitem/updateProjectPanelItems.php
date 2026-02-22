<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\RefProjectItemUnit;

$this->title = $model->project_production_panel_code;
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
$this->params['breadcrumbs'][] = ['label' => $model->projProdMaster->project_production_code, 'url' => ['view-production-main', 'id' => $model->proj_prod_master]];
$this->params['breadcrumbs'][] = $this->title;

//$model = new frontend\models\ProjectProduction\ProjectProductionPanels();
$panelItems = $model->projectProductionPanelItems;
array_multisort(array_column($panelItems, "sort"), SORT_ASC, $panelItems);
echo yii\jui\AutoComplete::widget(['options' => ['class' => 'hidden']]);
?>

<div class="project-qpanels-view">
    <div class="row">
        <h3 class="col-xs-12 col-xl-9"><?= $model->panel_description ?></h3>
    </div>
    <div class="row">
        <div class="col-md-5 order-md-2">
            <?= $this->render("../materialbq/_detailViewProjProdDetail", ['projProdMaster' => $model->projProdMaster, 'panel' => $model]) ?>
        </div>
        <div class="col-md-7 order-md-1"> 
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Items List:</legend>
                <?php
                $form = ActiveForm::begin([
                            'id' => 'myForm',
                            'options' => ['autocomplete' => 'off']
                ]);
                ?>
                <table class="table table-sm table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Description</th>
                            <th class="col-2 text-right">Qty</th>
                            <th class="col-2">Unit</th>
                        </tr>
                    </thead>
                    <tbody id='divItems'>
                        <?php
                        $unitList = RefProjectItemUnit::getDropDownList();
                        $items = $model->projectProductionPanelItems;
                        foreach ($items as $item) {
                            echo $this->render('__ajaxInsertItems.php', [
                                'item' => $item,
                                'unitList' => $unitList
                            ]);
                        }
                        ?>
                    </tbody>
                </table>
                <div class="form-group">
                    <?= Html::a('Add Row <i class="fas fa-plus"></i>', "javascript:", ['class' => 'btn btn-success', 'onclick' => 'addRow()']) ?>
                    <?= Html::submitButton("Save",  ['class' => 'btn btn-success float-right', 'onclick' => 'saveForm(1)']) ?>
                </div>
                <?php
                ActiveForm::end();
                ?>
            </fieldset>
        </div>
    </div>
</div>

<script>
    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-insert-items']) ?>',
            dataType: 'html'
        }).done(function (response) {
            $("#divItems").append(response);
            $("#divItems").append(response);
            initialAutocomplete();
        });
    }

    function initialAutocomplete() {
        $(".isItemDesc").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "ajax-get-item-history",
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