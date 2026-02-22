<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;
use yii\web\JsExpression;
use common\models\myTools\MyFormatter;

$this->title = 'Report - Quotation Hit Chart';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><?= Html::encode($this->title) ?></h3>
<div class="chart-quotation-hit">

    <?=
    $this->render('_dateForm', [
        'model' => $model
    ])
    ?>

    <div class="row">
        <div class="col-6">
            <?=
            $this->renderAjax('_chartQuotationHitEngineer', [
                'coordinators' => $chartDataCoord['coordinators'],
                'chartDataJson' => $chartDataCoord['chartDataJson'],
                'dateFrom' => $chartDataCoord['dateFrom'],
                'dateTo' => $chartDataCoord['dateTo'],
            ])
            ?>
        </div>
        <div class="col-6">
            <?=
            $this->renderAjax('_chartQuotationHitType', [
                'chartDataJson' => $chartDataType['chartDataJson'],
                'dateFrom' => $chartDataType['dateFrom'],
                'dateTo' => $chartDataType['dateTo'],
            ])
            ?>
        </div>
    </div>


</div>