<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
?>

<div class="">
    <fieldset class = "form-group border-dark border col-sm-12 col-md-12 col-xl-8" style = "position:relative;">
        <legend class = "w-auto px-2 m-0 text-uppercase">Update Entitlement Details</legend>

        <?php
        $form = ActiveForm::begin([
                    'method' => 'post',
                    'options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data'],
        ]);
        ?><div class="form-main col-12">
            <div class="mb-3">
                <table  border="0">
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
                            <td> : <span class="bold"><?= $vEntitlement->year ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Leave Type</td>
                            <td> : <span class="bold"><?= $vEntitlement->leave_type_name ?></span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="form-group hidden">
                <?= Html::textInput('leaveEntitlementId', $vEntitlement["entitle_id"]) ?>
                <?= Html::textInput('leaveType', $vEntitlement->leave_type_code) ?>
                <?= Html::textInput('entitlementDetailsId', $vEntitlement->entitle_detail_id) ?>
            </div>
            <div class="form-group" id="multiMonth">
                <div class="form-row mt-2 mb-0">
                    <div class="col-sm-12 col-md-3 col-xl-3 align-middle text-center d-none d-md-block">
                        <h3>Days/Year</h3>
                    </div>
                    <div class="col-sm-12 col-md-3 col-xl-4 align-middle text-center d-none d-md-block">
                        <h3>Start Month</h3>
                    </div>
                    <div class="col-sm-12 col-md-3 col-xl-4 align-middle text-center d-none d-md-block">
                        <h3>End Month</h3>
                    </div>
                    <div class="col-sm-12 col-md-1 col-xl-1">
                    </div>
                </div>
                <?php
                foreach ($entitleDetail as $key => $singleDetail) {
                    echo $this->render('__formSingleEntitleDetailSub', [
                        'singleDetail' => $singleDetail,
                        'key' => $key]);
                }
                ?>
            </div>
            <div class="mb-3 p-0 col-md-11 col-xl-11 text-right">        
                <a class="btn btn-primary mb-2" href="javascript:addRow()">Add New Row <i class="fas fa-plus-circle" title="Add New Row"></i></a><br>
                <button type="submit" class="btn btn-success">Save <i class="far fa-save"></i></button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </fieldset>
</div>

<script>

    var currentKey = <?= sizeof($entitleDetail) ?>;
    var selectedYear = <?= $vEntitlement->year ?>;

    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $("#tr_" + rowNum).remove();
        }
    }

    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-leave-adjustment']) ?>',
            dataType: 'html',
            data: {
                key: currentKey++,
                year: selectedYear
            }
        }).done(function (response) {
            $("#multiMonth").append(response);
        });
    }

</script>