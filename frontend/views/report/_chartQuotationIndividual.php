<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\User;

$this->title = 'Report - Project Coordinator';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><?= Html::encode($this->title) ?></h3>
<div class="chart-quotation-hit mt-3">
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
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                        $form->field($model, 'userId', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                        ->dropdownList(
                                ArrayHelper::map(User::getProjectCoordinatorList(), "id", "fullname")
                        )
                        ->label("Project Coordinator");
                ?>
            </div>

        </div>
    </div>
    <?=
    $this->render('_dateForm', [
        'model' => $model
    ])
    ?>
    <?php if ($project_coordinator !== null && $qDoneData !== null) { ?>
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                $this->renderAjax('_chartQuotationDoneIndividual', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'qDoneData' => $qDoneData,
                    'totalQuotationAllProjectCoordinator' => $totalQuotationAllProjectCoordinator,
                    'project_coordinator' => $project_coordinator
                ])
                ?>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                $this->renderAjax('_chartQuotationHitIndividual', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'qHitsData' => $qHitsData,
                    'totalQuotationIndividual' => $totalQuotationIndividual,
                    'project_coordinator' => $project_coordinator
                ])
                ?>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                $this->renderAjax('_chartTaskCompletionIndividual', [
                    'model' => $model,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'tasksCompletionData' => $tasksCompletionData,
                    'project_coordinator' => $project_coordinator
                ])
                ?>
            </div>
        </div>
    <?php }
    ?>
    <?php ActiveForm::end(); ?>

</div>