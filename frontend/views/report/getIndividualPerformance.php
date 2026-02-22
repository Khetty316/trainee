<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;
use yii\web\JsExpression;
use common\models\myTools\MyFormatter;
use yii\helpers\ArrayHelper;
use common\models\User;

$this->title = 'Report - Individual Performance';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><?= Html::encode($this->title) ?></h3>
<div class="work-assignment-master-form">
    <?php
    $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-sm-6\">{input}\n{error}</div>",
                    'labelOptions' => ['class' => 'col-sm-6 control-label'],
                ],
                'options' => ['autocomplete' => 'off']
    ]);
    ?>
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <?=
                        $form->field($model, 'userId', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->dropdownList(ArrayHelper::map(User::getStaffList_productionAssignee(), "id", "fullname"))
                        ->label("Staff");
                ?>
            </div>

        </div>
    </div>
    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <?=
                        $form->field($model, 'dateFrom', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(DatePicker::className(), [
                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                            'dateFormat' => 'dd/MM/yyyy',
                            'clientOptions' => [
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                            ],
                        ])->label("Complete Date From");
                ?>
            </div>

        </div>
    </div>

    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">

                <?=
                        $form->field($model, 'dateTo', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->widget(DatePicker::className(), [
                            'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                            'dateFormat' => 'dd/MM/yyyy',
                            'clientOptions' => [
                                'showButtonPanel' => true,
                                'closeText' => 'Close',
                            ],
                        ])->label("Complete Date To");
                ?>
            </div>
        </div>
    </div>

    <div class="form-group my-0">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <?=
                        $form->field($model, 'is_internalProject')
                        ->dropDownList(\frontend\models\report\ReportingModel::PROJECT_TYPE_OPTIONS)
                        ->label("Project Type");
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <?= Html::submitButton('Search <i class="fas fa-search"></i>', ['class' => 'btn btn-primary float-right']) ?>
            </div>
        </div>
    </div>
    <div class="form-group" id='viewArea1'>
        <div class="col-md-12">
            <?php
            $totalMonetoryPerformance = [];
            if (!empty($reportDetail)) {


                foreach ($reportDetail as $type => $report) {
                    if (!isset($totalMonetoryPerformance[$type])) {
                        $totalMonetoryPerformance[$type] = 0;
                    }
                    ?>
                    <h4><?= $type ?></h4>
                    <table class="table table-sm table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <!--<th>Complete Date</th>-->
                                <th class="text-center" style="width:10%;">Project Code</th>
                                <th class="text-center">Project Name</th>
                                <th class="text-center" style="width:12%;">Panel Code</th>
                                <th class="text-center" style="width:30%;">Panel Description</th>
                                <th>Amount (RM)</th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php
                            foreach ($report as $key => $detail) {
                                ?>
                                <tr>
                                    <!--<td><?php //= $detail['complete_date']     ?></td>-->
                                    <td><?= $detail['project_production_code'] ?></td>
                                    <td><?= $detail['project_name'] ?></td>
                                    <td><?= $detail['project_production_panel_code'] ?></td>
                                    <td><?= $detail['panel_description'] ?></td>
                                    <td class="text-right">
                                        <?php
                                        $amt = MyFormatter::asDecimal2NoSeparator($detail['performanceAmount'] ?? 0);
                                        $totalMonetoryPerformance[$type] += $amt;
                                        echo $amt;
                                        ?>
                                    </td>

                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <th colspan="5" class="text-right">
                                    <h4>Total:   <u><?= MyFormatter::asDecimal2_emptyZero($totalMonetoryPerformance[$type]) ?></u></h4>
                                </th>
                            </tr>
                        </tbody>
                    </table>

                    <?php
                }
            } else {
                echo "<div class='tdnowrap'>-- No Record --</div>";
            }
            ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<script>
    $(function () {

        $("input").on('change', function () {
            $("#viewArea1, #viewArea2").html('');
        });
    });

</script>