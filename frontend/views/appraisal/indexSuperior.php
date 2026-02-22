<?php

use yii\bootstrap4\Html;

$this->title = "Appraisal List";
$this->params['breadcrumbs'][] = 'Superior Appraisal';
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="app">
    <div>
        <h3><?= $this->title ?></h3>
    </div>
    <div class="row m-0 p-0">
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>    
    </div>
    <div>
        <?=
        $this->render('_viewMain', [
            'models' => $models,
            'statusOptions' => $statusOptions,
            'super' => true
        ])
        ?>
    </div>
</div>

<script>
    window.models = <?= $models ?>;

</script>
<script src="\js\vueTable.js"></script>