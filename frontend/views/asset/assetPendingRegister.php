<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\asset\AssetMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="asset-master-index">



    <?php
    echo $this->render('__assetNavBar', ['module' => 'personal', 'pageKey' => '3']);
//    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <p>
        <?= Html::a('Register New Asset <i class="fas fa-plus"></i>', ['create-asset-personal'], ['class' => 'btn btn-success']) ?>
        <?php //= Html::a('Register New Asset <i class="fas fa-plus"></i>', ['create-asset-personal'], ['class' => 'btn btn-success'])  ?>
    </p>


    <div class="pt-2">
        <div class="asset-list-grid-view">

            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pager' => ['class' => yii\bootstrap4\LinkPager::class],
                'headerRowOptions' => ['class' => 'my-thead'],
                'layout' => "{summary}\n{pager}\n{items}\n{pager}",
                'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'headerOptions' => ['style' => 'width:10px'],
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                $url = '/asset/view-asset-pending-register?assetId=' . $model->id;
//                                return Html::a('<i class="far fa-eye"></i>', "#", ["value" => \yii\helpers\Url::to($url), "class" => "modalButton"]);
                                return Html::a('<i class="far fa-eye"></i>', \yii\helpers\Url::to($url), []);
                            },
                        ],
//                        'contentOptions' => ['class' => 'd-none d-md-table-cell'],
//                        'headerOptions' => ['class' => 'd-none d-md-table-cell']
                    ],
                    [
                        'attribute' => 'asset_category_name',
                        'filter' => \frontend\models\common\RefAssetCategory::getDropDownList(),
                    ],
                    [
                        'attribute' => 'asset_sub_category_name',
                        'filter' => frontend\models\common\RefAssetSubCategory::getDropDownList()
                    ],
                    'description',
                    'brand',
                    'model',
                    [
                        'attribute' => 'file_image',
                        'label' => 'Image',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->file_image ? Html::a("<i class='fas fa-image fa-lg' ></i>", "#",
                                            [
                                                'title' => "Click to view me",
                                                "value" => ("/asset/get-image?assetId=" . $model->id),
                                                "class" => "modalButtonPdf m-2"]) : null;
                        }
                    ],
                    [
                        'attribute' => 'purchase_by_name',
                    ],
                    [
                        'attribute' => 'own_type_desc',
                        'filter' => frontend\models\common\RefAssetOwnType::getDropDownList()
                    ],
                    //'rental_fee',
                    [
                        'attribute' => 'idle_sts',
                        'filter' => [1 => "Yes", 0 => "No"],
                        'value' => function($model) {
                            return $model->idle_sts ? "Yes" : "No";
                        },
                    ],
                //'specification:ntext',
                //'remarks:ntext',
                //'condition',
                //'cost',
                //'warranty_due_date',
//            'active_sts',
                //'created_at',
                //'created_by',
                ],
            ]);
            ?>

        </div>
    </div>


</div>
