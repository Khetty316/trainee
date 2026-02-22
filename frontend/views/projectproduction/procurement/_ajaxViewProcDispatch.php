<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\myTools\MyFormatter;

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Dispatched Detail</legend>
                <?php
                echo DetailView::widget(array_merge(Yii::$app->params['detailViewOption28'], [
                    'model' => $model,
                    'attributes' => [
                        'dispatch_no',
                        [
                            'attribute' => 'dispatched_by',
                            'format' => 'raw',
                            'value' => function($model) {
                                $dispatchUser = User::findOne($model->dispatched_by);
                                if ($dispatchUser) {
                                    return $dispatchUser->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->dispatched_at);
                                } else {
                                    return null;
                                }
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function($model) {
                                return $model->status0->status_name??null;
                            }
                        ],
                        [
                            'attribute' => 'responded_by',
                            'format' => 'raw',
                            'value' => function($model) {
                                $receivedUser = User::findOne($model->responded_by);
                                if ($receivedUser) {
                                    return $receivedUser->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->responded_at);
                                } else {
                                    return null;
                                }
                            }
                        ],
                    ],
                ]));
                ?>
            </fieldset>
        </div>
        <div class="col-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Dispatched Item List</legend>
                <table class="table table-sm table-bordered table-striped ">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-right ">Qty</th>
                            <th class="">Unit</th>
                        </tr>
                    </thead>
                    <tbody id='divItems'>
                        <?php
                        $items = $model->projectProductionPanelProcDispatchItems;
                        foreach ($items as $item) {
                            ?>
                            <tr>
                                <td>
                                    <?= Html::encode($item->item_description) ?>
                                </td>
                                <td class="text-right ">    
                                    <?= $item->quantity ?>
                                </td>
                                <td class=" ">
                                    <?= $item->unitCode->unit_name_single ?? null ?>
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
<?php ?>


