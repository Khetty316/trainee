<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Edit Punchlist';
$this->params['breadcrumbs'][] = ['label' => "Panel's Test List", 'url' => ['/test/testing/index-master']];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = ['label' => 'Punchlist', 'url' => ["/test/punchlist/index", 'id' => $testForm->id]];
$this->params['breadcrumbs'][] = $this->title;
echo yii\jui\DatePicker::widget(['options' => ['class' => 'hidden']]);
?>
<div class="leave-master-index">
    <div class="form-row m-3">
        <?php
        $form = ActiveForm::begin([
                    'options' => ['class' => 'w-100', 'autocomplete' => 'off'],
        ]);
        ?>
        <table class="table table-sm col-sm-12">
            <thead class="text-center">
                <tr>
                    <th class="col-2">Form</th>
                    <th class="col-3">Error</th>
                    <th class="col-3">Comments / Remarks</th>
                    <th class="col-2">Date of Rectification</th>
                    <th class="col-2">Verified By</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <?php
                foreach ($punchlists as $key => $punchlist) {
                    echo $this->render('_formPunchlistItem', ['form' => $form, 'key' => $key, 'punchlist' => $punchlist, 'master' => $master]);
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12">       
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
    var currentKey = <?= sizeof($punchlists) ?>;
    var selectedYear = <?= date('Y') ?>;
    var masterId = <?= $master->id ?>;
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
        if (!isValidReadDate(date) && date !== "") {
            $(e).val('');
        } else if (date !== "") {
            let bits = date.split('/');
            let newDate = bits[0] + '/' + bits[1] + '/' + selectedYear;
            $(e).val(newDate);
        }
    }

    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $("#tr_" + rowNum).hide();
            $(".functionality-input").removeAttr("required");
            $("#toDelete-" + rowNum).val("1");
        }
    }

    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-punchlist-item']) ?>',
            dataType: 'html',
            data: {
                masterId: masterId,
                key: currentKey++,
                year: selectedYear
            }
        }).done(function (response) {
            $("#listTBody").append(response);
            initiateDatepicker();
        });
    }


</script>