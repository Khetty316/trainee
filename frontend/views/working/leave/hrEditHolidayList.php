<?php

//use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use yii\helpers\Html;

//$this->title = 'Holiday List';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
$this->params['breadcrumbs'][] = ['label' => 'Holiday List', 'url' => '/working/leavemgmt/hr-holiday-list?year=' . $selectYear];
$this->params['breadcrumbs'][] = 'Edit';


echo yii\jui\DatePicker::widget(['options' => ['class' => 'hidden']]);
?>
<div class="leave-master-index">
    <h5>Edit Holidays of Year <?= $selectYear ?></h5>

    <div class="form-row">
        <?php
        $form = ActiveForm::begin([
                    'options' => ['class' => 'w-100', 'autocomplete' => 'off'],
        ]);
        ?>
        <table class="table table-sm col-sm-12 col-md-6">

            <thead>
                <tr>
                    <th class="col-1">Date</th>
                    <th class="col-2" colspan="2">Holiday</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <?php
                foreach ($holidayList as $key => $holiday) {
                    echo $this->render('__ajaxHolidayItem', ['key' => $key, 'holiday' => $holiday]);
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">       
                        <a class='btn btn-primary' href='javascript:addRow()'> <i class="fas fa-plus-circle"></i></a>
                            <?= Html::submitButton('Save', ['class' => 'float-right btn btn-success']) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php
        ActiveForm::end();
        ?>
    </div>
    <div>
        <br/><br/><br/>

    </div>
</div>
<script>
    var currentKey = <?= sizeof($holidayList) ?>;
    var selectedYear = <?= $selectYear ?>;
    $(function () {
        initiateDatepicker();
    });

    function initiateDatepicker() {
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            yearRange: (selectedYear + ':' + selectedYear),
            minDate: new Date(selectedYear, 1 - 1, 1),
            maxDate: new Date(selectedYear, 11, 31)
        });
    }

    function checkDate(e) {
        let date = $(e).val();
        // Check if date format is correct
        if (!isValidReadDate(date) && date !== "") {
            $(e).val('');
        } else if (date !== "") {
            let bits = date.split('/');
            // Force date year to be selected year
            let newDate = bits[0] + '/' + bits[1] + '/' + selectedYear;
            $(e).val(newDate);
        }
    }

    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $("#tr_" + rowNum).hide();
            $("#toDelete-" + rowNum).val("1");
        }
    }


    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-holiday-item']) ?>',
            dataType: 'html',
            data: {
                key: currentKey++,
                year: selectedYear
            }
        }).done(function (response) {
            $("#listTBody").append(response);
            initiateDatepicker();
        });
    }
</script>