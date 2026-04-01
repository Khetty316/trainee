<?php

use yii\helpers\Html;

$this->title = 'Material List';
$production = $bomMaster->productionPanel->projProdMaster;
$this->params['breadcrumbs'][] = ['label' => 'Stock Outbound', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['view-panels', 'id' => $production->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="stock-dispatch mb-5">

    <h4>Panel's Code: <?= $bomMaster->productionPanel->project_production_panel_code ?>
        <?php
//        =
//        Html::a("Add Material", "javascript:void(0)", [
//            'title' => "Add Material",
//            "value" => yii\helpers\Url::to(['add-material', 'productionPanelId' => $bomMaster->productionPanel->id]),
//            "class" => "modalButton btn btn-success ml-1",
//            'data-modaltitle' => 'Add Material',
//            'id' => 'AddMaterialBtn'
//        ]);
        ?>
    </h4>

    <?php foreach ($stockMasters as $keyMaster => $master): ?>
        <div class="table-responsive">
            <div class="card mt-2 bg-light">
                <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                     id="heading_<?= $keyMaster ?>" 
                     data-toggle="collapse" 
                     data-target="#collapse_<?= $keyMaster ?>" 
                     aria-expanded="true" 
                     aria-controls="collapse_<?= $keyMaster ?>">
                    <span class="p-0 m-0 accordionHeader">
                        #<?= $master->order ?>
                    </span>
                </div>

                <div id="collapse_<?= $keyMaster ?>" 
                     class="collapse <?= $keyMaster === 0 ? 'show' : '' ?>" 
                     aria-labelledby="heading_<?= $keyMaster ?>">
                    <div class="card-body p-1" style="background-color:white">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Model Type</th>
                                    <th>Brand</th>
                                    <th>Description</th>
                                    <th>Total Quantity</th>
                                    <th>Dispatched Quantity</th>
                                    <th>Unacknowledged Quantity</th>
                                    <th>Available Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($master->stockOutboundDetails as $keyDetail => $detail): ?>
                                    <tr style="<?= ($detail->active_sts == 0) ? 'text-decoration: line-through; color: red;' : '' ?>">
                                        <td class="text-center"><?= $keyDetail + 1 ?></td>
                                        <td>
                                            <?php
                                            if ($detail->active_sts == 0) {
                                                echo $detail->model_type;
                                            } else {
                                                echo Html::a($detail->model_type,
                                                        "javascript:",
                                                        [
                                                            'title' => "Update Detail",
                                                            "value" => yii\helpers\Url::to([
                                                                'update-item-detail',
                                                                'productionPanelId' => $bomMaster->productionPanel->id,
                                                                'stockDetailId' => $detail->id
                                                            ]),
                                                            "class" => "modalButton",
                                                            'data-modaltitle' => "Update Detail"
                                                        ]
                                                );
                                            }
                                            ?>
                                        </td>
                                        <td><?= $detail->brand ?></td>
                                        <td><?= $detail->descriptions ?></td>
                                        <td><?= $detail->qty ?></td>
                                        <td><?= ($detail->dispatched_qty === null) ? 0 : $detail->dispatched_qty ?></td>
                                        <td><?= ($detail->unacknowledged_qty === null) ? 0 : $detail->unacknowledged_qty ?></td>
                                        <td><?=
                                            (($detail->qty_stock_available === null) ? 0 : $detail->qty_stock_available) . Html::a(
                                                    "<i class='far fa-edit m-1 float-right'></i>",
                                                    "javascript:void(0)",
                                                    [
                                                        'title' => "View Detail",
                                                        'value' => yii\helpers\Url::to(['view-inventory-stockoutbound-detail', 'id' => $detail->id, 'productionPanelId' => $bomMaster->productionPanel->id, 'type' => "bomstockoutbound"]),
                                                        'class' => 'modalButton',
                                                        'data-modaltitle' => 'View Detail',
                                                    ])
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div> 
<script>
    let isPageRefreshed = false;

    document.addEventListener('DOMContentLoaded', function () {
        if (performance.navigation.type === 1) {
            isPageRefreshed = true;
        }

        const activeTabId = localStorage.getItem('activeTab');
        if (activeTabId) {
            const activeTab = document.getElementById(activeTabId);
            if (activeTab) {
                activeTab.click();
            }
        }

        const jumpTo = getCookie('jumpToScrollPosition');
        if (jumpTo !== null && jumpTo !== "undefined") {
            window.scrollTo(0, parseInt(jumpTo, 10));
        }
    });

    window.addEventListener('beforeunload', function () {
        if (!isPageRefreshed) {
            const currentYOffset = window.pageYOffset;
            setCookie('jumpToScrollPosition', currentYOffset, 1);
        } else {
            eraseCookie('jumpToScrollPosition');
            localStorage.removeItem('activeTab');
        }
    });

    document.querySelectorAll('.btn-header-link').forEach(function (element) {
        element.addEventListener('click', function () {
            localStorage.setItem('activeTab', this.getAttribute('id'));
        });
    });
</script>


