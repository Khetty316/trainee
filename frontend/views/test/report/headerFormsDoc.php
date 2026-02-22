<div id="main">
    <h2>TENAGA KENARI SDN BHD</h2>
    <p>Lot 3203, Block 12, MTLD, Samajaya Free Industrial Zone, 93450 Kuching, Sarawak, Malaysia.</p>
    
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
        <tbody>
            <!-- Header row -->
            <tr>
                <td colspan="4" style="border: 1px solid black;">&nbsp;Description
                    <div style="text-align: center; font-weight: bold; font-size: 14pt; margin: 5px 0 10px;">
                        <?= ($main->test_type === frontend\models\test\TestMain::TEST_FAT_TITLE) ? "FAT" : "ITP"; ?> - <?= $desc ?>
                    </div>
                </td>
            </tr>
            
            <?php if ($desc === "COVER"): ?>
                <!-- Project and Client row -->
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Project&nbsp;:</td>
                    <td width="45%" style="border: 1px solid black;">&nbsp;<?= $project->name ?></td>
                    <td width="10%" style="border: 1px solid black;">&nbsp;Client&nbsp;:</td>
                    <td width="30%" style="border: 1px solid black;">&nbsp;<?= $main->client ?></td>
                </tr>
                
                <!-- TC Ref and Elect. Consultant row -->
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;TC Ref&nbsp;:</td>
                    <td width="45%" style="border: 1px solid black;">&nbsp;<?= $master->tc_ref ?></td>
                    <td width="10%" style="border: 1px solid black;">&nbsp;Elect.<br>&nbsp;Consultant&nbsp;:</td>
                    <td width="30%" style="border: 1px solid black;">&nbsp;<?= $main->elec_consultant ?></td>
                </tr>
                
                <!-- Switchboard and Elect. Contractor row -->
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Switchboard&nbsp;:</td>
                    <td width="45%" style="border: 1px solid black;">&nbsp;<?= $panel->panel_description ?></td>
                    <td width="10%" style="border: 1px solid black;">&nbsp;Elect.<br>Contractor&nbsp;:</td>
                    <td width="30%" style="border: 1px solid black;">&nbsp;<?= $main->elec_contractor ?></td>
                </tr>
                
                <!-- Serial No. and Report date row -->
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Serial No.&nbsp;:</td>
                    <td width="45%" style="border: 1px solid black;">&nbsp;<?= $panel->project_production_panel_code ?></td>
                    <td width="10%" style="border: 1px solid black;">&nbsp;Report date&nbsp;:</td>
                    <td width="30%" style="border: 1px solid black;">&nbsp;<?= date('j F Y') ?></td>
                </tr>
                
            <?php else : ?>
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Switchboard&nbsp;:</td>
                    <td width="85%" colspan="3" style="border: 1px solid black;">&nbsp;<?= $panel->panel_description ?></td>
                </tr>
                <tr>
                    <td width="15%" style="border: 1px solid black;">&nbsp;Rev. No&nbsp;:</td>
                    <td width="85%" colspan="3" style="border: 1px solid black;">&nbsp;<?= $model->rev_no ?? null ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>