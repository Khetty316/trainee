<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\cmms\CmmsAssetFaults;
use frontend\models\cmms\RefCmmsStatus;
use yii\widgets\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\cmms\CmmsFaultListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($moduleIndex === 'personal') {
    $pageName = 'Personal';
} else if ($moduleIndex === 'superior') {
    $pageName = 'Superior';
}

$this->title = 'Faults List';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<php
    use yii\bootstrap4\Modal;

    Modal::begin([
        'id' => 'modalSingle',
        'size' => Modal::SIZE_LARGE,
    ]);
    echo '<h5 id="modalSingleTitle"></h5>';
    echo '<div id="modalSingleContent"></div>';
    Modal::end();
?>-->

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div id="loading-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
    <div style="text-align:center; color:white;">
        <div style="font-size: 48px; margin-bottom: 20px;">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
        <h3 style="margin:0; font-weight:bold;">Processing CM submissions...</h3>
        <p style="margin-top:10px; font-size:16px;">Please wait, do not close or refresh this page.</p>
        <div id="progress-info" style="margin-top:15px; font-size:14px; background:rgba(255,255,255,0.1); padding:10px; border-radius:5px;">
            <span id="progress-text">Initializing...</span>
        </div>
    </div>
</div>

<div class="cmms-fault-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($pageStatus === 'active'): ?>
        <?= $this->render('_faultlistNavBar', ['module' => $moduleIndex, 'pageKey' => '1']) ?>
    <?php elseif ($pageStatus === 'all'): ?>
        <?= $this->render('_faultlistNavBar', ['module' => $moduleIndex, 'pageKey' => '2']) ?>
            <!--<php else: ?>-->
                <!--<? $this->render('_faultlistNavBar', ['module' => $moduleIndex, 'pageKey' => '3']) ?>-->
    <?php endif; ?>

    <p>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-inventory'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
            <?php if ($moduleIndex === 'superior'): ?>
            <?=
            Html::button('Select for CM Work Order', [
                'class' => 'btn btn-success float-right mb-2 mr-1',
                'id' => 'approve-selected'
            ])
            ?>
        <?php endif; ?>
    </p>
    <?php if ($moduleIndex === 'superior'): ?>
        <?php \yii\widgets\Pjax::begin(['id' => 'pjax-fault-grid']); ?>
        <?= Html::hiddenInput('selectAll', '0', ['id' => 'selectAllInput']) ?>
        <?= Html::hiddenInput('excludedIds', '', ['id' => 'excludedIdsInput']) ?>
        <?= Html::hiddenInput('selectedIds', '', ['id' => 'selectedIdsInput']) ?>
                    <!--<php if ($pageStatus === 'active' || $pageStatus === 'all'): ?>-->
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
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'asset_id',
                    'label' => 'Asset ID',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->fault_asset_id ?? '-';
                        //                    return $model->cmmsFaultListDetails[0]->cmmsAssetList->asset_id ?? '-';
                    }
                ],
                [
                    'attribute' => 'machine_priority_id',
                    'label' => 'Priority Class',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $priorityID = $model->machine_priority_id;
                        $priorityName = \frontend\models\cmms\RefMachinePriority::find()
                                ->select('name')
                                ->where(['id' => $priorityID])
                                ->scalar();
                        return $priorityName ?? '-';
                    }
                ],
                [
                    'attribute' => 'fault_type',
                    'label' => 'Fault Type',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->fault_type ?? '-';
                    }
                ],
                [
                    'attribute' => 'fault_primary_detail',
                    'label' => 'Fault Primary Description',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->fault_primary_detail ?? '-';
                    }
                ],
                [
                    'attribute' => 'fault_secondary_detail',
                    'label' => 'Fault Secondary Description',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->fault_secondary_detail ?? '-';
                    }
                ],
                [
                    'attribute' => 'reported_at',
                    'label' => 'Reported At',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->reported_at ?? '-';
                    }
                ],
                //            'reported_by',
                [
                    'attribute' => 'reported_by',
                    'label' => 'Reported By',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $user = User::findOne(['id' => $model->reported_by]);
                        return $user->fullname ?? '-';
                    }
                ],
                [
                    'attribute' => 'reviewed_by',
                    'label' => 'Reviewed By',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $user = User::findOne(['id' => $model->reviewed_by]);
                        return $user->fullname ?? '-';
                    }
                ],
                [
                    'attribute' => 'reviewed_at',
                    'label' => 'Reviewed At',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->reviewed_at ?? '-';
                    }
                ],
                [
                    'attribute' => 'active_sts',
                    'label' => 'Active?',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->active_sts ? 'Yes' : 'No';
                    }
                ],
//                [
//                    'attribute' => 'part_list_id',
//                    'label' => 'Selected Part',
//                    'contentOptions' => [
//                        'style' => '
//                            max-width: 250px;
//                            white-space: normal;
//                            word-break: break-word;
//                        ',
//                    ],
////                    'value' => function ($model) {
////                        if ($model->partList) {
////                            return $model->partList->name;
////                        }
////                        return '-';
////                    },
//                    'value' => function ($model) {
//                        $partList = \frontend\models\cmms\CmmsWoMaterialRequestDetails::findAll([
//                            'part_or_tool' => 1,
//                            'fault_id' => $model->id,
//                            'active_sts' => 1
//                        ]);
//                        print_r($model->id);
//                        if (!empty($partList)) {
//                            print_r($model->id);
//                            return implode(', ', array_map(fn($p) => $p->model_type, $partList));
//                        }
//                        return '-';
//                    },
//                ],
//                [
//                    'attribute' => 'tool_list_id',
//                    'label' => 'Selected Tool',
//                    'contentOptions' => [
//                        'style' => '
//                            max-width: 250px;
//                            white-space: normal;
//                            word-break: break-word;
//                        ',
//                    ],
//                    'value' => function ($model) {
//                        $toolList = \frontend\models\cmms\CmmsWoMaterialRequestDetails::findAll([
//                            'part_or_tool' => 2,
//                            'fault_id' => $model->id,
//                            'active_sts' => 1
//                        ]);
//                        if (!empty($toolList)) {
//                            print_r($model->id);
//                            return implode(', ', array_map(fn($p) => $p->model_type, $toolList));
//                        }
//                        return '-';
//                    },
//                ],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => Html::tag('div', 'Select All for CM', ['style' => 'margin-bottom:5px;']) .
                    Html::checkbox('select_all', false, ['id' => 'select-all']),
                    'headerOptions' => ['class' => 'col-sm-2 text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'content' => function ($model) {
                        // STATUS: APPROVED → show span
                        if ($model->status == RefCmmsStatus::$STATUS_WORK_ORDER_CREATION) {
                            $user = User::findOne(['id' => $model->updated_by]);

                            return Html::tag(
                                            'span',
                                            'Reviewed and selected for CM Work Order',
                                            ['class' => 'text-success']
                                    );
                        }

                        // STATUS: SCREENING → show checkbox
                        if ($model->status == RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION) {
                            return Html::checkbox(
                                            'cm_ids[]',
                                            false,
                                            [
                                                'value' => $model->id,
                                                'class' => 'my-checkbox approve-cm',
                                                'data-id' => $model->id,
                                            ]
                                    );
                        }

                        return null;
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) use ($moduleIndex) {
                            if ($model->status == RefCmmsStatus::$STATUS_WORK_ORDER_CREATION) {
                                return '';
                            }
                            return Html::a(
                                    '<i class="bi bi-eye"></i>',
                                    'javascript:void(0);',
                                    [
                                        'class' => 'modalButtonSingle text-success',
                                        'title' => 'View',
                                        'data-url' => Url::to([
                                            'view',
                                            'id' => $model->id,
                                            'moduleIndex' => $moduleIndex,
                                        ]),
                                        'data-modaltitle' => 'View Fault Details Form',
                                        'aria-label' => 'View',
                                    ]
                            );
                        },
                        'update' => function ($url, $model) use ($moduleIndex) {
                            if ($model->status == RefCmmsStatus::$STATUS_WORK_ORDER_CREATION) {
                                return '';
                            }
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
                                            'moduleIndex' => $moduleIndex,
                                        ]),
                                        'data-modaltitle' => 'Update Fault Details Form',
//                                    'aria-label' => 'Update',
                                    ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            if ($model->status == RefCmmsStatus::$STATUS_WORK_ORDER_CREATION) {
                                return '';
                            }

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
        ]);
        ?>
        <?php \yii\widgets\Pjax::end(); ?>
    <?php elseif ($moduleIndex === 'personal'): ?>
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2  m-0 ">Upload By Template:</legend>
            <div class="container-fluid">
                <div class="row">
                    <div class="">
                        <?php
                        $form = ActiveForm::begin([
                            'action' => ['upload-excel'],
                            'options' => ['enctype' => 'multipart/form-data'],
                        ]);

                        echo Html::fileInput('excelTemplate', null, [
                            'accept' => '.xlsx', 'required' => true,
                        ]);
                        echo Html::submitButton(
                                'Upload Excel <i class="fas fa-upload"></i>',
                                ['class' => 'btn btn-success mb-2 mt-2']);
                        ActiveForm::end();
                        ?>
                        <?php
//                        $canDownload = MyCommonFunction::checkRoles([AuthItem::ROLE_CMMS_Superior]);
//                        
//                        echo $canDownload 
//                                ? Html::a(
//                                'Download Template <i class="fas fa-download"></i>',
//                                ['download-asset-template'],
//                                [
//                                    'class' => 'btn btn-primary',
//                                    'data-pjax' => 0, // ✅ disables PJAX
//                                    'target' => '_blank', // optional: avoids locking current page
//                                ]
//                            )
//                            : Html::tag(
//                                'span',
//                                'Download Template <i class="fas fa-download"></i>',
//                                [
//                                    'class' => 'btn btn-primary disabled',
//                                    'aria-disabled' => 'true',
//                                ]
//                            );
                        
                        echo Html::a(
                                'Download Template <i class="fas fa-download"></i>',
                                ['download-asset-template'],
                                [
                                    'class' => 'btn btn-primary',
                                    'data-pjax' => 0, // ✅ disables PJAX
                                    'target' => '_blank', // optional: avoids locking current page
                                ]
                            )
                        ?>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="report-fault">
            <?=
            Html::a(
                    '<i class="fas fa-plus-circle"></i>',
                    'javascript:void(0);',
                    [
                        'class' => 'modalButtonSingle btn btn-sm btn-success mb-1',
                        'data-url' => Url::to([
                            'create',
                            'moduleIndex' => $moduleIndex
                                //                                    'id' => $model->cmmsFaultListDetails[0]->cmms_asset_list_id ?? null
                        ]),
                        'data-modaltitle' => 'Fault Details Form',
                    ]
            );
            ?>
        </div>
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
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'fault_asset_id',
                    'label' => 'Asset ID',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->fault_asset_id ?? '-';
                    }
                ],
                [
                    'attribute' => 'machine_priority_id',
                    'label' => 'Priority Class',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $priorityID = $model->machine_priority_id;
                        $priorityName = \frontend\models\cmms\RefMachinePriority::find()
                                ->select('name')
                                ->where(['id' => $priorityID])
                                ->scalar();
                        return $priorityName ?? '-';
                    }
                ],
                [
                    'attribute' => 'fault_type',
                    'label' => 'Fault Type',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->fault_type ?? '-';
                    }
                ],
                [
                    'attribute' => 'fault_primary_detail',
                    'label' => 'Fault Primary Description',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->fault_primary_detail ?? '-';
                    }
                ],
                [
                    'attribute' => 'fault_secondary_detail',
                    'label' => 'Fault Secondary Description',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->fault_secondary_detail ?? '-';
                    }
                ],
                [
                    'attribute' => 'reported_at',
                    'label' => 'Reported At',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->reported_at ?? '-';
                    }
                ],
                [
                    'attribute' => 'reported_by',
                    'label' => 'Reported By',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $user = User::findOne(['id' => $model->reported_by]);
                        return $user->fullname ?? '-';
                    }
                ],
                [
                    'attribute' => 'reviewed_by',
                    'label' => 'Reviewed By',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        $user = User::findOne(['id' => $model->reviewed_by]);
                        return $user->fullname ?? '-';
                    }
                ],
                [
                    'attribute' => 'reviewed_at',
                    'label' => 'Reviewed At',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->reviewed_at ?? '-';
                    }
                ],
                [
                    'attribute' => 'active_sts',
                    'label' => 'Active?',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'value' => function ($model) {
                        return $model->active_sts ? 'Yes' : 'No';
                    }
                ],
                [
                    'label' => 'Fault Description Status',
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => '
                            max-width: 250px;
                            white-space: normal;
                            word-break: break-word;
                        ',
                    ],
                    'content' => function ($model) {
                        // STATUS: APPROVED → show span
                        if ($model->status == RefCmmsStatus::$STATUS_WORK_ORDER_CREATION) {
//                            $user = User::findOne($model->updated_by);
                            return Html::tag(
                                            'span',
                                            'Reviewed and selected for CM Work Order',
//                                    . Html::encode($user->username) .
//                                '<br>@ ' . MyFormatter::asDateTime_ReaddmYHi($model->updated_at),
                                            ['class' => 'text-success']
                                    );
                        }

                        // STATUS: SCREENING → show checkbox
                        if ($model->status == RefCmmsStatus::$STATUS_SCREENING_AND_PRIORITISATION) {
                            return Html::tag(
                                            'span',
                                            'Submitted for screening and prioritisation',
                                            ['class' => 'text-danger']
                                    );
                        }
                        return '';
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) use ($moduleIndex) {
                            if ($model->status == RefCmmsStatus::$STATUS_WORK_ORDER_CREATION) {
                                return '';
                            }
                            return Html::a(
                                    Yii::$app->formatter->asRaw('<i class="bi bi-eye"></i>'),
                                    'javascript:void(0);',
                                    [
                                        'class' => 'modalButtonSingle text-success',
                                        'title' => 'View',
                                        'data-url' => Url::to([
                                            'view',
                                            'id' => $model->id,
                                            'moduleIndex' => $moduleIndex,
                                        ]),
                                        'data-modaltitle' => 'View Fault Details Form',
                                        'aria-label' => 'View',
                                    ]
                            );
                        },
                        'update' => function ($url, $model) use ($moduleIndex) {
                            if ($model->status == RefCmmsStatus::$STATUS_WORK_ORDER_CREATION) {
                                return '';
                            }
                            return Html::a(
                                    Yii::$app->formatter->asRaw('<i class="bi bi-pencil"></i>'),
                                    //                            '<span class="bi bi-pencil"></span>',
                                    'javascript:void(0);',
                                    [
                                        'class' => 'modalButtonSingle text-success',
                                        'title' => 'Update',
                                        'data-url' => Url::to([
                                            'update',
                                            'id' => $model->id,
                                            'moduleIndex' => $moduleIndex,
                                        ]),
                                        'data-modaltitle' => 'Update Fault Details Form',
                                        'aria-label' => 'Update',
                                    ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            if ($model->status == RefCmmsStatus::$STATUS_WORK_ORDER_CREATION) {
                                return '';
                            }

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
        ]);
        ?>
    <?php endif; ?>
</div>
<style>
    /* Loading Overlay Styles */
    #loading-overlay {
        display: none;
    }

    #loading-overlay.active {
        display: flex !important;
    }

    /* Prevent scrolling when loading */
    body.loading-active {
        overflow: hidden;
    }

    /* Pulse animation for loading spinner */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    #loading-overlay .fa-spinner {
        animation: pulse 1.5s ease-in-out infinite;
    }
</style>
<script>
    $(document).on('click', 'a.modalButtonSingle', function (e) {
        e.preventDefault();

        const url = $(this).attr('data-url');
        const title = $(this).data('modaltitle');

        $('#myModal').modal('show')             // <-- point to existing modal
                .find('#myModalContent')
                .load(url);

//        $('#myModal .modal-title').html(title); // <-- update title
    });

    (function () {
        const pjaxContainer = '#pjax-fault-grid';
        let selectedIds = new Set();
        let selectAllActive = false;
        let excludedIds = new Set();

        // ✅ Loading overlay functions
        function showLoading(message = 'Processing...') {
            $('#loading-overlay').addClass('active');
            $('body').addClass('loading-active');
            $('#progress-text').text(message);
        }

        function hideLoading() {
            $('#loading-overlay').removeClass('active');
            $('body').removeClass('loading-active');
        }

        function updateLoadingMessage(message) {
            $('#progress-text').text(message);
        }

        // Prevent page navigation during processing
        let isProcessing = false;

        window.addEventListener('beforeunload', function (e) {
            if (isProcessing) {
                e.preventDefault();
                e.returnValue = 'Processing is in progress. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        function reapplyChecks() {
            const $checkboxes = $(pjaxContainer).find('.my-checkbox');

            $checkboxes.each(function () {
                const id = String($(this).val());
                if (selectAllActive) {
                    $(this).prop('checked', !excludedIds.has(id));
                } else {
                    $(this).prop('checked', selectedIds.has(id));
                }
            });

            $(pjaxContainer).find('#select-all').prop('checked', selectAllActive);
        }

        $(document).on('change', pjaxContainer + ' .my-checkbox', function () {
            const id = String($(this).val());
            if (selectAllActive) {
                if (this.checked)
                    excludedIds.delete(id);
                else
                    excludedIds.add(id);
            } else {
                if (this.checked)
                    selectedIds.add(id);
                else
                    selectedIds.delete(id);
                const visible = $(pjaxContainer).find('.my-checkbox:visible');
                const allChecked = visible.length > 0 && visible.length === visible.filter(':checked').length;
                $(pjaxContainer).find('#select-all').prop('checked', allChecked);
            }
        });

        $(document).on('change', '#select-all', function () {
            const checked = this.checked;
            selectAllActive = checked;

            if (checked) {
                excludedIds.clear();
                selectedIds.clear();
                $(pjaxContainer).find('.my-checkbox').prop('checked', true);
            } else {
                excludedIds.clear();
                selectedIds.clear();
                $(pjaxContainer).find('.my-checkbox').prop('checked', false);
            }
        });

//    $(document).on('pjax:end', function () {
//        setTimeout(reapplyChecks, 50);
//    });

// previous version
//    function reapplyChecks() {
//        const $checkboxes = $(pjaxContainer).find('.my-checkbox');
//
//        $checkboxes.each(function () {
//            const id = String($(this).val());
//            if (selectAllActive) {
//                $(this).prop('checked', !excludedIds.has(id));
//            } else {
//                $(this).prop('checked', selectedIds.has(id));
//            }
//        });
//
//        $(pjaxContainer).find('#select-all').prop('checked', selectAllActive);
//    }
//
//    $(document).on('change', pjaxContainer + ' .my-checkbox', function () {
//        const id = String($(this).val());
//        if (selectAllActive) {
//            if (this.checked) excludedIds.delete(id);
//            else excludedIds.add(id);
//        } else {
//            if (this.checked) selectedIds.add(id);
//            else selectedIds.delete(id);
//            const visible = $(pjaxContainer).find('.my-checkbox:visible');
//            const allChecked = visible.length > 0 && visible.length === visible.filter(':checked').length;
//            $(pjaxContainer).find('#select-all').prop('checked', allChecked);
//        }
//    });
//
//    $(document).on('change', '#select-all', function () {
//        const checked = this.checked;
//        selectAllActive = checked;
//
//        if (checked) {
//            excludedIds.clear();
//            selectedIds.clear();
//            $(pjaxContainer).find('.my-checkbox').prop('checked', true);
//        } else {
//            excludedIds.clear();
//            selectedIds.clear();
//            $(pjaxContainer).find('.my-checkbox').prop('checked', false);
//        }
//    });
        $(document).on('pjax:end', function () {
            setTimeout(reapplyChecks, 50);
        });

        // Approve selected with enhanced loading feedback
        $(document).on('click', '#approve-selected', function (e) {
            e.preventDefault();

            let dataToSend = {};
            let confirmMessage = '';
            let itemCount = 0;

            if (selectAllActive) {
                dataToSend.selectAll = true;
                dataToSend.excludedIds = Array.from(excludedIds);

                const excludeCount = excludedIds.size;
                confirmMessage = excludeCount > 0
                        ? `Are you sure you want to approve ALL faults except ${excludeCount} unchecked item(s)?`
                        : 'Are you sure you want to approve ALL active faults?';

                itemCount = 'all';
            } else {
                const ids = Array.from(selectedIds);
                if (ids.length === 0) {
                    alert('Please select at least one fault to approve.');
                    return;
                }
                dataToSend.ids = ids;
                itemCount = ids.length;
                confirmMessage = `Are you sure you want to approve ${ids.length} selected fault(s)?`;
            }

            console.log('Payload:', dataToSend);

            if (!confirm(confirmMessage))
                return;

            const $button = $(this);
            const originalText = $button.html();

            // Disable button and show loading
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            isProcessing = true;

            // Show loading overlay
            if (itemCount === 'all') {
                showLoading('Processing all faults... This may take a while.');
            } else {
                showLoading(`Processing ${itemCount} fault(s)...`);
            }

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['approve-cm-submission']) ?>',
                type: 'POST',
                data: dataToSend,
                dataType: 'json',
                timeout: 300000, // 5 minutes timeout for large batches
                success: function (response) {
                    console.log('Work orders:', response.count);
                    updateLoadingMessage('CM Work Order generation successful! Reloading page...');

                    // ✅ Clear selections
                    selectedIds.clear();
                    selectAllActive = false;
                    excludedIds.clear();
                    isProcessing = false;

                    // ✅ Short delay before reload to show success message
                    setTimeout(function () {
                        location.reload(); // triggers FlashHandler::success()
                    }, 1000);
                },
                error: function (xhr, status, error) {
                    isProcessing = false;
                    hideLoading();

                    let errorMessage = 'Server error while approving: ';

                    // ✅ Better error handling
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += xhr.responseJSON.message;
                    } else if (status === 'timeout') {
                        errorMessage += 'Request timeout. The operation may still be processing. Please refresh the page.';
                    } else if (xhr.status === 0) {
                        errorMessage += 'Network error. Please check your connection.';
                    } else {
                        errorMessage += error || 'Unknown error';
                    }

                    alert(errorMessage);
                    $button.prop('disabled', false).html(originalText);
                },
                complete: function () {
                    // Note: don't hide loading here because we're reloading the page on success
                    // add another ajax function to call the create work order function?
                }
            });
        });

    })();
</script>
