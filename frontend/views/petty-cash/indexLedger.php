<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\pettyCash\PettyCashRequestMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ($module === 'finance' ? 'Petty Cash Ledger - Finance' : 'Petty Cash Ledger - Director');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="petty-cash-request-master-index">

    <?= $this->render('__pettycashNavBar', ['module' => $module, 'pageKey' => $key]) ?>

    <p class="mt-2">
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>

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
//            'id',
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-right'],
                'value' => function ($model) {
                    return \common\models\myTools\MyFormatter::asDecimal2($model->amount ?? 0);
                }
            ],
            [
                'attribute' => 'created_at',
//                'contentOptions' => ['class' => 'col-sm-4'],
                'format' => 'raw',
                'value' => function ($model) {
                    return "By " . ($model->createdBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'created_at'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) use ($module) {
                    if ($module === 'finance') {
                        $action = 'finance-view-ledger';
                    } else {
                        $action = 'director-view-ledger';
                    }
                    return Html::a('View <i class="far fa-eye"></i>', [$action, 'id' => $model->id], ['class' => 'btn btn-sm btn-success mx-1']);
                }
            ],
        ],
    ]);
    ?>

</div>
