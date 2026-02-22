<?php

use frontend\models\test\TestDetailPunchlist;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\jui\DatePicker;
use frontend\models\test\RefTestStatus;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\test\TestDetailPunchlistSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Punchlist';
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $master->testMain->panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => 'Test Panel Details', 'url' => ['/test/testing/index-panel', 'id' => $master->testMain->panel->id]];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-detail-punchlist-index">

    <div class="col-12 mb-3">
        <div class="row justify-content-between">
            <h3><?= Html::encode($this->title) ?></h3>
            <div>
                <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', yii\helpers\Url::to(['/test/punchlist/index', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>    
                <?php
                if ($model->status != RefTestStatus::STS_COMPLETE) {
                    echo Html::a("Add Punchlist  <i class='fas fa-plus'></i>", "javascript:", [
                        'title' => "Add a punchlist",
                        "value" => yii\helpers\Url::to(['/test/punchlist/add-punchlist', 'id' => $model->id]),
                        "class" => "modalButton btn btn-success",
                        'data-modaltitle' => "Add a Punchlist"
                    ]);
                    echo Html::a('Edit All <i class="far fa-edit"></i>', "edit-punchlist?id=$model->id", ['class' => 'btn btn-success ml-1']);
//                    echo Html::a('Set Punchlist as Complete <i class="fas fa-check"></i>', "complete-punchlist?id=$model->id", ['class' => 'float-right btn btn-success ml-1']);
                }
                if ($model->status == RefTestStatus::STS_IN_TESTING) {
                    echo Html::a('Set Punchlist as Complete <i class="fas fa-check"></i>', "complete-punchlist?id=$model->id", ['class' => 'float-right btn btn-success ml-1']);
                }
                ?>
            </div>
        </div>
    </div>


    <div>
        <?=
        GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'test_form_code',
                    'filter' => frontend\models\test\RefTestFormList::getDropDownList(),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->testFormCode->formname, "javascript:", [
                            'title' => "Edit a punchlist",
                            "value" => yii\helpers\Url::to(['/test/punchlist/edit-single-punchlist', 'id' => $model->id]),
                            "class" => "modalButton",
                            'data-modaltitle' => "Add a Punchlist"
                        ]);
                    }
                ],
                [
                    'attribute' => 'error_id',
                    'value' => function ($model) {
                        return $model->error->description;
                    }
                ],
                [
                    'attribute' => 'remark',
                ],
                [
                    'attribute' => 'rectify_date',
                    'value' => function ($model) {
                        return common\models\myTools\MyFormatter::asDate_Read($model->rectify_date);
                    },
                    'filter' => yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'rectify_date',
                        'language' => 'en',
                        'dateFormat' => 'dd/MM/yy',
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                        ],
                        'clientOptions' => [
                            'altFormat' => 'dd/mm/yy', // Format for sending to the server
                            'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'rectify_date'), // Hidden input for sending formatted date
                        ],
                    ]),
                ],
                [
                    'attribute' => 'verify_by',
                ],
        ]]));
        ?>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <?php
            if ($model->status == RefTestStatus::STS_COMPLETE) {
                echo Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'float-right btn revert btn-danger ml-2', 'data-confirm' => 'Revert this form?']);
            }
            ?>
        </div>
    </div>

</div>
