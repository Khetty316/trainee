<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
?>
<style>
    .borderTable th{
        border: 1px solid black!important;
    }

</style>
<div class="project-master-view">
    <?= $this->render('__ProjectNavBar', ['pageKey' => '6', 'id' => $model->id, 'projectCode' => $model->proj_code, 'model' => $model]); ?>

    <fieldset class="border border-dark pl-3 pr-3 pb-3">
        <legend class="w-auto pl-2 pr-2 mb-0 text-primary">Closing</legend>

        <table class="table table-sm table-bordered">
            <thead class="thead-light text-center">
                <tr>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Updated By</th>
                    <th>Other Info</th>
                    <th style="width:3%">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Certificate of Practical Completion (CPC)</td>
                    <td class="text-center"><?= $closing->cpc_status ? "Yes" : "No" ?></td>
                    <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($closing->cpc_amount) ?></td>
                    <td class=""><?= $closing->cpcBy['fullname'] ?></td>
                    <td class="bg-secondary"></td>
                    <td class="text-center">    
                        <?=
                        Html::a('<i class="fas fa-edit"></i>',
                                "javascript:",
                                [
                                    'class' => 'text-success ml-2 mr-2',
                                    'title' => 'Update',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#model_cpc',
                        ]);
                        ?></td>
                </tr>
                <tr>
                    <td>Certificate of Making Good Practise(CMGD)</td>
                    <td class="text-center"><?= $closing->cmgd_status ? "Yes" : "No" ?></td>
                    <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($closing->cmgd_amount) ?></td>
                    <td class=""><?= $closing->cmgdBy['fullname'] ?></td>
                    <td class="bg-secondary"></td>
                    <td class="text-center">    
                        <?=
                        Html::a('<i class="fas fa-edit"></i>',
                                "javascript:",
                                [
                                    'class' => 'text-success ml-2 mr-2',
                                    'title' => 'Update',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#model_cmgd',
                        ]);
                        ?></td>
                </tr>
                <tr>
                    <td>Final Account</td>
                    <td class="text-center"><?= $closing->final_acc_status ? "Yes" : "No" ?></td>
                    <td class="text-right"><?= MyFormatter::asDecimal2_emptyDash($closing->final_acc_amount) ?></td>
                    <td class=""><?= $closing->finalAccBy['fullname'] ?></td>
                    <td>Payment Received: <b><?= $closing->pay_rec_status ? "Yes" : "No" ?></b> </td>
                    <td class="text-center">    
                        <?=
                        Html::a('<i class="fas fa-edit"></i>',
                                "javascript:",
                                [
                                    'class' => 'text-success ml-2 mr-2',
                                    'title' => 'Update',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#model_finAcc',
                        ]);
                        ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>


<!-- MODAL -->
<?php $modalName = "cpc"; ?>
<div class="modal fade" id="model_<?= $modalName ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel_<?= $modalName ?>" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
            <?php
            $formDate = ActiveForm::begin([
                        'action' => '/working/project/update-closing',
                        'method' => 'post',
                        'id' => 'modalForm_' . $modalName,
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel_<?= $modalName ?>">Update CPC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td style="width:40%">Project Code</td>
                            <td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $model->proj_code ?></span>
                                <input type='text' name='ProjectClosing[id]' value='<?= $closing->id ?>' class='hidden'/>
                                <input type='text' name='updateType' value='<?= $modalName ?>' class='hidden'/>
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td><?= Html::dropDownList('ProjectClosing[cpc_status]', $closing->cpc_status, ['0' => 'No', '1' => 'Yes'], ['class' => 'form-control']) ?></td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>:</td>
                            <td><input type="number" name="ProjectClosing[cpc_amount]" class="form-control text-right" value="<?= $closing->cpc_amount ?>"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success submitBtn" id="submitBtn">Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $modalName = "cmgd"; ?>
<div class="modal fade" id="model_<?= $modalName ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel_<?= $modalName ?>" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
            <?php
            $formDate = ActiveForm::begin([
                        'action' => '/working/project/update-closing',
                        'method' => 'post',
                        'id' => 'modalForm_' . $modalName,
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel_<?= $modalName ?>">Update CMGD</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td style="width:40%">Project Code</td>
                            <td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $model->proj_code ?></span>
                                <input type='text' name='ProjectClosing[id]' value='<?= $closing->id ?>' class='hidden'/>
                                <input type='text' name='updateType' value='<?= $modalName ?>' class='hidden'/>
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td><?= Html::dropDownList('ProjectClosing[cmgd_status]', $closing->cmgd_status, ['0' => 'No', '1' => 'Yes'], ['class' => 'form-control']) ?></td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>:</td>
                            <td><input type="number" name="ProjectClosing[cmgd_amount]" class="form-control text-right"  value="<?= $closing->cmgd_amount ?>"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success submitBtn" id="submitBtn">Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php $modalName = "finAcc"; ?>
<div class="modal fade" id="model_<?= $modalName ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel_<?= $modalName ?>" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
            <?php
            $formDate = ActiveForm::begin([
                        'action' => '/working/project/update-closing',
                        'method' => 'post',
                        'id' => 'modalForm_' . $modalName,
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel_<?= $modalName ?>">Update Final Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td style="width:40%">Project Code</td>
                            <td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $model->proj_code ?></span>
                                <input type='text' name='ProjectClosing[id]' value='<?= $closing->id ?>' class='hidden'/>
                                <input type='text' name='updateType' value='<?= $modalName ?>' class='hidden'/>
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td><?= Html::dropDownList('ProjectClosing[final_acc_status]', $closing->final_acc_status, ['0' => 'No', '1' => 'Yes'], ['class' => 'form-control']) ?></td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td>:</td>
                            <td><input type="number" name="ProjectClosing[final_acc_amount]" class="form-control text-right"  value="<?= $closing->final_acc_amount ?>"/></td>
                        </tr>
                        <tr>
                            <td>Payment Received</td>
                            <td>:</td>
                            <td><?= Html::dropDownList('ProjectClosing[pay_rec_status]', $closing->pay_rec_status, ['0' => 'No', '1' => 'Yes'], ['class' => 'form-control']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success submitBtn" id="submitBtn">Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>

    $(function () {
        $('.customFile').on('change', function () {
            //get the file name
            var fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).next('.customFileLabel').html(fileName);
        });

        $('#model_date').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#projDateInput').val(button.data('date'));
            modal.find('#projDateType').val(button.data('datetype'));
            modal.find('#projDateTypeName').html(button.data('datetypename'));
        });



    });
</script>