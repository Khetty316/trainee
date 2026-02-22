<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\appraisal\AppraisalMaster */

$this->title = "$main->index";
$this->params['breadcrumbs'][] = 'Staff Appraisal';
$this->params['breadcrumbs'][] = ['label' => "Staff Appraisal List", 'url' => ['index-rating']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
    <?=
    $this->render('_formFactors', [
        'main' => $main,
        'model' => $model,
        'forms' => $forms,
        'factors' => $factors,
        'type' => frontend\models\appraisal\AppraisalMaster::TYPE_RATING,
        'staff' => true
    ]);
    ?>
</div>
