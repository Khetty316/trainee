<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

//$model=new \frontend\models\working\project\ProjectMaster();
$voList = $model->projectVos; // List of V.O.
$subConList = $model->projectSubcons;
?>
<style>
    .borderTable th{
        border: 1px solid black!important;
    }

</style>
<div class="project-master-view">
    <?= $this->render('__ProjectNavBar', ['pageKey' => '1', 'id' => $model->id, 'projectCode' => $model->proj_code, 'model' => $model]); ?>

    <fieldset class="border border-dark pl-3 pr-3 pb-3">
        <legend class="w-auto pl-2 pr-2 mb-0 text-primary">Dates</legend>
        <div class="form-row">
            <div class="col-sm-12 col-md-2">
                Award Date:<br/><b><?= $model->award_date ? MyFormatter::asDate_Read($model->award_date) : "(No Record) " ?></b>
                <?php
                echo Html::a('<i class="fas fa-edit"></i>',
                        "javascript:",
                        [
                            'class' => 'text-success ml-2 mr-2',
                            'title' => 'Edit',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_date',
                            'data-date' => MyFormatter::asDate_Read($model->award_date),
                            'data-datetype' => 'award',
                            'data-datetypename' => 'Award Date'
                ]);
                ?>
            </div>
            <div class="col-sm-12 col-md-2">
                Commencement Date:<br/><b><?= $model->commencement_date ? MyFormatter::asDate_Read($model->commencement_date) : "(No Record)" ?></b>
                <?php
                echo Html::a('<i class="fas fa-edit"></i>',
                        "javascript:",
                        [
                            'class' => 'text-success ml-2 mr-2',
                            'title' => 'Edit',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_date',
                            'data-date' => MyFormatter::asDate_Read($model->commencement_date),
                            'data-datetype' => 'comm',
                            'data-datetypename' => 'Commencement Date'
                ]);
                ?>
            </div>
            <div class="col-sm-12 col-md-2">                  
                EOT Date:<br/><b><?= $model->eot_date ? MyFormatter::asDate_Read($model->eot_date) : "(No Record)" ?></b>
                <?php
                echo Html::a('<i class="fas fa-edit"></i>',
                        "javascript:",
                        [
                            'class' => 'text-success ml-2 mr-2',
                            'title' => 'Edit',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_date',
                            'data-date' => MyFormatter::asDate_Read($model->eot_date),
                            'data-datetype' => 'eot',
                            'data-datetypename' => 'EOT Date'
                ]);
                ?>
            </div>
            <div class="col-sm-12 col-md-2">                 
                Handover Date:<br/><b><?= $model->handover_date ? MyFormatter::asDate_Read($model->handover_date) : "(No Record)" ?></b>
                <?php
                echo Html::a('<i class="fas fa-edit"></i>',
                        "javascript:",
                        [
                            'class' => 'text-success ml-2 mr-2',
                            'title' => 'Edit',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_date',
                            'data-date' => MyFormatter::asDate_Read($model->handover_date),
                            'data-datetype' => 'hand',
                            'data-datetypename' => 'Handover Date'
                ]);
                ?>
            </div>

            <div class="col-sm-12 col-md-2">      
                DLP Expiry Date:<br/><b><?= $model->dlp_expiry_date ? MyFormatter::asDate_Read($model->dlp_expiry_date) : "(No Record)" ?></b>
                <?php
                echo Html::a('<i class="fas fa-edit"></i>',
                        "javascript:",
                        [
                            'class' => 'text-success ml-2 mr-2',
                            'title' => 'Edit',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_date',
                            'data-date' => MyFormatter::asDate_Read($model->dlp_expiry_date),
                            'data-datetype' => 'dlp',
                            'data-datetypename' => 'DLP Expiry Date'
                ]);
                ?>
            </div>
        </div>
    </fieldset>
    <br/>
    <fieldset class="border border-dark p-3">
        <legend class="w-auto pl-2 pr-2 text-primary">Variation Order (V.0.)</legend>
        <div class="form-row">
            <?php if ($voList) { ?>
                <table class="table table-striped table-sm table-bordered borderTable">
                    <thead  class="thead-light">
                        <tr class="text-center text-primary">
                            <th>V.O. Number</th>
                            <th>Description</th>
                            <th>File</th>
                            <th>Date</th>
                            <th>Amount (RM)</th>
                            <th style="width:1px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($voList as $key => $vo) {
                            ?>
                            <tr>
                                <td class="bl"><?= $vo->ref_no ?></td>
                                <td><?= $vo->description ?></td>
                                <td><?= ($vo->file ? Html::a("<i class='far fa-file-alt' ></i>", ['get-file-vo', 'id' => $vo->id, 'type' => 'submit'], ['target' => '_blank']) : "&nbsp;- ") ?></td>
                                <td><?= MyFormatter::asDate_Read($vo->date) ?></td>
                                <td class="text-right br"><?= MyFormatter::asDecimal2($vo->amount) ?></td>
                                <td class="text-right br"><?php
                                    echo Html::a('<i class="fas fa-edit"></i>',
                                            "javascript:",
                                            [
                                                'class' => 'text-success ml-2 mr-2',
                                                'title' => 'Edit',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#model_VO',
                                                'data-vo_id' => $vo->id,
                                                'data-amount' => MyFormatter::asDecimal2NoSeparator($vo->amount),
                                                'data-file' => $vo->file ? substr($vo->file, 15) : "",
                                                'data-date' => MyFormatter::asDate_Read($vo->date),
                                                'data-ref_no' => $vo->ref_no,
                                                'data-description' => $vo->description
                                    ]);
                                    ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <tfoot class="thead-light">
                        <tr class="btt font-weight-bold ">
                            <td colspan="4" class="text-right bl bb">Total V.O.</td>
                            <td class="text-right bb br"><?= MyFormatter::asDecimal2($model->getVoTotal()) ?></td>
                            <td class="text-right bb br"></td>
                        </tr>
                    </tfoot>
                </table>
                <?php
            } else {
                echo Html::tag('p', '-- No Records --', ['class' => 'text-center w-100']);
            }
            ?>
            <p>
                <?php
                echo Html::a('Add V.O. <i class="fas fa-plus"></i>',
                        "javascript:",
                        [
                            'class' => 'btn btn-success btn-sm',
                            'title' => 'Process',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_VO',
                        ]
                );
                ?>
            </p>
        </div>
    </fieldset>
    <br/>
    <fieldset class="border border-dark p-3">
        <legend class="w-auto pl-2 pr-2 text-primary">Sub Contractor</legend>
        <div class="form-row">
            <?php if ($subConList) { ?>
                <table class="table table-striped table-sm table-bordered borderTable">
                    <thead  class="thead-light">
                        <tr class="text-center text-primary">
                            <th>Vendor Company Name</th>
                            <th>Description</th>
                            <th>File</th>
                            <th>Date</th>
                            <th>Amount (RM)</th>
                            <th style="width:1px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subconTotal = 0;
                        foreach ($subConList as $key => $subCon) {
                            $subconTotal += $subCon->amount;
                            ?>
                            <tr>
                                <td class="bl"><?= $subCon->vendor->company_name ?></td>
                                <td><?= $subCon->description ?></td>
                                <td><?= ($subCon->file ? Html::a("<i class='far fa-file-alt' ></i>", ['get-file-subcon', 'id' => $subCon->id, 'type' => 'submit'], ['target' => '_blank']) : "&nbsp;- ") ?></td>
                                <td><?= MyFormatter::asDate_Read($subCon->date) ?></td>
                                <td class="text-right br"><?= MyFormatter::asDecimal2($subCon->amount) ?></td>
                                <td class="text-right br"><?php
                                    echo Html::a('<i class="fas fa-edit"></i>',
                                            "javascript:",
                                            [
                                                'class' => 'text-success ml-2 mr-2',
                                                'title' => 'Edit',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#model_Subcon',
                                                'data-subcon_id' => $subCon->id,
                                                'data-vendor_id' => $subCon->vendor_id,
                                                'data-subcon_name' => $subCon->vendor->company_name,
                                                'data-description' => $subCon->description,
                                                'data-file' => $subCon->file ? substr($subCon->file, 15) : "",
                                                'data-date' => MyFormatter::asDate_Read($subCon->date),
                                                'data-amount' => MyFormatter::asDecimal2NoSeparator($subCon->amount),
                                    ])
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                    <tfoot class="thead-light">
                        <tr class="btt font-weight-bold ">
                            <td colspan="4" class="text-right bl bb">Total Sub Contract</td>
                            <td class="text-right bb br"><?= MyFormatter::asDecimal2($subconTotal) ?></td>
                            <td class="text-right bb br"></td>
                        </tr>
                    </tfoot>
                </table>
                <?php
            } else {
                echo Html::tag('p', '-- No Records --', ['class' => 'text-center w-100']);
            }
            ?>
            <p>
                <?php
                echo Html::a('Add Subcon. <i class="fas fa-plus"></i>',
                        "javascript:",
                        [
                            'class' => 'btn btn-success btn-sm',
                            'title' => 'Process',
                            'data-toggle' => 'modal',
                            'data-target' => '#model_Subcon',
                        ]
                );
                ?>
            </p>
        </div>
    </fieldset>
</div>


<!-- MODAL -->


<?php $modalName = "date"; ?>
<div class="modal fade" id="model_<?= $modalName ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel_<?= $modalName ?>" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
            <?php
            $formDate = ActiveForm::begin([
                        'action' => '/working/project/update-dates',
                        'method' => 'post',
                        'id' => 'modalForm_' . $modalName,
                        'options' => ['autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel_<?= $modalName ?>">Change Date</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td>Project Code</td>
                            <td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $model->proj_code ?></span>
                                <input type='text' name='projId' value='<?= $model->id ?>' class='hidden'/>
                                <input type='text' name='projDateType' value='' class='hidden' id="projDateType"/>
                            </td>
                        </tr>
                        <tr>
                            <td id="projDateTypeName"></td>
                            <td>:</td>
                            <td>             
                                <?=
                                \yii\jui\DatePicker::widget([
                                    'name' => 'projDateInput',
                                    'language' => 'en',
                                    'dateFormat' => 'php:d/m/Y',
                                    'options' => ['class' => 'form-control'],
                                    'id' => 'projDateInput'
                                ])
                                ?>
                            </td>
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

<?php $modalName = "VO"; ?>
<div class="modal fade" id="model_<?= $modalName ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel_<?= $modalName ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $formVO = ActiveForm::begin([
                        'action' => '/working/project/create-vo',
                        'method' => 'post',
                        'id' => 'modalForm_' . $modalName,
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel_<?= $modalName ?>">Create V.O. Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td>Project Code</td>
                            <td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $model->proj_code ?></span>
                                <input type='text' name='ProjectVo[project_id]' value='<?= $model->id ?>' class='hidden'/> 
                                <input type='text' name='ProjectVo[id]' value='' class='hidden' id="vo_id"/> 
                            </td>
                        </tr>
                        <tr>
                            <td>V.O. Number</td>
                            <td>:</td>
                            <td>               
                                <input type="text" class="form-control" name="ProjectVo[ref_no]" id="vo_ref_no" />
                            </td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td>:</td>
                            <td>               
                                <input type="text" class="form-control" name="ProjectVo[description]" id="vo_description" />
                            </td>
                        </tr>
                        <tr>
                            <td>Attachment</td>
                            <td>:</td>
                            <td>               
                                <span id="vo_currentFile"></span>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input customFile" id="customFile<?= $modalName ?>" name='ProjectVo[scannedFile]'>
                                    <label class="custom-file-label customFileLabel" for="customFile<?= $modalName ?>" >Choose file</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>:</td>
                            <td>             
                                <?=
                                \yii\jui\DatePicker::widget([
                                    'name' => 'ProjectVo[date]',
                                    'language' => 'en',
                                    'dateFormat' => 'php:d/m/Y',
                                    'options' => ['class' => 'form-control'],
                                    'id' => 'vo_date'
                                ])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="req">Amount (RM)</td>
                            <td>:</td>
                            <td>               
                                <input type="number" class="form-control text-right" name="ProjectVo[amount]" id="vo_amount" required/>
                            </td>
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

<?php $modalName = "Subcon"; ?>
<div class="modal fade" id="model_<?= $modalName ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel_<?= $modalName ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $formSubcon = ActiveForm::begin([
                        'action' => '/working/project/create-subcon',
                        'method' => 'post',
                        'id' => 'modalForm_' . $modalName,
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel_<?= $modalName ?>">Create Sub Contractor. Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td>Project Code</td>
                            <td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $model->proj_code ?></span>
                                <input type='text' name='ProjectSubcon[project_id]' value='<?= $model->id ?>' class='hidden'/> 
                                <input type='text' name='ProjectSubcon[id]' value='' class='hidden' id="ProjectSubcon_id"/> 
                            </td>
                        </tr>
                        <tr>
                            <td>Vendor<req/></td>
                    <td>:</td>
                    <td>               
                        <input type="text" class="form-control hidden" name="ProjectSubcon[vendor_id]" id="ProjectSubcon_vendor_id" />
                        <?php
                        echo AutoComplete::widget([
                            'clientOptions' => [
                                'source' => $vendorList,
                                'minLength' => '1',
                                'autoFill' => true,
                                'delay' => 100,
                                'appendTo' => '#modalForm_' . $modalName,
                                'select' => new \yii\web\JsExpression("function( event, ui ) { $('#ProjectSubcon_vendor_id').val(ui.item.id) }"),
                                'search' => new \yii\web\JsExpression("function( event, ui ) { $('#ProjectSubcon_vendor_id').val(''); }"),
                                'change' => new \yii\web\JsExpression("function( event, ui ) { $(this).val((ui.item ? ui.item.value : '')); }"),
                            ],
                            'options' => [
                                'class' => 'form-control',
                                'required' => 'true',
                                'id' => 'vendor_name'
                            ]
                        ]);
                        ?>
                    </td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td>:</td>
                        <td>               
                            <input type="text" class="form-control" name="ProjectSubcon[description]" id="ProjectSubcon_description" />
                        </td>
                    </tr>
                    <tr>
                        <td>Attachment</td>
                        <td>:</td>
                        <td>               
                            <span id="ProjectSubcon_currentFile"></span>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input customFile" id="customFile<?= $modalName ?>" name='ProjectSubcon[scannedFile]'>
                                <label class="custom-file-label customFileLabel" for="customFile<?= $modalName ?>" >Choose file</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td>             
                            <?=
                            \yii\jui\DatePicker::widget([
                                'name' => 'ProjectSubcon[date]',
                                'language' => 'en',
                                'dateFormat' => 'php:d/m/Y',
                                'options' => ['class' => 'form-control'],
                                'id' => 'ProjectSubcon_date'
                            ])
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="req">Amount (RM)</td>
                        <td>:</td>
                        <td>               
                            <input type="number" class="form-control text-right" name="ProjectSubcon[amount]" id="ProjectSubcon_amount" required/>
                        </td>
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

        $('#model_VO').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#vo_id').val(button.data('vo_id'));
            modal.find('#vo_amount').val(button.data('amount'));
            modal.find('#vo_currentFile').html(button.data('file'));
            modal.find('#vo_date').val(button.data('date'));
            modal.find('#vo_ref_no').val(button.data('ref_no'));
            modal.find('#vo_description').val(button.data('description'));
        });

        $('#model_Subcon').on('show.bs.modal', function (event) {

            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#ProjectSubcon_id').val(button.data('subcon_id'));
            modal.find('#ProjectSubcon_vendor_id').val(button.data('vendor_id'));
            modal.find('#vendor_name').val(button.data('subcon_name'));
            modal.find('#ProjectSubcon_description').val(button.data('description'));
            modal.find('#ProjectSubcon_currentFile').html(button.data('file'));
            modal.find('#ProjectSubcon_date').val(button.data('date'));
            modal.find('#ProjectSubcon_amount').val(button.data('amount'));
        });



    });
</script>