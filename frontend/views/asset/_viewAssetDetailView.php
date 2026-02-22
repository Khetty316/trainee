<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

$isGuest = is_null(Yii::$app->user->identity);

?>
<div class="asset-master-view-detail">

    <?php
    if ($isGuest) {
        echo DetailView::widget([
            'model' => $model,
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
            'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
            'attributes' => [
                'asset_idx_no',
                [
                    'attribute' => 'asset_category',
                    'value' => function($model) {
                        return $model->assetCategory->name;
                    }
                ],
                [
                    'attribute' => 'asset_sub_category',
                    'value' => function($model) {
                        return $model->assetSubCategory->name;
                    }
                ],
                'description',
                'brand',
                'model',
                'specification:ntext',
                'remarks:ntext',
            ],
        ]);
    } else {
        echo DetailView::widget([
            'model' => $model,
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
            'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
            'attributes' => [
                'asset_idx_no',
                [
                    'attribute' => 'asset_category',
                    'value' => function($model) {
                        return $model->assetCategory->name;
                    }
                ],
                [
                    'attribute' => 'asset_sub_category',
                    'value' => function($model) {
                        return $model->assetSubCategory->name;
                    }
                ],
                'description',
                'brand',
                'model',
                'specification:ntext',
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
                    'attribute' => 'file_invoice_image',
                    'label' => 'Invoice',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->file_invoice_image ? Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
                                        [
                                            'title' => "Click to view me",
                                            "value" => ("/asset/get-invoice?assetId=" . $model->id . '&filename=' . $model->file_invoice_image),
                                            "class" => "modalButtonPdf m-2"]) : null;
                    }
                ],
                [
                    'attribute' => 'purchased_by',
                    'value' => function($model) {
                        return $model['purchasedBy']['fullname'];
                    }
                ],
                [
                    'attribute' => 'own_type',
                    'value' => function($model) {
                        return $model->ownType->description;
                    }
                ],
                [
                    'attribute' => 'rental_fee',
                    'value' => function($model) {
                        return MyFormatter::asDecimal2_emptyDash($model->rental_fee);
                    }
                ],
                [
                    'attribute' => 'idle_sts',
                    'value' => function($model) {
                        return $model->idle_sts ? "Yes" : "No";
                    }
                ],
                'remarks:ntext',
                [
                    'attribute' => 'condition',
                    'value' => function($model) {
                        return $model->condition0->description;
                    }
                ],
                [
                    'attribute' => 'cost',
                    'label' => 'Cost (RM)',
                    'value' => function($model) {
                        return MyFormatter::asDecimal2_emptyDash($model->cost);
                    }
                ],
                [
                    'attribute' => 'warranty_due_date',
                    'label' => 'Warranty Due Date',
                    'value' => function($model) {
                        return MyFormatter::asDate_Read($model->warranty_due_date);
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'label' => 'Asset Registered By',
                    'value' => function($model) {
                        return $model->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
                ],
            ],
        ]);
    }
    ?>

</div>
