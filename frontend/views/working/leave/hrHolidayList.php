<?php

//use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use yii\helpers\Html;

//$this->title = 'Holiday List';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-master-index">
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '8']) ?>

    <?php
    $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['autocomplete' => 'off'],
                'id' => 'myForm',
                'action' => '/working/leavemgmt/hr-holiday-list'
    ]);
    ?> 
    <div class="form-row mx-1">
        <div class='form-group'>
            <?= MyCommonFunction::myDropDownNoEmpty($yearsList, 'year', 'form-control', 'selectYear', $selectYear) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?> 

    <?php
    /**
     * Form removed by Paul @ 2022-11-21
     * No longer using Excel
     */
    /* $form2 = ActiveForm::begin([
      'method' => 'post',
      'options' => ['autocomplete' => 'off', 'enctype' => 'multipart/form-data', 'class' => ''],
      'id' => 'myForm',
      'action' => '/working/leavemgmt/hr-batch-upload-holiday-list'
      ]); */
    ?> 
    <!--<div class="form-row mx-1">
        <div class='form-group'>
            <div class="custom-file pull-left">
    <?php //= Html::fileInput('excelFile', '', ['class' => 'custom-file-input', 'required' => true, 'accept' => '.csv']) ?>
                <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                <div class="invalid-feedback">Example invalid custom file feedback</div>
            </div>
        </div>
        <div class='form-group'>
    <?php //= Html::submitButton('Upload <i class="fa fa-upload"></i>', ['class' => 'btn btn-success ml-3 ']) ?>
        </div>
        <div class='form-group'>        
    <?php //= Html::a('CSV Year ' . $selectYear . ' <i class="fas fa-download"></i>', ['/working/leavemgmt/hr-holiday-list-excel', 'year' => $selectYear], ['class' => 'btn btn-primary mx-2','target'=>'_blank']) ?>
        </div>-->
</div>
<?php /* ActiveForm::end(); */ ?>

<h5> Holidays of Year <?= $selectYear ?> <?= Html::a('Edit <i class="far fa-edit"></i>', "hr-edit-holiday-list?year=$selectYear", ['class' => 'btn btn-success ml-2']) ?></h5>

<table class="table table-striped col-lg-6 table-bordered table-sm" id='holidayList'>
    <thead>
        <tr>
            <th scope="col">Date</th>
            <th scope="col" class='d-none d-md-block'>Day</th>
            <th scope="col">Holiday</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($holidayList as $key => $holiday) {
            $isSunSetRed = (MyFormatter::asDay_Read($holiday->holiday_date) == 'Sun' ? 'table-danger' : '');
            echo '<tr>';
            echo '<td>' . MyFormatter::asDate_Read($holiday->holiday_date) . '</td>';
            echo "<td class='d-none d-md-block $isSunSetRed'>" . MyFormatter::asDayLong_Read($holiday->holiday_date) . '</td>';
            echo '<td>' . $holiday->holiday_name . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>
</div>
<script>
    $(function () {

        $("#selectYear").change(function () {
            $("#holidayList").hide();
            $("#myForm").submit();
        });
    });
</script>