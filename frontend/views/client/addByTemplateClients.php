<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Import Outstanding Balance';
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<fieldset class="form-group border p-3">
    <legend class="w-auto px-2  m-0 ">Upload By Template:</legend>

    <div class="container-fluid">
        <div class="row">

            <div class="col-md-4">
                <?php
                $form = ActiveForm::begin([
                    'options' => ['enctype' => 'multipart/form-data'],
                ]);

                echo $form->field($clientDebt, 'tk_group_code')
                        ->dropDownList(frontend\models\common\RefCompanyGroupList::COMPANYGROUP3);
                ?>
            </div>

            <!-- Month -->
            <div class="col-md-4">
                <?= $form->field($clientDebt, 'month')->dropDownList([
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December',
                ], ['prompt' => 'Select Month']) ?>
            </div>

            <!-- Year -->
            <div class="col-md-4">
                <?php
                $currentYear = date('Y');

                $years = [];
                for ($y = 2020; $y <= $currentYear; $y++) {
                    $years[$y] = $y;
                }

                echo $form->field($clientDebt, 'year')
                        ->dropDownList($years, ['prompt' => 'Select Year']);
                ?>
            </div>

            <div class="col-md-12">
                <?php
                echo Html::fileInput('excelTemplate', null, [
                    'accept' => '.xls,.xlsx',
                    'required' => true,
                ]);

                echo Html::submitButton(
                        'Upload Excel <i class="fas fa-upload"></i>',
                        ['class' => 'btn btn-success mb-2 mt-1']
                );

                ActiveForm::end();
                ?>
            </div>

        </div>
    </div>

</fieldset>