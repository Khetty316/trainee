<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\profile\UserDocuments */

$this->title = 'Documents'; // . " - " . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'My Space', 'url' => ['/profile/view-profile']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="">

    <h3> <?= Html::encode(Yii::$app->user->identity->fullname) ?> </h3>

    <div class="col-lg-12">
        <p>
        </p>

        <?php
        $form = ActiveForm::begin([
                    'id' => 'user_doc_form',
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
            <div class="col-sm-12">
                <?php
                $userDoctypeList = \frontend\models\profile\RefUserDoctypes::getDropDownList();
                echo $form->field($model, 'doctype_code')->dropdownList($userDoctypeList, ['prompt' => 'Select...', 'id' => 'userDocTypeList']);
                echo $form->field($model, 'id')->hiddenInput()->label(false);
                ?>
            </div>
        </div>
        <?php if ($model->doc_file_link) { ?>
            <div class="form-row">
                <div class="col-sm-12">
                    <h1 class="text-primary">
                        <?php
                        echo Html::a("<i class='far fa-file-alt fa-lg' ></i>", "/profile/get-document?filename=" . urlencode($model->doc_file_link), ['target' => "_blank", 'class' => 'm-2', 'title' => "Current Reference"]);
                        ?>
                    </h1>
                </div>
            </div>
        <?php } ?>
        <div class="form-row">
            <div class="col-sm-12 mr-3">
                <?php
                echo $form->field($model, 'scannedFile')->fileInput(['class' => 'custom-file-input'])->label($model->doc_file_link ? '(Replace File)' : '(Add File)', ['class' => 'custom-file-label m-1']);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 mr-3">
                <?php
                echo $form->field($model, 'description')->textInput();
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 mr-3">
                <?php
                echo $form->field($model, 'doc_date')
                        ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy', 'id' => 'dateFrom'])
                        ->label("Document Date");
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 mr-3">
                <?php
                echo $form->field($model, 'doc_expiry_date')
                        ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy', 'id' => 'dateFrom'])
                        ->label("Expiry Date");
                ?>
            </div>
        </div>
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>


<script>
// Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).parent().siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $(document).on('beforeSubmit', 'form', function (event) {
        $(".btn-success").attr('disabled', true).addClass('disabled');
    });
</script>