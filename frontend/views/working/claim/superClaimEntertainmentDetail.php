<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;
use common\models\myTools\MyCommonFunction;
use yii\bootstrap4\ActiveForm;

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
    echo $this->render('__ClaimNavBar', ['module' => 'super_claims', 'pageKey' => '4']);
    $this->params['breadcrumbs'][] = $this->title;

    $subTitle = "Detail";

    $minYear = $yearMinMax['minYear'] ? $yearMinMax['minYear'] : date("Y");
    $maxYear = $yearMinMax['maxYear'] ? $yearMinMax['maxYear'] : date("Y");


    if (date("Y") < $minYear) {
        $minYear = date("Y");
    }
    if (date("Y") > $maxYear) {
        $maxYear = date("Y");
    }

    $yearsList = [];

    for ($i = $maxYear; $i >= $minYear; $i--) {
        $yearsList[$i] = "Year $i";
    }

    $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['autocomplete' => 'off'],
                'id' => 'myForm',
                'action' => '/working/claim/super-claim-entertainment-detail'
    ]);
    echo '<div class="form-group row">';
    echo MyCommonFunction::myDropDownNoEmpty($yearsList, 'selectYear', 'form-control p-0 m-0 ml-3 col-sm-2', 'selectYear', $selectYear);
    echo Html::submitButton(
            'Show Summary ',
            [
                'class' => 'btn btn-success ml-3 ',
            ]
    );
    echo Html::a("CSV Year $selectYear <i class='fas fa-download'></i>",['/working/claim/super-claim-entertainment-detail','selectYear'=>$selectYear,'exportToExcel'=>true],['class'=>'btn btn-primary mx-2','target'=>'_blank']);
    echo '</div>';
    ActiveForm::end();
    ?>

    <div class="d-none d-lg-block pb-2">
        <ul class='nav nav-tabs'>
            <?php
            $linkList = array(
                '1' => array(
                    'name' => 'Monthly Summary',
                    'link' => '/working/claim/super-claim-entertainment'),
                '2' => array(
                    'name' => 'Detail',
                    'link' => '/working/claim/super-claim-entertainment-detail'),
            );

            echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $subTitle, 'linkList' => $linkList]);
            ?>
        </ul>
    </div>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Detail:</legend>
        <table class="table table-sm table-striped table-bordered">
            <tr class="thead-light text-center">
                <th>Date</th>
                <th>Name</th>
                <th>Project Code</th>
                <th>Amount (RM)</th>
            </tr>
            <?php
            foreach ($claimsDetails as $key => $details) {
                ?>
                <tr>
                    <td>
                        <?= MyFormatter::asDate_Read($details['invoice_date']) ?>
                    </td>
                    <td><?= $details['claimant'] ?></td>
                    <td><?= $details['project_account'] ?></td>
                    <td class="text-right"><?= MyFormatter::asDecimal2($details['total']) ?></td>
                </tr>
                <?php
            }
            if (!$claimsDetails) {
                ?> 
                <tr>
                    <th class="text-center" colspan="4">-- No Record --</th>
                </tr>
                <?php
            }
            ?>
        </table>
    </fieldset>
</div>

