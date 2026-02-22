<?php

use yii\helpers\Html;
use yii\bootstrap4\DetailView;
use yii\widgets\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhTravelAllowanceMaster */

$this->title = "Travel Allowance Rates (Per Day)";
if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) {
    $this->params['breadcrumbs'][] = ['label' => $eh->name . ' - Super User', 'url' => ['/office/employee-handbook/view?id=' . $eh->id]];
}else{
    $this->params['breadcrumbs'][] = ['label' => $eh->name . ' - Personal', 'url' => ['/office/employee-handbook/view-employee-handbook?id=' . $eh->id]];
}
$this->params['breadcrumbs'][] = ['label' => 'Edition: ' . $eh->edition_no . ', ' . Yii::$app->formatter->asDate($eh->edition_date, 'php:d M Y')];
$this->params['breadcrumbs'][] = ['label' => 'Travel Allowance'];

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
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><?= $this->title ?></h3>
                <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success px-3']) ?>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Grade</th>
                        <?php foreach ($gradeList as $grade): ?>
                            <th>
                                <div class="d-flex justify-content-between">
                                    <span><?= $grade->name ?></span>
                                    <span>(RM)</span>
                                </div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locationList as $location): ?>
                        <tr>
                            <td><?= $location->name ?></td>
                            <?php
                            foreach ($gradeList as $grade):
                                $amount = $dataMatrix[$location->code][$grade->code] ?? '';
                                ?>
                                <td>
                                    <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                                        <input type="number" 
                                               name="Details[<?= $location->code ?>][<?= $grade->code ?>]" 
                                               value="<?= \common\models\myTools\MyFormatter::asDecimal2($amount?: 0.00) ?>" 
                                               placeholder="Amount per day"
                                               class="form-control text-right"
                                               required="true"
                                               step="any"
                                               min="0"/>
                                           <?php } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Normal])) { ?>
                                        <div class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($amount ?: 0.00) ?></div>
                                    <?php } ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

