<meta charset="UTF-8">
<?php
//$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
$attendanceList = $model1;
?>
<style>
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 3px solid #000;
    }
    
    table td {
        border-right: 1px solid #000;
        border-bottom: 1px solid #000;
        padding: 8px;
    }
    
    table td:last-child {
        border-right: 3px solid #000;
    }
    
    table tr:last-child td {
        border-bottom: 3px solid #000;
    }
</style>
<body>
    <div class="content col-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                    <thead>
                        <tr>
                            <td style="border: 1px solid black;" width="5%">No.</td>
                            <td style="border: 1px solid black;" width="20%">Name</td>
                            <td style="border: 1px solid black;" width="20%">Organization</td>
                            <td style="border: 1px solid black;" width="20%">Position/Designation</td>
                            <td style="border: 1px solid black;" width="15%">Role</td>
                            <td style="border: 1px solid black;" width="20%">Signature</td>
                        </tr>
                    </thead>
                    <tbody id="listTBody">
                        <?php
                        $counter = 0;
                        if (empty($attendanceList)) {
                            $noData = true;
                            // Create default 10 rows
                            for ($i = 0; $i < 10; $i++) {
                                echo $this->render('_formAttendanceItemDoc', ['noData' => $noData, 'main' => $main, 'panel' => $panel, 'project' => $project, 'master' => $master]);
                            }
                        } else {
                            $noData = false;
                            foreach ($attendanceList as $key => $attendance) {
                                if ($counter % 10 === 0 && $counter !== 0) {
                                    echo '</table>';
                                    echo '<div class="nextPage"></div>';
                                    echo '<table style="width: 100%; border-collapse: collapse; table-layout: fixed;">';
                                    echo '<thead>
                                <tr>
                                   <td style="border: 1px solid black;" width="5%">No.</td>
                                    <td style="border: 1px solid black;" width="20%">Name</td>
                                    <td style="border: 1px solid black;" width="20%">Organization</td>
                                    <td style="border: 1px solid black;" width="20%">Position/Designation</td>
                                    <td style="border: 1px solid black;" width="15%">Role</td>
                                    <td style="border: 1px solid black;" width="20%">Signature</td>
                                </tr>
                            </thead>';
                                }
                                echo $this->render('_formAttendanceItemDoc', ['key' => $key, 'noData' => $noData, 'main' => $main, 'panel' => $panel, 'project' => $project, 'master' => $master, 'attendance' => $attendance]);
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

