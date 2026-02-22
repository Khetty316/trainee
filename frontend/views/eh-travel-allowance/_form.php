<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\employeeHandbook\EhTravelAllowanceMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="eh-travel-allowance-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //= $form->field($model, 'eh_master_id')->textInput() ?>

    <div class="row">
        <div class="col-lg-5 col-md-6 col-sm-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Location</th>
                        <?php foreach ($gradeList as $grade): ?>
                            <th><?= $grade->name ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locationList as $location): ?>
                        <tr>
                            <td><?= $location->name ?></td>
                            <?php
                            foreach ($gradeList as $grade):
                                $amount = $dataMatrix[$location->code][$grade->code] ?? '';
                                ?>
                                <td>
                                    <input type="text" 
                                           name="Details[<?= $location->code ?>][<?= $grade->code ?>]" 
                                           value="<?= $amount ?>" 
                                           class="form-control" />
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
