<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">


    <?php
//    $this->title = "HR Claim List";
//    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <?php
    echo $this->render('__ClaimNavBar', ['module' => 'super_claims', 'pageKey' => '3']);
    $this->params['breadcrumbs'][] = $this->title;
    ?>



    <?php
    foreach ($dataList as $year => $claimants) {
        ?>

        <div class="card mt-2 border-dark bg-light">
            <div class="card-header hoverItem border-dark btn-header-link <?= (date("Y") == $year) ? "" : "collapsed" ?>" id="heading_scopes" 
                 data-toggle="collapse" data-target="#collapse_<?= $year ?>" aria-expanded="false" aria-controls="collapse_scopes">
                <span class="accordionHeader">Year <?= $year ?></span>
            </div>
            <div id="collapse_<?= $year ?>" class="collapse <?= (date("Y") == $year) ? "show" : "" ?>" aria-labelledby="heading_scopes"  >
                <div class="card-body" style="background-color:white">
                    <table class="table table-sm table-striped table-bordered">
                        <thead  class="thead-dark">
                            <tr class=''>
                                <th>Staff Full Name</th>
                                <th class='text-right'>Total Medical Claims (RM)</th>
                            </tr>
                        </thead>
                        <?php
                        foreach ($claimants as $claimant => $detail) {
                            ?>
                            <tr>
                                <td> 
                                    <?php
                                    $url = '/working/claim/super-claim-medical-detail-ajax?year=' . $year . '&claimantId=' . $detail['claimant_id'];
                                    echo Html::a($claimant, "#", ["value" => \yii\helpers\Url::to($url), "class" => "modalButton"]);
                                    ?>
                                </td>
                                <td class='text-right'>  <?= MyFormatter::asDecimal2($detail['total']) ?> </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>

        <?php
    }
    ?>


</div>

