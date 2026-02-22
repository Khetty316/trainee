<?php

use common\models\myTools\MyFormatter;
?>
<?php if ($noData): ?>
    <tr class="p-0 m-0">
        <td width="5%" height="30"></td>
        <td width="15%"></td>
        <td width="20%"></td>
        <td width="30%"></td>
        <td width="15%"></td>
        <td width="15%"></td>
    </tr>
<?php else : ?>
    <tr class="p-0 m-0" id='tr_<?= $key ?>'>
        <td class="text-center" width="5%"><?= $key + 1 ?></td>
        <td style="padding:10px;" width="15%"><?= $punchlist->testFormCode->formname ?></td>
        <td width="20%">&nbsp;<?= $punchlist->error->description ?></td>
        <td width="30%">&nbsp;<?= $punchlist->remark ?></td>
        <td class="text-center" width="15%"><?= MyFormatter::asDate_Read($punchlist->rectify_date) ?></td>
        <td class="text-center" width="15%"><?= $punchlist->verify_by ?></td>
    </tr>
<?php endif ?>
