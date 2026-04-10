<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Check Client Detail';
$this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Import Outstanding Balance', 'url' => ['add-by-template-clients']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::beginForm(['client/save-exist-client'], 'post') ?>

<div class="row">
    <div class="col-md-6">
        <h4 class="text-success">Existing Clients</h4>
        <table class="table table-bordered">
            <thead>
                <tr><th>#</th><th>Cust No</th><th>Name</th><th>Balance</th><th>Company Group</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($existData)): ?>
                    <?php foreach ($existData as $i => $row): ?>
                    
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= Html::encode($row['cust_no']) ?></td>
                            <td><?= Html::encode($row['name']) ?></td>
                            <td><?= Html::encode($row['balance']) ?></td>
                            <td><?= Html::encode($row['company_group']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No existing data</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="col-md-6">
        <h4 class="text-danger">Not Found Clients</h4>
        <table class="table table-bordered">
            <thead>
                <tr><th>#</th><th>Cust No</th><th>Name</th><th>Balance</th><th>Company Group</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($notExistData)): ?>
                    <?php foreach ($notExistData as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= Html::encode($row['cust_no']) ?></td>
                            <td><?= Html::encode($row['name']) ?></td>
                            <td><?= Html::encode($row['balance']) ?></td>
                            <td><?= Html::encode($row['company_group']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">All data valid</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<p class="mb-5">
    <?=
    Html::submitButton('Confirm & Save <i class="fas fa-check"></i>', [
        'class' => 'btn btn-success float-right',
    ])
    ?>

    <?=
    Html::a(
            'Export to CSV <i class="fas fa-file-csv"></i>',
            '#',
            [
                'class' => 'btn btn-primary float-right mr-1',
                'id' => 'exportCsvButton',
            ]
    )
    ?>
</p>

<script>
    $('#exportCsvButton').on('click', function (e) {
        e.preventDefault();
        var data = <?= json_encode($notExistData) ?>;

        $.ajax({
            url: '/client/export-not-found-clients',
            type: 'POST',
            data: {
                data: JSON.stringify(data),
                _csrf: yii.getCsrfToken()
            },
            success: function (response) {
                var blob = new Blob([response], {type: 'application/vnd.ms-excel'});
                var url = window.URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                var today = new Date();
                var day = String(today.getDate()).padStart(2, '0');
                var month = String(today.getMonth() + 1).padStart(2, '0');
                var year = today.getFullYear();
                var formattedDate = day + '-' + month + '-' + year;
                var companyGroup = <?= json_encode($companyGroup) ?>;

                link.download = companyGroup + ' - NotFoundClientsInDMS - ' + formattedDate + '.xls';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            }
        });
    });
</script>
<?= Html::endForm() ?>