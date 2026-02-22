<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\profile\UserDocuments */

$this->title = 'HR Documents'; // . " - " . $model->fullname;
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-documents-view">

    <h3> <?= Html::encode(Yii::$app->user->identity->fullname) ?> </h3>

    <?= $this->render('__ProfileNavBar', ['module' => 'account_claims', 'pageKey' => '4']); ?>
    <div class="col-lg-12">
        <p class="mt-3">
            <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>    
        </p>

        <?=
        GridView::widget([
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'options' => ['style' => 'overflow-x: auto;'], // Enable horizontal scrolling if needed
            'columns' => [
                'category',
                [
                    'attribute' => 'description',
                ],
//                [
//                    'attribute' => 'filename',
//                    'format' => 'raw',
//                    'value' => function ($model) {
//                        if ($model->filename) {
//                            return Html::a(substr($model->filename, 15) . " <i class='far fa-file-alt m-1' ></i>", ["/profile/get-public-file", 'id' => $model->id], ['target' => "_blank"]);
//                        } else {
//                            return null;
//                        }
//                    }
//                ],
                [
                    'attribute' => 'filename',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $userId = Yii::$app->user->identity->id;

                        // Get read status (false if no record found)
                        $isRead = \frontend\models\working\hrdoc\HrPublicDocumentsRead::find()
                                ->where([
                                    'employee_id' => $userId,
                                    'hr_public_doc_id' => $model->id,
                                ])
                                ->select('is_read')
                                ->scalar();

                        // Red only if record exists and is_read = 0
                        $linkColor = ($isRead !== false && $isRead == 0) ? 'color:red;' : '';

                        if ($model->filename) {
                            return Html::a(
                                            substr($model->filename, 15) . " <i class='far fa-file-alt m-1'></i>",
                                            ["/profile/get-public-file", 'id' => $model->id],
                                            [
                                                'target' => "_blank",
                                                'style' => $linkColor,
                                            ]
                                    );
                        }
                        return null;
                    },
                ],
                [
                    'attribute' => 'file_date',
                    'format' => 'html',
                    'value' => function ($model) {
                        return MyFormatter::asDate_Read($model->file_date);
                    },
                    'filter' => yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'file_date',
                        'language' => 'en',
                        'dateFormat' => 'php:m/d/Y',
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                        ],
                        'clientOptions' => [
                            'altFormat' => 'dd/mm/yy', // Format for sending to the server
                            'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'file_date'), // Hidden input for sending formatted date
                        ],
                    ]),
                ],
                [
                    'headerOptions' => ['style' => 'width: 45%;'],
                    'attribute' => 'remark',
                    'format' => 'ntext',
                    'value' => function ($model) {
                        return $model->remark;
                    }
                ]
            ],
        ]);
        ?>

    </div> 
</div>
