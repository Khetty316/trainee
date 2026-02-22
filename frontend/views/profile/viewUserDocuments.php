<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\profile\UserDocuments */

$this->title = 'Documents'; // . " - " . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'My Space', 'url' => ['/profile/view-profile']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-documents-view">

    <h3> <?= Html::encode(Yii::$app->user->identity->fullname) ?> </h3>

    <?= $this->render('__ProfileNavBar', ['module' => 'account_claims', 'pageKey' => '2']); ?>
    <div class="col-lg-12">
        <p>
            <?php
            echo Html::a('New Doc <i class="fas fa-plus"></i>',
                    "#",
                    [
                        "value" => \yii\helpers\Url::to('update-user-documents'),
                        "class" => "modalButton btn btn-success"]);
            ?>

        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'headerRowOptions' => ['class' => 'my-thead'],
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
//            'options'=>['class'=>'table-responsive grid-view'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'columns' => [
                [
                    'attribute' => 'doctype_code',
                    'label' => 'Document Type',
                    'value' => function($model) {
                        return $model->doctypeCode['doc_name'];
                    }
                ],
                [
                    'attribute' => 'doc_file_link',
                    'label' => 'Reference',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->doc_file_link ? Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/profile/get-document?filename=" .
                                        urlencode($model->doc_file_link), ['target' => "_blank", 'class' => 'm-2', 'title' => "Click to view me"]) : null;
                        ;
                    }
                ],
                'description',
                [
                    'attribute' => 'doc_date',
                    'value' => function($model) {
                        return $model->doc_date == "" ? "" : MyFormatter::asDate_Read($model->doc_date); // Yii::$app->formatter->asDatetime($model->doc_due_date, 'php:d/m/Y');
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'doc_expiry_date',
                    'value' => function($model) {
                        return $model->doc_expiry_date == "" ? null : MyFormatter::asDate_Read($model->doc_expiry_date); // Yii::$app->formatter->asDatetime($model->doc_due_date, 'php:d/m/Y');
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function($model) {
                        return $model->created_at == "" ? "" : MyFormatter::asDateTime_ReaddmYHi($model->created_at); // Yii::$app->formatter->asDatetime($model->doc_due_date, 'php:d/m/Y');
                    },
                    'format' => 'html',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
//                    'headerOptions' => ['style' => 'width:120px'],
                    'buttons' => [
                        'update' => function ($url, $model, $key) {


                            $returnStr = Html::a('<i class="far fa-edit"></i>&nbsp;',
                                            "#",
                                            [
                                                "value" => \yii\helpers\Url::to('/profile/update-user-documents?id=' . $model->id),
                                                "class" => "modalButton"]);
                            return "$returnStr";
                        },
                        'delete' => function ($url, $model, $key) {
                            $returnStr = Html::a('<i class="far fa-trash-alt text-danger"></i>&nbsp;',
                                            ['/profile/delete-user-documents', 'id' => $model->id],
                                            [
                                                'data' => [
                                                    'confirm' => 'Are you sure you want to delete this item?',
                                                    'method' => 'post',
                                                ],
                                            ]
                            );
//                            $returnStr = Html::a("<i class='far fa-edit'></i>", '/profile/update-user-documents?id=' . $model->id, ['class' => 'm-1', 'title' => "Update", 'data-method' => 'post']);
                            return "$returnStr";
                        },
                    ],
//                    'contentOptions' => ['class' => 'd-none d-md-table-cell'],
//                    'headerOptions' => ['class' => 'd-none d-md-table-cell']
                ],
//            'created_by',
//            'updated_at',
//            'udpated_by',
            ],
        ]);
        ?>

    </div>
</div>
