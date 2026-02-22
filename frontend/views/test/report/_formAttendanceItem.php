<?php if ($noData): ?>
    <tr class="p-0 m-0">
        <td width="5%"></td>
        <td width="20%"></td>
        <td width="20%"></td>
        <td width="20%"></td>
        <td width="15%"></td>
        <td width="20%"> 
            <div width="100%">
                <img src="" height="35">
            </div>
        </td>
    </tr>
<?php else : ?>
    <tr class="p-0 m-0" id='tr_<?= $key ?>'>
        <td width="5%"><?= $key + 1 ?></td>
        <td width="20%"><?= $attendance->name ?></td>
        <td width="20%"><?= $attendance->org ?></td>
        <td width="20%"><?= $attendance->designation ?></td>
        <td width="15%"><?= $attendance->role ?></td>
        <td width="20%"> 
            <div width="100%">
                <?php if ($attendance->signature): ?>
                    <img src="<?= $attendance->signature ?>" alt="Signature Image" style="width: 100px;" height="35">
                <?php else : ?>
                    <img src="" height="35">
                <?php endif;
                ?>
            </div>
        </td>
    </tr>
<?php endif ?>
