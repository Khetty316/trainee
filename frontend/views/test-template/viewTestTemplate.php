<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use \common\models\myTools\MyFormatter;
use frontend\models\test\TestMain;
use \frontend\models\test\TestMaster;
use frontend\models\test\RefTestFormList;

/* @var $this yii\web\View */
/* @var $model frontend\models\test\TestTemplate */
$array = frontend\models\test\RefTestFormList::getDropDownList();
$mergeArray = array_merge($array, [TestMaster::TEMPLATE_ITP => TestMain::TEST_ITP_TITLE, TestMaster::TEMPLATE_FAT => TestMain::TEST_FAT_TITLE]);

$this->params['breadcrumbs'][] = ['label' => 'Test Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $mergeArray[$model->formcode];
\yii\web\YiiAsset::register($this);
?>
<div class="test-template-view">
    <div class="row justify-content-left">
        <div class="col-sm-12 col-md-9">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Test Template Detail</legend>
                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                    <?=
                    Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ])
                    ?>
                </p>

                <?=
                DetailView::widget([
                    'template' => "<tr><th style='width: 20%;'>{label}</th><td>{value}</td></tr>",
                    'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
                    'model' => $model,
                    'attributes' => [
                        'doc_ref',
                        'rev_no',
                        [
                            'attribute' => 'formname',
                            'format' => 'raw',
                            'value' => function ($model) use ($mergeArray) {
                                return $model->formcode ? $mergeArray[$model->formcode] : '';
                            },
                        ],
                        [
                            'attribute' => 'proctest1',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->proctest1;
                            },
                        ],
                        [
                            'attribute' => 'proctest2',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->proctest2;
                            },
                        ],
                        [
                            'attribute' => 'proctest3',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->proctest3;
                            },
                        ],
                        [
                            'attribute' => 'active_sts',
                            'format' => 'raw',
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
                    ],
                ])
                ?>
            </fieldset>
        </div>
    </div>

</div>
