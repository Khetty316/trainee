<?php
$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
$punchlists = $model1;
?>
<style>
<?php echo $css; ?>
</style>
<body>
    <div class="content">
        <table class="table table-sm table-bordered">
            <thead class="pl-0 ml-0">
                <tr>
                    <td class="text-center verticaltext" width="5%">No.</td>
                    <td class="text-center" width="15%">Form</td>
                    <td class="text-center" width="20%">Error</td>
                    <td class="text-center" width="30%">Comments / Remarks</td>
                    <td class="text-center" width="15%">Date of Rectification</td>
                    <td class="text-center" width="15%">Verified By</td>
                </tr>
            </thead>
            <tbody id="listTBody" class="pl-0 ml-0">
                <?php
                $noData = empty($punchlists) ? true : false;
                if ($noData) {
                    for ($i = 0; $i < 10; $i++) {
                        echo $this->render('_formPunchlistItem', ['noData' => $noData]);
                    }
                } else {
                    foreach ($punchlists as $key => $punchlist) {
                        echo $this->render('_formPunchlistItem', ['key' => $key, 'noData' => $noData, 'punchlist' => $punchlist]);
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
