<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\appraisal\AppraisalMaster */

$this->title = "$vModel->fullname";
$this->params['breadcrumbs'][] = 'Superior Appraisal';
$this->params['breadcrumbs'][] = ['label' => "Appraisal List", 'url' => ['index-main']];
$this->params['breadcrumbs'][] = ['label' => "$vModel->index", 'url' => ['index-review', 'id' => $main->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appraisal-master">

    <h3 class="ml-3 pl-3"><?= Html::encode($vModel->fullname) ?></h3>

    <div class="col-12">
        <div class="row px-3">
            <div class="col-md-6">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0">Appraisal Summary</legend>
                    <?=
                    $this->render('_viewAppraisalMaster', [
                        'model' => $vModel
                    ])
                    ?>
                </fieldset>
            </div>
            <div class="col-md-6">
                <?=
                $this->render('_viewOverallMark', [
                    'vModel' => $vModel
                ]);
                ?>
            </div>
        </div>
        <?=
        $this->render('_viewAppraisal', [
            'model' => $model,
            'forms' => $forms,
            'factors' => $factors
        ])
        ?>
    </div>
</div>
