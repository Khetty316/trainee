<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\cmms\CmmsAssetListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Asset Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cmms-asset-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0 ">Upload By Template:</legend>
        <div class="container-fluid">
            <div class="row">
                <div class="">
                    <?php
                        $form = ActiveForm::begin([
                                    'action' => ['upload-excel'],
                                    'options' => ['enctype' => 'multipart/form-data'],
                        ]);

                        echo Html::fileInput('excelTemplate', null, [
                            'accept' => '.xls', 'required' => true, 
                        ]);
                        echo Html::submitButton(                                   
                                'Upload Excel <i class="fas fa-upload"></i>',
                                ['class' => 'btn btn-success mb-2 mt-2']);
                        ActiveForm::end();
                    ?>
                    <?php
                        echo Html::a(
                                'Download Template <i class="fas fa-download"></i>',
                                yii\helpers\Url::to('@web/template/template-cmms-asset-details.xls'),
                                [
                                    'class' => 'btn btn-primary mb-5 mt-0',
                                    'download' => 'template-cmms-asset-details.xls',
                                    'title' => 'Download Excel Template'
                                ]
                        );
                    ?>
                </div>
            </div>
        </div>
    </fieldset>
    <p>
        <?= Html::a('Add Asset', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'asset_id',
//            'asset_code',
            'area',
            'section',
            'name',
            'manufacturer',
            'serial_no',
            'date_of_purchase',
            'date_of_installation',
            'active_sts',
//            [
//                'label' => 'Action',
//                'format' => 'raw',
//                'value' => function ($model) {
//                    return \yii\helpers\Html::a(
//                        'Create PM Work Order',
//                        ['cmms/cmms-fault-list/create', 
//                            'moduleIndex' => 'personal',
//                            'fault_area' => $model->area,
//                            'fault_section' => $model->section,
//                            'fault_asset_code' => $model->asset_code
//                        ],
//                        ['class' => 'btn btn-warning btn-sm']
//                    );
//                },
//            ],
//            [
//                'label' => 'Action',
//                'format' => 'raw',
//                'value' => function ($model) {
//                    return \yii\helpers\Html::a(
//                        'Report New Fault',
//                        ['cmms/cmms-fault-list/create', 
//                            'moduleIndex' => 'personal',
//                            'fault_area' => $model->area,
//                            'fault_section' => $model->section,
//                            'fault_asset_code' => $model->asset_code
//                        ],
//                        ['class' => 'btn btn-warning btn-sm']
//                    );
//                },
//            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
