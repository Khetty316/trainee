<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

//$this->title = 'ATS Functionality';
//$this->params['breadcrumbs'][] = ['label' => "Panel's Test List", 'url' => ['/test/testing/index-master']];
//$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
//$this->params['breadcrumbs'][] = $this->title;
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
    <div class="form-row">
        <div class="col-md-12">
            <div class="form-row">
                <div class="col-sm-12 col-md-12 mb-2">
                    <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
            <?php
            if ($template) {
                $procedureValues = explode('|', $procedures);
                ?>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($testTemplate, 'proctest1')->textarea(['class' => 'form-control proctest1', 'value' => isset($procedureValues[0]) ? $procedureValues[0] : '']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($testTemplate, 'proctest2')->textarea(['class' => 'form-control proctest2', 'value' => isset($procedureValues[1]) ? $procedureValues[1] : '']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($testTemplate, 'proctest3')->textarea(['class' => 'form-control proctest3', 'value' => isset($procedureValues[2]) ? $procedureValues[2] : '']) ?>
                    </div>
                </div>
            <?php } else {
                ?>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($testTemplate, 'proctest1')->textarea(['rows' => 8, 'class' => 'form-control proctest1']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($testTemplate, 'proctest2')->textarea(['rows' => 8, 'class' => 'form-control proctest2']) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <?= $form->field($testTemplate, 'proctest3')->textarea(['rows' => 8, 'class' => 'form-control proctest3']) ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

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
                ['insert', ['media', 'link', 'hr']],
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
                ['insert', ['media', 'link', 'hr']],
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
                ['insert', ['media', 'link', 'hr']],
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

