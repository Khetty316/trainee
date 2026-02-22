<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
//use frontend\models\common\RefAssetCategory;
//use frontend\models\common\RefAssetSubCategory;
//use frontend\models\common\RefAssetCondition;
//use frontend\models\common\RefAssetOwnType;
//use common\models\User;

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
                'action' => '/asset/personal-request-asset',
                'id' => 'form_receiveAsset'
    ]);
    ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">Receive Asset</legend>
        <div class="form-row">
            <div class="col-sm-12 pb-3">
                <?php
                $currentHolder = $currentTracking->receiveUser;
                echo 'Current Holder: <b>' . $currentHolder->fullname . '</b>';
                $modelRequest = new \frontend\models\asset\AssetTransferRequest();
                ?>
            </div>
        </div>
        <div class="form-row hidden">
            <div class="col-sm-12">
                <?php
                echo $form->field($modelRequest, 'requestor')->textInput(['value' => Yii::$app->user->id]);
                echo $form->field($modelRequest, 'asset_id')->textInput(['value' => $model->id]);
                ?>
            </div>
        </div>

        <div class="form-row">
            <div class="col-sm-12">
                <?= $form->field($modelRequest, 'remark')->textarea(['rows' => 6])->label('Remark: ') ?>
            </div>
        </div>
    </fieldset>


    <div class="form-group text-right">
        <?= Html::submitButton('Submit Request', ['class' => 'btn btn-success','id'=>'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
