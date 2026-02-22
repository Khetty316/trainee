<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="">

    <?php
    $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
                'action' => $formAction
    ]);
    ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">New Service Record</legend>
        <div class="form-row">
            <div class="col-sm-12 pb-3">
                <?php
                echo $form->field($model, 'asset_id', ['options' => ['class' => 'hidden']])->textInput(['value' => $assetId]);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-6">
                <?= $form->field($model, 'service_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy'])->label("Service Date:") ?>

            </div>
            <div class="col-sm-12 col-md-6">
                <?= $form->field($model, 'next_service_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy'])->label("Next Service Date:") ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12">
                <?= $form->field($model, 'service_remark')->textarea(['rows' => 6]) ?>
            </div>
        </div>
    </fieldset>


    <div class="form-group text-right">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
