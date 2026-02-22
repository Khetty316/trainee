<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="project-view">
    <div class="row">
        <div class="col-xs-12 col-xl-12 col-md-12">
            <?php
            $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
            ]);

            echo $form->field($model, 'scannedFile')->fileInput([
                'accept' => '.pdf',
                'class' => 'form-control'
            ]);

            echo "<br/>";

            echo Html::submitButton(
                    'Save',
                    ['class' => 'btn btn-success float-right mb-2 mt-3']
            );

            ActiveForm::end();
            ?>
        </div>
    </div>
</div>
