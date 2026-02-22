<?php

use frontend\models\attendance\MonthlyAttendance;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\attendance\MonthlyAttendanceSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Monthly Attendances';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="app" class="monthly-attendance-index">
    <div>
        <h3><?= $this->title ?></h3>
    </div>
    <div class="row m-0 my-3 p-0">

        <div class="m-0 col-md-5">
            <?php
            echo Html::beginForm(['/attendance/index'], 'get', ['class' => 'row']);
            echo Html::dropDownList('year', $year, $yearList, ['class' => 'form-control col-md-5 ml-0']);
            echo Html::dropDownList('month', $month, $monthList, ['class' => 'form-control col-md-5 mx-1']);
            echo Html::submitButton('Select', ['class' => 'btn btn-primary']);
            echo Html::endForm();
            ?>
        </div>
        <div class="form-group row m-0 col-md-6">
            <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', "?year=$year&month=$month", ['class' => 'btn btn-primary', 'title' => "Reset Filter",]) ?>    
            <?=
            Html::a("Upload File <i class='fas fa-plus'></i>", "javascript:", [
                'title' => "Upload File",
                "value" => yii\helpers\Url::to('/attendance/bulk-process?month=' . $month . '&year=' . $year),
                "class" => "modalButton btn btn-success ml-1",
                'data-modaltitle' => "Upload File"
            ]);
            ?>
            <?=
            Html::a("Add Single User <i class='fas fa-plus'></i>", "javascript:", [
                'title' => "Add Single User",
                "value" => yii\helpers\Url::to('/attendance/create?month=' . $month . '&year=' . $year),
                "class" => "modalButton btn btn-success ml-1",
                'data-modaltitle' => "Add Single User"
            ]);
            ?>
        </div>
    </div>
    <div>
        <?php
        if ($errorNames) {
            ?>
            <div class="alert alert-danger rounded">
                These user/s were unable to be processed. High likely because user/s does not exist or their name differ in this system. Please check and reupload or add attendance manually:<br/>
                <?= $errorNames ?>
            </div>
        <?php } ?>
        <div>
            <?php
            $output = "WP = Workday Present &nbsp;&nbsp;&nbsp; OP = Unpaid Leave Present &nbsp;&nbsp;&nbsp; R/H P = Restday/Holiday/Present &nbsp;&nbsp;&nbsp; "
                    . "OT = Overtime &nbsp;&nbsp;&nbsp; AB = Absent &nbsp;&nbsp;&nbsp; LV = Leave Taken &nbsp;&nbsp;&nbsp; LI = Late In &nbsp;&nbsp;&nbsp; "
                    . "EO = Early Out &nbsp;&nbsp;&nbsp; MP = Miss Punch";
            echo $output;
            ?>
        </div>
        <?=
        $this->render('_viewAttendance', [
            'models' => $models,
            'year' => $year
        ])
        ?>
    </div>
</div>



<script>
    window.models = <?= $models ?>;

</script>
<script src="\js\vueTable.js"></script>