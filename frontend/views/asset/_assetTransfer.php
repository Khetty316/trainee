<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="asset-master-form">

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
        <legend class="w-auto px-2  m-0">Transfer Asset</legend>
        <div class="form-row">
            <div class="col-sm-12 pb-3">
                <?php
                $currentHolder = $currentTracking->receiveUser;
                echo 'Current holder: <b>' . $currentHolder->fullname . '</b>';
                echo $form->field($modelTracking, 'asset_id', ['options' => ['class' => 'hidden']])->textInput(['value' => $model->id]);
                echo $form->field($modelTracking, 'from_user', ['options' => ['class' => 'hidden']])->textInput(['value' => $currentTracking->receive_user]);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-8">
                <?= $form->field($modelTracking, 'receive_user')->dropdownList(User::getActiveDropDownListExcludeOne($currentTracking->receive_user), ['prompt' => '(Select...)'])->label("Transfer to:") ?>
            </div>
            <div class="col-sm-12 col-md-4">
                <?= $form->field($modelTracking, 'deliver_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy'])->label("Transfer date:") ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12">
                <?= $form->field($modelTracking, 'deliver_remark')->textarea(['rows' => 6]) ?>
            </div>
        </div>
    </fieldset>


    <div class="form-group text-right">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success','id'=>'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
