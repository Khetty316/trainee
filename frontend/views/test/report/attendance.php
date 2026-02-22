<meta charset="UTF-8">
<?php
$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
$attendanceList = $model1;
?>
<style>
<?php echo $css; ?>
</style>
<body>
    <div class="content col-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <table class="table table-sm table-bordered text-center">
                    <thead>
                        <tr>
                            <td class="text-center" width="5%">No.</td>
                            <td class="text-center" width="20%">Name</td>
                            <td class="text-center" width="20%">Organization</td>
                            <td class="text-center" width="20%">Position/Designation</td>
                            <td class="text-center" width="15%">Role</td>
                            <td class="text-center" width="20%">Signature</td>
                        </tr>
                    </thead>
                    <tbody id="listTBody">
                        <?php
                        $counter = 0;
                        if (empty($attendanceList)) {
                            $noData = true;
                            // Create default 10 rows
                            for ($i = 0; $i < 10; $i++) {
                                echo $this->render('_formAttendanceItem', ['noData' => $noData, 'main' => $main, 'panel' => $panel, 'project' => $project, 'master' => $master]);
                            }
                        } else {
                            $noData = false;
                            foreach ($attendanceList as $key => $attendance) {
                                if ($counter % 10 === 0 && $counter !== 0) {
                                    echo '</table>';
                                    echo '<div class="nextPage"></div>';
                                    echo '<table class="table table-sm table-bordered text-center">';
                                    echo '<thead>
                                <tr>
                                    <td class="text-center" width="5%">No.</td>
                                    <td class="text-center" width="20%">Name</td>
                                    <td class="text-center" width="20%">Organization</td>
                                    <td class="text-center" width="20%">Position/Designation</td>
                                    <td class="text-center" width="15%">Role</td>
                                    <td class="text-center" width="20%">Signature</td>
                                </tr>
                            </thead>';
                                }
                                echo $this->render('_formAttendanceItem', ['key' => $key, 'noData' => $noData, 'main' => $main, 'panel' => $panel, 'project' => $project, 'master' => $master, 'attendance' => $attendance]);
                                $counter++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

