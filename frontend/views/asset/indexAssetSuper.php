<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\asset\AssetMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="asset-master-index">



    <?php
    echo $this->render('__assetNavBar', ['module' => 'super', 'pageKey' => '1']);
    ?>


    <p>
        <?= Html::a('Register New Asset <i class="fas fa-plus"></i>', ['super-create-asset'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="pt-2">
        <div class="asset-list-grid-view">

            <?php
            echo GridView::widget([
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
                        'template' => '{qr}',
                        'headerOptions' => ['style' => 'width:100px'],
                        'buttons' => [
                            'qr' => function ($url, $model, $key) {
                                $returnStr = Html::a('<i class="fas fa-qrcode"></i>',
                                                ['/asset/generate-qr', 'assetIdxNo' => $model->asset_idx_no],
                                                ['class' => 'm-1 text-success', 'title' => "Transfer to", 'target' => '_blank']);
                                return "$returnStr";
                            },
                        ],
                        'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                        'headerOptions' => ['class' => 'd-none d-md-table-cell']
                    ],
                    [
                        'attribute' => 'asset_category_name',
                        'filter' => \frontend\models\common\RefAssetCategory::getDropDownList()
                    ],
                    [
                        'attribute' => 'asset_sub_category_name',
                        'filter' => frontend\models\common\RefAssetSubCategory::getDropDownList()
                    ],
                    [
                        'attribute' => 'asset_idx_no',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::a($model->asset_idx_no, ['/asset/view-asset-super', 'id' => $model->id]);
                        }
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
                        }
                    ],
                    [
                        'attribute' => 'cur_user_fullname',
                        'label' => 'Current Holder'
                    ],
                    [
                        'attribute' => 'pend_user_fullname',
                        'label' => 'Tranferring To'
                    ],
//                    [
//                        'attribute' => 'currentHolder',
//                        'value' => function($model) {
//                            $activeTracking = $model->getAssetTrackingsActive();
//                            return $activeTracking['receiveUser']['fullname'];
//                        }
//                    ],
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
