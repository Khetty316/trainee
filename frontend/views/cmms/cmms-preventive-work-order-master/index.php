<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\cmms\CmmsPreventiveWorkOrderMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Preventive Work Order Schedule';
$this->params['breadcrumbs'][] = $this->title;
?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<div class="cmms-preventive-work-order-master-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ($moduleIndex === 'superior'): ?>
        <p>
            <?= Html::a('Create PM Schedule', ['create'], ['class' => 'btn btn-success']) ?>
            <?=
            Html::a(
                    'User Manual <i class="fas fa-book"></i>',
                    ['user-manual-inventory'],
                    ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
            )
            ?>
        </p>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'label' => 'Asset ID',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->cmmsAssetList->asset_id;
                    },
                ],
                [
                    'attribute' => 'frequency_id',
                    'label' => 'Frequency',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $frequencyID = $model->frequency_id;
                        $frequencyName = \frontend\models\cmms\RefFrequency::find()
                                ->select('name')
                                ->where(['id' => $frequencyID])
                                ->scalar();
                        return $frequencyName ?? '-';
                    }
                ],
                [
                    'label' => 'Predicted Dates',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        if (!$model->commencement_date || !$model->frequency_id) {
                            return '-';
                        }

                        $frequency = frontend\models\cmms\RefFrequency::findOne($model->frequency_id);

                        if (!$frequency) {
                            return '-';
                        }

                        $freqName = strtolower(trim($frequency->name));
                        $intervalSpec = null;
                        switch ($freqName) {
                            case 'weekly':
                                $intervalSpec = 'P1W';
                                break;

                            case 'monthly':
                                $intervalSpec = 'P1M';
                                break;

                            case 'quarterly':
                                $intervalSpec = 'P3M';
                                break;

                            case 'half-yearly':
                                $intervalSpec = 'P6M';
                                break;

                            case 'yearly':
                                $intervalSpec = 'P1Y';
                                break;

                            default:
                                $intervalSpec = null;
                                break;
                        }

                        if ($intervalSpec === null) {
                            return '-';
                        }

                        $dates = [];
                        $date = new \DateTime($model->commencement_date);
                        $holidayDates = frontend\models\working\leavemgmt\LeaveHolidays::find()
                                ->select('holiday_date')
                                ->column();

                        // retrieve ids of all the assigned PIC(s)
                        $requestorIDs = [];
                        foreach ($model->assignedPic as $requestor) {
                            $userID = \common\models\User::find()
                                    ->select('id')
                                    ->where(['fullname' => $requestor->name])
                                    ->scalar();
                            $requestorIDs[] = $userID;
                        }

                        $leaveRanges = \frontend\models\office\leave\LeaveMaster::find()
                                ->select(['start_date', 'end_date'])
                                ->where(['requestor_id' => $requestorIDs])
                                ->asArray()
                                ->all();

                        $leaveDates = [];

                        foreach ($leaveRanges as $range) {
                            $start = new \DateTime($range['start_date']);
                            $end   = new \DateTime($range['end_date']);
                            $end->modify('+1 day'); // include end date

                            $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

                            foreach ($period as $d) {
                                $leaveDates[] = $d->format('Y-m-d');
                            }
                        }

                        $excludedDates = array_unique(array_merge($holidayDates, $leaveDates));

                        $excludedDates = array_map(function ($d) {
                            return substr((string)$d, 0, 10);
                        }, $excludedDates);
                        $currentYear = date('Y');
    //                    $cursor = new \DateTime($model->start_time);
    //
    //                    $startYear = (int)$cursor->format('Y');
    //                    $yearEnd = new \DateTime(($startYear + 1) . '-01-01 00:00:00');

                        $interval = new \DateInterval($intervalSpec);

                        while (true) {
    //                        $cursor->add($interval);
    //                        $formatted = $cursor->format('Y-m-d');
    //
    //                        if ($cursor >= $yearEnd) {
    //                            break;
    //                        }
    //
    //                        if (in_array($formatted, $excludedDates)) {
    //                            continue;
    //                        }
    //
    //                        $dates[] = $cursor->format('d M Y');
                            $date->add($interval);
                            $formatted = $date->format('Y-m-d');
    //                        
                            if ($date->format('Y') != $currentYear) {
                                break;
                            }

                            if (in_array($formatted, $excludedDates)) {
                                continue;
                            }
                            $dates[] = $date->format('d M Y');
                        }

                        $dateList = implode('<br>', $dates);

                        // Unique button id per row
                        $btnId = 'pred-dates-btn-' . $model->id;

                        return Html::button(
                            'View dates',
                            [
                                'id' => $btnId,
                                'type' => 'button',
                                'class' => 'btn btn-sm btn-outline-primary',
                                'data-toggle' => 'popover',
                                'data-html' => 'true',
                                'data-content' => $dateList,
                                'data-trigger' => 'focus',
                                'data-placement' => 'auto',
                            ]
                        );
                    }
                ],
                [
                    'attribute' => 'progress_status_id',
                    'label' => 'Progress Status',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $progressStatusID = $model->progress_status_id;
                        $progressStatusName = \frontend\models\cmms\RefProgressStatus::find()
                                ->select('name')
                                ->where(['id' => $progressStatusID])
                                ->scalar();
                        return $progressStatusName ?? '-';
                    }
                ],
                [
                    'label' => 'PM WO Form',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) use ($moduleIndex) {
                        return Html::a('Open Form', [
                            'pm-wo-form', 'id' => $model->id, 'moduleIndex' => $moduleIndex
                                ], 
                                ['class' => 'btn btn-success']);
                    }
                ],      
                [
                    'label' => 'PM WO Summary',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) use ($moduleIndex) {
                        return Html::a(
                            'View',
                            'javascript:void(0);',
                            [
                                'class' => 'modalButtonSingle btn btn-sm btn-success mx-1',
                                'title' => 'View',
                                'data-url' => Url::to([
                                    'view-pm-wo-summary',
                                    'id' => $model->id,
                                    'moduleIndex' => $moduleIndex,
                                ]),
                                'data-modaltitle' => 'View PM WO Summary',
                                'aria-label' => 'View',
                            ]
                        );
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) use ($moduleIndex) {
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
                                        'moduleIndex' => $moduleIndex
                                    ]),
                                    'data-modaltitle' => 'Update PM Work Order Form',
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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Asset ID',
                'contentOptions' => [
                    'style' => '
                        max-width: 250px;
                        white-space: normal;
                        word-break: break-word;
                    ',
                ],
                'value' => function ($model) {
                    return $model->cmmsAssetList->asset_id;
                },
            ],
            [
                'attribute' => 'frequency_id',
                'label' => 'Frequency',
                'contentOptions' => [
                    'style' => '
                        max-width: 250px;
                        white-space: normal;
                        word-break: break-word;
                    ',
                ],
                'value' => function ($model) {
                    $frequencyID = $model->frequency_id;
                    $frequencyName = \frontend\models\cmms\RefFrequency::find()
                            ->select('name')
                            ->where(['id' => $frequencyID])
                            ->scalar();
                    return $frequencyName ?? '-';
                }
            ],
            [
                'label' => 'Predicted Dates',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => '
                        max-width: 250px;
                        white-space: normal;
                        word-break: break-word;
                    ',
                ],
                'value' => function ($model) {
                    if (!$model->commencement_date || !$model->frequency_id) {
                        return '-';
                    }
                    
                    $frequency = frontend\models\cmms\RefFrequency::findOne($model->frequency_id);
                    
                    if (!$frequency) {
                        return '-';
                    }
                    
                    $freqName = strtolower(trim($frequency->name));
                    $intervalSpec = null;
                    switch ($freqName) {
                        case 'weekly':
                            $intervalSpec = 'P1W';
                            break;
                           
                        case 'monthly':
                            $intervalSpec = 'P1M';
                            break;
                        
                        case 'quarterly':
                            $intervalSpec = 'P3M';
                            break;
                        
                        case 'half-yearly':
                            $intervalSpec = 'P6M';
                            break;
                        
                        case 'yearly':
                            $intervalSpec = 'P1Y';
                            break;
                        
                        default:
                            $intervalSpec = null;
                            break;
                    }
                    
                    if ($intervalSpec === null) {
                        return '-';
                    }
                    
                    $dates = [];
                    $date = new \DateTime($model->commencement_date);
                    $holidayDates = frontend\models\working\leavemgmt\LeaveHolidays::find()
                            ->select('holiday_date')
                            ->column();
                    
                    // retrieve ids of all the assigned PIC(s)
                    $requestorIDs = [];
                    foreach ($model->assignedPic as $requestor) {
                        $userID = \common\models\User::find()
                                ->select('id')
                                ->where(['fullname' => $requestor->name])
                                ->scalar();
                        $requestorIDs[] = $userID;
                    }
                    
                    $leaveRanges = \frontend\models\office\leave\LeaveMaster::find()
                            ->select(['start_date', 'end_date'])
                            ->where(['requestor_id' => $requestorIDs])
                            ->asArray()
                            ->all();
                    
                    $leaveDates = [];

                    foreach ($leaveRanges as $range) {
                        $start = new \DateTime($range['start_date']);
                        $end   = new \DateTime($range['end_date']);
                        $end->modify('+1 day'); // include end date

                        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

                        foreach ($period as $d) {
                            $leaveDates[] = $d->format('Y-m-d');
                        }
                    }
                    
                    $excludedDates = array_unique(array_merge($holidayDates, $leaveDates));
                    
                    $excludedDates = array_map(function ($d) {
                        return substr((string)$d, 0, 10);
                    }, $excludedDates);
                    $currentYear = date('Y');
//                    $cursor = new \DateTime($model->start_time);
//
//                    $startYear = (int)$cursor->format('Y');
//                    $yearEnd = new \DateTime(($startYear + 1) . '-01-01 00:00:00');

                    $interval = new \DateInterval($intervalSpec);
                    
                    while (true) {
//                        $cursor->add($interval);
//                        $formatted = $cursor->format('Y-m-d');
//
//                        if ($cursor >= $yearEnd) {
//                            break;
//                        }
//
//                        if (in_array($formatted, $excludedDates)) {
//                            continue;
//                        }
//
//                        $dates[] = $cursor->format('d M Y');
                        $date->add($interval);
                        $formatted = $date->format('Y-m-d');
//                        
                        if ($date->format('Y') != $currentYear) {
                            break;
                        }
                        
                        if (in_array($formatted, $excludedDates)) {
                            continue;
                        }
                        $dates[] = $date->format('d M Y');
                    }
                    
                    $dateList = implode('<br>', $dates);
                    
                    // Unique button id per row
                    $btnId = 'pred-dates-btn-' . $model->id;

                    return Html::button(
                        'View dates',
                        [
                            'id' => $btnId,
                            'type' => 'button',
                            'class' => 'btn btn-sm btn-outline-primary',
                            'data-toggle' => 'popover',
                            'data-html' => 'true',
                            'data-content' => $dateList,
                            'data-trigger' => 'focus',
                            'data-placement' => 'auto',
                        ]
                    );
                }
            ],
            [
                'attribute' => 'progress_status_id',
                'label' => 'Progress Status',
                'contentOptions' => [
                    'style' => '
                        max-width: 250px;
                        white-space: normal;
                        word-break: break-word;
                    ',
                ],
                'value' => function ($model) {
                    $progressStatusID = $model->progress_status_id;
                    $progressStatusName = \frontend\models\cmms\RefProgressStatus::find()
                            ->select('name')
                            ->where(['id' => $progressStatusID])
                            ->scalar();
                    return $progressStatusName ?? '-';
                }
            ],
            [
                'label' => 'PM WO Form',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => '
                        max-width: 250px;
                        white-space: normal;
                        word-break: break-word;
                    ',
                ],
                'value' => function ($model) use ($moduleIndex) {
                    return Html::a('Open Form', [
                        'pm-wo-form', 'id' => $model->id, 'moduleIndex' => $moduleIndex
                            ], 
                            ['class' => 'btn btn-success']);
                }
            ],      
            [
                'label' => 'PM WO Summary',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => '
                        max-width: 250px;
                        white-space: normal;
                        word-break: break-word;
                    ',
                ],
                'value' => function ($model) use ($moduleIndex) {
                    return Html::a(
                        'View',
                        'javascript:void(0);',
                        [
                            'class' => 'modalButtonSingle btn btn-sm btn-success mx-1',
                            'title' => 'View',
                            'data-url' => Url::to([
                                'view-pm-wo-summary',
                                'id' => $model->id,
                                'moduleIndex' => $moduleIndex,
                            ]),
                            'data-modaltitle' => 'View PM WO Summary',
                            'aria-label' => 'View',
                        ]
                    );
                }
            ],
        ],
    ]); ?>
    <?php endif; ?>
</div>
<?php 
    $this->registerJs(<<<JS
        function initPredictedDatesPopovers(){
          $('[data-toggle="popover"]').popover({
            html: true,
            trigger: 'focus',
            placement: 'auto',
            container: 'body'
          });
        }

        initPredictedDatesPopovers();

        $(document).on('pjax:end', function() {
          initPredictedDatesPopovers();
        });
    JS);
?>
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