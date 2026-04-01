<?php
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

if ($moduleIndex === 'execStock') {
    $pageName = 'Stock - Executive';
    $url = 'inventory/inventory/brand-list?type=execStock';
    $url2 = 'inventory/inventory/add-by-template-brand?type=execStock';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Stock - Assistant';
    $url = 'inventory/inventory/brand-list?type=assistStock';
    $url2 = 'inventory/inventory/add-by-template-brand?type=assistStock';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Stock - Project Coordinator';
    $url = 'inventory/inventory/brand-list?type=projcoorStock';
    $url2 = 'inventory/inventory/add-by-template-brand?type=projcoorStock';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Stock - Head of Maintenance';
    $url = 'inventory/inventory/brand-list?type=maintenanceHeadStock';
    $url2 = 'inventory/inventory/add-by-template-brand?type=maintenanceHeadStock';
}

$this->title = 'Confirm and Submit Brand Details';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = $pageName;
$this->params['breadcrumbs'][] = ['label' => 'Brand List', 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => 'Upload Template', 'url' => [$url2]];
$this->params['breadcrumbs'][] = $this->title;
?>
<h4><?= Html::encode($this->title) ?></h4>
<?php $form = ActiveForm::begin(['action' => ['save-brand-details', 'type' => $moduleIndex]]); ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th style="width: 1%;">#</th>
            <th>Name</th>
            <th style="width: 1%;">Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($buffer as $index => $row): ?>
            <tr>
                <td class="text-right px-2 pt-1">
                    <?= $index + 1 ?>
                </td>
                <td>
                    <?= Html::input('text', "InventoryBrand[name][$index]", $row['name'], ['class' => 'form-control', 'required' => true]) ?>
                    <?php if (isset($errors[$index])): ?>
                        <small class="text-danger"><strong><?= $errors[$index] ?></strong></small>
                    <?php endif; ?>
                </td>
                <td class="text-center" >
                    <button type="button" class="btn btn-danger btn-sm delete-row"><i class="far fa-trash-alt"></i></button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="form-group">
    <?= Html::submitButton('Save to Database', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
        $('td').addClass('p-0');
        $('.table').on('click', '.delete-row', function () {
            $(this).closest('tr').remove();
        });
    });
</script>