<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\appraisal\AppraisalMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Staff Appraisal List";
$this->params['breadcrumbs'][] = 'Staff Appraisal';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-md-12">
    <?php if ($models) { ?>
        <h3>Appraisal List</h3>

        <?=
        $this->render('_viewAppraisalList', [
            'statusOptions' => $statusOptions,
            'staff' => true
        ]);
        ?>

    <?php } else { ?>
        <p class="text-danger">You do not have an appraisal. Please contact HR if you think you should have one.</p>
    <?php } ?>
</div>

<script>
    window.models = <?= $modelsJson ?>;

</script>
<script src="\js\vueTable.js"></script>

