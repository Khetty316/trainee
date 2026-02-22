<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<link href="/css/summernote.css" rel="stylesheet">
<script src="/js/summernote.min.js" type="text/javascript"></script>

<div class="test-custom-content-create">
    <div id="custom-content-container">
        <?php
        if (!empty($customContentArray) && is_array($customContentArray)) {
            foreach ($customContentArray as $index => $content) {
                ?>
                <div class="form-row custom-content-row" data-index="<?= $index ?>">
                    <div class="col-sm-11 col-md-11">
                        <label class="control-label">Page <?= $index + 1 ?></label>
                        <?=
                        Html::textarea("custom_content[{$index}]", $content, [
                            'class' => 'form-control customContent',
                            'id' => "custom_content_{$index}"
                        ])
                        ?>
                    </div>
                    <div class="col-sm-1 col-md-1">
                        <br>
                        <button type="button" class="btn btn-danger btn-sm remove-content">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="form-row custom-content-row" data-index="0">
                <div class="col-sm-11 col-md-11">
                    <label class="control-label">Page 1</label>
                    <?=
                    Html::textarea('custom_content[0]', '', [
                        'class' => 'form-control customContent',
                        'id' => 'custom_content_0'
                    ])
                    ?>
                </div>
                <div class="col-sm-1 col-md-1">
                    <br>
                    <button type="button" class="btn btn-danger btn-sm remove-content">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php } ?>
    </div>
    <button type="button" class="btn btn-primary" id="add-content-btn">
        <i class="fas fa-plus-circle"></i> Add Custom Content Field
    </button>
</div>

<script type="text/javascript">
    let contentIndex = <?= !empty($model->custom_content) ? count($model->custom_content) : 1 ?>;

    const A4_HEIGHT = 1050; // Approximately A4 height in pixels

    $(function () {
        // Initialize existing Summernote editors
        initializeSummernote();

        // Add new content field
        $('#add-content-btn').on('click', function () {
            addCustomContentField();
        });

        // Remove content field
        $(document).on('click', '.remove-content', function () {
            if ($('.custom-content-row').length > 1) {
                $(this).closest('.custom-content-row').remove();
            } else {
                alert('At least one custom content field is required.');
            }
        });
    });

    function getSummernoteConfig() {
        return {
            height: A4_HEIGHT,
            maxHeight: A4_HEIGHT,
            minHeight: A4_HEIGHT,
//            disableResizeEditor: true, // Add this to disable resizing
//            resize: false, // Add this to disable resize handle
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']], // Added font size for better formatting
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['media', 'link', 'hr']],
                ['view', ['codeview', 'fullscreen']], // Added fullscreen option
                ['help', ['help']]
            ],
            styleTags: [
                'p',
                {title: 'Blockquote', tag: 'blockquote', className: 'blockquote', value: 'blockquote'},
                'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
            ],
            fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '36', '48'],
            callbacks: {
                onImageUpload: function (files) {
                    uploadImage(files, this);
                },
                onInit: function () {
                    // Set default font and styling for A4-like appearance
                    $(this).summernote('formatPara');
                }
            }
        };
    }

    function initializeSummernote() {
        $('.customContent').each(function () {
            // Check if this textarea is already a Summernote editor
            if (!$(this).next('.note-editor').length) {
                $(this).summernote(getSummernoteConfig());
            }
        });
    }

    function addCustomContentField() {
        const currentPages = $('.custom-content-row').length;
        const nextPageNumber = currentPages + 1;

        const newRow = `
        <div class="form-row custom-content-row" data-index="${contentIndex}">
            <div class="col-sm-11 col-md-11">
                <label class="control-label">Page ${nextPageNumber}</label>
                <textarea name="custom_content[${contentIndex}]" 
                          class="form-control customContent" 
                          id="custom_content_${contentIndex}"></textarea>
            </div>
            <div class="col-sm-1 col-md-1">
                <br>
                <button type="button" class="btn btn-danger btn-sm remove-content">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

        $('#custom-content-container').append(newRow);

        // Re-initialize all Summernote editors (simpler but less efficient)
        initializeSummernote();

        contentIndex++;
    }

// Also add a function to update page numbers after deletion
    function updatePageNumbers() {
        $('.custom-content-row').each(function (index) {
            $(this).find('.control-label').text('Page ' + (index + 1));
        });
    }

// Update the remove content handler
    $(document).on('click', '.remove-content', function () {
        if ($('.custom-content-row').length > 1) {
            $(this).closest('.custom-content-row').remove();
            updatePageNumbers(); // Update page numbers after removal
        } else {
            alert('At least one custom content field is required.');
        }
    });

    function uploadImage(files, editor) {
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
                $(editor).summernote('insertImage', response.imagePath);
            },
            error: function (error) {
                console.error('Error uploading image:', error);
            }
        });
    }
</script>

<style>
    .custom-content-row {
        margin-bottom: 20px;
    }

    .remove-content {
        height: 38px;
    }
</style>