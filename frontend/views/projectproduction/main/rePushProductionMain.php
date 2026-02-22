<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectProduction\ProjectProductionMaster */

$this->title = 'Re-Push Project';
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0">Project Detail:</legend>
                    <?php
                    echo $this->render("_viewProjectDetail", [
                        'model' => $model
                    ]);
                    ?>
                </fieldset>
            </div>
            <div class="col-xs-12 col-xl-9">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0">Panels:</legend>
                    <?php
                    $form = ActiveForm::begin([
                                'id' => 'myRepushForm',
                                'options' => ['autocomplete' => 'off'],
                                'method' => 'post',
                                'action' => 'repush-production?id=' . $model->id
                    ]);

                    $panels = $model->revision->projectQPanels;

                    if ($panels) {
                        array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                        ?> 
                        <table class="table table-sm table-striped table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th class="tdnowrap text-center">#</th>
                                    <th>Panel's Type</th>
                                    <th>Panel's Name</th>
                                    <th class="tdnowrap text-center">Quantity</th>
                                    <th class="text-right" >Unit Price (<?= $model->revision->currency->currency_sign ?>)</th>
                                    <th class="tdnowrap text-center align-top">Select<br/>
                                        <?= Html::checkbox("", false, ['id' => 'checkAllControl', 'onclick' => 'checkAllItems(this)']) ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="itemDisplayTable">
                                <?php
                                $key = 1; // Initialize the displayed row number

                                foreach ($panels as $panel) {
                                    $panelCheckboxOptions = [
                                        'value' => $panel->id,
                                        'class' => 'itemToFinalize',
                                    ];

                                    echo '<tr>';
                                    echo '<td class="text-right px-2">' . $key . '</td>';
                                    echo '<td class="col-1 tdnowrap">' . ($panel->panelType->project_type_name ?? ' - ') . '</td>';
                                    echo '<td style="">';
                                    echo $panel->panel_description;
                                    echo '</td>';
                                    echo '<td class="text-right px-3 tdnowrap">' . MyFormatter::asDecimal2($panel->quantity) . " " . $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") . '</td>';
                                    echo '<td class="text-right px-2 tdnowrap">' . MyFormatter::asDecimal2($panel->amount ?? 0) . '</td>';
                                    echo '<td class="text-center">';
                                    // Check if the current panel's id is in the $panelIds array
                                    if (in_array($panel->id, $panelIds)) {
                                        echo Html::checkbox("finalizeBox[]", false, ["style" => "display: none;"]);
                                    } else {
                                        echo Html::checkbox("finalizeBox[]", false, $panelCheckboxOptions);
                                        $hasPendingPanel = true;
                                    }

                                    echo '</td>';
                                    echo '</tr>';

                                    // Increment the row number
                                    $key++;
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    } else {
                        echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
                    }
                    ?> 
                    <?php ?>
                </fieldset>
            </div>
        </div>
        <?php
        echo Html::a('Save', 'javascript:validateAndRepush()',
                ["class" => "btn btn-success mb-2 mt-0"]);
        ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    function checkAllItems(me) {
        let toCheck = $(me).is(':checked');
        $("#myRepushForm").find(".itemToFinalize").each(function (idx, elem) {
            $(elem).prop('checked', toCheck);
        });
    }

    function validateAndRepush() {
        let checkedPanels = [];
        $("#myRepushForm").find(".itemToFinalize:checked").each(function (idx, elem) {
            checkedPanels.push($(elem).val());
        });

        if (checkedPanels.length <= 0) {
            myAlert("No panel is selected");
        } else if (confirm("Push selected panels to production?")) {
            $("#myRepushForm").submit();
        }
    }
</script>
