<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Confirm & Save';
$this->params['breadcrumbs'][] = ['label' => 'Project Quotation List', 'url' => ['/projectquotation/index']];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->project->quotation_display_no, 'url' => ['/projectquotation/view-projectquotation', 'id' => $model->projectQType->project_id]];
$this->params['breadcrumbs'][] = ['label' => $model->projectQType->type0->project_type_name, 'url' => ['/projectqtype/view-project-q-type', 'id' => $model->projectQType->id]];
$this->params['breadcrumbs'][] = ['label' => $model->revision_description, 'url' => ['view-project-q-revision', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$form = ActiveForm::begin([
    'method' => 'post',
    'action' => ['projectqrevision/save-panel-upload', 'revisionId' => $revisionId]
        ]);
?>

<?php
$panelData = $panelData ?? [];
$errors = $errors ?? [];
?>

<h2>Confirm & Save</h2>

<?php if (empty($panelData)): ?>
    <p>No data found.</p>
<?php else: ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Panel Type</th>
                <th>Panel</th>
                <th>Panel Remark</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($panelData as $index => $row): ?>

                <tr>

                    <td class="text-right px-2 pt-1">
                        <?= $index + 1 ?>
                    </td>

                    <td>
                        <?=
                        Html::dropDownList(
                                "Panel[$index][panel_type]",
                                $row['panel_type'] ?? '',
                                [
                                    'auto' => 'Automation',
                                    'enc' => 'Enclosure / Others',
                                    'lv' => 'Power DB',
                                    'mech' => 'Mechanical',
                                    'serv' => 'Service',
                                    'trade' => 'Trading',
                                ],
                                ['class' => 'form-control', 'prompt' => 'Select Panel Type']
                        )
                        ?>

                        <?php if (!empty($errors[$index]['panel_type'])): ?>
                            <div class="text-danger" style="font-size:12px;">
                                <?= $errors[$index]['panel_type'] ?>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php
                        $value = $row['panel'] ?? '';

                        if ($value == '#REF!' || $value == '=#REF!') {
                            $value = '';
                        }
                        ?>
                        <?=
                        Html::input(
                                'text',
                                "Panel[$index][panel]",
                                $value,
                                ['class' => 'form-control']
                        )
                        ?>

                        <?php if (!empty($errors[$index]['panel'])): ?>
                            <div class="text-danger" style="font-size:12px;">
                                <?= $errors[$index]['panel'] ?>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?=
                        Html::input(
                                'text',
                                "Panel[$index][remark]",
                                $row['remark'] ?? '',
                                ['class' => 'form-control']
                        )
                        ?>
                    </td>

                    <td>
                        <?=
                        Html::input(
                                'number',
                                "Panel[$index][qty]",
                                $row['qty'] ?? '',
                                ['class' => 'form-control',
                                    'step' => 'any']
                        )
                        ?>

                        <?php if (!empty($errors[$index]['qty'])): ?>
                            <div class="text-danger" style="font-size:12px;">
                                <?= $errors[$index]['qty'] ?>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?=
                        Html::dropDownList(
                                "Panel[$index][unit]",
                                $row['unit'] ?? '',
                                [
                                    '' => 'Select Unit',
                                    'lot' => 'LOT',
                                    'md' => 'MANDAY',
                                    'meter' => 'METER',
                                    'night' => 'NIGHT',
                                    'nos' => 'NO',
                                    'pc' => 'PC',
                                    'roll' => 'ROLL',
                                    'sets' => 'SET',
                                    'trip' => 'TRIP',
                                    'unit' => 'UNIT',
                                ],
                                ['class' => 'form-control']
                        )
                        ?>

                        <?php if (!empty($errors[$index]['unit'])): ?>
                            <div class="text-danger" style="font-size:12px;">
                                <?= $errors[$index]['unit'] ?>
                            </div>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?=
                        Html::input(
                                'number',
                                "Panel[$index][price]",
                                $row['price'] ?? '',
                                [
                                    'class' => 'form-control',
                                    'step' => 'any'
                                ]
                        )
                        ?>

                        <?php if (!empty($errors[$index]['price'])): ?>
                            <div class="text-danger" style="font-size:12px;">
                                <?= $errors[$index]['price'] ?>
                            </div>
                        <?php endif; ?>
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

<?php endif; ?>

<div class="mt-3">
    <?=
    Html::submitButton('Confirm & Save', ['class' => 'btn btn-success'])
    ?>

    <?=
    Html::a('Cancel',
            ['projectqrevision/upload-template', 'revisionid' => $revisionId],
            ['class' => 'btn btn-secondary'])
    ?>
</div>

<?php
ActiveForm::end();
?>

<script>
    $(document).ready(function () {

        $('td').addClass('p-0');

        $('.table').on('click', '.delete-row', function () {
            $(this).closest('tr').hide();

            $(this).closest('tr').find('input, select').val('');
        });

        $(document).on('submit', 'form', function () {

            console.log('REINDEX RUNNING');

        });

    });
</script>
