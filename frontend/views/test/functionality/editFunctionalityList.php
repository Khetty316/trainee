<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use frontend\models\test\RefTestStatus;

$this->title = 'Edit Functionality List';
$this->params['breadcrumbs'][] = ['label' => "Panel's Test List", 'url' => ['/test/testing/index-master']];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = ['label' => 'Functionality List', 'url' => ["/test/functionality/index", 'id' => $testForm->id]];
$this->params['breadcrumbs'][] = $this->title;

echo yii\jui\DatePicker::widget(['options' => ['class' => 'hidden']]);
?>
<div class="leave-master-index">
    <div class="form-row">
        <?php
        $form = ActiveForm::begin([
                    'options' => ['class' => 'w-100', 'autocomplete' => 'off', 'id' => 'myForm'],
        ]);
        ?>
        <div class="mt-5 mb-3">
            <h5 class="mb-0">Point of Test: 
                <span>
                    <?=
                    Html::a(
                            $detail->pot0->name . '-' . $detail->pot_val . ' <i class="fas fa-external-link-alt"></i>', "javascript:", [
                        'title' => "Edit",
                        "value" => yii\helpers\Url::to(['editpot', 'id' => $detail->id]),
                        "class" => "modalButton",
                        'data-modaltitle' => "Edit Point of Test"
                    ]);
                    ?>

                </span>
            </h5>
        </div>
        <table class="table table-sm col-sm-12">
            <thead class="text-center">
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Feeder Tag No.</th>
                    <td class="bold" rowspan="1" colspan="4">Output</td>
                </tr>
                <tr>
                    <th rowspan="1">Voltage At Power</br>Terminal (V)</th>
                    <th class="col-2" rowspan="1">Pass / Fail</br> / N/A</th>
                    <th rowspan="1">Wiring</br>Termination</br>Connection</th>
                    <th class="col-2" rowspan="1">Pass / Fail</br> / N/A</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <?php
                foreach ($functionalityList as $key => $functionality) {
                    echo $this->render('_formFunctionalityItem', ['form' => $form, 'key' => $key, 'functionality' => $functionality]);
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12">       
                        <a class='btn btn-primary' href='javascript:addRow()'> <i class="fas fa-plus-circle"></i></a>
                            <?= Html::submitButton('Save', ['class' => 'float-right btn btn-success save', 'data-status' => $testForm->status]) ?>
                            <?= Html::button('Pass All Wiring', ['class' => 'float-right btn btn-success mr-2 allwire-pass']); ?>
                            <?= Html::button('Pass All Voltage', ['class' => 'float-right btn btn-success mr-2 allvolt-pass']); ?>
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
    var currentKey = <?= sizeof($functionalityList) ?>;
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
            url: '<?= \yii\helpers\Url::to(['ajax-add-functionality-item']) ?>',
            dataType: 'html',
            data: {
                key: currentKey++
            }
        }).done(function (response) {
            $("#listTBody").append(response);

        });
    }

    $('.save').click(function () {
        var statusValue = $(this).data('status');
        var form = document.getElementById('myForm');
        if (statusValue === <?= RefTestStatus::STS_IN_TESTING ?>) {
            var inputs = form.querySelectorAll('input');
            inputs.forEach(function (input) {
                input.required = true;
            });
        }
    });

    $(document).on('click', '.allvolt-pass', function () {
        event.preventDefault();
        $('.succ-volt').prop('checked', true);
    });

    $(document).on('click', '.allwire-pass', function () {
        event.preventDefault();
        $('.succ-wire').prop('checked', true);
    });

</script>