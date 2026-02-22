<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EmployeeHandbookMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-handbook-master-form mt-3">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'edition_no')->textInput(['placeholder' => 'e.g., 1, 2, 3']) ?> 
        </div>
        <div class="col-8">
            <?=
            $form->field($model, 'edition_date')->widget(yii\jui\DatePicker::className(),
                    ['options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy']
                        , 'dateFormat' => 'dd/MM/yyyy'])->label("Date");
            ?>        
        </div>
    </div>

        <?= $form->field($model, 'is_active')->dropDownList(\frontend\models\office\employeeHandbook\EmployeeHandbookMaster::IS_ACTIVE) ?>

    <div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
