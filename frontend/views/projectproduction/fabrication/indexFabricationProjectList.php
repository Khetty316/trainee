<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Project List';
$this->params['breadcrumbs'][] = "Fabrication Progress";
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <?php //= $this->render('__materialBqNavBar', ['pageKey' => '2']) ?>
    <div class="row">
        <div class="col-12">
            <?=
            GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'project_production_code',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Html::a($model->project_production_code, ['view-production-fabrication-progress', 'projId' => $model->id]);
                        }
                    ],
                    'name',
                    'remark:ntext',
                    'quotation_id',
                ],
            ]));
            ?>


        </div>
    </div>
</div>
