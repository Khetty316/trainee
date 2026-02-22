<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProjectMaster */

//$this->params['breadcrumbs'][] = ['label' => 'Master Project', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-master-view">

    <?= $this->render('__ProjectNavBar', ['pageKey' => '8', 'id' => $model->id, 'projectCode' => $model->proj_code, 'model' => $model]); ?>

    <p>
        <?= Html::a('Update <i class="far fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'options' => ['class' => 'table table-striped table-bordered detail-view fix-width table-sm'],
        'attributes' => [
            'proj_code',
            'project_status',
            'title_short',
            'title_long',
            [
                'attribute' => 'location',
                'value' => function($model) {
                    return $model['location0']['area_name'];
                }
            ],
            'service',
            [
                'attribute' => 'contract_sum',
                'value' => function($model) {
                    return MyFormatter::asDecimal2($model['contract_sum']);
                }
            ],
            [
                'attribute' => 'client_id',
                'value' => function($model) {
                    return $model['client']['company_name'];
                }
            ],
            'client_pic_name',
            'client_pic_contact',
            [
                'attribute' => 'award_date',
                'value' => function($model) {
                    return MyFormatter::asDate_Read($model['award_date']);
                }
            ],
            [
                'attribute' => 'commencement_date',
                'value' => function($model) {
                    return MyFormatter::asDate_Read($model['commencement_date']);
                }
            ],
            [
                'attribute' => 'eot_date',
                'value' => function($model) {
                    return MyFormatter::asDate_Read($model['eot_date']);
                }
            ],
            [
                'attribute' => 'handover_date',
                'value' => function($model) {
                    return MyFormatter::asDate_Read($model['handover_date']);
                }
            ],
            [
                'attribute' => 'dlp_expiry_date',
                'value' => function($model) {
                    return MyFormatter::asDate_Read($model['dlp_expiry_date']);
                }
            ],
            [
                'attribute' => 'proj_director',
                'value' => function($model) {
                    return $model['projDirector']['fullname'];
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'proj_manager',
                'value' => function($model) {
                    return $model['projManager']['fullname'];
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'proj_coordinator',
                'value' => function($model) {
                    return $model['projCoordinator']['fullname'];
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'project_engineer',
                'value' => function($model) {
                    return $model['projectEngineer']['fullname'];
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'site_engineer',
                'value' => function($model) {
                    return $model['siteEngineer']['fullname'];
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'site_manager',
                'value' => function($model) {
                    return $model['siteManager']['fullname'];
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'site_supervisor',
                'value' => function($model) {
                    return $model['siteSupervisor']['fullname'];
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'project_qs',
                'value' => function($model) {
                    return $model['projectQs']['fullname'];
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'remarks',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::tag('span', $model->remarks, ['class' => 'text-wrap']);
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'files',
                'label' => 'Tender Documents',
                'format' => 'raw',
                'value' => function($model) {
                    if (sizeof($model->files) <= 0) {
                        return " - ";
                    }

                    $displayStr = "<ul class='list-group'>";
                    foreach ($model->files as $file) {
                        $displayStr .= Html::a($file, "/working/prospect/get-file-prospect?filename=" . urlencode($file)
                                        . "&projCode=" . $model->proj_code, ['target' => "_blank", 'class' => 'list-group-item', 'title' => "Click to view"]);
                    }
                    $displayStr .= "</ul>";

                    return $displayStr;
                }
            ],
            [
                'attribute' => 'show_in_resume',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->show_in_resume ? "Yes" : "No";
                }
            ],
            [
                'attribute' => 'created_by',
                'value' => function($model) {
                    return $model['createdBy']['fullname'] . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model['created_at']);
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
            [
                'attribute' => 'updated_by',
                'value' => function($model) {
                    return $model['updatedBy'] ? $model['updatedBy']['fullname'] . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model['updated_at']) : null;
                },
                'headerOptions' => ['style' => 'width:100%'],
            ],
        ],
    ])
    ?>

</div>
