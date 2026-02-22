<?php

use yii\helpers\Html;
?>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<style>
    .signature-modal {
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .signature-modal-content {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        max-width: 90%;
        max-height: 90%;
        width: 700px;
        animation: modalFadeIn 0.3s ease;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .signature-modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        border-radius: 8px 8px 0 0;
        flex-shrink: 0;
    }

    .signature-modal-header h3 {
        margin: 0;
        color: #495057;
        font-size: 1.25rem;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #6c757d;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .close-modal:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    .signature-modal-body {
        padding: 25px;
        text-align: center;
        flex: 1;
        overflow: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .signature-modal-footer {
        padding: 20px 25px;
        border-top: 1px solid #e9ecef;
        text-align: right;
        background-color: #f8f9fa;
        border-radius: 0 0 8px 8px;
        flex-shrink: 0;
        display: block !important;
        min-height: 70px;
    }

    .signature-modal-footer button {
        margin-left: 10px;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    #expanded-signature-pad {
        border: 2px solid #dee2e6;
        border-radius: 6px;
        cursor: crosshair;
        background-color: #fdfdfd;
    }

    /* Explicit button styling */
    .btn {
        display: inline-block !important;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        user-select: none;
        border: 1px solid transparent;
        padding: 8px 16px;
        font-size: 14px;
        line-height: 1.5;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    .btn-primary {
        color: #fff !important;
        background-color: #007bff !important;
        border-color: #007bff !important;
    }

    .btn-primary:hover {
        background-color: #0056b3 !important;
        border-color: #004085 !important;
    }

    .btn-danger {
        color: #fff !important;
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }

    .btn-danger:hover {
        background-color: #c82333 !important;
        border-color: #bd2130 !important;
    }

    .btn-success {
        color: #fff !important;
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

    .btn-success:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
    }

    /* Small canvas styling */
    .sign-block canvas {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background-color: #fdfdfd;
    }

    /* Status indicator for signed canvases */
    .sign-block.signed canvas {
        border-color: #28a745;
        background-color: #f8fff9;
    }
</style>
<?php //if ($witness->signature): ?>
<!--    <div class="sign-block">
        <div style="margin-left: 5px;">
            <img src="////<?php //= $witness->signature   ?>" id="signature-image-<?php //= $key   ?>" alt="Image" style="margin-right: 70px;">
        </div>
    </div>-->
<?php //endif; ?>

<!--<div class="sign-block">
    <div style="margin-left: 5px;">
        <canvas id="signature-pad-////<?php //= $key   ?>" width="200" height="200" style="margin: 0 auto; display: block; margin-right: 70px;"></canvas>
    </div>
<?php //= Html::hiddenInput("testItemWitness[$key][witnessSign]", $witness->signature, ['id' => "signature-data-$key"]) ?>

    <div class="mt-2" style="position: absolute; bottom: 5px; right: 5px;">
<?php //= Html::button('Clear', ['class' => 'btn btn-sm btn-danger able', 'id' => "clear-signature-$key"]) ?>
    </div>
</div>-->
<div class="sign-block" data-witness="<?= $key ?>">
    <?php if ($witness->signature): ?>
        <img src="<?= $witness->signature ?>" 
             id="signature-image-<?= $key ?>" 
             alt="Image" 
             style="width: 200px; height: 200px; object-fit: contain; border: 1px solid #dee2e6; border-radius: 4px;">
    <?php else: ?>
        <div id="signature-pad-<?= $key ?>" 
             style="margin: 0 auto; display: block; width: 200px; height: 200px; border: 1px solid #dee2e6; border-radius: 4px; background-color: #fdfdfd;">
        </div>
    <?php endif; ?>
    <div class="mt-2" style="position: absolute; bottom: 5px; right: 5px;">
        <?= Html::button('Sign', ['class' => 'btn btn-sm btn-primary sign-btn float-left able', 'data-witness' => $key, 'type' => 'button']) ?>
    </div>
    <?= Html::hiddenInput("testItemWitness[$key][witnessSign]", $witness->signature, ['id' => "signature-data-$key"]) ?>
</div>

<div id="signature-modal" class="signature-modal" style="display: none;">
    <div class="signature-modal-content">
        <div class="signature-modal-header">
            <h3>Digital Signature</h3>
            <button type="button" class="close-modal">&times;</button>
        </div>
        <div class="signature-modal-body">
            <p class="text-center mb-3" style="margin-bottom: 15px;">Please sign in the area below:</p>
            <canvas id="expanded-signature-pad" width="600" height="300"></canvas>
        </div>
        <div class="signature-modal-footer">
            <?= Html::button('Clear', [
                'id' => 'clear-expanded-signature',
                'class' => 'btn btn-danger',
                'type' => 'button',
                'style' => 'display: inline-block !important; margin-left: 10px;'
            ]) ?>
            <?= Html::button('Save', [
                'id' => 'save-signature',
                'class' => 'btn btn-success',
                'type' => 'button',
                'style' => 'display: inline-block !important; margin-left: 10px;'
            ]) ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let currentWitnessKey = null;
        let expandedSignaturePad = null;
        const originalPads = {};

        // Debug: Check if buttons exist
        console.log('Clear button exists:', $('#clear-expanded-signature').length);
        console.log('Save button exists:', $('#save-signature').length);

        if (typeof SignaturePad === 'undefined') {
            alert('Signature functionality requires SignaturePad library');
            return;
        }

        $('.sign-btn').each(function () {
            const button = $(this);
            const witnessKey = button.data('witness');
            const signBlock = button.closest('.sign-block').length ?
                    button.closest('.sign-block') :
                    button.siblings('.sign-block').first();

            if (!signBlock.length) {
                return;
            }

            if (!signBlock.data('witness')) {
                signBlock.attr('data-witness', witnessKey);
            }
        });

        $('.sign-block').each(function (index) {
            const signBlock = $(this);
            const witnessKey = signBlock.data('witness');

            if (witnessKey === undefined && witnessKey !== 0) {
                return;
            }

            const existingImage = signBlock.find('img[id^="signature-image-"]');
            if (existingImage.length) {
                originalPads[String(witnessKey)] = 'existing_image';
                return;
            }

            const signatureDiv = signBlock.find('div[id^="signature-pad-"]')[0];
            if (!signatureDiv) {
                return;
            }

            const canvas = document.createElement('canvas');
            canvas.width = 200;
            canvas.height = 200;
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            canvas.style.display = 'block';

            signatureDiv.innerHTML = '';
            signatureDiv.appendChild(canvas);

            const pad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255,255,255,1)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 0.5,
                maxWidth: 2
            });

            const keyString = String(witnessKey);
            originalPads[keyString] = pad;

            const existingSignature = $(`#signature-data-${witnessKey}`).val();
            if (existingSignature && existingSignature.trim() !== '') {
                pad.fromDataURL(existingSignature);
                signBlock.addClass('signed');
            }
        });

        $('.sign-btn').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const witnessKey = $(this).data('witness');

            if (witnessKey === undefined || witnessKey === null || witnessKey === '') {
                alert('Error: No witness key found');
                return;
            }

            openSignatureModal(witnessKey);
        });

        function openSignatureModal(witnessKey) {
            currentWitnessKey = String(witnessKey);

            $('#signature-modal').fadeIn(200);
            $('body').addClass('modal-open');

            // Debug: Check if buttons are visible after modal opens
            setTimeout(function() {
                console.log('After modal open - Clear button visible:', $('#clear-expanded-signature').is(':visible'));
                console.log('After modal open - Save button visible:', $('#save-signature').is(':visible'));
            }, 250);

            const expandedCanvas = document.getElementById('expanded-signature-pad');

            if (expandedSignaturePad) {
                expandedSignaturePad.off();
            }

            expandedSignaturePad = new SignaturePad(expandedCanvas, {
                backgroundColor: 'rgba(255,255,255,1)',
                penColor: 'rgb(0, 0, 0)',
                velocityFilterWeight: 0.1,
                minWidth: 1,
                maxWidth: 4
            });

            const existingSignature = $(`#signature-data-${witnessKey}`).val();
            if (existingSignature && existingSignature.trim() !== '') {
                expandedSignaturePad.fromDataURL(existingSignature);
            }
        }

        function closeSignatureModal() {
            $('#signature-modal').fadeOut(200);
            $('body').removeClass('modal-open');
            if (expandedSignaturePad) {
                expandedSignaturePad.off();
                expandedSignaturePad = null;
            }
            currentWitnessKey = null;
        }

        $('.close-modal').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            closeSignatureModal();
        });

        $('#signature-modal').on('click', function (e) {
            if (e.target === this) {
                e.preventDefault();
                e.stopPropagation();
                closeSignatureModal();
            }
        });

        $('#clear-expanded-signature').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Clear button clicked');
            if (expandedSignaturePad) {
                expandedSignaturePad.clear();
            }
        });

        $('#save-signature').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Save button clicked');

            if (currentWitnessKey === null || currentWitnessKey === undefined || currentWitnessKey === '') {
                alert('Error: No witness selected');
                return;
            }

            if (!expandedSignaturePad) {
                alert('Error: Signature pad not initialized');
                return;
            }

            try {
                const signatureData = expandedSignaturePad.toDataURL('image/png');

                $(`#signature-data-${currentWitnessKey}`).val(signatureData);

                const signBlock = $(`.sign-block[data-witness="${currentWitnessKey}"]`);
                const existingImage = signBlock.find('img[id^="signature-image-"]');

                if (existingImage.length) {
                    existingImage.attr('src', signatureData);
                } else {
                    const originalPad = originalPads[currentWitnessKey];
                    if (originalPad && typeof originalPad !== 'string') {
                        originalPad.clear();
                        originalPad.fromDataURL(signatureData);
                    }
                }

                signBlock.addClass('signed');

                closeSignatureModal();

            } catch (error) {
                alert('Error saving signature: ' + error.message);
            }
        });

        $('.signature-modal-content').on('click', function (e) {
            e.stopPropagation();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $('#signature-modal').is(':visible')) {
                e.preventDefault();
                closeSignatureModal();
            }
        });

        $('#myForm').submit(function (e) {
            var activeElement = $(document.activeElement);

            if (activeElement.closest('.signature-modal').length > 0 ||
                    activeElement.hasClass('sign-btn') ||
                    activeElement.hasClass('close-modal') ||
                    activeElement.attr('id') === 'clear-expanded-signature' ||
                    activeElement.attr('id') === 'save-signature') {
                e.preventDefault();
                return false;
            }

            if (!activeElement.hasClass('save-and-status') &&
                    !activeElement.hasClass('btn-success')) {
                e.preventDefault();
                return false;
            }

            var form = document.getElementById('myForm');
            var inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(function (input) {
                input.disabled = false;
            });

            return true;
        });
    });
</script>