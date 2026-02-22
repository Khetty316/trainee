<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\appraisal\AppraisalMaster */

$this->title = "$vModel->index";
$this->params['breadcrumbs'][] = ['label' => "Staff Appraisal", 'url' => ['index-rating']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appraisal-master">

    <h3 class="ml-3 pl-3"><?= Html::encode($vModel->fullname) ?></h3>

    <div class="row">
        <div class="col-md-8">
            <?=
            $this->render('_viewAppraisal', [
                'model' => $model,
                'forms' => $forms,
                'factors' => $factors,
                'toConfirm' => true
            ])
            ?>
        </div>
        <div class="col-md-4">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0"> Action </legend>
                <a href="<?= \yii\helpers\Url::to(['/appraisalgnrl/begin-staff-appraisal', 'id' => $vModel->id]) ?>" class="btn btn-lg btn-success">Update</a>
                <a href="<?= \yii\helpers\Url::to(['/appraisalgnrl/confirm-appraisal', 'id' => $vModel->id, 'sts' => true]) ?>" class="btn btn-lg btn-success"  data-confirm="Once confirmed, you cannot update. Proceed?">Confirm</a>
                <br/><br/><p class="text-success bold">Please confirm after reviewing. Once appraisal rating is confirmed, cannot update.</p>
                <p class="text-success bold">Sila sahkan setelah disemak. Setelah penarafan disahkan, tidak dapat dikemaskini.</p>
            </fieldset>
        </div>
    </div>
</div>