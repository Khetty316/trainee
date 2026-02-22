<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

$this->title = $model->project_production_code;
$this->params['breadcrumbs'][] = ['label' => 'B.Q. List (By Projects)', 'url' => ['index-material-bq-by-projects']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-5 order-md-2">
        <?= $this->render("_detailViewProjProdDetail", ['projProdMaster' => $model]) ?>
    </div>
    <div class="col-md-7 order-md-1">
        <?php
        $form = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'on'],
                    'id' => 'myForm'
        ]);
        ?>
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0">Panels:</legend>
            <?php
            $panels = $model->projectProductionPanels;
            if ($panels) {
                array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                ?> 
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th class="tdnowrap text-center">#</th>
                            <th>Panel Code</th>
                            <th>Panel's Name</th>
                            <th class="tdnowrap text-center">Quantity</th>
                            <th class="text-center">Attached File</th>
                        </tr>
                    </thead>
                    <tbody id="itemDisplayTable">
                        <?php
                        foreach ($panels as $key => $panel) {
                            ?>
                            <tr id="tr_<?= $panel->id ?>">
                                <td class="text-right px-2"><?= $key + 1 ?></td>
                                <td><?= Html::a($panel->project_production_panel_code, ['view-material-bq-panel', 'panelId' => $panel->id]) ?></td>
                                <td style=""> 
                                    <?= $panel->panel_description ?>
                                </td>
                                <td class="text-right px-3 tdnowrap">
                                    <?= MyFormatter::asDecimal2($panel->quantity) . " " . $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $panelDesigns = $panel->projectProductionPanelDesigns;
                                    echo '<ul class="list-group">';
                                    foreach ((array) $panelDesigns as $panelDesign) {
                                        echo "<li class='list-group-item p-1'>" .
                                        Html::a($panelDesign->designMaster->filename, ['get-file-by-id', 'id' => $panelDesign->designMaster->id], ['target' => '_blank'])
                                        . (empty($panel->design_completed_at) ? (Html::a('<i class="far fa-trash-alt fa-lg"></i>',
                                                        'javascript:deletePanel(' . $panel->id . ')',
                                                        ['title' => 'Remove', 'class' => 'mx-1 text-danger float-right', 'data-confirm' => "Are you sure to remove?", 'data-method' => 'post'])) : "")
                                        . "</li>";
                                    }
                                    echo "</ul>";
                                    ?>
                                </td>

                            </tr>
                            <?php
                        }
                        ?>        
                    </tbody>
                </table>
                <?php
            } else {
                echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
            }
            ?>
        </fieldset>
        <?php
        ActiveForm::end();
        ?>
    </div>
</div>
<script>
    $(function () {
        $(document).on('beforeSubmit', 'form', function (event) {
            setPositionCookie();
        });

        // check if we should jump to postion.
        getPositionCookie();
    });


    function saveAttachment() {
        if ($("#myForm input:checkbox:checked").length > 0) {
            if (checkAttachments()) {
                $("#myForm").submit();
            }
        } else {
            myAlert("No panel is selected");
            return;
        }
    }


    function checkAttachments() {
        if ($("#projectproductionpaneldesignform-scannedfile")[0].files.length === 0) {
            $(".field-projectproductionpaneldesignform-scannedfile").children('div.invalid-feedback').html("Please upload a file.").show();
            return false;
        } else {
            return true;
        }
    }

    function finalize() {
        if ($("#myForm input:checkbox:checked").length > 0) {
            if (confirm("Finalize the selected panels?")) {
                $("#myForm").attr('action', 'finalize-panels?id=<?= $model->id ?>').submit();
            }
        } else {
            myAlert("No panel is selected");
            return;
        }
    }


</script>