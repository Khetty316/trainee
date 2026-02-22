<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\test\TestTemplate */
?>
<link href="/css/summernote.css" rel="stylesheet">
<script src="/js/summernote.min.js" type="text/javascript"></script>

<div class="test-template-form">

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
                'options' => ['autocomplete' => 'off'],
    ]);
    ?>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0">Test Template Detail</legend>
        <div class="form-row">
            <div class="col-md-9">
                <div class="form-row">
                    <div class="col-sm-12 col-md-3">
                        <?= $form->field($model, 'doc_ref')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <?= $form->field($model, 'rev_no')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <?= $form->field($model, 'formcode')->dropDownList($formName, ['prompt' => 'Select...']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($model, 'proctest1')->textarea(['rows' => 8, 'class' => 'form-control proctest1']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($model, 'proctest2')->textarea(['rows' => 8, 'class' => 'form-control proctest2']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($model, 'proctest3')->textarea(['rows' => 8, 'class' => 'form-control proctest3']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-3">
                        <?=
                        $form->field($model, 'active_sts')->dropDownList([
                            '1' => 'Yes',
                            '0' => 'No',
                        ])
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12 col-md-12 text-right">
                        <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    $(function () {
        $('.proctest1').summernote({
            height: 500,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['media', 'link', 'hr', 'picture']],
                ['view', ['codeview']],
                ['help', ['help']]
            ],
            callbacks: {
                onImageUpload: function (files) {
                    // Create a FormData object to append the files
                    var formData = new FormData();

                    // Append each file to the FormData object
                    for (var i = 0; i < files.length; i++) {
                        formData.append('file', files[i]);
                    }

                    $.ajax({
                        url: '<?= yii\helpers\Url::to(['upload-image']) ?>',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('.proctest1').summernote('insertImage', response.imagePath);
                        },
                        error: function (error) {
                            console.error('Error uploading image:', error);
                        }
                    });
                },

            }

        });
    });
    $(function () {
        $('.proctest2').summernote({
            height: 500,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['media', 'link', 'hr', 'picture']],
                ['view', ['codeview']],
                ['help', ['help']]
            ],
            callbacks: {
                onImageUpload: function (files) {
                    var formData = new FormData();

                    for (var i = 0; i < files.length; i++) {
                        formData.append('file', files[i]);
                    }
                    $.ajax({
                        url: '<?= yii\helpers\Url::to(['upload-image']) ?>',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('.proctest2').summernote('insertImage', response.imagePath);
                        },
                        error: function (error) {
                            console.error('Error uploading image:', error);
                        }
                    });
                }
            }

        });
    });
    $(function () {
        $('.proctest3').summernote({
            height: 500,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['media', 'link', 'hr', 'picture']],
                ['view', ['codeview']],
                ['help', ['help']]
            ],
            callbacks: {
                onImageUpload: function (files) {
                    var formData = new FormData();

                    for (var i = 0; i < files.length; i++) {
                        formData.append('file', files[i]);
                    }
                    $.ajax({
                        url: '<?= yii\helpers\Url::to(['upload-image']) ?>',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $('.proctest3').summernote('insertImage', response.imagePath);
                        },
                        error: function (error) {
                            console.error('Error uploading image:', error);
                        }
                    });
                }
            }
        });
    });
</script>

