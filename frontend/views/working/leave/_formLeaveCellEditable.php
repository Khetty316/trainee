<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
?>

<div>
    <?php
    $month = common\models\myTools\MyCommonFunction::getMonthListArray();

    $form = ActiveForm::begin([
                'method' => 'post',
                'class' => 'myForm',
                'options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data'],
    ]);
    ?>
    <table border="0">
        <tbody>
            <tr>
                <td>Staff No.</td>
                <td> : <span class="bold"><?= $vEntitlement->staff_id ?></span>
                </td>
            </tr>
            <tr>
                <td>Name</td>
                <td> : <span class="bold"><?= $vEntitlement->fullname ?></span></td>
            </tr>
            <tr>
                <td>Year</td>
                <td> : <span class="bold"><?= $year ?></span>
                </td>
            </tr>
            <tr>
                <td>Leave Type</td>
                <td> : <span class="bold"><?= $leaveType->leave_type_name ?></span>
                </td>
            </tr>

        </tbody>
    </table>

    <div class="form-row mt-2">
        <div class="col-sm-12 col-md-12 col-xl-12">
            <?= $form->field($entitleDetail, 'days')->textInput(['type' => 'number', 'required' => true, 'placeholder' => 'Days', 'autofocus' => true, 'onFocus' => 'this.select()'])->label('Days per Year:')
            ?>
        </div>
        <?php if ($leaveType->is_pro_rata == 1) { ?>
            <div class="col-sm-12 col-md-12 col-xl-12">
                <?= $form->field($entitleDetail, 'month_start')->dropdownList($month, ['type' => 'number', 'required' => true, 'placeholder' => 'Days'])->label('Start Month:') ?>
            </div>
            <div class="col-sm-12 col-md-12 col-xl-12">
                <?= $form->field($entitleDetail, 'month_end')->dropdownList($month, ['type' => 'number', 'required' => true, 'placeholder' => 'Days'])->label('End Month:') ?>
            </div>
        <?php }
        ?>
        <small class="form-text text-success">**For no limit leave : -1</small>
    </div>


    <div class="modal-footer mt-5">
        <?php
        if ($leaveType->is_pro_rata == 1) {
            echo Html::a('Advance <i class="far fa-edit"></i>', ['entitlement-detail-adjustment', 'id' => $entitleDetail["id"]], [
                'class' => 'btn btn-primary',
                'title' => "Adding leave adjustment."
            ]);
        }
        ?>

        <button type="submit" class="btn btn-success" data-confirm="Are you sure to save">Save <i class="far fa-save"></i></button>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function () {
        $("input:text:visible:first").focus();
    });

    function autofocus() {
        $(this).focus();
    }
</script>