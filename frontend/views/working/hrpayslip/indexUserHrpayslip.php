<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\hr\HrPayslipSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pay Slip List';
$this->params['breadcrumbs'][] = ['label' => 'HR Payroll', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hr-payslip-index ">

    <h3><?= $user->fullname ?></h3>

    <p>
        <?= Html::a('Payslip <i class="fas fa-plus"></i>', ['create?userId=' . $user->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="col-md-4">
        <?=
        GridView::widget([
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'dataProvider' => $dataProvider,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'columns' => [
                [
                    'attribute' => 'pay_month',
                    'label' => 'Month of Payroll',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a(date("F", mktime(0, 0, 0, $model->pay_month, 10)) . " - " . $model->pay_year, 'view?id=' . $model->id);
                    }
                ],
                [
                    'attribute' => '',
                    'label' => 'PDF',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function($model) {
                        return Html::a('<i class="far fa-file-pdf fa-lg"></i>', ['/working/hrpayslip/hr-view-payslip-pdf', 'payslipId' => $model->id], ['target' => '_blank']);
                    }
                ],
            ],
        ]);
        ?>
    </div>
</div>
