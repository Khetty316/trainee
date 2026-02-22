<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\covid\testkit\CovidTestkitInventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Covid Testkit Inventories';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="covid-testkit-inventory-index">

    <?php
    echo $this->render('__covidTestkitNavBar', ['module' => 'admin', 'pageKey' => '1']);
    ?>
    <p>
        <?= Html::a('Stock In <i class="fas fa-download"></i>', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Stock Out <i class="far fa-share-square"></i>', ['stockout'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <div class="col-xs-12 col-md-6">

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
                'brand',
                [
                    'attribute' => 'total_movement',
                    'label' => 'Total',
                    'format' => 'raw',
                    'value' => function($model) {
                        return "<p class='text-right m-0'>" . $model->total_movement . "</p>";
                    }
                ],
            ],
        ]);
        ?>
    </div>

</div>
