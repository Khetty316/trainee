<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\hrdoc\HrEmployeeDocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'HR Employee Documents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hr-employee-documents-index">
    <?= $this->render('_hrNavBar', ['module' => 'hrdoc', 'pageKey' => '1']) ?>

    <p class="mt-3">
        <?= Html::a('Employee Documents <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>    

    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <?php
    $docTypeList = frontend\models\common\RefHrDoctypes::getDropDownListActiveOnly();
    echo GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'headerOptions' => ['style' => 'width:100px'],
                'buttons' => [
                    'delete' => function ($url, $model, $key) {

                        if ($model->active_sts == 0) {
                            return '<i class="far fa-trash-alt text-secondary"></i>';
                        } else {
                            return Html::a('<i class="far fa-trash-alt text-danger"></i>', ['/working/hr-employee-document/inactivate', 'id' => $model->id], ['data-confirm' => 'Are you sure to delete the file?']);
                        }
                    },
                ],
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell']
            ],
            [
                'attribute' => 'hr_doctype',
                'format' => 'raw',
                'filter' => $docTypeList,
                'value' => function ($model) {
                    return $model->hrDoctype->doc_type_name;
                }
            ],
            [
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => function ($model) {
                    $staff = $model->employee;
                    return $staff->staff_id . " " . $staff->fullname;
                }
            ],
            [
                'attribute' => 'filename',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(substr($model->filename, 7), ["/working/hr-employee-document/get-file", 'id' => $model->id],
                            ['target' => "_blank"]);
                }
            ],
            [
                'attribute' => 'active_sts',
                'format' => 'raw',
                'filter' => ['1' => 'Yes', '0' => 'No'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return ($model->active_sts) ? '<i class="far fa-check-circle text-success"></i>' : '<i class="far fa-times-circle text-danger"></i>';
                }
            ],
            [
                'attribute' => 'is_read',
                'format' => 'raw',
                'filter' => ['1' => 'Yes', '0' => 'No'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return ($model->is_read) ? '<i class="far fa-check-circle text-success"></i>' : '<i class="far fa-times-circle text-danger"></i>';
                }
            ],
            [
                'attribute' => 'read_at',
                'format' => 'html',
                'value' => function ($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model->read_at);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'read_at',
                    'language' => 'en',
                    'dateFormat' => "dd-MM-yyyy",
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'html',
                'value' => function ($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'en',
                    'dateFormat' => "dd-MM-yyyy",
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $staff = $model->createdBy;
                    return $staff['fullname'];
                }
            ],
        ],
    ]));
    ?>


</div>
