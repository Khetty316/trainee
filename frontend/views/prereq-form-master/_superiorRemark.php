<?php

use yii\helpers\Html;
use frontend\models\office\claim\ClaimMaster;
use common\models\User;
use common\models\myTools\MyFormatter;
use frontend\models\RefGeneralStatus;
?>
<td>
    <?=
    $form->field($model, "[$key]quantity_approved")->input('number', [
        'class' => 'form-control text-center',
        'required' => true,
//        'oninput' => 'updateTotalDisplay(this)',
        'id' => 'qty',
        'value' => ($model->quantity_approved !== null ? $model->quantity_approved : $model->quantity),
    ])->label(false)
    ?>
</td>
<td>
    <?=
    $form->field($model, "[$key]currency_approved")->dropDownList(
            $currencyList, [
        'value' => ($model->currency_approved !== null ? $model->currency_approved : $model->currency),
            ])->label(false)
    ?>
</td>
<td>
    <?=
    $form->field($model, "[$key]unit_price_approved")->input('number', [
        'class' => 'form-control text-right',
        'required' => true,
//        'oninput' => 'updateTotalDisplay(this)',
        'id' => 'unit_price',
        'step' => 'any',
        'min' => '0.01',
        'value' => ($model->unit_price_approved !== null ? $model->unit_price_approved : $model->unit_price),
    ])->label(false)
    ?>
</td>
<td>
    <?=
    $form->field($model, "[$key]total_price_approved")->input('number', [
        'readonly' => true,
        'class' => 'form-control text-right',
        'required' => true,
        'id' => 'total_price',
        'value' => ($model->total_price_approved !== null ? $model->total_price_approved : $model->total_price),
    ])->label(false)
    ?>
</td>
<td>
    <div class="decision-wrapper">
        <div class="d-flex justify-content-center ">
            <?=
            Html::activeRadio($worklist, "[{$key}]status", [
                'label' => false,
                'value' => RefGeneralStatus::STATUS_SuperiorRejected,
                'uncheck' => null,
                'id' => "reject-{$key}",
                'class' => 'decision-radio d-none',
                'required' => true,
            ])
            ?>

            <div class="card m-1 decision-card reject-btn" data-type="reject" style="width: 100px; cursor: pointer;">
                <div class="card-body text-center p-1">
                    <label for="reject-<?= $key ?>" class="btn btn-outline-danger btn-sm w-100 mt-1 mb-0">Reject</label>
                </div>
            </div>

            <?=
            Html::activeRadio($worklist, "[{$key}]status", [
                'label' => false,
                'value' => RefGeneralStatus::STATUS_Approved,
                'uncheck' => null,
                'id' => "approve-{$key}",
                'class' => 'decision-radio d-none',
                'required' => true,
            ])
            ?>

            <div class="card m-1 decision-card approve-btn" data-type="approve" style="width: 100px; cursor: pointer;">
                <div class="card-body text-center p-1">
                    <label for="approve-<?= $key ?>" class="btn btn-outline-success btn-sm w-100 mt-1 mb-0">Approve</label>
                </div>
            </div>
        </div>
        <div class="error-container mt-2 w-100"></div>
    </div>

    <?=
    $form->field($worklist, "[{$key}]remark", [
        'template' => "{input}\n{error}",
        'options' => ['tag' => false],
    ])->textarea([
        'class' => 'form-control reject-remark mt-2',
        'placeholder' => 'Enter reject remark',
        'style' => 'display: none;',
//                'id' => 'reject-textarea'
    ]);
    ?>
</td>
