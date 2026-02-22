<?php

use yii\helpers\Html;

$documents = $model->projectProductionDocuments;
echo "<h4>Documents:</h4>";
echo '<div class="list-group d-flex flex-row flex-wrap">';

foreach ($documents as $key => $document) {
    //<a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in</a>
    echo Html::a(Html::encode($document->filename) . ' <i class="fas fa-external-link-alt"></i>',
            ['/production/production/get-file-by-id', 'id' => $document->id],
            ['class' => 'list-group-item list-group-item-action text-primary col-10', 'target' => '_blank']);
    echo Html::a('<i class="fas fa-trash-alt"></i>',
            ['/production/production/delete-production-file', 'id' => $document->id],
            ['class' => 'list-group-item list-group-item-action text-danger col-1',
                'data' => ['confirm' => "Remove attachment?", 'method' => 'post']]);
}
echo '</div>';
?>