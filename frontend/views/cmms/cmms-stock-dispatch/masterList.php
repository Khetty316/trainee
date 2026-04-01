<?php

$this->title = 'Maintenance - Material Request Master List';
$this->params['breadcrumbs'][] = $this->title;

$module = "inventory";
?>
<div class="cmms-stock-dispatch-master-index">
    <?= $this->render('/cmms/cmms-wo-material-request/__cmmsMaterialRequestNavbar', ['module' => $module, 'pageKey' => '3']) ?>

    <?php
    echo $this->render('_acknowledgementList', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'moduleIndex' => "superuser"
    ]);
    ?>
    
</div>


