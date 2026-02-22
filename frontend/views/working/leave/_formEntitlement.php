<?php

use yii\bootstrap4\ActiveForm;
?>
<style>
    .form-group {
        /*width: 80%;*/
        margin: 5px !important;
    }

</style>

<div>
    <?php
    $form3 = ActiveForm::begin([
//            'action' => '/working/leavemgmt/add-entitlement-to-user?' . ['id' => $user->id, 'selectYear' => $selectYear],
                'method' => 'post',
                'options' => ['autocomplete' => 'off'],
    ]);
    ?>
    <table class="mx-3 mb-3" border="0">
        <tbody>
            <tr>
                <td>Staff No.</td>
                <td> : <span class="bold"><?= $user->staff_id ?></span>
                </td>
            </tr>
            <tr>
                <td>Name</td>
                <td> : <span class="bold"><?= $user->fullname ?></span></td>
            </tr>
            <tr>
                <td>Year</td>
                <td> : <span class="bold"><?= $selectYear ?></span>
                </td>
            </tr>

        </tbody>
    </table>

    <div id="leaveEntitlement">
        <?php
        if ($modelEntitle->annual_bring_forward_days === null) {
            $modelEntitle->annual_bring_forward_days = 0;
        }
        ?>
        <div class="form-group hidden">
            <?= $form3->field($modelEntitle, 'user_id')->textInput(['value' => $user->id]) ?>
            <?= $form3->field($modelEntitle, 'year')->textInput(['value' => $selectYear]) ?>
            <?= $form3->field($modelEntitle, 'sick_entitled_del')->textInput(['value' => 1]) ?>
            <?= $form3->field($modelEntitle, 'annual_entitled_del')->textInput(['value' => 1]) ?>
        </div>


        <?php
        $month = [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];
        ?>

        <div class="col-sm-12 col-md-12 col-xl-12 ">
            <?= $form3->field($modelEntitle, 'annual_bring_forward_days')->textInput(['type' => 'number', 'required' => true, 'placeholder' => 'Days'])->label('Brought Forward (From Last Year):') ?>
        </div>

        <div class="col-sm-12 col-md-12 col-xl-12">
            <table border="0" style="width: 100%; height: 100%;">
                <?php
                foreach ($leaveType as $key => $value) {
                    foreach ($modelDetail as $detail) {
                        if ($detail['leave_type_code'] == $value['leave_type_code']) {
                            ?>
                            <div class="form-group hidden">
                                <?= $form3->field($detail, "[$key]leave_type_code")->textInput(['value' => $leaveType[$key]["leave_type_code"]]) ?>
                            </div>

                            <tr class="col-xl-12">
                            <div class="col-xl-2">
                                <?php if ($value['is_pro_rata'] == 1) { ?>
                                    <th class="justify-content-between d-flex align-items-center" style="height: 100%;">
                                        <span class="pl-1"><?= $value["leave_type_name"] ?></span><span>:</span>
                                    </th>
                                <?php } else { ?>
                                    <th class="hidden" style="height: 100%;"><span class="pl-1"><?= $value["leave_type_name"] ?></span><span>:</span></th>
                                <?php } ?>
                            </div>
                            <div class="col-xl-8">
                                <?php if ($value['is_pro_rata'] == 1) { ?>
                                    <td>
                                        <div class="row d-flex" style="height: 100%;">
                                            <div class="col-xl-3 mr-0 pr-0">
                                                <?= $form3->field($detail, "[$key]days")->textInput(['class' => 'leaveDays form-control text-right', 'type' => 'number', 'required' => true, 'placeholder' => 'Days'])->label(false); ?>
                                            </div>
                                            <div class="col-xl-1 m-0 p-0 align-items-center d-flex justify-content-center">
                                                <span class="">days.</span>
                                            </div>
                                            <div class="col-xl-1 m-0 p-0 align-items-center d-flex justify-content-center">
                                                <span class="">From</span>
                                            </div>
                                            <div class="col-xl-3 m-0 p-0">
                                                <?= $form3->field($detail, "[$key]month_start")->dropdownList($month, ['class' => 'form-control m-0', 'type' => 'number', 'required' => true, 'placeholder' => 'Month'])->label(false); ?>
                                            </div>
                                            <div class="col-xl-1 m-0 p-0 align-items-center d-flex justify-content-center">
                                                <span class="">to</span>
                                            </div>
                                            <div class="col-xl-3 m-0 pl-0">
                                                <?= $form3->field($detail, "[$key]month_end")->dropdownList($month, ['class' => 'form-control m-0', 'type' => 'number', 'required' => true, 'placeholder' => 'Month', 'value' => !empty($detail->month_end) ? $detail->month_end : '12'])->label(false); ?>
                                            </div>
                                        </div>
                                    </td>
                                <?php } else { ?>
                                    <?= $form3->field($detail, "[$key]days")->textInput(['class' => 'leaveDays hidden', 'type' => 'number', 'required' => true])->label(false); ?>
                                <?php } ?>
                            </div>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </table>
        </div>


        <br><span class=" text-success">**For no limit leave : -1</span><br>
        <span class=" text-success">**The days are default value. Change if needed.</span><br/>
        <span class=" text-success">**Months defaulted to a year period. Change only when needed.</span>
    </div>


    <div class="modal-footer mt-2">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success submitBtn" data-confirm="Save?" >Submit</button>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script>
    $(function ()
    {
        $(".leaveDays").on('change keyup', function () {
            var leaveInput = this.value;
            var leaveId = this.getAttribute('id');

            $.ajax({
                url: "<?= yii\helpers\Url::to(["/working/leavemgmt/calculate-days-deduct", "id" => $user["id"]]) ?>",
                type: 'post',
                dataType: "json",
                data: {
                    leaveDays: leaveInput,
                    leaveId: leaveId
                },
                success: function (data) {
                    console.log(data);
                    $("#leaveentitlementdetails-" + data.number + "-days_deduct").val(data.deductedDays);
                }
            });
        });
    });

</script>
