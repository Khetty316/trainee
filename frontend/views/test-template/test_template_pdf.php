<meta http-equiv="content-type" content="charset=utf-8">
<?php

use common\models\myTools\MyFormatter;
use common\models\User;
?>
<style>
    body {
        font-size: 1em !important;
        line-height: 0.5 !important;
        font-family: 'Times Romance', serif !important;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 5px;
        text-align: left;
    }

    td {
        padding: 3px 5px 3px 5px;
        font-size: 12px !important;
    }

    table.table-bordered {
        border-collapse: collapse;
    }

    table.table-bordered th, table.table-bordered td {
        border: 1px solid black;
    }

    .title {
        margin-top: 50px;
        text-align: center !important;
    }

    .main-header {
        margin-bottom: 10px;
    }

    .cover, .program{
        page-break-after: always;
        margin-top: 50px; /* Adjust as needed for space between pages */
    }

    .content {
        margin-top: 20px;
        line-height: 1 !important;
        font-family: 'Times Romance', serif !important;
    }

    .attendance-list th, .attendance-list td{
        text-align: center;
        font-size: 12px !important;
    }

    .header th {
        text-align: left;
        font-weight: 5;
    }

    .header th h4 {
        display: block;
        margin: 0 auto;
    }
</style>

<body>
    <div class="cover">
        <div class="main-header">
            <h3>TENAGA KENARI SDN BHD</h3>
            <small>Lot 3203, Block 12, MTLD, Samajaya Free Industial Zone, 93450 Kuching, Sarawak, Malaysia.</small>
        </div>
        <div class="header">
            <table class="table table-striped table-bordered">
                <tr>
                    <th colspan="4" rowspan="3">
                        <small>Description</small>
                        <h4><?= ($main->test_type === 'fat') ? "FACTORY ACCEPTANCE TEST" : "INSPECTION TEST PLAN"; ?> - TEST PROCEDURE</h4>
                    </th>
                    <td>
                        <small>Document Reference: <br><?= $main->doc_ref ?></small>
                    </td>
                </tr>
                <tr>
                    <td><small>Rev. No: <?= $main->rev_no ?></small></td>
                </tr>
                <tr>
                    <td><small>Date: <?php //= date('j-n-Y', strtotime($master->date)) ?></small></td>
                </tr>
                <tbody>
                    <tr>
                        <td>Project :</td>
                        <td><?= $project->name ?></td>
                        <td>Client :</td>
                        <td colspan="2"><?= $main->client ?></td>
                    </tr>
                    <tr>
                        <td>TC Ref :</td>
                        <td><?php//= $master->tc_ref ?></td>
                        <td>Elect. Consultant :</td>
                        <td colspan="2"><?= $main->elec_consultant ?></td>
                    </tr>
                    <tr>
                        <td>Switchboard :</td>
                        <td><?= $panel->panel_description ?></td>
                        <td>Elect. Contractor :</td>
                        <td colspan="2">
                            <?= $main->client ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="proctest1">
            <h2><?= $testTemplate->proctest1 ?></h2>
        </div>
        <div class="proctest2">
            <h2><?= $testTemplate->proctest2 ?></h2>
        </div>
        <div class="proctest3">
            <h2><?= $testTemplate->proctest3 ?></h2>
        </div>
    </div>
</body>


