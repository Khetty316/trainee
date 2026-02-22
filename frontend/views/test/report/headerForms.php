<div id="main">
    <h2>TENAGA KENARI SDN BHD</h2>
    <p>Lot 3203, Block 12, MTLD, Samajaya Free Industrial Zone, 93450 Kuching, Sarawak, Malaysia.</p>
    <table class="table table-sm table-bordered mt-4">
        <tbody>
            <tr>
                <td style="border: 1px solid black;">&nbsp;Description
                    <h3 align="center"><b><?= ($main->test_type === frontend\models\test\TestMain::TEST_FAT_TITLE) ? "FAT" : "ITP"; ?>&nbsp;-&nbsp;<?= $desc ?></b></h3>
                    <br>
                </td>
            </tr>
            <?php if ($desc === "COVER"): ?>
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Project&nbsp;:</td>
                    <td width="35%" style="border: 1px solid black;">&nbsp;<?= $project->name ?></td>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Client&nbsp;:</td>
                    <td width="35%" colspan="2" style="border: 1px solid black;">&nbsp;<?= $main->client ?></td>
                </tr>
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;TC Ref&nbsp;:</td>
                    <td width="35%" style="border: 1px solid black;">&nbsp;<?= $master->tc_ref ?></td>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Elect. <br>&nbsp;Consultant&nbsp;:</td>
                    <td width="35%" colspan="2" style="border: 1px solid black;">&nbsp;<?= $main->elec_consultant ?></td>
                </tr>
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Switchboard&nbsp;:</td>
                    <td width="35%" style="border: 1px solid black;">&nbsp;<?= $panel->panel_description ?></td>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Elect. <br>&nbsp;Contractor&nbsp;:</td>
                    <td width="35%" colspan="2" style="border: 1px solid black;">&nbsp;<?= $main->elec_contractor ?></td>
                </tr>
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Serial No.&nbsp;:</td>
                    <td width="35%" colspan="4" style="border: 1px solid black;">&nbsp;<?= $panel->project_production_panel_code ?></td>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Report date&nbsp;:</td>
                    <!--<td width="35%" colspan="4" style="border: 1px solid black;">&nbsp;<?php //= date('j F Y') ?></td>-->
                    <td width="35%" colspan="4" style="border: 1px solid black;">&nbsp;<?= date('j F Y', strtotime($master->date)) ?></td>
                </tr>
            <?php else : ?>
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Switchboard&nbsp;:&nbsp;</td>
                    <td width="85%" colspan="4" style="border: 1px solid black;">&nbsp;<?= $panel->panel_description ?></td>
                </tr>
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Rev. No&nbsp;:</td>
                    <td width="85%" style="border: 1px solid black;">&nbsp;<?= $model->rev_no ?? null ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
