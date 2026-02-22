<?php if ($noData): ?>
    <tr class="p-0 m-0">
        <td style="border: 1px solid black;" width="5%"></td>
        <td style="border: 1px solid black;" width="20%"></td>
        <td style="border: 1px solid black;" width="20%"></td>
        <td style="border: 1px solid black;" width="20%"></td>
        <td style="border: 1px solid black;" width="15%"></td>
        <td style="border: 1px solid black;" width="20%"> 
            <div width="100%">
                <img src="" height="35">
            </div>
        </td>
    </tr>
<?php else : ?>
    <tr class="p-0 m-0" id='tr_<?= $key ?>'>
        <td style="border: 1px solid black;" width="5%"><?= $key + 1 ?></td>
        <td style="border: 1px solid black;" width="20%"><?= $attendance->name ?></td>
        <td style="border: 1px solid black;" width="20%"><?= $attendance->org ?></td>
        <td style="border: 1px solid black;" width="20%"><?= $attendance->designation ?></td>
        <td style="border: 1px solid black;" width="15%"><?= $attendance->role ?></td>
        <td style="border: 1px solid black;" width="20%"> 
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
