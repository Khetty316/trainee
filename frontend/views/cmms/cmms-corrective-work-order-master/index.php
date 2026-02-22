<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\cmms\CmmsCorrectiveWorkOrderMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Corrective Work Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    .grid-fit {
        table-layout: fixed;
        width: 100%;
    }
    .grid-fit th,
    .grid-fit td {
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
<div class="cmms-corrective-work-order-master-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($moduleStatus === 'superior'): ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-hover',
                'style' => 'width:100%;',
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => 'Fault List IDs',
                    'format' => 'raw',
                    'value' => function ($model) use ($moduleStatus) {
                        return Html::a(
                            'View',
                            'javascript:void(0);',
                            [
                                'class' => 'modalButtonSingle btn btn-sm btn-success mx-1',
                                'title' => 'View',
                                'data-url' => Url::to([
                                    'view-fault-list-ids',
                                    'id' => $model->id,
                                    'moduleStatus' => $moduleStatus,
                                ]),
                                'data-modaltitle' => 'View Fault Lists',
                                'aria-label' => 'View',
                            ]
                        );
                    }
                ],
                [
                    'attribute' => 'id',
                    'label' => 'CM WO ID',
                    'value' => function ($model) {
                        return $model->id ?? '-';
    //                    return $model->cmmsFaultListDetails[0]->cmmsAssetList->asset_id ?? '-';
                    }
                ],
                [
                    'attribute' => 'progress_status_id',
                    'label' => 'Progress Status',
                    'value' => function ($model) {
                        return $model->progress_status_id ?? '-';
                    }
                ],
                [
                    'attribute' => 'start_date',
                    'label' => 'Start Date',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->start_date;
                    }
                ],
                [
                    'attribute' => 'end_date',
                    'label' => 'End Date',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->end_date ?? '-';
                    }
                ],
                [
                    'attribute' => 'duration',
                    'label' => 'Duration (days)',
                    'value' => function ($model) {
                        return $model->duration ?? '-';
                    }
                ],
                [
                    'label' => 'Assigned Technician(s)',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return implode(', ', array_map(
                                fn($pic) => $pic->name,
                                $model->assignedPic
                        ));
                    },
                ],
                [
                    'label' => 'Selected Parts',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $text = implode(', ', $model->getSelectedParts());

                        return Html::tag(
                                'span',
                                Html::encode($text),
                                [
                                    'title' => $text,
                                    'data-toggle' => 'tooltip',
                                ]
                        );
    //                    $parts = [];
    //                    
    //                    foreach ($model->cmmsFaultLists as $fault) {
    //                        if ($fault->partList) {
    //                            $parts[] = Html::encode($fault->partList->inventory->brand_model);
    //                        }
    //                    }
    //                    
    //                    return $parts
    //                            ? implode('<br>', array_unique($parts))
    //                            : Html::tag('span', '-', ['class' => 'text-muted']);
                    },
                ],
                [
                    'label' => 'Selected Tools',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $text = implode(', ', $model->getSelectedTools());

                        return Html::tag(
                                'span',
                                Html::encode($text),
                                [
                                    'title' => $text,
                                    'data-toggle' => 'tooltip',
                                ]
                        );
    //                    $tools = [];

    //                    foreach ($model->cmmsFaultLists as $fault) {
    //                        if ($fault->toolList) {
    //                            $tools[] = Html::encode($fault->toolList->inventory->brand_model);
    //                        }
    //                    }
    //                    
    //                    return $tools
    //                            ? implode('<br>', array_unique($tools))
    //                            : Html::tag('span', '-', ['class' => 'text-muted']);
                    },
                ],      
                'remarks',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) use ($moduleStatus) {
                            return Html::a(
    //                                Yii::$app->formatter->asRaw('<i class="bi bi-pencil"></i>'),
                                '<i class="bi bi-pencil"></i>',
                                'javascript:void(0);',
                                [
                                    'class' => 'modalButtonSingle text-success',
                                    'title' => 'Update',
                                    'data-url' => Url::to([
                                        'update',
                                        'id' => $model->id,
                                        'moduleStatus' => $moduleStatus
                                    ]),
                                    'data-modaltitle' => 'Update Corrective Work Order Form',
    //                                    'aria-label' => 'Update',
                                ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            return Html::a(
                                '<i class="bi bi-trash"></i>',
                                Url::to(['delete', 'id' => $model->id]),
                                [
                                    'class' => 'text-danger',
                                    'title' => 'Delete',
                                    'aria-label' => 'Delete',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete this item?',
                                        'method' => 'post',
                                    ],
                                ]
                            );
                        }
                    ],
                ],
            ],
        ]); ?>
    <?php else: ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-hover',
                'style' => 'width:100%;',
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => 'Fault List IDs',
                    'format' => 'raw',
                    'value' => function ($model) use ($moduleStatus) {
                        return Html::a(
                            'View',
                            'javascript:void(0);',
                            [
                                'class' => 'modalButtonSingle btn btn-sm btn-success mx-1',
                                'title' => 'View',
                                'data-url' => Url::to([
                                    'view-fault-list-ids',
                                    'id' => $model->id,
                                    'moduleStatus' => $moduleStatus,
                                ]),
                                'data-modaltitle' => 'View Fault Lists',
                                'aria-label' => 'View',
                            ]
                        );
                    }
                ],
                [
                    'attribute' => 'id',
                    'label' => 'CM WO ID',
                    'value' => function ($model) {
                        return $model->id ?? '-';
                    }
                ],
                [
                    'attribute' => 'progress_status_id',
                    'label' => 'Progress Status',
                    'value' => function ($model) {
                        return $model->progressStatus->name ?? '-';
                    }
                ],
                [
                    'attribute' => 'start_date',
                    'label' => 'Start Date',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->start_date;
                    }
                ],
                [
                    'attribute' => 'end_date',
                    'label' => 'End Date',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->end_date ?? '-';
                    }
                ],
                [
                    'attribute' => 'duration',
                    'label' => 'Duration (days)',
                    'value' => function ($model) {
                        return $model->duration ?? '-';
                    }
                ],
                [
                    'label' => 'Assigned Technician(s)',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return implode(', ', array_map(
                                fn($pic) => $pic->name,
                                $model->assignedPic
                        ));
                    },
                ],
                [
                    'label' => 'Selected Parts',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $text = implode(', ', $model->getSelectedParts());

                        return Html::tag(
                                'span',
                                Html::encode($text),
                                [
                                    'title' => $text,
                                    'data-toggle' => 'tooltip',
                                ]
                        );
    //                    $parts = [];
    //                    
    //                    foreach ($model->cmmsFaultLists as $fault) {
    //                        if ($fault->partList) {
    //                            $parts[] = Html::encode($fault->partList->inventory->brand_model);
    //                        }
    //                    }
    //                    
    //                    return $parts
    //                            ? implode('<br>', array_unique($parts))
    //                            : Html::tag('span', '-', ['class' => 'text-muted']);
                    },
                ],
                [
                    'label' => 'Selected Tools',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $text = implode(', ', $model->getSelectedTools());

                        return Html::tag(
                                'span',
                                Html::encode($text),
                                [
                                    'title' => $text,
                                    'data-toggle' => 'tooltip',
                                ]
                        );
    //                    $tools = [];

    //                    foreach ($model->cmmsFaultLists as $fault) {
    //                        if ($fault->toolList) {
    //                            $tools[] = Html::encode($fault->toolList->inventory->brand_model);
    //                        }
    //                    }
    //                    
    //                    return $tools
    //                            ? implode('<br>', array_unique($tools))
    //                            : Html::tag('span', '-', ['class' => 'text-muted']);
                    },
                ],      
                'remarks',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) use ($moduleStatus) {
                            return Html::a(
    //                                Yii::$app->formatter->asRaw('<i class="bi bi-pencil"></i>'),
                                '<i class="bi bi-pencil"></i>',
                                'javascript:void(0);',
                                [
                                    'class' => 'modalButtonSingle text-success',
                                    'title' => 'Update',
                                    'data-url' => Url::to([
                                        'update',
                                        'id' => $model->id,
                                        'moduleStatus' => $moduleStatus
                                    ]),
                                    'data-modaltitle' => 'Update Corrective Work Order Form',
    //                                    'aria-label' => 'Update',
                                ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            return Html::a(
                                '<i class="bi bi-trash"></i>',
                                Url::to(['delete', 'id' => $model->id]),
                                [
                                    'class' => 'text-danger',
                                    'title' => 'Delete',
                                    'aria-label' => 'Delete',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete this item?',
                                        'method' => 'post',
                                    ],
                                ]
                            );
                        }
                    ],
                ],
            ],
        ]); ?>
    <?php endif; ?>
</div>
<script>
    $(document).on('click', 'a.modalButtonSingle', function (e) {
        e.preventDefault();

        const url = $(this).attr('data-url');
        const title = $(this).data('modaltitle');

        $('#myModal').modal('show')             // <-- point to existing modal
            .find('#myModalContent')
            .load(url);
    });
</script>