<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\appraisal\AppraisalMaster */

$this->title = $modelObject->user->fullname;
$this->params['breadcrumbs'][] = 'Superior Appraisal';
$this->params['breadcrumbs'][] = ['label' => "Appraisal List", 'url' => ['index-main']];
$this->params['breadcrumbs'][] = ['label' => "$main->index", 'url' => ['index-review', 'id' => $main->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
    <?=
    $this->render('_formFactors', [
        'main' => $main,
        'model' => $model,
        'forms' => $forms,
        'factors' => $factors,
        'type' => frontend\models\appraisal\AppraisalMaster::TYPE_REVIEW,
        'staff' => false
    ]);
    ?>
</div>
