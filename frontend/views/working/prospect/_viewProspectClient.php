<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
?>

<div class="">
    <table class="table table-sm table-striped table-bordered">
        <thead  class="thead-light">
            <tr>
                <th>Company Name</th>
                <th>Service</th>
                <?php if ($showDetail == "true") { ?>
                    <th>Client P.I.C.</th>
                    <th>Client P.I.C. contact no.</th>
                    <th>Email Address</th>
                    <th>Attention</th> 
                <?php } ?>
                <th class="text-right">Amount (RM)</th>
                <th></th>
            </tr>
        </thead>
        <?php
        foreach ($prospectDetails as $key => $prospect) {
            ?>
            <tr class="<?= $prospect->is_awarded == 1 ? "table-warning" : "" ?>"  >
                <td><?= $prospect->client->company_name ?></td>
                <td><?= $prospect->service ?></td>
                <?php if ($showDetail == "true") { ?>
                    <td><?= $prospect->pic_name ?></td>
                    <td><?= $prospect->pic_contact ?></td>
                    <td><?= $prospect->pic_email ?></td>
                    <td><?= $prospect->email_attention ?></td>
                <?php } ?>

                <td class="text-right"><?= common\models\myTools\MyFormatter::asDecimal2($prospect->amount) ?></td>
                <td class="text-center">
                    <?= Html::a('<i class="far fa-edit text-primary"></i>', "javascript:", ['title' => 'Edit', "value" => \yii\helpers\Url::to('/working/prospect/create-client-ajax?id=' . $prospect->id), "class" => "modalButton_"]) ?>&nbsp;
                    <?php
                    if (sizeof($prospect['prospectDetailRevisions']) == 0) {
                        echo Html::a('<i class="far fa-trash-alt text-danger"></i>', "javascript:deleteClient(" . $prospect->id . ")", ['title' => 'Delete', 'data-confirm' => "Remove this client?"]);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan=" <?= $showDetail == "true" ? 8 : 4 ?>">
                    <div>
                        <?php
                        $url = '/working/prospect/create-client-revision-ajax?prospectDetailId=' . $prospect->id . '&masterProspectId=' . $prospect->prospect_master;
                        echo Html::a('Revision <i class="fas fa-plus"></i>', "javascript:", ["value" => $url, "class" => "modalButton_ btn btn-success btn-sm mr-3"]);
                        $url2 = '/working/prospect/copy-client-revision-ajax?prospectDetailId=' . $prospect->id . '&masterProspectId=' . $prospect->prospect_master;
                        echo Html::a('Revision (Copy) <i class="fas fa-plus"></i>', "javascript:", ["value" => $url2, "class" => "modalButton_ btn btn-success btn-sm mr-3"]);
                        ?>
                    </div>
                    <?php
                    foreach ($prospect['prospectDetailRevisions'] as $key => $revision) {
                        ?>
                        <div class="card mt-2 border-dark bg-light ml-5">
                            <div class="card-header border-dark p-0 m-0" >
                                <table class="table table-sm p-0 m-0">
                                    <tr>
                                        <td class="accordionHeader btn-header-link hoverItem p-0 m-0 mt-2 pr-2 <?= $revision->awarded_sts == 1 ? "badge-warning" : "" ?> "  id="pdr_<?= $revision->id ?>" data-toggle="collapse" data-target="#collapse_pdr_<?= $revision->id ?>">
                                            <span class=" text-success ml-2">
                                                <?= "Revision " . ($key + 1) . ": RM " . MyFormatter::asDecimal2($revision->amount) ?> <?= $revision->awarded_sts == 1 ? "<span class='text-danger'>(Awarded)</span>" : "" ?>
                                            </span>
                                        </td>
                                        <?php if ($awardSts == 0 && $prospect->prospectMaster->push_to_project == 0) { ?>
                                            <td style="width:1%" class=" p-0 m-0">    
                                                <a class="btn btn-success btn-sm float-right text-nowrap" data-confirm="Are you sure to set this revision to Awarded?" href="javascript:award(<?= $revision->id ?>)" >Award <i class="fas fa-check"></i></a>
                                            </td>
                                        <?php } else if ($revision->awarded_sts == 1 && $prospect->prospectMaster->push_to_project == 0) { ?>
                                            <td style="width:1%" class=" p-0 m-0">    
                                                <a class="btn btn-primary btn-sm float-right text-nowrap" data-confirm="Push this to Project?" href="javascript:createProject(<?= $revision->id ?>)" >Create Project <i class="fas fa-check"></i></a>
                                            </td>
                                        <?php } else if ($revision->awarded_sts == 1 && $prospect->prospectMaster->push_to_project == 1) { ?>
                                            <td style="width:1%" class=" p-0 m-0">    
                                                <a class="btn btn-primary btn-sm float-right text-nowrap" data-confirm="Go to Project?" href="<?= yii\helpers\Url::to('/working/project/view-by-project-code?projectCode=' . $revision->prospectDetail->prospectMaster->proj_code) ?>" >Go to Project <i class="fas fa-chevron-right"></i></a>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                </table>
                            </div>
                            <!--<button  style="z-index: 30;">HELLO</button>-->
                            <div id="collapse_pdr_<?= $revision->id ?>" class="collapse show" aria-labelledby="pdr_<?= $revision->id ?>"  >
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
                                                foreach ($revision['prospectDetailRevisionScopes'] as $key => $scope) {
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
                        </div>
                        <?php
                    }
                    ?>

                </td>
            </tr>
            <?php
        }
        if (sizeof($prospectDetails) == 0) {
            echo '<tr><td colspan="4" class="text-center text-danger">(NO RECORD FOUND)</td></tr>';
        }
        ?>
    </table>
</div>
<script>
    $(function () {
        $(".modalButton_").click(function () {
            $("#myModal").modal("show")
                    .find("#myModalContent")
                    .load($(this).attr('value'));
        });


    });

    function deleteClient(id) {
        var url = '/working/prospect/delete-client-ajax';
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                id: id
            }
        }).done(function (response) {
            if (response.data.success === true) {
                reloadClientDiv();
            }
        }).fail(function (xhr, textStatus, errorThrown) {
//            alert(xhr.responseText);
        });
    }


    function editClient(id) {
        
        var url = '/working/prospect/create-client-ajax';
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                id: id
            }
        }).done(function (response) {
            if (response.data.success === true) {
                reloadClientDiv();
            }
        }).fail(function (xhr, textStatus, errorThrown) {
//            alert(xhr.responseText);
        });
    }

    function award(revisionId) {
        var url = '/working/prospect/award-client-revision-ajax';
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                revisionId: revisionId
            }
        }).done(function (response) {
            if (response.data.success === true) {
                alert("Awarded!");
                reloadClientDiv();
            } else {
                alert("Fail to award. Kindly get help from IT Department.");
            }
        }).fail(function (xhr, textStatus, errorThrown) {
//            alert(xhr.responseText);
        });
    }


    function createProject(revisionId) {
        var url = '/working/project/create-project-ajax';
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: {
                revisionId: revisionId
            }
        }).done(function (response) {
            if (response.data.success === true) {
                alert("Created!");
                reloadClientDiv();
            } else {
                alert("Fail to create project. Kindly get help from IT Department.");
            }
        }).fail(function (xhr, textStatus, errorThrown) {
//            alert(xhr.responseText);
        });
    }

</script>