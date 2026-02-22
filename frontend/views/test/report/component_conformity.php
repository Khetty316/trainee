<p><b>Components Conformity</b></p>
<table class="table table-sm table-bordered text-center">
    <thead>
        <tr>
            <td width="5%" class="vmiddle">No.</td>
            <td width="47%" class="vmiddle">Non-conform Component</td>
            <td width="47%" class="vmiddle">Remark</td>
        </tr>
    </thead>
    <?php
    if (empty($conformities)) {
        for ($i = 0; $i < 5; $i++) {
            ?>
            <tr height="30">
                <td width="5%"></td>
                <td width="47%"></td>
                <td width="47%"></td>
            </tr>
            <?php
        }
    } else {
        foreach ($conformities as $key => $conformity) {
            ?>
            <tr>
                <td width="5%" class="vmiddle text-center"><?= $key + 1 ?></td>
                <td width="47%" class="text-left"><?= $conformity->non_conform ?></td>
                <td width="47%" class="text-left"><?= $conformity->remark ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>