<?php

use yii\helpers\Html;
use yii\bootstrap4\DetailView;
use yii\widgets\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhOutpatientMedMaster */

$this->title = "Outpatient Medical Benefit";
if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) {
    $this->params['breadcrumbs'][] = ['label' => $eh->name . ' - Super User', 'url' => ['/office/employee-handbook/view?id=' . $eh->id]];
} else {
    $this->params['breadcrumbs'][] = ['label' => $eh->name . ' - Personal', 'url' => ['/office/employee-handbook/view-employee-handbook?id=' . $eh->id]];
}
$this->params['breadcrumbs'][] = ['label' => 'Edition: ' . $eh->edition_no . ', ' . Yii::$app->formatter->asDate($eh->edition_date, 'php:d M Y')];
$this->params['breadcrumbs'][] = ['label' => 'Outpatient Medical Benefit'];
\yii\web\YiiAsset::register($this);
?>
<div class="eh-outpatient-med-master-view">

    <?php
    $form = ActiveForm::begin([
        'action' => ['update', 'ehId' => $eh->id],
        'method' => 'post'
    ]);
    ?>
    <div class="row mb-3">
        <div class="col-lg-8 col-md-8 col-sm-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><?= $this->title ?></h3>
                <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success px-3']) ?>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12">
            <span>An employee shall be entitled to claim up to RM <?= !empty($details) ? \common\models\myTools\MyFormatter::asDecimal2($details[0]->amount_per_receipt) : "0.00" ?> per single receipt but not exceeding RM <?= $master->monthly_limit !== null ? \common\models\myTools\MyFormatter::asDecimal2($master->monthly_limit) : "0.00" ?> in total per month.</span>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-right">Amount Per Receipt (RM)</th>
                        <th class="text-right">Monthly Limit (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>                        
                        <td>
                            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                                <input type="number" 
                                       name="amount_per_receipt" 
                                       value="<?= !empty($details) ? \common\models\myTools\MyFormatter::asDecimal2($details[0]->amount_per_receipt) : "0.00" ?>" 
                                       placeholder="Amount per receipt"
                                       class="form-control text-right" 
                                       step="any"
                                       min="0" />
                                   <?php } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Normal])) { ?>
                                <div class="text-right">
                                    <?= !empty($details) ? \common\models\myTools\MyFormatter::asDecimal2($details[0]->amount_per_receipt) : "0.00" ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                                <input type="number" 
                                       name="monthly_limit" 
                                       value="<?= $master->monthly_limit !== null ? \common\models\myTools\MyFormatter::asDecimal2($master->monthly_limit) : "0.00" ?>" 
                                       placeholder="Monthly limit"
                                       class="form-control text-right" 
                                       step="any"
                                       min="0" />
                                   <?php } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Normal])) { ?>
                                <div class="text-right">
                                    <?= $master->monthly_limit !== null ? \common\models\myTools\MyFormatter::asDecimal2($master->monthly_limit) : "0.00" ?>
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
