<?php

use yii\helpers\Html;

$this->title = 'Check Client Detail';
$this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Import Outstanding Balance', 'url' => ['add-by-template-clients']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="successContainer"></div> 

<?= Html::beginForm(['client/save-exist-client'], 'post', ['id' => 'saveForm']) ?>

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

<div class="d-flex justify-content-end mt-3 mb-4">
    <button type="submit" id="saveBtn" class="btn btn-primary">
        Proceed & Save
    </button>
</div>

<?= Html::endForm() ?>

<script>
    document.getElementById('saveForm').addEventListener('submit', function () {

        const btn = document.getElementById('saveBtn');
        btn.innerText = 'Processing...';
        btn.disabled = true;

        setTimeout(function () {

            setTimeout(function () {
                window.location.href = "<?= \yii\helpers\Url::to(['client/index']) ?>";
            }, 2000); // wait 2 seconds for download
        });

    });
</script>

<?php if (Yii::$app->session->get('download_invalid')): ?>
    <script>
        window.onload = function () {
            window.location.href = '<?= \yii\helpers\Url::to(['client/export-invalid-clients']) ?>';
        };
    </script>
    <?php Yii::$app->session->remove('download_invalid'); ?>
<?php endif; ?>
