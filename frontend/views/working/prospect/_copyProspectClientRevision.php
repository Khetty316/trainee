<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
?>
<div class="prospect-client-rev-create">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <div class="prospect-client-rev-form">

        <?php
        $form = ActiveForm::begin([
                    'action' => '/working/prospect/copy-client-revision-ajax',
                    'method' => 'post',
                    'options' => ['autocomplete' => 'off', 'class' => 'form-inline'],
                    'id' => 'copyProspectClientRevisionForm',
        ]);

        ?>

        <div class="form-row w-100">
            <table class="table table-sm table-striped table-bordered w-100">
                <?php
                foreach ($prospectDetail as $key => $prospect) {
                    if ($prospect->id == $prospectDetailId) {
                        break;
                    }
                    ?>
                    <tr>
                        <td>
                            Client: <b><?= $prospect->client->company_name ?></b>, Service: <b><?= $prospect->service ?></b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            foreach ($prospect['prospectDetailRevisions'] as $key2 => $revision) {
                                ?>
                                <div class="card mt-2 border-dark bg-light ml-5">
                                    <div class="card-header m-0 p-1">
                                        <span class="accordionHeader"><?= "Revision " . ($key2 + 1) . ": RM " . MyFormatter::asDecimal2($revision->amount) ?></span>
                                        <a class="btn btn-success btn-sm float-right" data-confirm="Are you sure to copy this revision?" href="javascript:copyRevision(<?= $revision->id ?>)" >Select <i class="fas fa-check"></i></a>
                                    </div>
                                    <div class="card-body m-0 p-0" style="background-color:white">
                                        <?php if ($revision['prospectDetailRevisionScopes']) { ?>
                                            <div class='p-0'>
                                                <table class="table table-sm table-striped table-bordered">
                                                    <tr>
                                                        <th>Scope</th>
                                                        <th class="text-right">Amount (RM)</th>
                                                        <th class="text-right">Charges (%)</th>
                                                        <th class="text-right">Final Amt (RM)</th>
                                                    </tr>
                                                    <?php
                                                    $totalAmt = 0.00;
                                                    foreach ($revision['prospectDetailRevisionScopes'] as $key3 => $scope) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $scope['scope'] ?></td>
                                                            <td class="text-right"><?= MyFormatter::asDecimal2($scope['amount']) ?></td>
                                                            <td class="text-right"><?= MyFormatter::asDecimal2($scope['percentage']) . ' %' ?></td>
                                                            <td class="text-right"><?php
                                                                $finalAmt = ($scope['amount'] * $scope['percentage'] / 100);
                                                                $totalAmt += (float) $finalAmt;
                                                                echo MyFormatter::asDecimal2($finalAmt);
                                                                ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><b>Total: </b></td>
                                                        <td colspan="4" class="text-right bold"><?= MyFormatter::asDecimal2($totalAmt) ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

                        </td>

                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>

        <div class="form-row">
            <div class="col-xs-12 col-md-4">
            </div>
            <div class="col-xs-12 col-md-4">
            </div>
            <div class="col-xs-12 col-md-4">
            </div>
        </div>



    </div>
</div>



<?php ActiveForm::end(); ?>

<script>

    function copyRevision(id) {
        var form = $("#copyProspectClientRevisionForm");
        var data = form.serializeArray();
        var url = form.attr('action');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                revisionId:id,
                prospectDetailId:'<?= $prospectDetailId ?>'
                
            }
        }).done(function (response) {
            if (response.data.success === true) {
                $('#myModal').modal('toggle');
                reloadClientDiv();
            }
        }).fail(function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
        });
    }


</script>



