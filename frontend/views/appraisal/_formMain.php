<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;

// Create a new ActiveForm instance
$form = ActiveForm::begin();
?>

<div class="col-md-12">
    <?= $form->field($model, 'description')->textarea(['autocomplete' => 'off']) ?>
</div>
<div class="row col-md-12 m-0">
    <div class="col-md-4 pl-0">
        <?=
        MyCommonFunction::activeFormDateInput($form, $model, 'appraisal_start_date', "Appraisal Start Date");
        ?>
    </div>
    <div class="col-md-4 px-0">
        <?=
        MyCommonFunction::activeFormDateInput($form, $model, 'appraisal_end_date', "Appraisal End Date");
        ?>
    </div>
    <div class="col-md-4 pr-0">
        <?=
        MyCommonFunction::activeFormDateInput($form, $model, 'rating_end_date', "Rating End's Date");
        ?>
    </div>
</div>

<div class="col-md-12 form-group">
    <?= Html::submitButton('Submit', ['class' => 'btn btn-success float-right']) ?>
</div>

<?php ActiveForm::end(); ?>
