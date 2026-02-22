<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\documentreminder\DocumentReminderMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Document Reminder';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-reminder-master-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Create <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>    
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);    ?>

    <?=
    GridView::widget([
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:1%'],
                'value' => function($model) {
                    return $model->id;
                }
            ],
            'category',
//            [
//                'attribute' => 'active_sts',
//                'format' => 'raw',
//                'value' => function($model) {
//                    return $model->active_sts ? "Yes" : "No";
//                }
//            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->description, ['view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'filename',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->filename) {
                        return Html::a(substr($model->filename, 15) . " <i class='far fa-file-alt m-1' ></i>", ["/working/documentreminder/get-file", 'id' => $model->id], ['target' => "_blank"]);
                    } else {
                        return null;
                    }
                }
            ],
            [
                'attribute' => 'remind_date',
                'format' => 'raw',
                'value' => function($model) {
                    $warningLevel = $model->getDocumentPassReminderDate();
                    $icon = "";

                    if ($warningLevel == 1) {
                        $icon = ' <i class="fas fa-exclamation-triangle text-warning" title="Within Warning Date!"></i> ';
                    }
                    return $icon . MyFormatter::asDate_Read($model->remind_date) . (($model->remind_period && $model->remind_period_unit) ? ' (' . $model->remind_period . ' ' . $model->remind_period_unit . ') ' : "");
                }
            ],
            [
                'attribute' => 'expiry_date',
                'format' => 'raw',
                'value' => function($model) {
                    $warningLevel = $model->getDocumentPassExpiryDate();
                    $icon = "";
                    if ($warningLevel == 2) {
                        $icon = ' <i class="fas fa-exclamation-triangle text-danger" title="EXPIRED!"></i> ';
                    }
                    return $icon . MyFormatter::asDate_Read($model->expiry_date);
                }
            ],
        //'remark:ntext',
        //'created_at',
        //'created_by',
        //'updated_at',
        //'updated_by',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
