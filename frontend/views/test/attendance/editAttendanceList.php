<?php

//use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use yii\helpers\Html;

$this->title = 'Edit Attendace List';
$this->params['breadcrumbs'][] = ['label' => "Panel's Test List", 'url' => ['/test/testing/index-master']];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = ['label' => 'Attendace List', 'url' => ["/test/attendanceform/index", 'id' => $testForm->id]];
$this->params['breadcrumbs'][] = $this->title;

echo yii\jui\DatePicker::widget(['options' => ['class' => 'hidden']]);
?>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<style>
    .sign-block{
        position: relative;
        box-shadow: -0.5px -0.5px 0 0 #ccc,
            0.5px -0.5px 0 0 #ccc,
            -0.5px  0.5px 0 0 #ccc,
            0.5px  0.5px 0 0 #ccc;
    }
</style>
<div class="leave-master-index">
    <div class="form-row m-3">
        <?php
        $form = ActiveForm::begin([
                    'options' => ['class' => 'w-100', 'autocomplete' => 'off'],
        ]);
        ?>
        <table class="table table-sm col-sm-12 col-md-12">
            <thead>
                <tr>
                    <th class="col-3">Name</th>
                    <th class="col-3">Organization</th>
                    <th class="col-3">Position/Designation</th>
                    <th class="col-3">Role</th>
                    <th>Signature</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <?php
                foreach ($attendanceList as $key => $attendance) {
                    echo $this->render('_formAttendanceItem', ['form' => $form, 'key' => $key, 'attendance' => $attendance, 'userList' => $userList]);
                    $this->registerJs("initializeAutocomplete($key);", \yii\web\View::POS_READY);
                    $this->registerJs("initializeSignaturePad($key);", \yii\web\View::POS_READY);
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
    var currentKey = <?= sizeof($attendanceList) ?>;
    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $("#tr_" + rowNum).hide();
            $("#toDelete-" + rowNum).val("1");
        }
    }

    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-attendance-item']) ?>',
            dataType: 'html',
            data: {
                key: currentKey
            }
        }).done(function (response) {
            $("#listTBody").append(response);

            initializeAutocomplete(currentKey);
            initializeSignaturePad(currentKey);
            currentKey++;
        });
    }

    function initializeAutocomplete(key) {
        var autocompleteInput = $("textarea[name='testDetailAttendance[" + key + "][attendeeName]']");
        autocompleteInput.autocomplete({
            source: <?= json_encode($userList) ?>,
            minLength: 1,
            autoFill: true,
            select: function (event, ui) {
                $(this).val(ui.item.value);
                $('textarea[name="testDetailAttendance[' + key + '][attendeeOrg]"]').val(ui.item.org);
                $('textarea[name="testDetailAttendance[' + key + '][attendeeDesign]"]').val(ui.item.designation);
            }
        });
    }

    function initializeSignaturePad(key) {
        var canvas = document.getElementById('signature-pad-' + key);
        var signaturePad = new SignaturePad(canvas);

        if ($('#signature-data-' + key).val() === '') {
            $('#signature-pad-' + key).show();
        } else {
            $('#signature-pad-' + key).hide();
        }

        $('#clear-signature-' + key).on('click', function () {
            signaturePad.clear();
            $('#signature-image-' + key).attr('src', '').hide();
            $('#signature-data-' + key).val('');
            $('#signature-pad-' + key).show();
        });

        function onCanvasClick() {
            var signatureData = signaturePad.toDataURL();
            $('#signature-data-' + key).val(signatureData);
        }

        function onTouchEnd() {
            var signatureData = signaturePad.toDataURL();
            $('#signature-data-' + key).val(signatureData);
        }

        canvas.addEventListener('click', onCanvasClick);
        canvas.addEventListener('touchend', onTouchEnd);

    }
</script>