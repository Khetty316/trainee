<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

// Calculate the number of columns per month (5 without entitlement, 7 with)
$colsPerMonth = $hasEntitlement ? 7 : 5;

// Define month names and determine which months to display
$allMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
$monthMap = [
    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
];

// Determine which months to display
if (!empty($month) && isset($monthMap[$month])) {
    $displayMonths = [$monthMap[$month]];
} else {
    $displayMonths = $allMonths;
}

if ($module !== 'finance') {
    unset($claimTypes[\frontend\models\office\claim\RefClaimType::codeDirector]);
    $claimTypes = $claimTypes;
} else {
    $claimTypes = $claimTypes;
}
?>

<div class="claim-master-index">
    <?php
    $formShow = ActiveForm::begin([
        'method' => 'get',
    ]);

    echo '<div class="form-row align-items-center mt-2 mb-2">';

    echo '<div class="col-lg-1 col-md-3 col-sm-6 mb-2">';
    echo MyCommonFunction::myDropDownNoEmpty($yearList, 'year', 'form-control', '', $year);
    echo '</div>';

    echo '<div class="col-lg-1 col-md-3 col-sm-6 mb-2">';
    echo Html::dropDownList('month', $month, $monthList, [
        'class' => 'form-control',
        'prompt' => 'All Months'
    ]);
    echo '</div>';

    echo '<div class="col-lg-3 col-md-3 col-sm-6 mb-2">';
    echo MyCommonFunction::myDropDownNoEmpty($claimTypes, 'claim_type', 'form-control', 'All Claim Types', $claimType);
    echo '</div>';

    echo '<div class="col-lg-3 col-md-3 col-sm-6 mb-2">';
    if ($module === 'finance') {
        echo '<select name="staff" class="form-control">';
        echo '<option value="">All Staff</option>';
        foreach ($staffList as $id => $name) {
            $selected = ($staff == $id) ? 'selected' : '';
            echo "<option value='$id' $selected>$name</option>";
        }
        echo '</select>';
    } else {
        echo '<select class="form-control" disabled>';
        foreach ($staffList as $id => $name) {
            if ($id == Yii::$app->user->identity->id) {
                echo "<option selected>$name</option>";
            }
        }
        echo '</select>';
        echo "<input type='hidden' name='staff' value='" . Yii::$app->user->identity->id . "'>";
    }
    echo '</div>';

    echo '<div class="col-lg-1 col-md-3 col-sm-6 mb-2">';
    echo Html::submitButton('Show', ['class' => 'btn btn-primary px-3']);
    echo '</div>';

    if ($module === 'finance') {
        echo '<div class="col-lg-2 col-md-3 col-sm-6 mb-2 ml-auto">';
        echo Html::a(
                'Export to CSV <i class="fas fa-file-csv fa-lg"></i>',
                '#',
                [
                    'class' => 'btn btn-info float-right',
                    'id' => 'exportCsvButton',
                ]
        );
        echo '</div>';
    }

    echo '</div>';
    ActiveForm::end();
    ?>
</div>

<div class="view">
    <div class="table-scroll">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <td class="text-center align-bottom font-weight-bold" rowspan="2" style="width:65px; min-width:65px; max-width:65px;">Staff ID</td>
                    <td class="text-center align-bottom font-weight-bold" rowspan="2" style="width:200px; min-width:200px; max-width:200px;">Staff Name</td>
                    <?php
                    foreach ($displayMonths as $monthName) {
                        ?>
                        <!-- ADDED month-header class for top row border alignment -->
                        <th colspan="<?= $colsPerMonth ?>" class="month-header" style="padding: 8px 2px !important;"><?= $monthName ?></th>
                    <?php }
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach ($displayMonths as $monthName) {
                        // Determine which column is the start of the month for header row 2
                        $entitlementColumnClass = $hasEntitlement ? 'section-border' : '';
                        $submittedClaimClass = !$hasEntitlement ? 'section-border' : '';
                        ?>
                        <?php if ($hasEntitlement): ?>
                            <!-- Entitlement is the first column when hasEntitlement is true -->
                            <th class="verticaltext <?= $entitlementColumnClass ?>" style="width: 70px;"><span>Entitlement</span></th>
                            <th class="verticaltext" style="width: 70px;"><span>Available Balance</span></th>
                        <?php endif; ?>
                        <!-- Submitted Claim is the first column when hasEntitlement is false -->
                        <th class="verticaltext <?= $submittedClaimClass ?>" style="width: 70px;"><span>Submitted Claim</span></th>
                        <th class="verticaltext" style="width: 70px;"><span>Approved Claim</span></th>
                        <th class="verticaltext" style="width: 70px;"><span>Rejected Claim</span></th>
                        <th class="verticaltext" style="width: 70px;"><span>Pending Payment</span></th>
                        <th class="verticaltext" style="width: 70px;"><span>Paid</span></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($claimSummarys as $claimSummary) {
                    ?>
                    <tr>
                        <th class="text-center"><?= $claimSummary["staffid"] ?></th>
                        <th><?= $claimSummary["fullname"] ?></th>
                        <?php
                        foreach ($intMonth as $monthNum) {
                            // Determine which column is the start of the month for data rows
                            $entitlementDataClass = $hasEntitlement ? 'section-border' : '';
                            $submittedDataClass = !$hasEntitlement ? 'section-border' : '';
                            ?>
                            <?php if ($hasEntitlement): ?>
                                <!-- Entitlement is the first column when hasEntitlement is true -->
                                <td class="text-right <?= $entitlementDataClass ?>">
                                    <?= is_numeric($claimSummary[$monthNum]['Entitlement']) ? number_format($claimSummary[$monthNum]['Entitlement'], 2) : $claimSummary[$monthNum]['Entitlement'] ?>
                                </td>
                                <td class="text-right">
                                    <?= is_numeric($claimSummary[$monthNum]['Balance']) ? number_format($claimSummary[$monthNum]['Balance'], 2) : $claimSummary[$monthNum]['Balance'] ?>
                                </td>
                            <?php endif; ?>
                            <!-- Submitted Claim is the first column when hasEntitlement is false -->
                            <td class="text-right <?= $submittedDataClass ?>"><?= number_format($claimSummary[$monthNum]['ClaimSubmit'], 2) ?></td>
                            <td class="text-right"><?= number_format($claimSummary[$monthNum]['ClaimApprove'], 2) ?></td>
                            <td class="text-right"><?= number_format($claimSummary[$monthNum]['ClaimReject'], 2) ?></td>
                            <td class="text-right"><?= number_format($claimSummary[$monthNum]['Pending'], 2) ?></td>
                            <td class="text-right"><?= number_format($claimSummary[$monthNum]['Paid'], 2) ?></td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
            <tfoot>
                <?php
                // Initialize arrays for monthly totals
                $monthTotals = [];
                $grandTotals = [
                    'Entitlement' => 0,
                    'Balance' => 0,
                    'ClaimSubmit' => 0,
                    'ClaimApprove' => 0,
                    'ClaimReject' => 0,
                    'Pending' => 0,
                    'Paid' => 0,
                    'Unlimited' => false
                ];

                // Prepare monthly totals
                foreach ($intMonth as $monthNum) {
                    $monthTotals[$monthNum] = [
                        'Entitlement' => 0,
                        'Balance' => 0,
                        'ClaimSubmit' => 0,
                        'ClaimApprove' => 0,
                        'ClaimReject' => 0,
                        'Pending' => 0,
                        'Paid' => 0,
                        'Unlimited' => false
                    ];
                }

                // Calculate monthly totals and grand totals
                foreach ($claimSummarys as $claimSummary) {
                    foreach ($intMonth as $monthNum) {
                        // Entitlement
                        if (is_numeric($claimSummary[$monthNum]['Entitlement'])) {
                            $monthTotals[$monthNum]['Entitlement'] += $claimSummary[$monthNum]['Entitlement'];
                            $grandTotals['Entitlement'] += $claimSummary[$monthNum]['Entitlement'];
                        }

                        // Submitted Claim
                        if (is_numeric($claimSummary[$monthNum]['ClaimSubmit'])) {
                            $monthTotals[$monthNum]['ClaimSubmit'] += $claimSummary[$monthNum]['ClaimSubmit'];
                            $grandTotals['ClaimSubmit'] += $claimSummary[$monthNum]['ClaimSubmit'];
                        }

                        // Approved Claim
                        if (is_numeric($claimSummary[$monthNum]['ClaimApprove'])) {
                            $monthTotals[$monthNum]['ClaimApprove'] += $claimSummary[$monthNum]['ClaimApprove'];
                            $grandTotals['ClaimApprove'] += $claimSummary[$monthNum]['ClaimApprove'];
                        }

                        // Rejected Claim
                        if (is_numeric($claimSummary[$monthNum]['ClaimReject'])) {
                            $monthTotals[$monthNum]['ClaimReject'] += $claimSummary[$monthNum]['ClaimReject'];
                            $grandTotals['ClaimReject'] += $claimSummary[$monthNum]['ClaimReject'];
                        }

                        // Pending
                        if (is_numeric($claimSummary[$monthNum]['Pending'])) {
                            $monthTotals[$monthNum]['Pending'] += $claimSummary[$monthNum]['Pending'];
                            $grandTotals['Pending'] += $claimSummary[$monthNum]['Pending'];
                        }

                        // Paid
                        if (is_numeric($claimSummary[$monthNum]['Paid'])) {
                            $monthTotals[$monthNum]['Paid'] += $claimSummary[$monthNum]['Paid'];
                            $grandTotals['Paid'] += $claimSummary[$monthNum]['Paid'];
                        }

                        // Balance
                        if ($claimSummary[$monthNum]['Balance'] === 'No limit') {
                            $monthTotals[$monthNum]['Unlimited'] = true;
                            $grandTotals['Unlimited'] = true;
                        } elseif (is_numeric($claimSummary[$monthNum]['Balance'])) {
                            $monthTotals[$monthNum]['Balance'] += $claimSummary[$monthNum]['Balance'];
                            $grandTotals['Balance'] += $claimSummary[$monthNum]['Balance'];
                        }
                    }
                }
                ?>

                <tr class="font-weight-bold bg-light text-right">
                    <th colspan="2">Monthly Total (RM) : </th>
                    <?php
                    foreach ($intMonth as $monthNum):
                        // Determine which column is the start of the month for the total row
                        $entitlementTotalClass = $hasEntitlement ? 'section-border' : '';
                        $submittedTotalClass = !$hasEntitlement ? 'section-border' : '';
                        ?>
                        <?php if ($hasEntitlement): ?>
                            <!-- Entitlement is the first column when hasEntitlement is true -->
                            <th class="<?= $entitlementTotalClass ?>"><?= number_format($monthTotals[$monthNum]['Entitlement'], 2) ?></th>
                            <th><?= number_format($monthTotals[$monthNum]['Balance'], 2) ?></th>
                        <?php endif; ?>
                        <!-- Submitted Claim is the first column when hasEntitlement is false -->
                        <th class="<?= $submittedTotalClass ?>"><?= number_format($monthTotals[$monthNum]['ClaimSubmit'], 2) ?></th>
                        <th><?= number_format($monthTotals[$monthNum]['ClaimApprove'], 2) ?></th>
                        <th><?= number_format($monthTotals[$monthNum]['ClaimReject'], 2) ?></th>
                        <th><?= number_format($monthTotals[$monthNum]['Pending'], 2) ?></th>
                        <th><?= number_format($monthTotals[$monthNum]['Paid'], 2) ?></th>
                    <?php endforeach; ?>
                </tr>

                <tr class="font-weight-bold bg-light">
                    <th colspan="2" class="text-right">Grand Total <?= !empty($month) ? '(' . $monthList[$month] . ')' : '(All Months)' ?> (RM) : </th>
                    <th colspan="<?= count($intMonth) * $colsPerMonth ?>">
                        <?php if ($hasEntitlement): ?>
                            Entitlement: <?= $grandTotals['Unlimited'] ? 'Unlimited' : number_format($grandTotals['Entitlement'], 2) ?><br>
                            Available Balance: <?= number_format($grandTotals['Balance'], 2) ?><br>
                        <?php endif; ?>
                        Submitted Claim: <?= number_format($grandTotals['ClaimSubmit'], 2) ?><br>
                        Approved Claim: <?= number_format($grandTotals['ClaimApprove'], 2) ?><br>
                        Rejected Claim: <?= number_format($grandTotals['ClaimReject'], 2) ?><br>
                        Pending Payment: <?= number_format($grandTotals['Pending'], 2) ?><br>
                        Paid: <?= number_format($grandTotals['Paid'], 2) ?><br>
                    </th>
                </tr>

            </tfoot>

        </table>
    </div>
</div>
<style>
    .view {
        margin: auto;
    }

    .table-scroll{
        width:100%;
        max-height:calc(100vh - 320px);
        overflow:auto;
        white-space:nowrap;
    }

    td{
        font-weight: normal;
        position: relative;
    }

    th{
        font-weight: normal;
    }

    .table thead{
        background-color: #e9ecef;
        color: #495057;
    }
    .table thead th{
        padding:3px !important;
        text-align: center;
        font-weight: bold;
    }
    .verticaltext span{
        padding-bottom: 0px ;
        max-width: 30px;
        writing-mode: vertical-rl;
        transform: rotate(180deg);
    }
    .table td{
        vertical-align: middle;
        text-align: center;
    }

    /* ADDED/UNCOMMENTED: Use this class for the left border of the first column of each month */
    .section-border {
        border-left-width: 10px !important;
        border-left-color: #e9ecef !important;
    }

    /* ADDED: Ensure the month header also has the border */
    th.month-header {
        border-left-width: 10px !important;
        border-left-color: #e9ecef !important;
    }
    .table-scroll thead tr:nth-child(2) th{
        position: sticky;
        top: 38px;     /* adjust if necessary */
        background: #e9ecef;
        z-index: 29;
    }
    .table-scroll thead tr:first-child th,
    .table-scroll thead tr:first-child td{
        position: sticky;
        top: 0;
        background: #e9ecef;
        z-index: 30;
    }
    .table-scroll thead tr:first-child td:first-child{
        left: 0;
        z-index: 120;
        background: #e9ecef;
    }
    .table-scroll thead tr:first-child td:nth-child(2){
        left: 65px;
        z-index: 119;
        background: #e9ecef;
    }
    .table-scroll tbody th:first-child{
        position: sticky;
        left: 0;
        z-index: 50;
        background: #fff;
        background-clip: padding-box;
    }
    .table-scroll tbody th:nth-child(2){
        position: sticky;
        left:65px;
        background:#fff;
        z-index:49;
    }
    .table-scroll tbody tr:hover th:first-child,
    .table-scroll tbody tr:hover th:nth-child(2){
        background: rgba(0,162,226,0.5) !important;
        z-index: 20;
    }
    .table-scroll thead td:first-child,
    .table-scroll thead td:nth-child(2){
        z-index: 120;
    }
    .table-scroll tbody th:nth-child(2),
    .table-scroll thead td:nth-child(2){
        width:300px;
        min-width:300px;
        max-width:300px;
    }
    .table-scroll tbody th:first-child,
    .table-scroll tbody th:nth-child(2){
        position: sticky;
        background: #fff;
        isolation: isolate;
    }
    .table-scroll tbody th:first-child{
        border-right: 1px solid #dee2e6;
    }
    .table-scroll tbody th:nth-child(2){
        border-right: 1px solid #dee2e6;
    }
    .table-scroll table{
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-scroll tbody tr:hover th:first-child,
    .table-scroll tbody tr:hover th:nth-child(2){
        background-color: #7fd3f7 !important;
        z-index: 9999;
    }
    .table-scroll tbody tr:hover th:first-child{
        background:#7fd3f7 !important;
        z-index:60;
    }
    .table-scroll tbody tr:hover th:nth-child(2){
        background:#7fd3f7 !important;
        z-index:59;
    }
    .table-scroll tbody tr:hover td,
    .table-scroll tbody tr:hover th {
        background-color: #7fd3f7;
    }
    .table-scroll tbody .column-hover{
        background-color: #7fd3f7 !important;
    }
    .table-scroll tbody tr:hover th:first-child{
        background:#7fd3f7 !important;
        z-index:60;
    }
</style>
<script>
    $(document).ready(function () {
        $('#exportCsvButton').on('click', function (e) {
            e.preventDefault();

            var data = {
                claimSummarys: JSON.stringify(<?= json_encode($claimSummarys) ?>),
                intMonth: JSON.stringify(<?= json_encode($intMonth) ?>),
                monthlist: JSON.stringify(<?= json_encode($monthList ?? []) ?>),
                year: '<?= $year ?>',
                month: '<?= $month ?>',
                claimType: '<?= $claimType ?>',
                staff: '<?= $staff ?>',
                hasEntitlement: '<?= $hasEntitlement ?>',
                _csrf: yii.getCsrfToken()
            };

            $.ajax({
                url: '/office/claim-entitlement/export-to-excel',
                type: 'POST',
                data: data,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response, status, xhr) {
                    // Get filename from server response header
                    var filename = 'Claim_Summary_Report.xls'; // Fallback only
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition) {
                        var matches = /filename="([^"]*)"/.exec(disposition);
                        if (matches && matches[1]) {
                            filename = matches[1];
                        }
                    }

                    // Trigger download
                    var blob = new Blob([response], {type: 'application/vnd.ms-excel'});
                    var link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();

                    // Cleanup
                    document.body.removeChild(link);
                    URL.revokeObjectURL(link.href);
                },
                error: function (xhr, status, error) {
                    console.error('Export failed:', error);
                    alert('Failed to export file. Please try again.');
                }
            });
        });
    });

    $(function () {
        $('.table-scroll tbody').on('mouseenter', 'td', function () {
            var colIndex = this.cellIndex;
            if (colIndex < 2)
                return;
            $('.table-scroll tbody tr').each(function () {
                $(this).children().removeClass('column-hover');
                if (this.cells[colIndex]) {
                    this.cells[colIndex].classList.add('column-hover');
                }
            });
        });
        $('.table-scroll tbody').on('mouseleave', function () {
            $('.column-hover').removeClass('column-hover');
        });
    });
</script>