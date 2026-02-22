<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster */

$this->title = $model->ref_code;
$action = ($module === 'personal' ? 'index' : 'index-finance');
$this->params['breadcrumbs'][] = ['label' => 'Production Overtime Meal Record', 'url' => [$action]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="prod-ot-meal-record-master-view">

    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Detail:</legend>
                <?php
                $creator = ($model->created_by == Yii::$app->user->identity->id);
                if ($creator && $module === 'personal') {
                    ?>
                    <div class="mb-2">
                        <?php
                        $notFinalize = ($model->status == ProdOtMealRecordMaster::STATUS_NOT_FINALIZE);
                        $finalized = ($model->status == ProdOtMealRecordMaster::STATUS_FINALIZE);
                        $gotReceiptList = frontend\models\office\prodOtMealRecord\ProdOtMealRecordDetail::find()
                                ->where([
                                    'prod_ot_meal_record_master_id' => $model->id,
                                    'deleted_by' => null,
                                    'deleted_at' => null,
                                ])
                                ->exists();
                        ?>
                        <?php if ($notFinalize && $gotReceiptList) { ?>      
                            <?=
                            Html::a('Finalize', ['finalize-record', 'id' => $model->id], [
                                'class' => 'btn btn-primary',
                                'data' => [
                                    'confirm' => 'Are you sure you want to finalize this record?',
                                    'method' => 'post',
                                ],
                            ])
                            ?> 
                        <?php } ?>
                        <?php if ($finalized) { ?>  
                            <?=
                            Html::a('Revert Finalization', ['revert-finalize-record', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Are you sure you want to revert this record?',
                                    'method' => 'post',
                                ],
                            ])
                            ?> 
                            <?php
                        }
                        if ($notFinalize) {
                            ?>    
                            <?=
                            Html::a(
                                    " Update Detail",
                                    "javascript:",
                                    [
                                        'title' => $model->ref_code,
                                        "value" => yii\helpers\Url::to(['update', 'id' => $model->id]),
                                        "class" => "modalButtonMedium btn btn-success",
                                        'data-modaltitle' => "Update Detail"
                                    ]
                            )
                            ?>
                            <?=
                            Html::a('Delete', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger float-right',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this record?',
                                    'method' => 'post',
                                ],
                            ])
                            ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <table class="table table-sm table-striped table-bordered">
                    <tbody>
                        <tr>
                            <th class="w-25">Reference Code</th>
                            <td><?= $model->ref_code ?></td>
                        </tr>
                        <tr>
                            <th>Month</th>
                            <td><?= DateTime::createFromFormat('!m', $model->month)->format('F') ?></td>
                        </tr>
                        <tr>
                            <th>Year</th>
                            <td><?= $model->year ?></td>
                        </tr>
                        <tr>
                            <th>Selected Period</th>
                            <td><?= Yii::$app->formatter->asDate($model->dateFrom, 'php:d/m/Y') ?> to <?= Yii::$app->formatter->asDate($model->dateTo, 'php:d/m/Y') ?></td>
                        </tr>
                        <tr>
                            <th>Total Amount (RM)</th>
                            <td><?= \common\models\myTools\MyFormatter::asDecimal2($model->total_amount ?? 0.00) ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <?php
                            switch ($model->status) {
                                case 0:
                                    $status = '<span class="text-warning">Save Temporary</span>';
                                    break;
                                case 1:
                                    $status = '<span class="text-primary">Has Been Finalized</span>';
                                    break;
                                case 2:
                                    $status = '<span class="text-success">Claim Submitted</span>';
                                    break;
                                case 3:
                                    $status = '<span class="text-danger">Deleted</span>';
                                    break;
                                default:
                                    $status = '<span class="text-muted">Unknown</span>';
                                    break;
                            }
                            ?>
                            <td><?= $status ?></td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td><?= $model->createdBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($model->created_at) ?></td>
                        </tr>
                        <tr>
                            <th>Updated By</th>
                            <?php
                            $update = ($model->updated_by === null ? '-' : ($model->updatedBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at));
                            ?>
                            <td><?= $update ?></td>
                        </tr>
                        <tr>
                            <th>Deleted By</th>
                            <?php
                            $delete = ($model->deleted_by === null ? '-' : ($model->deletedBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->deleted_at));
                            ?>
                            <td><?= $delete ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-lg-12 col-md-8 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Daily Record List:</legend>
                <?=
                $this->render('indexDetail', [
                    'model' => $model,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'hideFilter' => false,
                    'module' => $module,
                ]);
                ?>
            </fieldset>
        </div>
    </div>
</div>
