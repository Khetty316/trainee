<?php

use yii\helpers\Html;

use yii\bootstrap4\ActiveForm;


$this->title = $model->project_production_code;
$this->params['breadcrumbs'][] = ['label' => 'Fabrication Progress - Project', 'url' => ['index-fabrication-project-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3>
    <?= Html::encode($model->name) ?>
    <?=
    Html::a('<i class="fas fa-info-circle"></i>',
            'javascript:',
            ['value' => '/production/production/ajax-view-project-detail?id=' . $model->id,
                'class' => 'modalButton',
                'title' => "Project Detail"])
    ?>
</h3>
<div class="row">
    <div class="col-12">
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2  m-0 ">Panels:</legend>
            <?php
            $formFinalize = ActiveForm::begin([
                        'id' => 'myForm',
                        'options' => ['autocomplete' => 'off'],
                        'method' => 'post',
                        'action' => 'start-fabrication-process?projId=' . $model->id
            ]);

            $panels = $model->projectProductionPanels;
            if ($panels) {
//                array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                ?> 
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead-light">
                        <tr class="">
                            <th class="tdnowrap text-center align-top">#</th>
                            <th class="align-top">Panel's Code</th>
                            <!--<th class="align-top">Panel's Name</th>-->
                            <th class="tdnowrap text-center align-top">Quantity</th>
                            <th class="col-1 text-right align-top">Cutting & Punching</th>
                            <th class="col-1 text-right align-top">Bending</th>
                            <th class="col-1 text-right align-top">Welding & Grinding</th>
                            <th class="col-1 text-right align-top">Power Coating</th>
                            <th class="col-1 text-right align-top">Assembling</th>
                            <th class="col-1 text-right align-top">Dispatch</th>
                            <th class="col-1 text-right align-top">Partially Dispatch</th>
                            <th class="col-1 text-right align-top">Completion %</th>
                            <th class="tdnowrap text-center align-top">
                                <?= Html::checkbox("", false, ['id' => 'checkAllControl', 'onclick' => 'checkAllItems(this)']) ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="itemDisplayTable">
                        <?php
                        foreach ($panels as $key => $panel) {
                            ?>
                            <tr id="tr_<?= $panel->id ?>">
                                <td class="text-right px-2"><?= $key + 1 ?></td>
                                <td class="tdnowrap">
                                    <?php
//                                    echo Html::a($panel->project_production_panel_code,
//                                            ['view-project-panel-items', 'panelId' => $panel->id],
//                                            ['title' => 'View', 'class' => 'mx-1 text-primary no-text-deco'])
                                    ?>
                                    <?= $panel->project_production_panel_code ?>
                                </td>
                                <td class="text-right px-3 tdnowrap">
                                    <?= $panel->quantity ?> <?= $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") ?>
                                </td>

                                <?php
                                $tasks = $panel->projectProductionFabricationProgress;
//                                $tasks = new \frontend\models\ProjectProduction\fabrication\ProjectProductionFabricationProgress();
                                if ($tasks) {
                                    ?>
                                    <td class="text-right"><?= $tasks->cutnpunch ?>/<?= $panel->quantity ?></td>
                                    <td class="text-right"><?= $tasks->bend ?>/<?= $panel->quantity ?></td>
                                    <td class="text-right"><?= $tasks->weldngrind ?>/<?= $panel->quantity ?></td>
                                    <td class="text-right"><?= $tasks->powcoat ?>/<?= $panel->quantity ?></td>
                                    <td class="text-right"><?= $tasks->assembling ?>/<?= $panel->quantity ?></td>
                                    <td class="text-right"><?= $tasks->dispatch ?>/<?= $panel->quantity ?></td>
                                    <td class="text-right"><?= $tasks->partial_dispatch ?>/<?= $panel->quantity ?></td>
                                    <td class="text-right"><?= $tasks->progress_percentage . " %" ?></td>
                                <?php } else { ?>
                                    <td colspan="8" class=" text-center"> Not Initiated</td><?php } ?>
                                <td class="text-center">
                                    <?php
                                    if (empty($tasks)) {
                                        echo Html::checkbox("itemCheckbox[]", false, ['value' => $panel->id, 'class' => 'itemCheckbox']);
                                    } else {
                                        echo Html::a("<i class='far fa-edit'></i>", "javascript:", ['class' => "modalButtonMedium text-success", 'value' => 'ajax-update-fabrication-progress?id=' . $panel->id]);
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
            <div class="container-fluid p-0">
                <?php
                echo Html::a('Start Production <i class="fas fa-check"></i>', 'javascript:validateAndInitiate()',
                        ["class" => "btn btn-success mb-2 mt-0 float-right"]);
                ?>
            </div>
            <?php
            ActiveForm::end();
            ?>
        </fieldset>
    </div>
</div>
<div class="hidden">
    <?php
    $form = ActiveForm::begin(['id' => 'myHiddenForm']);
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

        $(".itemCheckbox").click(function () {
            $("#checkAllControl").prop('checked', false);
        });
    });


    function checkAllItems(me) {
        let toCheck = $(me).is(':checked');
        $("#myForm").find(".itemCheckbox").each(function (idx, elem) {
            $(elem).prop('checked', toCheck);
        });
    }

    function validateAndInitiate() {
        let checked = 0;
        $("#myForm").find(".itemCheckbox").each(function (idx, elem) {
            checked += $(elem).is(':checked');
        });

        if (checked <= 0) {
            myAlert("No panel is selected");
        } else if (confirm("Start fabrication processes?")) {
            $("#myForm").submit();
        }
    }

</script>