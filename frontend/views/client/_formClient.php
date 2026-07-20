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
            <?php //= $form->field($model, 'area')->textInput()->label(false)  ?>
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
            <?php //= $form->field($model, 'state')->textInput()   ?>
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
            <?php //= $form->field($model, 'country')->textInput(['maxlength' => true])   ?>
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
    <legend class="w-auto px-2 m-0">Contact (For Quotation):</legend>
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
        <tbody id="contactTBody">
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
                    <a class='btn btn-primary' href='javascript:addContactRow()'> 
                        Add Row <i class="fas fa-plus-circle"></i></a>
                </td>
            </tr>
        </tfoot>
    </table>

    <legend class="w-auto px-2 m-0">Contact (Receiver):</legend>
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
        <tbody id="receiverTBody">
            <?php if (!empty($receiverModels)) : ?>
                <?php foreach ($receiverModels as $i => $receiverModel) : ?>
                    <?=
                    $this->render('_formClientReceiver_row', [
                        'contact' => $receiverModel,
                        'index' => $i,
                        'isUpdate' => $isUpdate
                    ])
                    ?>
                <?php endforeach; ?>
            <?php else : ?>
                <?=
                $this->render('_formClientReceiver_row', [
                    'contacts' => $receivers,
                    'contactModels' => $receivers,
                    'index' => 0,
                    'isUpdate' => $isUpdate
                ])
                ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <a class='btn btn-primary' href='javascript:addReceiverRow()'> 
                        Add Row <i class="fas fa-plus-circle"></i></a>
                </td>
            </tr>
        </tfoot>
    </table>

    <legend class="w-auto px-2 m-0">Contact (Account):</legend>
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
        <tbody id="accountTBody">
            <?php if (!empty($accountModels)) : ?>
                <?php foreach ($accountModels as $i => $accountModel) : ?>
                    <?=
                    $this->render('_formClientAccount_row', [
                        'contact' => $accountModel,
                        'index' => $i,
                        'isUpdate' => $isUpdate
                    ])
                    ?>
                <?php endforeach; ?>
            <?php else : ?>
                <?=
                $this->render('_formClientAccount_row', [
                    'contacts' => $accounts,
                    'contactModels' => $accounts,
                    'index' => 0,
                    'isUpdate' => $isUpdate
                ])
                ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <a class='btn btn-primary' href='javascript:addAccountRow()'> 
                        Add Row <i class="fas fa-plus-circle"></i></a>
                </td>
            </tr>
        </tfoot>
    </table>
    <div class="form-group">
        <?= Html::submitButton('Save <i class="fas fa-check"></i>', ['class' => 'btn btn-success']) ?>
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
    let receiverKey = <?= count($receiverModels) ?>;
    let accountKey = <?= count($accountModels) ?>;

    let isValidating = false;
    let isSubmitting = false;

    function addContactRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-contact']) ?>',
            type: 'GET',
            data: {key: currentKey, isUpdate: '<?= $isUpdate ?>'},
            success: function (response) {
                $('#contactTBody').append($.trim(response));
                currentKey++;
                attachEmailValidation();
            },
            error: function () {
                alert('Unable to add new contact row.');
            }
        });
    }

    function addReceiverRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-receiver']) ?>',
            type: 'GET',
            data: {key: receiverKey, isUpdate: '<?= $isUpdate ?>'},
            success: function (response) {
                $('#receiverTBody').append($.trim(response));
                receiverKey++;
                attachEmailValidation();
            },
            error: function () {
                alert('Unable to add new receiver row.');
            }
        });
    }

    function addAccountRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-account']) ?>',
            type: 'GET',
            data: {key: accountKey, isUpdate: '<?= $isUpdate ?>'},
            success: function (response) {
                $('#accountTBody').append($.trim(response));
                accountKey++;
                attachEmailValidation();
            },
            error: function () {
                alert('Unable to add new receiver row.');
            }
        });
    }

    function showError(inputElement, errorElementId, message) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            inputElement.classList.remove('is-valid');
            inputElement.classList.add('is-invalid');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function showSuccess(inputElement, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
            errorElement.style.display = 'none';
        }
    }

    function clearValidation(inputElement, errorElementId) {
        const errorElement = document.getElementById(errorElementId);
        if (errorElement) {
            inputElement.classList.remove('is-invalid', 'is-valid');
            errorElement.style.display = 'none';
        }
    }

    async function validateEmail(email) {
        if (!email || email.trim() === '')
            return {success: true};
        try {
            return await $.post(
                    '<?= \yii\helpers\Url::to(['/projectqrevision/check-email-exists']) ?>',
                    {email_address: email}
            );
        } catch (error) {
            return {success: false, error: {type: 'Server error. Please try again.'}};
        }
    }

    function attachEmailValidation() {
        $('#contactTBody, #receiverTBody, #accountTBody').off('input', '.email-input');
        $('#contactTBody, #receiverTBody, #accountTBody').on('input', '.email-input', function () {
            const errorId = $(this).data('error-id');
            clearValidation(this, errorId);
        });
    }

// =========================
// SINGLE FORM SUBMIT HANDLER
// =========================
    $('form').on('submit', async function (e) {
        e.preventDefault();

        if (isSubmitting)
            return false;

        isSubmitting = true;

        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"], input[type="submit"]');
        $submitButton.prop('disabled', true).text('Validating...');

        isValidating = true;
        let isValid = true;

        const emailInputs = $('.email-input');
        for (let i = 0; i < emailInputs.length; i++) {
            const input = emailInputs[i];
            const errorId = $(input).data('error-id');
            const email = $(input).val();

            if (email && email.trim() !== '') {
                const result = await validateEmail(email);
                if (result.success) {
                    showSuccess(input, errorId);
                } else {
                    showError(input, errorId, result.error?.type || 'Invalid email address');
                    isValid = false;
                }
            }
        }

        isValidating = false;

        if (isValid) {
            $submitButton.text('Saving...');
            $form[0].submit();
        } else {
            isSubmitting = false;
            $submitButton.prop('disabled', false).text('Save');
            alert('Please fix the invalid email addresses before saving.');
        }

        return false;
    });

    $(document).ready(function () {
        attachEmailValidation();
    });
</script>


