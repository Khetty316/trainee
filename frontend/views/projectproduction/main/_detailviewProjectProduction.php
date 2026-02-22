<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\User;
?>
<style>
    .border-thick {
        border-width: 2px !important;
    }

</style>
<div class="project-production-master-view">
    <?=
    DetailView::widget(array_merge(Yii::$app->params['detailViewOption28'], [
        'model' => $model, // Model = ProjectProductionMaster
        'attributes' => [
            'name',
            [
                'attribute' => 'project_production_code',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->project_production_code . ' <i class="fas fa-external-link-alt"></i>', ['/production/production/view-production-main', 'id' => $model->id], ['target' => '_blank']);
                }
            ],
            [
                'attribute' => 'quotation_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->quotation->quotation_display_no . ' <i class="fas fa-external-link-alt"></i>', ['/projectquotation/view-projectquotation', 'id' => $model->quotation_id], ['target' => '_blank']);
                }
            ],
            [
                'attribute' => 'client_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->client->company_name ?? null;
                }
            ],
            'remark:ntext',
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    return ($model->createdBy->fullname ?? " - ") . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    return (User::findOne($model->updated_by)->fullname ?? " - ") . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                }
            ],
            [
                'attribute' => 'current_target_date',
                'format' => 'raw',
                'value' => function ($model) {
                    $details = \frontend\models\ProjectProduction\ProjProdTargetDateTrial::find()
                            ->where(['proj_prod_master_id' => $model->id])
                            ->orderBy(['created_at' => SORT_DESC])
                            ->all();

                    if (!$details) {
                        $html = '<span class="text-muted">-</span>';
                    } else {

                        $html = '<div class="table-responsive">';
                        $html .= '<table class="table table-bordered">';
                        $html .= '<thead>';
                        $html .= '<tr>';
                        $html .= '<th width="20%">Target Date</th>';
                        $html .= '<th width="40%">Set By</th>';
                        $html .= '<th width="40%">Remark</th>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '<tbody>';

                        foreach ($details as $index => $detail) {
                            $isLatest = $index === 0 ? 'table-success fw-bold' : '';
                            $html .= '<tr class="' . $isLatest . '">';

                            // Target Date
                            $html .= '<td>';
                            $html .= MyFormatter::asDate_Read($detail->target_date);
                            $html .= '</td>';

                            // Set By
                            $html .= '<td>';
                            $html .= $detail->createdBy ? (Html::encode($detail->createdBy->fullname) . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($detail->created_at)) : '<em class="text-muted">-</em>';
                            $html .= '</td>';

                            // Remark
                            $html .= '<td>';
                            $html .= !empty($detail->remark) ? Html::encode($detail->remark) : '<em class="text-muted">No remark</em>';
                            $html .= '</td>';

                            $html .= '</tr>';
                        }

                        $html .= '</tbody>';
                        $html .= '</table>';
                        $html .= '</div>';
                    }
                    // Update button
                    $html .= '<div class="mt-2">';
                    $html .= Html::a("Update Target Date <i class='fas fa-edit'></i>",
                            "javascript:",
                            [
                                "onclick" => "event.preventDefault();",
                                "value" => \yii\helpers\Url::to(['update-project-target-date', 'id' => $model->id]),
                                "class" => "modalButtonMedium btn btn-sm btn-primary",
                                'data-modaltitle' => "Update Project Target Completion Date"
                            ]
                    );
                    $html .= '</div>';

                    return $html;
                }
            ]
        ],
    ]))
    ?>
</div>