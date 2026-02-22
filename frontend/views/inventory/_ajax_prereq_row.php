<?php
use yii\helpers\Html;

/* 
 * This file is used by the ajax-add-form-item action
 * Returns HTML for a new row
 */

// Create new item model
$model = new \frontend\models\office\preReqForm\PrereqFormItem();
$model->id = 'new_' . $key;

// Render the edit cells for the new row
echo '<tr id="tr_' . $key . '" data-index="' . $key . '">';
echo '<td class="text-center">' . ($key + 1) . '</td>';
echo $this->render('_prereq_edit_cells', [
    'form' => null, // No form for ajax rows
    'model' => $model,
    'index' => $key,
    'moduleIndex' => $moduleIndex,
    'hasSuperiorUpdate' => $hasSuperiorUpdate,
    'worklist' => null,
    'departmentList' => $departmentList,
    'supplierList' => $supplierList,
    'brandList' => $brandList,
    'currencyList' => $currencyList,
]);
echo '</tr>';