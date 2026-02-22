<!--<table class="table table-sm table-bordered mt-3">
    <tr>
        <td class="pt-2 pl-2" height="18">&nbsp;Tested By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php //= $master->tested_by                ?></td>
        <td class="pt-2 pl-2" rowspan="3">&nbsp;Certified By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php //= $master->certified_by                ?></td>
    </tr>
    <tr>
        <td class="pt-2 pl-2" height="18">&nbsp;Signed By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </td>
    </tr>
    <tr>
        <td class="pt-2 pl-2" height="25">&nbsp;Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php //= date('j F Y', strtotime($master->date))                ?></td>
        <td class="pt-2 pl-2" height="18">&nbsp;Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php //= date('j F Y')                ?></td>
    </tr>
</table>-->
<?php if ($main->test_type === \frontend\models\test\TestMain::TEST_FAT_TITLE) { ?>
    <?php
    
    if ($formStatus->status == frontend\models\test\RefTestStatus::STS_FAIL || $formStatus->status == frontend\models\test\RefTestStatus::STS_COMPLETE) {
        ?>
        <table class="table table-sm table-bordered mt-5" style="table-layout: fixed; width: 100%;">
            <tr>
                <td style="height: 45px;">
                    <br><br>
                    &nbsp;Tested By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= $master->tested_by ?>
                </td>
                <td rowspan="3" style="width: 50%; text-align: left;">
                    <br>
                    &nbsp;Certified By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                    <div style="text-align: center; margin-top: 5px;">
                        <?php
                        $stampImg = '/frontend/uploads/sign/mbot.png';
                        ?>
                        <img src="<?= $stampImg ?>" alt="Stamp" style="height: 120px; width: auto;">
                    </div>
                </td>
            </tr>
            <tr>
                <td style="height: 45px;">
                    <br><br>
                    &nbsp;Signed By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:
                </td>
            </tr>
            <tr>
                <td style="height: 45px;">
                    <br><br>
                    &nbsp;Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= date('j F Y', strtotime($master->date)) ?>
                </td>
            </tr>
        </table>

        <?php
        $hasSignature = array_filter($witnesses, function ($witness) {
            return !empty($witness->signature);
        });

        if (!empty($hasSignature)) {
            ?>
            <br>
            <p>Witnessed By :</p>
        <?php } ?>

        <table class="table table-bordered mt-3">
            <?php
            if ($witnesses) {
                $columnsPerRow = 5;
                $currentColumn = 0;
                $openRow = false;

                foreach ($witnesses as $key => $witness) {
                    if (!$witness->signature) {
                        continue;
                    }

                    if ($currentColumn % $columnsPerRow == 0) {
                        if ($openRow) {
                            echo '</tr>';
                        }
                        echo '<tr>';
                        $openRow = true;
                    }
                    ?>
                    <td height="30" style="text-align: center; vertical-align: middle;">
                        <?= $witness->name ?>
                        <br/><img src="<?= $witness->signature ?>" style="width: 50px;" height="30"/>
                    </td>
                    <?php
                    $currentColumn++;
                }

                // Close the last row if it was opened and not closed yet
                if ($openRow) {
                    echo '</tr>';
                }
            }
            ?>
        </table>

    <?php } else { ?>
        <p></p><p></p>
        <table class="table table-sm table-bordered mt-5">
            <tr>
                <td class="pt-2 pl-2" height="18">
                    &nbsp;Tested By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= $master->tested_by ?></td>
                <td class="pt-2 pl-2" rowspan="3">
                    &nbsp;Certified By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php //= $master->certified_by                ?></td>
            </tr>
            <tr>
                <td class="pt-2 pl-2" height="18">
                    &nbsp;Signed By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </td>
            </tr>
            <tr>
                <td class="pt-2 pl-2" height="25">
                    &nbsp;Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= date('j F Y', strtotime($master->date)) ?></td>
            </tr>
        </table>
        <?php
    }
} else {
    ?>
    <p></p>
    <table class="table table-sm table-bordered mt-3">
        <tr>
            <td class="pt-2 pl-2" height="18">&nbsp;Tested By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= $master->tested_by ?></td>
            <td class="pt-2 pl-2" rowspan="3">&nbsp;Signed By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php //= $master->certified_by                ?></td>
        </tr>
        <tr>
            <td class="pt-2 pl-2" height="18">&nbsp;Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= date('j F Y', strtotime($master->date)) ?></td>
        </tr>
    </table>
    <?php
}
?>
