<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'form-edit', 'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']]); ?>
    <div class="row isre">
        <div class="col-xs-12 col-lg-6 pb-3">
            <div class="justify-content-center d-flex pb-3">
                <img style="height: 250px" src="<?= yii\helpers\Url::to("/profile/get-file?filename=" . urlencode($model->profile_pic) . "&id=" . $model->id) ?>" class="img-thumbnail rounded"
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
            <?php
            $positionList = frontend\models\common\RefUserDesignation::getDropDownList();
            $areaList = frontend\models\common\RefArea::getDropDownList();
            $ethnicList = frontend\models\common\RefUserEthnic::getDropDownList();
            $religionList = frontend\models\common\RefUserReligion::getDropDownList();
            $sexList = frontend\models\common\RefUserSex::getDropDownList();
            $superiorList = \common\models\User::getActiveDropDownListExcludeOne($model->id);
            $companyList = frontend\models\common\RefCompanyGroupList::getDropDownList();
            $employmentTypeList = frontend\models\common\RefUserEmploymentType::getDropDownList();
            $gradeList = \frontend\models\RefStaffGrade::getDropDownList();
            echo $form->field($model, 'username')->textInput();
            echo $form->field($model, 'staff_id')->textInput();
            echo $form->field($model, 'fullname')->textInput();
            echo $form->field($model, 'email')->textInput();
            echo $form->field($model, 'company_name')->dropdownList($companyList, ['prompt' => 'Select...']);
            echo $form->field($model, 'employment_type')->dropdownList($employmentTypeList, ['prompt' => 'Select...']);
            echo $form->field($model, "superior_id")->dropDownList($superiorList, ['prompt' => 'Select...'])->label('Superior');
            echo $form->field($model, 'sex')->dropdownList($sexList, ['prompt' => 'Select...'])->label("Sex");
            echo $form->field($model, 'ethnic_id')->dropdownList($ethnicList, ['prompt' => 'Select...'])->label("Ethnic");
            echo $form->field($model, 'religion_id')->dropdownList($religionList, ['prompt' => 'Select...'])->label("Religion");
            echo $form->field($model, "designation")->dropDownList($positionList, ['prompt' => 'Select...']);
            echo $form->field($model, 'grade')->dropdownList($gradeList, ['prompt' => 'Select...']);
            echo $form->field($model, 'date_of_join')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']);
            echo $form->field($model, 'ic_no')->textInput();
            echo $form->field($model, 'contact_no')->textInput();
            echo $form->field($model, 'address')->textInput();
            echo $form->field($model, 'address_line_2')->textInput();
            echo $form->field($model, 'postcode')->textInput();
            echo $form->field($model, 'area_id')->dropdownList($areaList, ['prompt' => 'Select...'])->label("Area");

            echo $form->field($model, 'emergency_contact_person')->textInput();
            echo $form->field($model, 'emergency_contact_no')->textInput();
//            echo $form->field($model, 'is_leave_superior')->dropdownList(array('1' => 'Yes', '0' => 'No'), ['prompt' => 'Select...']);
//            echo $form->field($model, 'skip_claim_authorize')->dropdownList(array('1' => 'Yes', '0' => 'No'), ['prompt' => 'Select...']);
//            echo $form->field($model, 'epf_percent')->textInput(['type' => 'number', 'step' => '0.01'])->label('EPF Percentage (%)');
            ?>
            <div class="form-group">
                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success pull-right', 'id' => 'submitButton']) ?>

                    <?php
                    if ($model->status == $model::STATUS_ACTIVE) {
                        echo Html::a("Deactivate", 'javascript:deactivateUser("' . $model->id . '")', ['class' => 'btn btn-warning float-right']);
                    }
                    if ($model->status != $model::STATUS_DELETED) {
                        echo Html::a("Delete", 'javascript:deleteUser()', ['class' => 'btn btn-danger float-right mr-2']);
                    }
                    if ($model->status != $model::STATUS_ACTIVE) {
                        echo Html::a("Activate", 'javascript:activateUser("' . $model->id . '")', ['class' => 'btn btn-success float-right mr-2']);
                    }
                    ?>
                </div>
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