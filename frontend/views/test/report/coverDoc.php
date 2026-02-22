<?php
//$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
?>
<style>
    <?php // echo $css; ?>
</style>
<body>
    <div class="content">
        <div class="text-center" style="margin-top: 100px; text-align: center">
            <h2><b><?= $main->test_type === frontend\models\test\TestMain::TEST_FAT_TITLE ? frontend\models\test\TestMain::TEST_FAT_TITLE : frontend\models\test\TestMain::TEST_ITP_TITLE ?></b></h2>
        </div>
    </div>
</body>
