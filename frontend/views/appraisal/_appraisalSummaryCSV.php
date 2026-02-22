<?php

$filename = "Appraisal Summary ({$model->description}) - " . date("d.m.Y") . ".xls";

// Start output buffering to capture the data
ob_start();

// Output the headers for an HTML file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

// Output BOM (Byte Order Mark) to handle UTF-8 characters properly in Excel
echo "\xEF\xBB\xBF";

// Echo the HTML content with basic formatting
echo '<table border="1" style="font-size:14px;">';
echo '<tr>';
echo '<th>No.</th>';
echo '<th style="width:250px;">Fullname</th>';
echo '<th style="width:100px;">Staff Type</th>';
echo '<th style="width:100px;">Overall Rating</th>';
echo '<th style="width:100px;">Overall Review</th>';
echo '<th style="width:100px;">Appraisal Date</th>';
echo '<th>Reviewed By</th>';
echo '</tr>';

$typeMappings = [
    'prod' => 'Production',
    'office' => 'Office',
    'exec' => 'Executive',
];

// Output the data
foreach ($masters as $key => $master) {
    $fullname = ucwords(strtolower($master->fullname));
    $staffType = $typeMappings[$master['staff_type']] ?? $master['staff_type'];
    $overallRating = $master->overall_rating;
    $overallReview = $master->overall_review;
    $appraisalDate = common\models\myTools\MyFormatter::asDate_Read($master->created_at);
    $reviewBy = $master->review_by_name;

    echo '<tr>';
    echo '<td>' . ($key + 1) . '</td>';
    echo '<td>' . $fullname . '</td>';
    echo '<td>' . $staffType . '</td>';
    echo '<td>' . $overallRating . '</td>';
    echo '<td>' . $overallReview . '</td>';
    echo '<td>' . $appraisalDate . '</td>';
    echo '<td>' . $reviewBy . '</td>';
    echo '</tr>';
}

echo '</table>';
ob_end_flush();
exit();
