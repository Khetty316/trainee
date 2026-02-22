<?php

use yii\bootstrap4\ActiveForm;
?>
<h3>Hi, <?= Yii::$app->user->identity->fullname ?>!!</h3>
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
    <?=
    $this->render('/report/_dateForm', [
        'model' => $model
    ])
    ?>
    <?php if ($project_coordinator !== null && $qDoneData !== null) { ?>
        <div class="row">
            <div class="col-lg-4 col-md-12 col-sm-12">
                <?=
                $this->renderAjax('/report/_chartQuotationDoneIndividual', [
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
                $this->renderAjax('/report/_chartQuotationHitIndividual', [
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
                $this->renderAjax('/report/_chartTaskCompletionIndividual', [
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