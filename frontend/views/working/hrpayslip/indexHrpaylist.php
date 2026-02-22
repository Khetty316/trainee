<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\hr\HrPayslipSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$selectedMonthYear = $selectedMonthYear ? $selectedMonthYear : "";
?>
<div class="hr-payslip-index">




    <?php
    echo $this->render('__HrpayslipNavBar', ['module' => 'hr_payslip', 'pageKey' => '2']);
    $this->params['breadcrumbs'][] = ['label' => 'HR - Payroll'];
    $this->params['breadcrumbs'][] = $this->title;
    ?>
    <div class="col-lg-6">
        <div class="hr-payslip-search">

            <?php
            $model = new \frontend\models\working\hrpayslip\HrPayslipSearch();
            $form = ActiveForm::begin([
                        'id' => 'myform',
                        'action' => ['/working/hrpayslip/index-hrpayslip'],
//                        'method' => 'get',
            ]);

            $monthYearSel = array("" => "Select....") + $monthYearSel;


            $result = [];
            foreach ($dataProvider->getModels() as $element) {
                if (!$element['pdf_released']) {
                    $result[] = $element['id'];
                }
            }
            echo Html::textInput('payslip_id', implode(",", $result), ['class' => 'hidden']);
            ?>

            <div class="form-inline">
                <?= Html::dropDownList('selectedMonthYear', $selectedMonthYear, $monthYearSel, ['class' => 'form-control d-inline', 'sytle' => 'display:inline!important']) ?>
                <div class="form-group">
                    <?= Html::submitButton('Search', ['class' => 'btn btn-primary m-2', 'data-method' => 'get']) ?>
                    <?php
                    if (!$year == 0) {
//                        if (sizeof($result) > 0) {
//                            echo Html::submitButton("Generate pay slip  <b>" . MyCommonFunction::numberToMonthFull($month) . " " . $year . "</b>", ['class' => 'btn btn-success', 'data-method' => 'post', 'data-confirm' => 'Release payslips?']);
//                        } else {
//                            echo Html::submitButton("All payslip already genereated for <b>" . MyCommonFunction::numberToMonthFull($month) . " " . $year . "</b>", ['class' => 'btn btn-success disabled']);
//                        }

                        echo Html::a('Summary <i class="fas fa-file-excel"></i>', ['/working/hrpayslip/get-monthly-summary', 'month' => $month, 'year' => $year], ['class' => 'btn btn-success m-2']);
                    }
                    ?>
                </div>
            </div>


            <?php ActiveForm::end(); ?>

        </div>
        <div>         
            <?php
            $form2 = ActiveForm::begin([
                        'id' => 'myformPayslip',
                        'action' => ['/working/hrpayslip/bulk-release-payslip'],
//                        'method' => 'get',
            ]);


            if (!$year == 0) {
                if (sizeof($result) > 0) {
                    echo Html::a("Generate pay slip  <b>" . MyCommonFunction::numberToMonthFull($month) . " " . $year . "</b>",
                            'javascript:releasePayslip()',
                            ['class' => 'btn btn-success', 'data-method' => 'post', 'data-confirm' => 'Release payslips?']);
                } else {
                    echo Html::a("All payslip already genereated for <b>" . MyCommonFunction::numberToMonthFull($month) . " " . $year . "</b>",
                            "#", ['class' => 'btn btn-success disabled']);
                }
            }
            echo Html::input('text', 'selectedMonthYear', $selectedMonthYear, ['class' => 'hidden']);

            echo GridView::widget([
                'layout' => "{summary}\n{pager}\n{items}\n{pager}",
                'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
                'pager' => ['class' => yii\bootstrap4\LinkPager::class],
                'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                'columns' => [
                    [
                        'attribute' => 'staff_id',
                        'contentOptions' => ['style' => 'width:100px!important'],
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->user->staff_id;
                        }
                    ],
                    [
                        'attribute' => 'fullname',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->user->fullname;
                        }
                    ],
                    [
                        'attribute' => '',
                        'label' => 'Payslip Released Status',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->pdf_released == 1 ? "<span class='text-success'><i class='far fa-circle'></i> Released</span>" : "<span class='text-danger'><i class='fas fa-times'></i> Not yet</span>";
                        }
                    ],
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model, $key, $index, $column) {
                            return ['value' => $model->id, 'disabled' => $model->pdf_released ? true : false];
                        }
                    ],
                ],
            ]);
            ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    function releasePayslip() {

        $(document).on('beforeSubmit', 'form', function (event) {
            $(".btn-success").attr('disabled', true).addClass('disabled');
        });
        $("#myformPayslip").submit();
    }
</script>