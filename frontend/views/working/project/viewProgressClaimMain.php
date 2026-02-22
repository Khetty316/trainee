<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

$model = $dataProvider->getModels();
//$model = new frontend\models\working\project\ProjectProgressClaim();

$grandTotalSubmit = 0.00;
$grandTotalCertified = 0.00;
$grandTotalPaid = 0.00;
?>
<style>

    .table-bordered td, .table-bordered th{
        /*border:1px solid black!important;*/
    }



</style>
<div class="">

    <?= $this->render('__ProjectNavBar', ['pageKey' => '3', 'id' => $project->id, 'projectCode' => $project->proj_code, 'model' => $project]); ?>
    <p>
        <?php
        echo Html::a('Add Progress Claim <i class="fas fa-plus"></i>',
                "javascript:",
                [
                    'class' => 'btn btn-success btn-sm',
                    'title' => 'Add Record',
                    'data-toggle' => 'modal',
                    'data-target' => '#workingModel',
                    'data-remarks' => ''
        ]);
        ?>
    </p>


    <table class="table table-sm table-bordered table-striped" style="border: black!important">
        <thead  class="thead-light">
            <tr class="text-center text-primary">
                <th rowspan="2" class='b'>
                </th>
                <th colspan="5" class='bt bb brt'>
                    Progress Claim Submitted (Outgoing)
                </th>
                <th colspan="5" class='bt bb brt'>
                    Progress Claim Certified (Incoming)
                </th>
                <th rowspan="2" class='bt bb brt'>
                    Invoice<br/>Issued
                </th>
                <th rowspan="2" class='bt bb brt'>
                    Payment Received
                </th>
                <th rowspan="2" class='b'>
                    Balance
                </th>
            </tr>
            <tr class="text-center text-primary">
                <th class='bb'>Reference</th>
                <th class='bb' style="width:1px"><i class='far fa-file-alt'></i></th>
                <th class='bb'>Date</th>
                <th class='bb br'>Amount (RM)</th>
                <th class='bt bb brt bl'></th>
                <th class='bb'>Reference</th>
                <th class='bb' style="width:1px"><i class='far fa-file-alt'></i></th>
                <th class='bb'>Date</th>
                <th class='bb br'>Amount (RM)</th>
                <th class='bt bb brt'></th>
            </tr>
        </thead>
        <tbody>
            <?php
//            echo sizeof($model);
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
                        <?= ($claims->submit_file ? Html::a("<i class='far fa-file-alt fa-lg' ></i>", ['get-file-p-claim-main', 'id' => $claims->id, 'type' => 'submit'], ['target' => '_blank', 'title' => 'View Certified Document']) : "&nbsp;- ") ?>
                    </td>
                    <td>
                        <?= ($claims->submit_date ? MyFormatter::asDate_Read($claims->submit_date) : "&nbsp;- ") ?>
                    </td>
                    <td class="text-right bl br">
                        <?= MyFormatter::asDecimal2($claims->submit_amount) ?>
                    </td>
                    <td class="text-center brt">
                        <?=
                        Html::a('<i class="fas fa-edit"></i>',
                                "javascript:",
                                [
                                    'class' => 'text-success',
                                    'title' => 'Edit Submit Record',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#workingModel',
                                    'data-pclaimid' => $claims->id,
                                    'data-remarks' => Html::encode($claims->remarks),
                                    'data-amount' => MyFormatter::asDecimal2NoSeparator($claims->submit_amount),
                                    'data-file' => $claims->submit_file ? substr($claims->submit_file, 15) : "",
                                    'data-date' => MyFormatter::asDate_Read($claims->submit_date),
                                    'data-reference' => $claims->submit_reference
                        ])
                        ?>
                    </td>
                    <td>
                        <?= $claims->certified_reference ? $claims->certified_reference : "-" ?>
                    </td>
                    <td>
                        <?= ($claims->certified_file ? Html::a("<i class='far fa-file-alt fa-lg' ></i>", ['get-file-p-claim-main', 'id' => $claims->id, 'type' => 'certified'], ['target' => '_blank']) : "&nbsp;- ") ?>
                    </td>
                    <td>
                        <?= ($claims->certified_date ? MyFormatter::asDate_Read($claims->certified_date) : "&nbsp;-" ) ?>
                    </td>
                    <td class="text-right bl br">
                        <?= MyFormatter::asDecimal2_emptyDash($claims->certified_amount) ?>
                    </td>
                    <td class="text-center brt">
                        <?php
                        $balance = $claims->certified_amount;
                        if ($claims->certified_amount) {
                            echo Html::a('<i class="fas fa-edit"></i>',
                                    "javascript:",
                                    [
                                        'class' => 'text-success',
                                        'title' => 'Edit Certified Record',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#workingModel_certified',
                                        'data-pclaimid' => $claims->id,
                                        'data-remarks' => $claims->remarks ? Html::encode($claims->remarks) : '',
                                        'data-amount' => MyFormatter::asDecimal2NoSeparator($claims->certified_amount),
                                        'data-file' => $claims->certified_file ? substr($claims->certified_file, 15) : "",
                                        'data-date' => MyFormatter::asDate_Read($claims->certified_date),
                                        'data-reference' => $claims->certified_reference
                            ]);
                        } else {
                            echo Html::a('<i class="fas fa-plus"></i>',
                                    "javascript:",
                                    [
                                        'class' => 'text-primary m-0 p-0',
                                        'title' => 'Add Certified Record',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#workingModel_certified',
                                        'data-pclaimid' => $claims->id,
                                        'data-remarks' => Html::encode($claims->remarks),
                                        'data-file' => $claims->certified_file ? substr($claims->certified_file, 15) : ""
                            ]);
                        }
                        ?>
                    </td>
                    <td class="bl brt text-center">
                        <?= ($claims->invoice_file ? Html::a("<i class='fas fa-file-invoice fa-lg'></i>", ['get-file-p-claim-main', 'id' => $claims->id, 'type' => 'invoice'], ['target' => '_blank', 'title' => 'View Invoice']) 
                                :'<i class="fas fa-exclamation-circle fa-lg text-red" title="Invoice Needed!"></i>') ?>
                    </td>

                    <td class="bl brt">
                        <?php
                        $payments = $claims->projectProgressClaimPays;
                        ?>
                        <table class='table table-sm m-0 p-0 bl br'>
                            <?php
                            foreach ($payments as $key => $payment) {
                                echo '<tr>';
                                echo '<td class="text-right" style="background-color:white">' . MyFormatter::asDate_Read($payment->pay_date) . '</td>';
                                echo '<td class="text-right" style="background-color:white">RM ' . MyFormatter::asDecimal2($payment->amount) . '</td>';
                                echo '</tr>';
                                $balance -= $payment->amount;
                                $grandTotalPaid += $payment->amount;
                            }
                            ?>
                        </table>
                        <?php
                        echo Html::a('<i class="far fa-money-bill-alt fa-lg"></i>',
                                "javascript:",
                                [
                                    'class' => 'text-success m-0 p-0',
                                    'title' => 'Add Payment',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#workingModel_payment',
                                    'data-pclaimid' => $claims->id,
                        ]);
                        ?>
                    </td>
                    <td class="text-right bl br">
                        <?php
                        if ($claims->certified_amount == "") {
                            
                        } else if ($balance == 0) {
                            echo "<span class='text-primary'>Paid</span>";
                        } else if ($balance < 0) {
                            echo "<span class='text-danger'>RM " . MyFormatter::asDecimal2($balance) . "</span>";
                        } else {
                            echo "RM " . MyFormatter::asDecimal2($balance);
                        }

                        $grandTotalSubmit += $claims->submit_amount;
                        $grandTotalCertified += $claims->certified_amount;
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <th class='btt bl br bb'></th>
                <th colspan="4" class='text-right btt bl br bb'>
                    Total Submitted: RM <?= MyFormatter::asDecimal2($grandTotalSubmit) ?>
                </th>
                <th colspan="1" class='btt bl brt bb'></th>
                <th colspan="4" class='text-right btt bl br bb'>
                    Total Certified: RM <?= MyFormatter::asDecimal2($grandTotalCertified) ?>
                </th>
                <th colspan="1" class='btt bl brt bb'></th>
                <th colspan="1" class='text-right btt bl brt bb'>
                <th colspan="1" class='text-right btt bl brt bb'>
                    Total Paid:&nbsp;&nbsp;&nbsp;&nbsp;RM <?= MyFormatter::asDecimal2($grandTotalPaid) ?>
                </th>
                <th class='text-right btt bl br bb'>
                    <?php
                    $grandTotalBalance = $grandTotalCertified - $grandTotalPaid;
                    echo 'RM' . MyFormatter::asDecimal2($grandTotalBalance);
                    ?>
                </th>
            </tr>
<!--            <tr>
                <td colspan="10" class='text-right bl bb brt bold'>
                    Contract Sum:&nbsp;&nbsp;&nbsp;&nbsp;RM <?= MyFormatter::asDecimal2($project->contract_sum) ?>
                </td>
                <td  class='b'></td>
            </tr>
            <tr>
                <td colspan="10" class='text-right bl bb brt bold'>
                    Balance:&nbsp;&nbsp;&nbsp;&nbsp;RM <?= MyFormatter::asDecimal2($project->contract_sum - $grandTotalPaid) ?>
                </td>
                <td  class='b'></td>
            </tr>-->
        </tfoot>
    </table>
</div>

<div class="modal fade" id="workingModel" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/project/create-progress-claim-main',
                        'method' => 'post',
                        'id' => 'project-form',
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Progress Claim Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td>Project Code</td>
                            <td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $project->proj_code ?></span>
                                <input type='text' name='ProjectProgressClaim[project_id]' value='<?= $project->id ?>' class='hidden'/> 
                                <input type='text' name='ProjectProgressClaim[id]' id="ProjectProgressClaim-id3" value='' class='hidden'/> 
                            </td>
                        </tr>
                        <tr>
                            <td class="req">Reference</td><td>:</td>
                            <td>               
                                <input type="text" class="form-control" name="ProjectProgressClaim[submit_reference]" id="submitReference" required/>
                            </td>
                        </tr>
                        <tr>
                            <td>Attachment</td>
                            <td>:</td>
                            <td>               
                                <span id="currentSubmitFile"></span>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFile" name='ProjectProgressClaim[scannedFile]'>
                                    <label class="custom-file-label" for="customFile" id="customFileLabel">Choose file</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>:</td>
                            <td>             
                                <?=
                                \yii\jui\DatePicker::widget([
                                    'model' => $searchModel,
                                    'name' => 'ProjectProgressClaim[submit_date]',
                                    'language' => 'en',
                                    'dateFormat' => 'php:d/m/Y',
                                    'options' => ['class' => 'form-control'],
                                    'id' => 'submitDate'
                                ])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="req">Amount (RM)</td>
                            <td>:</td>
                            <td>               
                                <input type="number" class="form-control text-right" name="ProjectProgressClaim[submit_amount]" id="submitAmount" required/>
                            </td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td>:</td>
                            <td>               
                                <?= yii\bootstrap4\Html::textarea("ProjectProgressClaim[remarks]", "", ['class' => 'form-control', 'id' => 'remarks', 'rows' => 8]) ?>
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

<div class="modal fade" id="workingModel_certified" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/project/add-progress-claim-main-certified',
                        'method' => 'post',
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Certified Progress Claim </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td>Project Code</td><td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $project->proj_code ?></span>
                                <input type='text' name='ProjectProgressClaim[id]' id="ProjectProgressClaim-id" value='' class='hidden'/> 
                            </td>
                        </tr>
                        <tr>
                            <td class="req">Reference</td><td>:</td>
                            <td>               
                                <input type="text" class="form-control" name="ProjectProgressClaim[certified_reference]" id="certifiedReference" required/>
                            </td>
                        </tr>
                        <tr>
                            <td>Attachment</td><td>:</td>
                            <td>               
                                <span id="currentCertifiedFile"></span>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFile2" name='ProjectProgressClaim[scannedFile]'>
                                    <label class="custom-file-label" for="customFile2" id="customFileLabel2">Choose file</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Date</td><td>:</td>
                            <td>             
                                <?=
                                \yii\jui\DatePicker::widget([
                                    'model' => $searchModel,
                                    'name' => 'ProjectProgressClaim[certified_date]',
                                    'language' => 'en',
                                    'dateFormat' => 'php:d/m/Y',
                                    'options' => ['class' => 'form-control'],
                                    'id' => 'certifiedDate'
                                ])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="req">Amount (RM)</td><td>:</td>
                            <td>               
                                <input type="number" class="form-control text-right" name="ProjectProgressClaim[certified_amount]" id="certifiedAmount" required/>
                            </td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td>:</td>
                            <td>               
                                <?= yii\bootstrap4\Html::textarea("ProjectProgressClaim[remarks]", "", ['class' => 'form-control', 'id' => 'certified-remarks', 'rows' => 8]) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success submitBtn" id="submitBtn2">Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="workingModel_payment" tabindex="-1" role="dialog" aria-labelledby="workingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'action' => '/working/project/add-progress-claim-main-payment',
                        'method' => 'post',
                        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="workingModalLabel">Add Payment Record </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table border="0" width="100%" class='table table-borderless'>
                    <tbody>
                        <tr>
                            <td>Project Code</td><td>:</td>
                            <td>
                                <span class="bold" id="modal-idxno"><?= $project->proj_code ?></span>
                                <input type='text' name='ProjectProgressClaimPay[progress_claim_id]' id="ProjectProgressClaim-id2" value='' class='hidden'/> 
                            </td>
                        </tr>
                        <tr>
                            <td>Date</td><td>:</td>
                            <td>
                                <?=
                                \yii\jui\DatePicker::widget([
                                    'model' => $searchModel,
                                    'name' => 'ProjectProgressClaimPay[pay_date]',
                                    'language' => 'en',
                                    'dateFormat' => 'php:d/m/Y',
                                    'options' => ['class' => 'form-control'],
                                    'id' => 'payDate'
                                ])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="req">Amount (RM)</td><td>:</td>
                            <td>               
                                <input type="number" class="form-control text-right" name="ProjectProgressClaimPay[amount]" id="payAmount" required/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success submitBtn" id="submitBtn2">Submit</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>


    $(function () {
//        move();
        $('#workingModel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#ProjectProgressClaim-id3').val(button.data('pclaimid'));
            modal.find('#remarks').html(unescape(button.data('remarks')));
            modal.find('#submitAmount').val(button.data('amount'));
            modal.find('#currentSubmitFile').html(button.data('file'));
            modal.find('#submitDate').val(button.data('date'));
            modal.find('#submitReference').val(button.data('reference'));
        });

        $('#workingModel_certified').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#ProjectProgressClaim-id').val(button.data('pclaimid'));
            modal.find('#certified-remarks').html(unescape(button.data('remarks')));
            modal.find('#certifiedAmount').val(button.data('amount'));
            modal.find('#currentCertifiedFile').html(button.data('file'));
            modal.find('#certifiedDate').val(button.data('date'));
            modal.find('#certifiedReference').val(button.data('reference'));
        });

        $('#workingModel_payment').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#ProjectProgressClaim-id2').val(button.data('pclaimid'));
            modal.find('#payAmount').val(button.data('amount'));
            modal.find('#payDate').val(button.data('date'));
        });

        $('#customFile').on('change', function () {
            //get the file name
            var fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).next('#customFileLabel').html(fileName);
        });
        $('#customFile2').on('change', function () {
            //get the file name
            var fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).next('#customFileLabel2').html(fileName);
        });


        $(".modalButtonCustom").click(function () {
            var content = $(this).attr('value').replace(/(?:\r\n|\r|\n)/g, '<br>');
            $("#myModal").find('.modal-header').prepend("<h5>Remarks</h5>");
            $("#myModal").modal("show")
                    .find("#myModalContent").html(content);
        });
//
//        $(document).on('beforeSubmit', 'form', function (event) {
//            $(".submitBtn").attr('disabled', true).addClass('disabled');
//            $(".submitBtn").html('Submitting...');
//            var currentYOffset = window.pageYOffset;  // save current page postion.
//            setCookie('jumpToScrollPostion', currentYOffset, 2);
//        });
//
//
//        // check if we should jump to postion.
//        var jumpTo = getCookie('jumpToScrollPostion');
//        if (jumpTo !== "undefined" &&    jumpTo !== null ) {
//            console.log("jumpTo:"+jumpTo);
//            alert(jumpTo);
//            window.scrollTo(0, jumpTo);
//            eraseCookie('jumpToScrollPostion');  // and delete cookie so we don't jump again.
//        }

    });
//
//    var i = 0;
//
//    var maxWidth = <?= MyFormatter::asDecimal2NoSeparator($grandTotalPaid / ($project->contract_sum > 0 ? $project->contract_sum : 1) * 100) ?>;
//    function move() {
//        if (i == 0) {
//            i = 1;
//            var elem = document.getElementById("myBar");
//            var width = 1;
//            var id = setInterval(frame, 10);
//            function frame() {
//                if (width >= maxWidth) {
//                    clearInterval(id);
//                    i = 0;
//                } else {
//                    width++;
//                    elem.style.width = width + "%";
//                }
//            }
//        }
//        $("#myBar").html("<b class='text-white'>" + maxWidth + " %</b>");
//    }


</script>
