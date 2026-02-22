<?php

use yii\helpers\Html;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EmployeeHandbookMaster */
if ($model || $model !== null || !empty($model)) {
    $this->title = $model->name;
    if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) {
        $this->params['breadcrumbs'][] = ['label' => 'Employee Handbook Masters - Super User', 'url' => ['index']];
    } else {
        $this->title = "Employee Handbook - Personal";
    }
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['breadcrumbs'][] = ['label' => 'Edition: ' . $model->edition_no . ', ' . Yii::$app->formatter->asDate($model->edition_date, 'php:d M Y')];
} else {
    $this->title = "Employee Handbook - Personal";
    $this->params['breadcrumbs'][] = $this->title;
}
\yii\web\YiiAsset::register($this);
?>
<style>
    .hover-shadow {
        transition: all 0.2s ease-in-out;
        box-shadow: 0 6px 16px rgba(0,0,0,0.15) !important;
    }
    .hover-shadow:hover {
        transform: translateY(-4px);
    }
</style>
<div class="employee-handbook-master-view">
    <?php if ($model) { ?>
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <fieldset class="form-group border rounded p-3 shadow-sm">
                    <legend class="w-auto px-2 m-0 h4"><i class='fas fa-book'></i> Handbook Details</legend>

                    <table class="table table-sm mb-3">
                        <tbody>
                            <tr>
                                <th class="w-25">Title</th>
                                <td><?= Html::encode($model->name) ?></td>
                            </tr>
                            <tr>
                                <th>Edition</th>
                                <td>
                                    <?= Html::encode($model->edition_no) ?>, 
                                    <?= Yii::$app->formatter->asDate($model->edition_date, 'php:d M Y') ?>
                                </td>
                            </tr>
                            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                                <tr>
                                    <th>Is Active?</th>
                                    <td>
                                        <?= \frontend\models\office\employeeHandbook\EmployeeHandbookMaster::IS_ACTIVE_HTML[$model->is_active] ?? null ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Eh_Super]) && $superUser == 1) { ?>
                        <div class="text-right">
                            <?=
                            Html::a(
                                    " Update Detail",
                                    "javascript:",
                                    [
                                        'title' => $model->name,
                                        "value" => yii\helpers\Url::to(['update', 'id' => $model->id]),
                                        "class" => "modalButtonMedium btn btn-sm btn-success",
                                        'data-modaltitle' => "Update Detail"
                                    ]
                            )
                            ?>
                        </div>
                    <?php } ?>
                </fieldset>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <fieldset class="form-group border rounded p-3 shadow-sm">
                    <legend class="w-auto px-2 m-0 h5"><i class="fa fa-list"></i> Content</legend>

                    <div class="row">
                        <?php foreach ($contentList as $content): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <a href="<?= \yii\helpers\Url::to(['view-content', 'id' => $model->id, 'contentTypeCode' => $content->code, 'superUser' => $superUser]) ?>" class="text-decoration-none">
                                    <div class="card h-100 shadow-sm border-0 hover-shadow bg-light">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">

                                            <!-- Title -->
                                            <p class="card-title font-weight-bold text-dark mb-0">
                                                <?= Html::encode($content->name) ?>
                                            </p>
                                        </div>
                                    </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </fieldset>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-danger mt-3">
            Please contact HR for assistance.
        </div>
    <?php } ?>
</div>

