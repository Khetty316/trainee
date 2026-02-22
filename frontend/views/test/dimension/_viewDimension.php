<table class="table table-sm table-bordered text-center">
    <tr>
        <th>Panel</th>
        <th>Subject</th>
        <th>H (mm)</th>
        <th>W (mm)</th>
        <th>D (mm)</th>
    </tr>
    <tbody id="listTBody">
        <?php foreach ($dimensionList as $key => $dimension) { ?>
            <tr class="p-0 m-0 tr_<?= $key ?>">
                <td rowspan="4">
                    <?= $dimension->panel_name ?: $panel->panel_description ?>
                </td>
                <td class="vmiddle">Drawing</td>
                <td>
                    <?= $dimension->drawing_h ?>
                </td>
                <td>
                    <?= $dimension->drawing_w ?>
                </td>
                <td>
                    <?= $dimension->drawing_d ?>
                </td>
            </tr>
            <tr class="p-0 m-0 tr_<?= $key ?>">
                <td class="vmiddle">As-built</td>
                <td>
                    <?= $dimension->built_h ?>
                </td>
                <td>
                    <?= $dimension->built_w ?>
                </td>
                <td>
                    <?= $dimension->built_d ?>
                </td>
            </tr>
            <tr class="p-0 m-0 tr_<?= $key ?>" >
                <td class="vmiddle">Error</td>
                <td>
                    <?= $dimension->error_h ?>
                </td>
                <td>
                    <?= $dimension->error_w ?>
                </td>
                <td>
                    <?= $dimension->error_d ?>
                </td>
            </tr>
            <tr class="p-0 m-0 tr_<?= $key ?>" >
                <td class="vmiddle">Result</td>
                <td>
                    <?= ($dimension->res_h === null) ? '' : ($dimension->res_h == 0 ? 'Fail' : 'Pass') ?>
                </td>
                <td>
                    <?= ($dimension->res_w === null) ? '' :  ($dimension->res_w == 0 ? 'Fail' : 'Pass') ?>
                </td>
                <td>
                    <?= ($dimension->res_d === null) ? '' :  ($dimension->res_d == 0 ? 'Fail' : 'Pass') ?>
                </td>
            </tr>
        <?php }
        ?>
    </tbody>
</table>
