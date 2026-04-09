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
    Html::submitButton('Confirm Save', [
        'class' => 'btn btn-success float-right ',
    ])
    ?>
    
    <?=
    Html::a(
            'Export to CSV <i class="fas fa-file-csv fa-lg"></i>',
            ['client/export-not-found-clients'],
            [
                'class' => 'btn btn-primary float-right mr-1',
                'id' => 'exportCsvButton',
                'encode' => false
            ]
    )
    ?>
</p>

<?= Html::endForm() ?>