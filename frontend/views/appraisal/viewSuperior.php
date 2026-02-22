<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\appraisal\AppraisalMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "$main->index";
$this->params['breadcrumbs'][] = 'Superior Appraisal';
$this->params['breadcrumbs'][] = ['label' => "Appraisal List", 'url' => ['index-main']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-md-12">
    <?php if ($models) { ?>

        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0"> Appraisal Info - <?= $main->index ?></legend>
            <?=
            $this->render('_viewAppraisalMain', [
                'model' => $main,
            ]);
            ?>
        </fieldset>

        <?=
        $this->render('_viewAppraisalList', [
            'main' => $main,
            'statusOptions' => $statusOptions,
            'staff' => false
        ]);
        ?>

    <?php } else { ?>
        <p class="text-danger">You do not have an appraisal to review. Please contact HR if you think you should have one.</p>
    <?php } ?>
</div>

<script>
    window.models = <?= $modelsJson ?>;

</script>
<script src="\js\vueTable.js"></script>

