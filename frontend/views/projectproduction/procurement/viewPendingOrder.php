<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\User;
use common\models\myTools\MyFormatter;

$this->title = $panel->project_production_panel_code;
$this->params['breadcrumbs'][] = ['label' => 'Procurement - Pending Orders', 'url' => ['index-pending-order-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= Html::encode($this->title) ?></h3> 

<div class="row">
    <div class="col-md-5 order-md-2">
        <?= $this->render("/projectproduction/materialbq/_detailViewProjProdDetail", ['projProdMaster' => $panel->projProdMaster, 'panel' => $panel]) ?>
    </div>
    <div class="col-md-7 order-md-1">
        <div class="row">
            <div class="col">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0">Item List</legend>
                    <?php
                    $form = ActiveForm::begin([
                                'id' => 'myForm',
                                'options' => [
                                    'autocomplete' => 'off',
                                ]
                    ]);
                    $canSubmit = false;
                    ?>
                    <div class="hidden">
                        <?= $form->field($model, 'proj_prod_panel_id')->textInput() ?>
                    </div>
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-center">Panel QTY</th>
                                <th class="col-2 tdnowrap">Unit</th>
                                <th class="col-2 text-right tdnowrap">Qty</th>
                                <th class="col-2 text-right tdnowrap">Balance</th>
                                <th class="col-2 text-right tdnowrap">Dispatch</th>
                            </tr>
                        </thead>
                        <tbody id='divItems'>
                            <?php
                            $items = $panel->projectProductionPanelItems;
                            foreach ($items as $item) {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        if ($item->balance > 0) {
                                            $canSubmit = true;
                                            echo Html::textInput('itemId[]', $item->id, ['class' => 'hidden']);
                                        }
                                        echo Html::encode($item->item_description);
                                        ?>
                                    </td>
                                    <td class="text-center tdnowrap">          
                                        <?= $panel->quantity ?>
                                        <?= $panel->unitCode->unit_name ?>
                                    </td>
                                    <td class=" tdnowrap">
                                        <?= $item->unitCode->unit_name_single ?>
                                    </td>
                                    <td class="text-right tdnowrap">    
                                        <?= $item->quantity ?>
                                    </td>
                                    <td class="text-right tdnowrap align-middle">    
                                        <?= $item->balance ?>
                                    </td>
                                    <?php
                                    if ($item->balance > 0) {
                                        echo '<td class="p-0 align-middle">' . Html::textInput('dispatchQty[]', $item->balance,
                                                [
                                                    'class' => 'form-control form-control-sm text-right isQty m-0',
                                                    'type' => 'number',
                                                    'step' => '0.01',
                                                    'min' => '0',
                                                    "oninput" => "this.value=Math.abs(this.value)"
                                        ]) .
                                        '</td>';
                                    } else {
                                        echo '<td class="text-right">' . $item->balance . '</td>';
                                    }
                                    ?>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-right">
                                    <?php
                                    if ($canSubmit) {
                                        echo Html::a("Dispatch", "javascript:submitForm()", ['class' => 'btn btn-success submitButton', 'data-loadingword' => 'Dispatching...']);
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php ActiveForm::end(); ?>
                </fieldset>
            </div>
            <div class="col">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0">Dispatched Item List</legend>
                    <table class="table table-sm table-bordered table-striped ">
                        <thead>
                            <tr>
                                <th class="">Dispatch No</th>
                                <th class="tdnowrap">Dispatched by</th>
                                <th class="tdnowrap">Status</th>
                                <th class="tdnowrap">Responded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $dispatchedList = $panel->projectProductionPanelProcDispatchMasters;
                            foreach ($dispatchedList as $list) {
                                ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo Html::a($list->dispatch_no . ' <i class="fas fa-external-link-alt"></i>',
                                                "javascript:",
                                                [
                                                    "onclick" => "event.preventDefault();",
                                                    "value" => \yii\helpers\Url::to(['ajax-view-proc-dispatch', 'dispatchId' => $list->id]),
                                                    "class" => "modalButton text-primary",
                                                    'title' => 'View'
                                                ]
                                        );
                                        ?>
                                    </td>
                                    <td class="tdnowrap">
                                        <?php
                                        $dispatchUser = User::findOne($list->dispatched_by);
                                        if ($dispatchUser) {
                                            echo $dispatchUser->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($list->dispatched_at);
                                        } else {
                                            echo " - ";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= $list->status0->status_name ?>
                                    </td>
                                    <td class="tdnowrap">
                                        <?php
                                        $respondedUser = User::findOne($list->responded_by);
                                        if ($respondedUser) {
                                            echo $respondedUser->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($list->responded_at);
                                        } else {
                                            echo " - ";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>

</div>
<script>
    function submitForm() {
        let total = 0;
        $("input[name='dispatchQty[]']").each(function (idx, e) {
            if ($(e).val() == "") {
                $(e).val(0);
            }
            total += parseFloat($(e).val());
        });
        if (total > 0) {
            $("#myForm").submit();
        } else {
            myAlert("Cannot dispatch empty items");
        }
    }
</script>