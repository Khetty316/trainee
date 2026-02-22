<?php

use yii\helpers\Html;
use yii\bootstrap4\DetailView;
use yii\widgets\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhTravelAllowanceMaster */

$this->title = "Executive Overtime Meal";
if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) {
    $this->params['breadcrumbs'][] = ['label' => $eh->name . ' - Super User', 'url' => ['/office/employee-handbook/view?id=' . $eh->id]];
} else {
    $this->params['breadcrumbs'][] = ['label' => $eh->name . ' - Personal', 'url' => ['/office/employee-handbook/view-employee-handbook?id=' . $eh->id]];
}
$this->params['breadcrumbs'][] = ['label' => 'Edition: ' . $eh->edition_no . ', ' . Yii::$app->formatter->asDate($eh->edition_date, 'php:d M Y')];
$this->params['breadcrumbs'][] = ['label' => 'Exexcutive OT Meal'];

\yii\web\YiiAsset::register($this);
?>
<div class="eh-travel-allowance-master-view">
    <?php
    $form = ActiveForm::begin([
        'action' => ['update', 'ehId' => $eh->id],
        'method' => 'post'
    ]);
    ?>
    <div class="row mb-3">
        <div class="col-lg-7 col-md-7 col-sm-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><?= $this->title ?></h3>
                <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success px-3']) ?>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 col-md-7 col-sm-12">
            <span>Executives are eligible to claim an overtime meal allowance after completing at least two (2) continuous hours of overtime work beyond normal working hours. The claim amount is limited to RM <?= \common\models\myTools\MyFormatter::asDecimal2($execPersonal->amount_per_day) ?> per day.</span>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-12">
            <table class="table table-bordered">
                <thead>
                    <tr class="text-right">
                        <th></th>
                        <th>Amount per Day (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Executive</td>
                        <td>
                            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                                <?=
                                $form->field($execPersonal, 'amount_per_day')->textInput([
                                    'type' => 'number',
                                    'name' => 'ExecPersonal[amount_per_day]',
                                    'value' => number_format($execPersonal->amount_per_day ?? 0.00, 2, '.', ''),
                                    'placeholder' => 'Amount per day',
                                    'class' => 'form-control text-right',
                                    'required' => true,
                                    'step' => 'any',
                                    'min' => 0,
                                ])->label(false)
                                ?>
                            <?php } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Normal])) { ?>
                                <div class="text-right">
                                    <?= \common\models\myTools\MyFormatter::asDecimal2($execPersonal->amount_per_day ?: 0.00) ?>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> 
    </div>

    <?php ActiveForm::end(); ?>
</div>

