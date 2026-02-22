<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\working\hrpayslip\HrPayslip;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\hr\HrPayslipSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="hr-payslip-index">

    <!--<h3><?= Html::encode($this->title) ?></h3>-->

    <?php
    echo $this->render('__HrpayslipNavBar', ['module' => 'hr_payslip', 'pageKey' => '1']);
    $this->params['breadcrumbs'][] = ['label' => 'HR - Payroll'];
    $this->params['breadcrumbs'][] = $this->title;
    ?>
    <div class="col-lg-6">

        <?=
        GridView::widget([
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'columns' => [
                [
                    'attribute' => 'staff_id',
                    'contentOptions' => ['style' => 'width:100px!important'],
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a($model->staff_id, '/working/hrpayslip/index-by-staff?userId=' . $model->id);
                    }
                ],
                'fullname',
                [
                    'attribute' => '',
                    'label' => 'Last Payroll',
                    'format' => 'raw',
                    'value' => function($model) {
                        $lastMonth = HrPayslip::findBySql('SELECT * FROM hr_payslip AS a WHERE a.user_id=' . $model->id . ' ORDER BY pay_year DESC, pay_month DESC LIMIT 1')->one();
                        return $lastMonth['pay_year'] ? date("F", mktime(0, 0, 0, $lastMonth['pay_month'], 10)) . " - " . ( $lastMonth['pay_year']) : NULL;
                    }
                ]
            ],
        ]);
        ?>

    </div>
</div>
