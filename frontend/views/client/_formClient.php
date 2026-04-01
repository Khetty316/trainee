<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\Clients */
/* @var $form yii\widgets\ActiveForm */
//

if ($model->area) {
    $model->areaName = $model->area0->area_name;
}
if ($model->state) {
    $model->stateName = $model->state0->state_name;
}
if ($model->country) {
    $model->countryName = $model->country0->country_name;
}
?>

<div class="clients-form">

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
        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
    ]);
    ?>

    <?php if ($model->client_code) { ?>
        <div class="form-row">
            <div class="col-sm-12">
                <?= $form->field($model, 'client_code')->textInput(['maxlength' => true, 'disabled' => true, 'readonly' => true]) ?>
            </div>
        </div>
    <?php } ?>
    <div class="form-row">
        <div class="col-sm-12">
            <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-6 col-lg-6">
            <?= $form->field($model, 'company_registration_no')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <?= $form->field($model, 'company_tin')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-12 col-lg-3">
            <?= $form->field($model, 'payment_term')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-12 col-md-3">
            <?=
            $form->field($model, 'ac_no_tk')->textInput([
                
                'class' => 'text-right form-control'
            ])
            ?>
        </div>

        <div class="col-sm-12 col-md-3">
            <?=
            $form->field($model, 'ac_no_tke')->textInput([
               
                'class' => 'text-right form-control'
            ])
            ?>
        </div>

        <div class="col-sm-12 col-md-3">
            <?=
            $form->field($model, 'ac_no_tkm')->textInput([
                
                'class' => 'text-right form-control'
            ])
            ?>
        </div>

        <!--hidden--!>
        <!--        <div class="col-sm-12 col-md-6 col-lg-6">
        <?= $form->field($model, 'current_outstanding_balance')->textInput(['maxlength' => true, 'type' => 'number', 'step' => '0.01', 'value' => number_format($model->current_outstanding_balance, 2, '.', ''), 'class' => 'text-right form-control']) ?>
                </div>-->

    </div>
    <div class="form-row">
        <div class="col-sm-12">
            <?= $form->field($model, 'address_1')->textInput(['maxlength' => true, 'placeholder' => 'Address line 1'])->label('Address') ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'address_2')->textInput(['maxlength' => true, 'placeholder' => 'Address line 2'])->label(false) ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-6 col-lg-3">
            <?= $form->field($model, 'postcode')->textInput(['maxlength' => true, 'type' => 'number']) ?>

        </div>
        <div class="col-sm-12 col-md-6 col-lg-3">
            <?php //= $form->field($model, 'area')->textInput()->label(false) ?>
            <?php
            echo $form->field($model, 'areaName')->widget(\yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => \yii\helpers\Url::to(['/list/get-ref-area-list']),
                    'minLength' => '1',
                    'autoFill' => true,
                    'search' => new \yii\web\JsExpression("function( event, ui ) { 
			     }"),
                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
                      $('#clients-statename').val(ui.item.stateName);
//			            $(this).val((ui.item ? ui.item.id : ''));
			     }"),
                    'delay' => 100,
                ],
                'options' => [
                    'class' => 'form-control',
                ]
            ])->label('Area');
            ?>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-3">
            <?php //= $form->field($model, 'state')->textInput()  ?>
            <?php
            echo $form->field($model, 'stateName')->widget(\yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => \yii\helpers\Url::to(['/list/get-ref-state-list']),
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 100,
                ],
                'options' => [
                    'class' => 'form-control',
                ]
            ])->label('State');
            ?>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-3">
            <?php //= $form->field($model, 'country')->textInput(['maxlength' => true])  ?>
            <?php
            echo $form->field($model, 'countryName')->widget(\yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => \yii\helpers\Url::to(['/list/get-ref-country-list']),
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 100,
                ],
                'options' => [
                    'class' => 'form-control',
                ]
            ])->label('Country');
            ?>
        </div>
    </div>
    <legend class="w-auto px-2 m-0">Contact:</legend>
    <table class="table table-sm table-borderless" width="100%">
        <!--<div class="form-row">-->
        <thead class="table-dark">
            <tr>
                <th class="text-center">Name</th>
                <th class="text-center">Position</th>
                <th class="text-center">Contact number</th>
                <th class="text-center">Fax</th>
                <th class="text-center">Email address</th>
            </tr>
        </thead>
<!--<tr>-->
        <tbody id="listTBody">
            <?php if (!empty($contactModels)) : ?>
                <?php foreach ($contactModels as $i => $contactModel) : ?>
                    <?=
                    $this->render('_formClient_row', [
                        'contact' => $contactModel,
                        'index' => $i,
                        'isUpdate' => $isUpdate
                    ])
                    ?>
                <?php endforeach; ?>
            <?php else : ?>
                <?=
                $this->render('_formClient_row', [
                    'contacts' => $contacts,
                    'contactModels' => $contacts,
                    'index' => 0,
                    'isUpdate' => $isUpdate
                ])
                ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <a class='btn btn-primary' href='javascript:addRow()'> 
                        <i class="fas fa-plus-circle"></i></a>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style>
    .is-valid {
        border-color: #28a745 !important;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
<script>
    let currentKey = <?= count($contactModels) ?>;
    let isValidating = false;
    let isSubmitting = false; // Add this flag

    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-contact']) ?>',
            type: 'GET',
            data: {
                key: currentKey,
                isUpdate: '<?= $isUpdate ?>'
            },
            success: function (response) {
                $('#listTBody').append($.trim(response));
                currentKey++;

                // Attach email validation to newly added row
                attachEmailValidation();
            },
            error: function () {
                alert('Unable to add new contact row.');
            }
        });
    }

    // Helper function to show error message
    function showError(inputElement, errorElementId, message) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            inputElement.classList.remove('is-valid');
            inputElement.classList.add('is-invalid');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    // Helper function to show success
    function showSuccess(inputElement, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
            errorElement.style.display = 'none';
        }
    }

    // Helper function to clear validation
    function clearValidation(inputElement, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            inputElement.classList.remove('is-invalid', 'is-valid');
            errorElement.style.display = 'none';
        }
    }

    // Helper function to reset button state
    function resetButtonState() {
        const $submitButton = $('form').find('button[type="submit"], input[type="submit"]');
        if (isValidating && !isSubmitting) { // Check both flags
            $submitButton.prop('disabled', false).text('Save');
            isValidating = false;
        }
    }

    // Validate single email
    async function validateEmail(email) {
        if (!email || email.trim() === '') {
            return {success: true}; // Empty is valid (optional field)
        }

        try {
            const response = await $.post('<?= \yii\helpers\Url::to(['/projectqrevision/check-email-exists']) ?>', {
                email_address: email
            });
            return response;
        } catch (error) {
            return {
                success: false,
                error: {type: 'Server error. Please try again.'}
            };
        }
    }

    // Attach email validation to all email inputs using event delegation
    function attachEmailValidation() {
        // Remove old event listener to prevent duplicates
        $('#listTBody').off('input', '.email-input');

        // Use event delegation for dynamic rows
        $('#listTBody').on('input', '.email-input', function () {
            const key = $(this).data('key');
            clearValidation(this, 'email-error-' + key);

            // Reset button to "Save" when user starts typing
            if (!isSubmitting) { // Only reset if not currently submitting
                resetButtonState();
            }
        });
    }

    // Also attach to form inputs outside the table
    $(document).on('input', '.email-input', function () {
        const key = $(this).data('key');
        if (key !== undefined && !isSubmitting) {
            clearValidation(this, 'email-error-' + key);
            resetButtonState();
        }
    });

    // Validate all emails on form submit
    $('form').on('submit', async function (e) {
        e.preventDefault();

        // Prevent double submission
        if (isSubmitting) {
            console.log('Form already submitting, ignoring duplicate submit');
            return false;
        }

        let isValid = true;
        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"], input[type="submit"]');
        const originalText = 'Save';

        // Show loading state
        $submitButton.prop('disabled', true).text('Validating...');
        isValidating = true;

        // Get all email inputs
        const emailInputs = $('.email-input');

        for (let i = 0; i < emailInputs.length; i++) {
            const input = emailInputs[i];
            const key = $(input).data('key');
            const email = $(input).val();

            if (email && email.trim() !== '') {
                const result = await validateEmail(email);

                if (result.success) {
                    showSuccess(input, 'email-error-' + key);
                } else {
                    showError(input, 'email-error-' + key, result.error?.type || 'Invalid email address');
                    isValid = false;
                }
            }
        }

        isValidating = false;

        // If all valid, submit the form
        if (isValid) {
            // Set submitting flag BEFORE submission
            isSubmitting = true;

            // Change button text to indicate submission
            $submitButton.text('Saving...');

            // Submit the form natively (without triggering this event again)
            $form[0].submit();
        } else {
            // Restore button state if validation failed
            $submitButton.prop('disabled', false).text(originalText);
            alert('Please fix the invalid email addresses before saving.');
        }

        return false; // Always prevent default
    });

    // Initialize validation on page load
    $(document).ready(function () {
        attachEmailValidation();
    });
</script>
