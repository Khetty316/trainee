<?php

use yii\bootstrap4\Html;

echo Html::a("<i class='fas fa-plus'></i>", "javascript:", [
    'title' => "Add a punchlist",
    "value" => yii\helpers\Url::to(['/test/punchlist/add-punchlist-from-form', 'masterId' => $id, 'formType' => $formType]),
    "class" => "modalButton btn",
    'id' => 'newPunchlistBtn',
    'data-modaltitle' => "Add a Punchlist",
]);
?>