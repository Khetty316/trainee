<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestStatus;

$this->title = 'Attendance List';
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $master->testMain->panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => 'Test Panel Details', 'url' => ['/test/testing/index-panel', 'id' => $master->testMain->panel->id]];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-main-index">

    <div class="col-12 mb-3 pl-0">
        <h3><?= Html::encode($this->title) ?></h3>
        <div class="row justify-content-between pl-3">
            <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', "?id=$model->id", ['class' => 'btn btn-primary']) ?>
            <div>
                <?php
                echo Html::a('Edit <i class="far fa-edit"></i>', "edit-attendance-list?id=$model->id", ['class' => 'btn btn-success']);
                if ($model->status == RefTestStatus::STS_READY_FOR_TESTING) {
                    echo Html::a('Set Attendance as Complete <i class="fas fa-check"></i>', ["attendance-status", 'id' => $model->id, 'sts' => RefTestStatus::STS_COMPLETE], ['class' => 'float-right btn btn-success ml-2']);
                }
                ?>
            </div>
        </div>
    </div>

    <?=
    GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'org',
            'designation',
            'role',
            [
                'attribute' => 'signature',
                'format' => 'raw',
                'filter' => '',
                'header' => 'Signature',
                'headerOptions' => ['width' => '15%;'],
                'value' => function ($model) {
                    if (!$model->signature) {
                        return '<div class="text-danger float-right">No Signature</div>';
                    }

                    $imageHtml = \yii\helpers\Html::img($model->signature, ['alt' => 'Signature', 'style' => 'max-width:20%;']);
//                    uncomment for toggle function
//                    $buttonHtml = '<button class="float-right toggleSignatureBtn btn btn-sm btn-primary" onclick="toggleSignature(this)">Toggle</button>';
//                    return '<div class="signatureContainer hidden">' . $imageHtml . '</div>' . $buttonHtml;
                    return $imageHtml;
                },
            ],
        ],
    ]));
    ?>
    <?php
    echo Html::a('Delete Form &nbsp;<i class="fa fa-trash"></i>', ["delete-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2', 'data-confirm' => 'Delete this form?']);
    ?>

</div>
<style>
    .signatureContainer .hidden {
        display: none;
    }
</style>

<script>
//    if want to use toggle function for the signature above
//    function toggleSignature(button) {
//        var signatureContainer = button.parentElement.querySelector('.signatureContainer');
//        signatureContainer.classList.toggle('hidden');
//    }
</script>
