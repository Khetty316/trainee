<?php

use yii\helpers\Html;

$this->title = "Confirm Panel Upload";
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->project->quotation_display_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $model->projectQType->project_id]];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->type0->project_type_name, 'url' => ['/projectqtype/view-project-q-type', 'id' => $model->projectQType->id]];
$this->params['breadcrumbs'][] = ['label' => $model->revision_description, 'url' => ['view-project-q-revision', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<h3>Confirm Panel Data</h3>

<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>#</th>
            <th>Panel Type</th>
            <th>Panel</th>
            <th>Panel Remark</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        <?php if (!empty($panelData)): ?>
            <?php foreach ($panelData as $key => $row): ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= Html::encode($row['panel_type']) ?></td>
                    <td><?= Html::encode($row['panel']) ?></td>
                    <td><?= Html::encode($row['remark']) ?></td>
                    <td><?= Html::encode($row['qty']) ?></td>
                    <td><?= Html::encode($row['unit']) ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td><?= number_format($row['total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center text-muted">No data found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="mt-3">
    <?= Html::a('Save to Database', 
        ['projectqrevision/save-panel-upload', 'revisionId' => $revisionId], 
        ['class' => 'btn btn-success']) ?>

    <?= Html::a('Cancel', 
        ['projectqrevision/view', 'id' => $revisionId], 
        ['class' => 'btn btn-secondary ml-2']) ?>
</div>

<?= Html::a('Save to Database', 
['projectqrevision/save-panel-upload', 'revisionId' => $revisionId], 
['class' => 'btn btn-success']) ?>
