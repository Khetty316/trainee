<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventorySupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Purchasing - Reorder Items'];
?>
<style>
    .grid-wrap {
        white-space: normal !important;
        word-break: break-word;
        text-align: left;
    }

    .w-500 {
        max-width: 500px;
    }

    .my-thead th {
        vertical-align: middle;
        padding: 8px 5px;
    }

    .table-responsive {
        overflow-x: auto;
    }
</style>

<div class="inventory-supplier-index">

    <?= $this->render('_purchasingNavBar', ['module' => 'reorderItem', 'pageKey' => '1']) ?>

    <p>
        <?=
        Html::button('Order Selected Item', [
            'class' => 'btn btn-success float-right mb-2',
            'id' => 'order-selected-item',
            'data-url' => \yii\helpers\Url::to(['order-selected-item'])
        ])
        ?>

        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>

    <div class="table-responsive">
        <?=
        GridView::widget([
            'id' => 'detail-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'headerRowOptions' => ['class' => 'my-thead'],
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'width: 50px;'],
                ],
                [
                    'attribute' => 'inventory_code',
                    'headerOptions' => ['style' => 'width: 150px; white-space: nowrap;'],
                    'contentOptions' => ['style' => 'white-space: nowrap;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->inventory_code;
                    }
                ],
                [
                    'attribute' => 'department_name',
                    'label' => 'Department',
                    'headerOptions' => ['style' => 'width: 120px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'value' => fn($model) => $model->department_name,
                    'filter' => Html::activeDropDownList(
                            $searchModel,
                            'department_code', // Use department_code for filtering
                            \yii\helpers\ArrayHelper::map(
                                    frontend\models\common\RefUserDepartments::find()
                                            ->select(['code', 'department_name'])
                                            ->orderBy(['department_name' => SORT_ASC])
                                            ->all(),
                                    'code',
                                    'department_name'
                            ),
                            [
                                'class' => 'form-control',
                                'prompt' => 'All'
                            ]
                    )
                ],
                [
                    'attribute' => 'supplier_code',
                    'label' => 'Supplier',
                    'headerOptions' => ['style' => 'width: 300px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->supplier_code . '</br>' . $model->supplier_name;
                    },
                    'filter' => Html::activeTextInput($searchModel, 'supplier_code', [
                        'class' => 'form-control',
                    ])
                ],
                [
                    'attribute' => 'brand_code',
                    'label' => 'Brand',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->brand_code . '</br>' . $model->brand_name;
                    },
                    'filter' => Html::activeTextInput($searchModel, 'brand_code', [
                        'class' => 'form-control',
                    ])
                ],
                [
                    'attribute' => 'model_type',
                    'label' => 'Model Type',
                    'headerOptions' => ['style' => 'width: 120px;'],
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'value' => fn($model) => $model->model_type,
                ],
                [
                    'attribute' => 'model_description',
                    'label' => 'Description',
                    'headerOptions' => ['style' => 'width: 150px;'],
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'value' => fn($model) => $model->model_description,
                ],
                [
                    'attribute' => 'group',
                    'headerOptions' => ['style' => 'width: 100px; white-space: nowrap;'],
                    'contentOptions' => ['style' => 'white-space: nowrap;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->group;
                    }
                ],
                [
                    'attribute' => 'minimum_qty',
                    'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->minimum_qty;
                    }
                ],
                [
                    'attribute' => 'stock_on_hand',
                    'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->stock_on_hand;
                    }
                ],
                [
                    'attribute' => 'required_qty',
                    'label' => 'Required Qty',
                    'headerOptions' => ['style' => 'width: 80px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->required_qty;
                    }
                ],
                [
                    'attribute' => 'qty_pending_receipt',
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->qty_pending_receipt;
                    }
                ],
                [
                    'attribute' => 'active_sts',
                    'headerOptions' => ['style' => 'width: 90px;'],
                    'contentOptions' => ['class' => 'grid-wrap', 'style' => 'text-align: center;'],
                    'value' => function ($model) {
                        return $model->active_sts == 1 ? 'No' : 'Yes';
                    },
                    'filter' => Html::activeDropDownList(
                            $searchModel,
                            'active_sts',
                            [
                                '' => 'All',
                                '1' => 'No',
                                '2' => 'Yes'
                            ],
                            ['class' => 'form-control text-center']
                    )
                ],
                [
                    'attribute' => 'created_by_fullname',
                    'label' => 'Created By',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'value' => function ($model) {
                        return ($model->created_by_fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
                ],
                [
                    'attribute' => 'updated_by_fullname',
                    'label' => 'Updated By',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'value' => function ($model) {
                        return $model->updated_by_fullname ? $model->updated_by_fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : null;
                    }
                ],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => Html::tag('div', 'Select</br>All', ['style' => 'margin-bottom:5px;']) .
                    Html::checkbox('selection_all', false, [
                        'id' => 'select-all-checkbox',
                        'style' => 'margin:0;',
                    ]),
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'checkboxOptions' => function ($model) {
                        return ['value' => $model->inventory_id, 'class' => 'select-on-check-item'];
                    },
                ]
            ],
        ]);
        ?>
    </div>

</div>
<?php $deleteUrl = \yii\helpers\Url::to(['order-selected-item']); ?>
<script>
    $('#order-selected-item').on('click', function () {
        var keys = $('#detail-grid').yiiGridView('getSelectedRows');
        var url = $(this).data('url');

        if (keys.length === 0) {
            alert('Please select at least one record to order.');
            return;
        }

        if (!confirm('Are you sure you want to order ' + keys.length + ' item(s)?')) {
            return;
        }

        $.post(url, {ids: keys}, function (response) {
            if (response.success) {
                alert('Items ordered successfully!');
                // ✅ Redirect to the URL returned from server
                window.location.href = response.redirect;
            } else {
                alert(response.message || 'Error occurred while ordering items.');
            }
        }).fail(function () {
            alert('Error occurred while ordering items.');
        });
    });
</script>
