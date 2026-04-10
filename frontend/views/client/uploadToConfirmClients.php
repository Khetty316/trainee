<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$this->title = 'Confirm and Submit';
$this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Import Outstanding Balance', 'url' => ['add-by-template-clients']];
$this->params['breadcrumbs'][] = $this->title;

$company = frontend\models\common\RefCompanyGroupList::findOne($companyGroup);
?>
<h4><?= Html::encode($this->title) ?></h4>

<?php $form = ActiveForm::begin([
    'method' => 'post',
    'action' => ['client/process-client-data'] 
]); ?>

<!--hidden input-->
<?= Html::hiddenInput('companyGroup', $companyGroup) ?>
<?= Html::hiddenInput('month', $month) ?>
<?= Html::hiddenInput('year', $year) ?>

<div class="mb-3">

    <strong>Company Group :</strong> <?= Html::encode($company->company_name) ?><br>

    <strong>Month :</strong> <?= date('F', mktime(0, 0, 0, $month, 1)) ?><br>

    <strong>Year :</strong> <?= Html::encode($year) ?>

</div>

<table class="table table-bordered">

    <thead>
        <tr>
            <th style="width:1%">#</th>
            <th>Cust. No</th>
            <th>Name</th>
            <th>Balance</th>
            <th style="width:1%">Delete</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($buffer as $index => $row): ?>

            <tr>

                <td class="text-right px-2 pt-1">
                    <?= $index + 1 ?>
                </td>

                <td>
                    <?=
                    Html::input(
                            'text',
                            "Clients[cust_no][$index]",
                            $row['cust_no'] ?? '',
                            ['class' => 'form-control']
                    )
                    ?>
                </td>

                <td>
                    <?=
                    Html::input(
                            'text',
                            "Clients[name][$index]",
                            $row['name'] ?? '',
                            ['class' => 'form-control']
                    )
                    ?>
                </td>

                <td>
                    <?=
                    Html::input(
                            'number',
                            "Clients[balance][$index]",
                            $row['balance'] ?? '',
                            ['class' => 'form-control',
                             'step' => 'any']
                    )
                    ?>
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm delete-row">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </td>
            </tr>

        <?php endforeach; ?>

    </tbody>

</table>

<p class="mb-5 pb-2">
    <?= Html::submitButton('Proceed', [
        'class' => 'btn btn-primary float-right mb-5'
    ]) ?>
</p>

<script>
    $(document).ready(function () {
        $('td').addClass('p-0');
        $('.table').on('click', '.delete-row', function () {
            $(this).closest('tr').remove();
        });
    });
</script>

<?php ActiveForm::end(); ?>