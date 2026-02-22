<meta charset="UTF-8">
<?php
$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
$visualpaint = $model1;
$visualpaintProcedures = $model2;
?>
<style>
<?php echo $css; ?>
</style>
<body>
    <?php
    $modelProcedures = explode('|', $visualpaintProcedures);
    echo '<p>' . (isset($modelProcedures[0]) ? $modelProcedures[0] : '') . '</p>';
    ?>
    <?php
    echo '<p>' . (isset($modelProcedures[1]) ? $modelProcedures[1] : '') . '</p>';
    ?>
    <div class="nextPage">
        <p><b>Visual Inspection Test Result</b></p>
        <?php
        echo $this->render('_formVisual', [
            'visualpaint' => $visualpaint
        ]);
        ?>
        <br>
        <p><b>Physical Measurement Test Result</b></p>
        <?=
        $this->render('_formPaint', [
            'visualpaint' => $visualpaint
        ]);
        ?>
    </div>

    <?= $this->render('sign', ['formStatus' => $visualpaint, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_VISUALPAINT)]) ?>

    <!--    <div class="nextPage">
    <?php
//        echo '<p>' . (isset($modelProcedures[1]) ? $modelProcedures[1] : '') . '</p>';
    ?>
    
            <p><b>Physical Measurement Test Result</b></p>
    <?php
//        =
//        $this->render('_formPaint', [
//            'visualpaint' => $visualpaint
//        ]);
    ?>
            <br/><br/>
    <?php //= $this->render('sign', ['master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_VISUALPAINT)])   ?>
        </div>-->
</body>

