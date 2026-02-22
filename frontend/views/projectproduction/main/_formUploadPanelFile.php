<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'action' => 'panel-upload-attachment?panelId=' . $model->id
        ]);
echo $form->field($model, 'scannedFile', ['options' => ['class' => ['my-3']]])->fileInput(['multiple' => false])->label(false);
echo Html::submitButton('Upload', ['class' => 'btn btn-success']);
ActiveForm::end();
?>
