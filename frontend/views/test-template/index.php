<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use \common\models\myTools\MyFormatter;
use \frontend\models\test\TestMaster;
use frontend\models\test\TestMain;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\test\TestTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test Templates';
$this->params['breadcrumbs'][] = $this->title;
$array = frontend\models\test\RefTestFormList::getDropDownList();
$mergeArray = array_merge($array, [TestMaster::TEMPLATE_ITP => TestMain::TEST_ITP_TITLE, TestMaster::TEMPLATE_FAT => TestMain::TEST_FAT_TITLE]);
?>
<div class="test-template-index">

    <p>
        <?= Html::a('Create Test Template <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
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
            ['class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['style' => 'width: 2%; text-align:center;'],
                'headerOptions' => ['style' => 'width: 2%; text-align:center;'],
            ],
            [
                'attribute' => 'doc_ref',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->doc_ref;
                }
            ],
            [
                'attribute' => 'rev_no',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->rev_no;
                }
            ],
            [
                'attribute' => 'formname',
                'contentOptions' => ['class' => 'col-sm-1'],
                'filter' => $mergeArray,
                'value' => function ($model) use ($mergeArray) {
                    return $model->formcode ? $mergeArray[$model->formcode] : '';
                }
            ],
            [
                'attribute' => 'active_sts',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'filter' => ['0' => 'No', '1' => 'Yes'],
                'value' => function ($model) {
                    return $model->active_sts ? "Yes" : "<span class='text-danger'>No</span>";
                },
            ],
            [
                'attribute' => 'created_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return(User::findOne($model->created_by) !== null) ? User::findOne($model->created_by)->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at) : " - ";
                }
            ],
            [
                'attribute' => 'updated_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return(User::findOne($model->updated_by) !== null) ? User::findOne($model->updated_by)->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : " - ";
                },
            ],
            [
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'label' => 'Action',
                'value' => function ($model) {
                    return
                    Html::a('View', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary', 'title' => 'Click to View']) .
                    ' ' .
                    Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-success', 'title' => 'Click to Update']) .
                    ' ' .
                    Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-sm btn-danger', 'title' => 'Click to Delete']);
                },
            ],
        ],
    ]);
    ?>

</div>
