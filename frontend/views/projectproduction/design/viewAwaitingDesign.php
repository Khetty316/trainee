<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectProduction\ProjectProductionMaster */

$this->title = $model->project_production_code;
$this->params['breadcrumbs'][] = ['label' => 'Design - Project List', 'url' => ['index-awaiting-design']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div>

    <h3><?= Html::encode($model->project_production_code . " - " . $model->name) ?></h3>
    <?php
    $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'on'],
                'id' => 'myForm'
    ]);

    echo DetailView::widget([
        'model' => $model,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'template' => "<tr><th style='width: 20%;'>{label}</th><td>{value}</td></tr>",
        'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
        'attributes' => [
            'remark:ntext',
        ],
    ])
    ?>
    <div class="row">
        <div class="col-xs-12 col-md-8">
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
                                <th class="text-center tdnowrap">Select</th>
                            </tr>
                        </thead>
                        <tbody id="itemDisplayTable">
                            <?php
                            foreach ($panels as $key => $panel) {
                                ?>
                                <tr id="tr_<?= $panel->id ?>">
                                    <td class="text-right px-2"><?= $key + 1 ?></td>
                                    <td><?= $panel->project_production_panel_code ?></td>
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
                                            echo "<li class='list-group-item p-1' id='design_" . $panelDesign->id . "'>" .
                                            Html::a($panelDesign->designMaster->filename, ['get-file-by-id', 'id' => $panelDesign->designMaster->id], ['target' => '_blank'])
                                            . (empty($panel->design_completed_at) ? (Html::a('<i class="far fa-trash-alt fa-lg"></i>',
                                                            'javascript:deleteDesign(' . $panelDesign->id . ')',
                                                            ['title' => 'Remove', 'class' => 'mx-1 text-danger float-right'])) : "")
                                            . "</li>";
                                        }
                                        echo "</ul>";
                                        ?>
                                    </td>
                                    <td class="text-center tdnowrap">
                                        <?php
                                        if (empty($panel->design_completed_at)) {
                                            echo Html::checkbox('selectedPanelIds[]', false, ['value' => $panel->id]);
                                        } else {
                                            echo "Completed.<br/>";
                                            echo $panel->designCompletedBy->fullname ?? NULL;
                                            echo "<br/>";
                                            echo MyFormatter::asDateTime_ReaddmYHi($panel->design_completed_at);
                                        }
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
        </div>
        <div class="col-xs-12 col-md-4">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Design:</legend>
                <?php
//                $design->scannedFile = "test";
                echo $form->field($design, 'scannedFile[]')->fileInput(['multiple' => true]);
//                echo $form->field($design, 'remarks')->textarea(['rows' => 6]);
                ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col m-0 p-0">
                            <?php
                            echo Html::a(
                                    'Attach File(s) <i class="far fa-save"></i>',
                                    "javascript:saveAttachment()",
                                    ["class" => "btn btn-success m-0 submitButton"]
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Finalize Design:</legend>
                <?php
                echo Html::a(
                        'Finalize <i class="fas fa-check"></i>',
                        "javascript:finalize()",
                        ["class" => "btn btn-success m-0 submitButton"]
                );
                ?>
            </fieldset>

        </div>
    </div>
    <?php
    ActiveForm::end();
    ?>

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

    function deleteDesign(designId) {
        let ans = confirm('Are you sure to remove?');

        if (ans) {


            $.ajax({
                type: "POST",
                url: "ajax-delete-design",
                dataType: "json",
                data: {
                    id: designId
                },
                success: function (data) {
//                    alert(data.success);
                    if (data.success) {
                        $("#design_" + designId).hide();
                    }else{
                        alert("Unable to remove. Kindly seek help from IT Department.");
                    }
                }
            });
        }
    }


</script>