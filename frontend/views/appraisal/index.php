<?php

use yii\bootstrap4\Html;

$this->title = "Appraisal List";
$this->params['breadcrumbs'][] = "HR Appraisal";
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="app">
    <div>
        <h3><?= $this->title ?></h3>
    </div>
    <div class="row m-0 p-0">
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>    
        <?=
        Html::a("Initiate Staff Appraisal <i class='fas fa-external-link-square-alt'></i>", "javascript:", [
            'title' => "Initiate Staff Appraisal",
            "value" => yii\helpers\Url::to('/appraisal/initiate-main'),
            "class" => "modalButton btn btn-success ml-1",
            'data-modaltitle' => 'Appraisal Details'
        ]);
        ?>
    </div>
    <div>
        <?=
        $this->render('_viewMain', [
            'models' => $models,
            'statusOptions' => $statusOptions,
            'super' => false
        ])
        ?>
    </div>
</div>



<script>
    window.models = <?= $models ?>;

</script>
<script src="\js\vueTable.js"></script>