<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Update User: ' . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Profile', 'url' => ['view-profile', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <?php $form = ActiveForm::begin(['id' => 'form-edit', 'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']]); ?>
    <div class="row isre">
        <div class="col-xs-12 col-lg-6 pb-3">
            <div class="justify-content-center d-flex pb-3">
                <img style="height: 250px" src="<?= yii\helpers\Url::to("/profile/get-file?filename=" . urlencode($model->profile_pic)) ?>" class="img-thumbnail rounded"
                     onError="this.onerror=null;this.src='<?= Yii::$app->request->getBaseUrl() ?>/images/blank-profile-picture.png';">    
            </div>
            <div class="justify-content-center d-flex ">
                <div class="custom-file col-xs-12 col-lg-6 ">
                    <?php
                    echo $form->field($model, 'scannedFile')->fileInput(['class' => 'custom-file-input'])->label('(Replace Profile Image)', ['class' => 'custom-file-label']);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-lg-6">


            <div class="form-group">
                <p>Username: <u>&nbsp;<?= $model->username ?>&nbsp;</u></p>
                <p>Staff ID: <u>&nbsp;<?= $model->staff_id ?>&nbsp;</u></p>
                <p>Full Name: <u>&nbsp;<?= $model->fullname ?>&nbsp;</u></p>
            </div>
            

            <?php
            echo $form->field($model, 'username')->hiddenInput()->label(false);
            echo $form->field($model, 'email');
            echo $form->field($model, 'address')->textInput();
            echo $form->field($model, 'postcode')->textInput();
            echo $form->field($model, 'area_id')->dropdownList($areaList, ['prompt' => 'Select...'])->label("Area");
            echo $form->field($model, 'contact_no')->textInput();
            echo $form->field($model, 'emergency_contact_person')->textInput();
            echo $form->field($model, 'emergency_contact_no')->textInput();
            ?>
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success pull-right', 'id' => 'submitButton']) ?>
                <br/>
                <br/>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script>
// Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>