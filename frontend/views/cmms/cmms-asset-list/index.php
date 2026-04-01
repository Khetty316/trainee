<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use common\modules\auth\models\AuthItem;
use common\models\myTools\MyCommonFunction;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\cmms\CmmsAssetListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Asset Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    fieldset {
        max-width: 350px;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="cmms-asset-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_CMMS_Superior])){ ?>

    <div class="d-flex align-items-start gap-3 mb-3">
        <fieldset class="form-group border p-3 mb-0">
            <legend class="w-auto px-2 m-0">Upload By Template:</legend>

            <?php
            $form = ActiveForm::begin([
                'action' => ['upload-excel'],
                'options' => ['enctype' => 'multipart/form-data'],
            ]);

            echo Html::fileInput('excelTemplate', null, [
                'accept' => '.xlsx',
                'required' => true,
            ]);

            echo Html::submitButton(
                    'Upload Excel <i class="fas fa-upload"></i>',
                    ['class' => 'btn btn-success mb-2 mt-2']
            );

            ActiveForm::end();
            ?>
            <?php
            $canDownload = MyCommonFunction::checkRoles([AuthItem::ROLE_CMMS_Superior]);

            echo $canDownload
                ? Html::a(
                    'Download Template <i class="fas fa-download"></i>',
                    yii\helpers\Url::to(['download-asset-template']),
                    [
                        'class' => 'btn btn-primary',
                        'download' => 'template-cmms-asset-details.xlsx',
                    ]
                )
                : Html::tag(
                    'button',
                    'Download Template <i class="fas fa-download"></i>',
                    [
                        'class' => 'btn btn-primary',
                        'disabled' => true,
                    ]
                );
            ?>
        </fieldset>

    </div>
     <?php } ?>
    <p>
        <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_CMMS_Superior])){ ?>
        <?= Html::a('Add Asset', ['create'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-inventory'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>
    <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_CMMS_Superior])): ?>
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
            'asset_id',
//            'asset_code',
            'area',
            'section',
            'name',
            'manufacturer',
            'serial_no',
            'date_of_purchase',
            'date_of_installation',
            [
                'attribute' => 'active_sts',
                'label' => 'Active?',
                'contentOptions' => [
                    'style' => '
                        max-width: 250px;
                        white-space: normal;
                        word-break: break-word;
                    ',
                ],
                'value' => function ($model) {
                    return $model->active_sts ? 'Yes' : 'No';
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
    <?php elseif (MyCommonFunction::checkRoles([AuthItem::ROLE_CMMS_Normal])): ?>
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
                'asset_id',
    //            'asset_code',
                'area',
                'section',
                'name',
                'manufacturer',
                'serial_no',
                'date_of_purchase',
                'date_of_installation',
                [
                    'attribute' => 'active_sts',
                    'label' => 'Active?',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->active_sts ? 'Yes' : 'No';
                    }
                ],
//                [
//                    'class' => 'yii\grid\ActionColumn',
//                    'template' => '{view}',
//                    'buttons' => [
//                        'view' => function ($url, $model) {
//                            return Html::a(
//                                    '<i class="bi bi-eye"></i>',
//                                    Url::to(['view', 'id' => $model->id]),
//                                [
//                                    'class' => 'text-success',
//                                    'title' => 'View',
//                                    'aria-label' => 'View',
//                                ]
//                            );
//                        },
//                    ],
//                ],
            ],
        ]);
    ?>
    <?php endif; ?>


</div>
