<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

//$model = $dataProvider->getModels();
//$model = new frontend\models\working\project\ProjectProgressClaim();

$grandTotalSubmit = 0.00;
$grandTotalCertified = 0.00;
$grandTotalPaid = 0.00;
?>

<div class="">

    <?= $this->render('__ProjectNavBar', ['pageKey' => '4', 'id' => $project->id, 'projectCode' => $project->proj_code, 'model' => $project]); ?>

    <?php if ($subConList) { ?>
        <table class="table table-striped table-sm table-bordered borderTable" style="border: black!important">
            <?php
            foreach ($subConList as $key => $subCon) {
                $model = $subCon->projectSubconClaims;
                ?>
                <thead  class="thead-light">
                    <tr class="text-center text-primary">
                        <th class='b'>Vendor Company Name</th>
                        <th class='b'>Description</th>
                        <th class='b'>File</th>
                        <th class='b'>Date</th>
                        <th class='b'>Amount (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="b"><?= $subCon->vendor->company_name ?></td>
                        <td class='b'><?= $subCon->description ?></td>
                        <td class='b'><?= ($subCon->file ? Html::a("<i class='far fa-file-alt' ></i>", ['get-file-subcon', 'id' => $subCon->id, 'type' => 'submit'], ['target' => '_blank']) : "&nbsp;- ") ?></td>
                        <td class='b'><?= MyFormatter::asDate_Read($subCon->date) ?></td>
                        <td class="text-right b"><?= MyFormatter::asDecimal2($subCon->amount) ?></td>

                    </tr>
                    <tr>
                        <td colspan="6" class='pl-5 pr-5 pb-3 b'>
                            <p>
                                <?php
                                echo Html::a('Add Record <i class="fas fa-plus"></i>',
                                        "javascript:",
                                        [
                                            'class' => 'btn btn-success btn-sm m-0',
                                            'title' => 'Create',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#workingModel',
                                            'data-sub_con_vendor' => $subCon->vendor->company_name,
                                            'data-sub_con_description' => $subCon->description,
                                            'data-id' => '',
                                            'data-proj_sub_id' => $subCon->id,
                                            'data-submit_reference' => '',
                                            'data-current_submit_file' => '',
                                            'data-submit_date' => '',
                                            'data-submit_amount' => '',
                                            'data-certified_reference' => '',
                                            'data-current_certified_file' => '',
                                            'data-certified_date' => '',
                                            'data-certified_amount' => '',
                                            'data-current_invoice_file' => '',
                                            'data-remarks' => '',
                                ]);
                                ?>
                            </p>
                            <?php if ($model) { ?>
                                <table class="table table-sm table-bordered table-striped" style="border: black!important">
                                    <thead  class="thead-light">
                                        <tr class="text-center text-primary">
                                            <th rowspan="2" class='b'></th>
                                            <th colspan="4" class='bt bb brt'>
                                                Progress Claim Submitted (Incoming)
                                            </th>
                                            <th colspan="4" class='bt bb brt'>
                                                Progress Claim Certified (Outgoing)
                                            </th>
                                            <th rowspan="2" class='b'  style="width:1px">
                                                Invoice<br/><i class='far fa-file-alt' ></i>
                                            </th>
                                            <th rowspan="2" class='b' style='width:1px'></th>
                                        </tr>
                                        <tr class="text-center text-primary">
                                            <th class='bb'>Reference</th>
                                            <th class='bb' style="width:1px"><i class='far fa-file-alt' ></i></th>
                                            <th class='bb'>Date</th>
                                            <th class='bt bb brt bl'>Amount (RM)</th>
                                            <th class='bb'>Reference</th>
                                            <th class='bb' style="width:1px"><i class='far fa-file-alt' ></i></th>
                                            <th class='bb'>Date</th>
                                            <th class='bb br'>Amount (RM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($model as $key => $claims) {
                                            ?>
                                            <tr>
                                                <td class="bl br">
                                                    <?=
                                                    ($claims->remarks ? Html::a('<i class="fas fa-info-circle text-warning"></i>', '#',
                                                                    [
                                                                        "value" => Html::encode($claims->remarks),
                                                                        "title" => "Remarks",
                                                                        "class" => "modalButtonCustom"
                                                                    ]
                                                            ) : "&nbsp;- ")
                                                    ?>
                                                </td>
                                                <td class="bl">
                                                    <?= $claims->submit_reference ? $claims->submit_reference : "-" ?>
                                                </td>
                                                <td>
                                                    <?=
                                                    $claims->submit_file ? (Html::a("<i class='far fa-file-alt' ></i>", ['get-file-p-claim-sub', 'id' => $claims->id, 'type' => 'submit'], ['target' => '_blank'])) : "&nbsp;- "
                                                    ?>
                                                </td>

                                                <td>
                                                    <?= ($claims->submit_date ? MyFormatter::asDate_Read($claims->submit_date) : "&nbsp;- ") ?>
                                                </td>
                                                <td class="text-right bl brt">
                                                    <?= MyFormatter::asDecimal2($claims->submit_amount) ?>
                                                </td>
                                                <td>
                                                    <?= $claims->certified_reference ? $claims->certified_reference : "-" ?>
                                                </td>
                                                <td>
                                                    <?= $claims->certified_file ? Html::a("<i class='far fa-file-alt' ></i>", ['get-file-p-claim-sub', 'id' => $claims->id, 'type' => 'certified'], ['target' => '_blank']) : "&nbsp;- " ?>
                                                </td>
                                                <td>
                                                    <?= $claims->certified_date ? MyFormatter::asDate_Read($claims->certified_date) : "&nbsp;-" ?>
                                                </td>
                                                <td class="text-right bl brt">
                                                    <?php
                                                    echo MyFormatter::asDecimal2_emptyDash($claims->certified_amount);
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?=
                                                    $claims->invoice_file ? (Html::a("<i class='far fa-file-alt' ></i>", ['get-file-p-claim-sub', 'id' => $claims->id, 'type' => 'invoice'], ['target' => '_blank'])) : "&nbsp;- "
                                                    ?>
                                                </td>
                                                <td class="text-center br">
                                                    <?php
                                                    echo Html::a('<i class="fas fa-edit"></i>',
                                                            "javascript:",
                                                            [
                                                                'class' => 'text-success',
                                                                'title' => 'Edit',
                                                                'data-toggle' => 'modal',
                                                                'data-target' => '#workingModel',
                                                                'data-sub_con_vendor' => $subCon->vendor->company_name,
                                                                'data-sub_con_description' => $subCon->description,
                                                                'data-id' => $claims->id,
                                                                'data-proj_sub_id' => $claims->proj_sub_id,
                                                                'data-submit_reference' => ($claims->submit_reference),
                                                                'data-current_submit_file' => $claims->submit_file ? substr($claims->submit_file, 15) : "",
                                                                'data-submit_date' => MyFormatter::asDate_Read($claims->submit_date),
                                                                'data-submit_amount' => MyFormatter::asDecimal2NoSeparator($claims->submit_amount),
                                                                'data-certified_reference' => ($claims->certified_reference),
                                                                'data-current_certified_file' => $claims->certified_file ? substr($claims->certified_file, 15) : "",
                                                                'data-certified_date' => MyFormatter::asDate_Read($claims->certified_date),
                                                                'data-certified_amount' => MyFormatter::asDecimal2NoSeparator($claims->certified_amount),
                                                                'data-current_invoice_file' => $claims->invoice_file ? substr($claims->invoice_file, 15) : "",
                                                                'data-remarks' => ($claims->remarks),
                                                    ]);
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $grandTotalSubmit += $claims->submit_amount;
                                            $grandTotalCertified += $claims->certified_amount;
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class='btt bl br bb'></th>
                                            <th colspan="4" class='text-right btt bl brt bb'>
                                                Total Submitted: RM <?= MyFormatter::asDecimal2($grandTotalSubmit) ?>
                                            </th>
                                            <th colspan="4" class='text-right btt bl brt bb'>
                                                Total Certified: RM <?= MyFormatter::asDecimal2($grandTotalCertified) ?>
                                            </th>
                                            <th class='text-right btt bl br bb' colspan="2">

                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <?php
                            } else {
                                echo Html::tag('p', '-- No Payment Records --', ['class' => 'text-center w-100']);
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
                <?php
            }
            ?>
        </table>
        <?php
    } else {
        echo Html::tag('p', '-- No Sub Con Records --', ['class' => 'text-center w-100']);
    }
    ?>


</div>

<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/project/create-progress-claim-sub',
                        'method' => 'post',
                        'id' => 'project-form',
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Progress Claim Record (Sub Con)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless table-sm'>
                    <tbody>
                        <tr>
                            <td>Project Code</td><td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $project->proj_code ?></span>
                                <input type='text' name='ProjectSubconClaim[proj_sub_id]' id="ProjectSubconClaim-proj_sub_id" class='hidden'/> 
                                <input type='text' name='ProjectSubconClaim[id]' id="ProjectSubconClaim-id" value='' class='hidden'/> 
                            </td>
                        </tr>
                        <tr>
                            <td>Vendor/Sub Con:</td><td>:</td>
                            <td>
                                <span class="bold" id="sub_con_vendor"></span>       
                            </td>
                        </tr>
                        <tr>
                            <td>Description</td><td>:</td>
                            <td>
                                <span class="bold" id="sub_con_description"></span>
                            </td>
                        </tr>
                        <!---------------- INPUTS ------------------>
                        <!---------------- SUBMITTED ------------------>
                        <tr><td colspan="3"><hr/>
                                <h6 class="font-weight-bold">SUBMITTED (INCOMING)</h6></td></tr>
                        <tr>
                            <td>Reference</td><td>:</td>
                            <td>
                                <input type="text" class="form-control" id="submitReference" name='ProjectSubconClaim[submit_reference]'/>
                            </td>
                        </tr>
                        <tr>
                            <td>Attachment</td><td>:</td>
                            <td>
                                <span id="currentSubmitFile"></span>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="scannedFileSubmit" name='ProjectSubconClaim[scannedFileSubmit]'/>
                                    <label class="custom-file-label" for="scannedFileSubmit" id="scannedFileSubmitLabel">Choose file</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>:</td>
                            <td> 
                                <?=
                                \yii\jui\DatePicker::widget([
                                    'name' => 'ProjectSubconClaim[submit_date]',
                                    'language' => 'en',
                                    'dateFormat' => 'php:d/m/Y',
                                    'options' => ['class' => 'form-control'],
                                    'id' => 'submitDate'
                                ])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Amount (RM)</td><td>:</td>
                            <td>
                                <input type="number" class="form-control text-right" name="ProjectSubconClaim[submit_amount]" id="submitAmount"/>
                            </td>
                        </tr>
                        <!---------------- CERTIFIED ------------------>
                        <tr><td colspan="3"><hr/>
                                <h6 class="font-weight-bold">CERTIFIED (OUTGOING)</h6></td></tr>
                        <tr>
                        <tr>
                            <td>Reference</td><td>:</td>
                            <td>
                                <input type="text" class="form-control" id="certifiedReference" name='ProjectSubconClaim[certified_reference]'/>
                            </td>
                        </tr>
                        <tr>
                            <td>Attachment</td><td>:</td>
                            <td>
                                <span id="currentCertifiedFile"></span>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="scannedFileCertified" name='ProjectSubconClaim[scannedFileCertified]'>
                                    <label class="custom-file-label" for="scannedFileCertified" id="scannedFileCertifiedLabel">Choose file</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Certified Date</td><td>:</td>
                            <td> 
                                <?=
                                \yii\jui\DatePicker::widget([
                                    'name' => 'ProjectSubconClaim[certified_date]',
                                    'language' => 'en',
                                    'dateFormat' => 'php:d/m/Y',
                                    'options' => ['class' => 'form-control'],
                                    'id' => 'certifiedDate'
                                ])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Certified Amount (RM)</td><td>:</td>
                            <td>
                                <input type="number" class="form-control text-right" name="ProjectSubconClaim[certified_amount]" id="certifiedAmount"/>
                            </td>
                        </tr>
                        </tr>
                        <!---------------- INVOICE ------------------>
                        <tr><td colspan="3"><hr/>
                                <h6 class="font-weight-bold">INVOICE (INCOMING)</h6></td></tr>
                        <tr>
                        <tr>
                            <td>Attachment</td><td>:</td>
                            <td>
                                <span id="currentInvoiceFile"></span>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="scannedFileInvoice" name='ProjectSubconClaim[scannedFileInvoice]'>
                                    <label class="custom-file-label" for="scannedFileInvoice" id="scannedFileInvoiceLabel">Choose file</label>
                                </div>
                            </td>
                        </tr>
                        <tr><td colspan="3"><hr/></td></tr>
                        <tr>
                            <td>Remarks</td><td>:</td>
                            <td>
                                <?= yii\bootstrap4\Html::textarea("ProjectSubconClaim[remarks]", "", ['class' => 'form-control', 'id' => 'remarks', 'rows' => 8]) ?>
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
//  move();
        $('#workingModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal  
            var modal = $(this);

            modal.find('#sub_con_vendor').text(button.data('sub_con_vendor'));
            modal.find('#sub_con_description').html(button.data('sub_con_description'));
            modal.find('#ProjectSubconClaim-id').val(button.data('id'));
            modal.find('#ProjectSubconClaim-proj_sub_id').val(button.data('proj_sub_id'));
            modal.find('#submitReference').val(button.data('submit_reference'));
            modal.find('#currentSubmitFile').html(button.data('current_submit_file'));
            modal.find('#submitDate').val(button.data('submit_date'));
            modal.find('#submitAmount').val(button.data('submit_amount'));
            modal.find('#certifiedReference').val(button.data('certified_reference'));
            modal.find('#currentCertifiedFile').html(button.data('current_certified_file'));
            modal.find('#certifiedDate').val(button.data('certified_date'));
            modal.find('#certifiedAmount').val(button.data('certified_amount'));
            modal.find('#currentInvoiceFile').html(button.data('current_invoice_file'));
            modal.find('#remarks').val(button.data('remarks'));
        });


        $(".modalButtonCustom").click(function () {
            var content = $(this).attr('value').replace(/(?:\r\n|\r|\n)/g, '<br>');
            $("#myModal").find('.modal-header').prepend("<h5>Remarks</h5>");
            $("#myModal").modal("show")
                    .find("#myModalContent").html(content);
        });

        $('#scannedFileSubmit').on('change', function () {
            var fileName = $(this).val();
            $(this).next('#scannedFileSubmitLabel').html(fileName);
        });
        $('#scannedFileCertified').on('change', function () {
            var fileName = $(this).val();
            $(this).next('#scannedFileCertifiedLabel').html(fileName);
        });
        $('#scannedFileInvoice').on('change', function () {
            var fileName = $(this).val();
            $(this).next('#scannedFileInvoiceLabel').html(fileName);
        });


    });


</script>
