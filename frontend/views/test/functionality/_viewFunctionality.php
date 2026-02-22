<?php
foreach ($functionalities as $key => $data) {
    $detail = $data['detail'];
    $functionality = $data['items'];
    ?>
    <div class="mb-3">
        <h5 class="mb-0">Point of Test: 
            <span>
                <?= $detail->pot0->name ?>-<?= $detail->pot_val ?>
            </span>
        </h5>
    </div>
    <table class="table table-sm text-center table-bordered">
        <thead>
            <tr>
                <th class="vmiddle" rowspan="2">No.</th>
                <th class="vmiddle" rowspan="2">Feeder Tag No.</th>
                <th rowspan="1" colspan="4">Output</th>
            </tr>
            <tr>
                <th rowspan="1">Voltage At Power</br>Terminal (V)</th>
                <th rowspan="1">Pass / Fail</br> / Not Available</th>
                <th rowspan="1">Wiring</br>Termination</br>Connection</th>
                <th rowspan="1">Pass / Fail</br> / Not Available</th>
            </tr>
        </thead>
        <tbody  class="sortableTable">
            <?php
            foreach ($functionality as $val => $function) {
                ?>
                <tr data-functionality-id="<?= $function->id ?>" data-detail-id="<?= $key ?>">
                    <td><?= $function->no ?></td>
                    <td><?= $function->feeder_tag ?></td>
                    <td><?= $function->voltage_apt ?></td>
                    <td><?= $function->voltage_apt_sts == 1 ? '<i class="fas fa-check text-success"></i>' : ($function->voltage_apt_sts === null ? '<span class="text-warning">N/A</span>' : '<i class="fas fa-times text-danger"></i>') ?></td>
                    <td><?= $function->wiring_tc ?></td>
                    <td><?= $function->wiring_tc_sts == 1 ? '<i class="fas fa-check text-success"></i>' : ($function->wiring_tc_sts === null ? '<span class="text-warning">N/A</span>' : '<i class="fas fa-times text-danger"></i>') ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}?>