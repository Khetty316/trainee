<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hrdoc\HrEmployeeDocuments */

$this->title = 'Add Employee Documents';
$this->params['breadcrumbs'][] = ['label' => 'HR Employee Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hr-employee-documents-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php
//    echo $this->render('_form', [
//        'model' => $model,
//    ])
    ?>

    <?php
    $form = ActiveForm::begin([
//                'id' => 'new_claim_form',
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
//                'action' => '/project/newquotation',
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
    ]);
    ?>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'hr_doctype')->dropdownList(frontend\models\common\RefHrDoctypes::getDropDownListActiveOnly(), ['prompt' => '(Select...)', 'value' => 'proj']) ?>
        </div>
    </div>

    <div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'scannedFile[]')->fileInput(['multiple' => true])->label("Attachments") ?>
        </div>
    </div>
    <div class="form-row">
        <div class="text-danger">
            Reminder: The documents' name have to start with staff id, followed by a dash.<br/>
            For example: <b>123456-Confirmation Letter.pdf</b>
        </div>
    </div> 
    <div class="form-group">
        <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
