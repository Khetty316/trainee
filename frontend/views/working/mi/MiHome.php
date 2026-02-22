<?php
/* @var $this yii\web\View */



$this->title = 'Master Incoming';
//$this->params['breadcrumbs'][] = ['label' => 'Employees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<script>
    $(document).ready(function () {
        $("#navbardropWorking").addClass("active");
        $("#navMasterIncoming").addClass("active");
    });
</script>

<div class="container mainContainer">
    <div class="row">
        <h1>Working</h1>
    </div>
    <div class="row isYellow p-0">   

        <h1>Welcome to NPL Portal</h1>
    </div>
</div>
