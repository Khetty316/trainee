<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\bootstrap4\ActiveForm;

?>

<div class="modal fade" id="checkboxWorkingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <?php
        $form = ActiveForm::begin([
                    'action' => $action,
                    'method' => 'post',
                    'options' => ['autocomplete' => 'off'],
                    'id' => 'checkboxForm'
        ]);
        ?>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkboxWorkingModelLabel"><?= $title ?></h5>
            </div>
            <div class="modal-body">               
                <div class="form-group">
                    <input type="text" style="display:none" class="form-control" id="stepList" name="stepList">
                    <input type="text" style="display:none" class="form-control" id="checkedList" name="checkedList">
                </div>    
                <div class="form-group">
                    <label for="remarks" class="col-form-label">Message:</label>
                    <?= yii\bootstrap4\Html::textarea("remarks", "", ['class' => 'form-control', 'id' => 'remarks', 'rows' => "4"]) ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" >Yes</button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>